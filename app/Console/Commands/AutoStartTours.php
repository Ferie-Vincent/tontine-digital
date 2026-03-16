<?php

namespace App\Console\Commands;

use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Models\ActivityLog;
use App\Models\Tontine;
use App\Models\TontineMessage;
use App\Services\NotificationService;
use App\Services\StatusTransitionService;
use Illuminate\Console\Command;

class AutoStartTours extends Command
{
    protected $signature = 'tontine:auto-start-tours';

    protected $description = 'Démarre automatiquement les tours dont la date d\'échéance est arrivée';

    public function handle(NotificationService $notificationService): int
    {
        $tontines = Tontine::where('status', TontineStatus::ACTIVE)->get();
        $totalStarted = 0;

        foreach ($tontines as $tontine) {
            if (!$tontine->getSetting('auto_start_tours', false)) {
                continue;
            }

            // Ne pas démarrer un nouveau tour s'il y en a déjà un en cours
            $hasOngoingTour = $tontine->tours()
                ->where('status', TourStatus::ONGOING)
                ->exists();

            if ($hasOngoingTour) {
                continue;
            }

            // Trouver le prochain tour UPCOMING dont la date est arrivée ou dépassée
            $tour = $tontine->tours()
                ->where('status', TourStatus::UPCOMING)
                ->where('due_date', '<=', now()->addDays($tontine->frequency->days()))
                ->orderBy('tour_number')
                ->first();

            if (!$tour) {
                continue;
            }

            if (!StatusTransitionService::canTransition('tour', 'UPCOMING', 'ONGOING')) {
                $this->warn("Transition UPCOMING → ONGOING non autorisée pour tour #{$tour->id}");
                continue;
            }

            $tour->update(['status' => TourStatus::ONGOING]);
            $totalStarted++;

            ActivityLog::log('auto_started_tour', $tour, tontineId: $tontine->id, properties: [
                'tour_number' => $tour->tour_number,
                'due_date' => $tour->due_date->toDateString(),
            ]);

            $beneficiary = $tour->beneficiary;

            $notificationService->notifyTontineMembers(
                $tontine,
                'tour_started',
                'Tour démarré automatiquement',
                'Le tour #' . $tour->tour_number . ' de ' . $tontine->name
                    . ' a démarré automatiquement. Bénéficiaire : ' . ($beneficiary ? $beneficiary->name : 'N/A')
                    . '. Échéance : ' . $tour->due_date->format('d/m/Y') . '.',
                ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
                sendEmail: true
            );

            TontineMessage::create([
                'tontine_id' => $tontine->id,
                'user_id' => null,
                'type' => 'system',
                'content' => 'Le tour #' . $tour->tour_number . ' a démarré automatiquement. Bénéficiaire : '
                    . ($beneficiary ? $beneficiary->name : 'N/A') . '. Échéance : ' . $tour->due_date->format('d/m/Y') . '.',
                'metadata' => ['tour_id' => $tour->id],
            ]);
        }

        $this->info("Tours démarrés automatiquement : {$totalStarted}.");

        return self::SUCCESS;
    }
}

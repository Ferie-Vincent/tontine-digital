<?php

namespace App\Console\Commands;

use App\Enums\TourStatus;
use App\Enums\TontineStatus;
use App\Models\ActivityLog;
use App\Models\Tontine;
use App\Models\Tour;
use App\Services\NotificationService;
use App\Services\StatusTransitionService;
use Illuminate\Console\Command;

class AutoCloseTours extends Command
{
    protected $signature = 'tontine:auto-close-tours';
    protected $description = 'Auto-confirme la réception et clôture les tours décaissés sans réponse du bénéficiaire';

    public function handle(NotificationService $notificationService): int
    {
        $pausedCount = Tontine::where('status', TontineStatus::PAUSED)->count();
        if ($pausedCount > 0) {
            $this->info("{$pausedCount} tontine(s) en pause ignorée(s).");
        }

        $tontines = Tontine::where('status', TontineStatus::ACTIVE)
            ->where('status', '!=', TontineStatus::PAUSED)
            ->get();
        $totalClosed = 0;

        foreach ($tontines as $tontine) {
            if (!$tontine->getSetting('auto_close_tour_enabled', false)) {
                continue;
            }

            $days = $tontine->getSetting('auto_close_tour_days', 7);

            $tours = $tontine->tours()
                ->where('status', TourStatus::ONGOING)
                ->whereNotNull('disbursed_at')
                ->whereNull('beneficiary_confirmed_at')
                ->where('disbursed_at', '<=', now()->subDays($days))
                ->with('beneficiary')
                ->get();

            foreach ($tours as $tour) {
                if (!StatusTransitionService::canTransition('tour', 'ONGOING', 'COMPLETED')) {
                    $this->warn("Transition ONGOING → COMPLETED non autorisée pour tour #{$tour->id}");
                    continue;
                }

                $tour->update([
                    'beneficiary_confirmed_at' => now(),
                    'status' => 'completed',
                    'notes' => ($tour->notes ? $tour->notes . "\n" : '') . "[Auto] Réception confirmée automatiquement après {$days} jours sans réponse du bénéficiaire.",
                ]);

                $totalClosed++;

                ActivityLog::log('auto_confirmed_receipt', $tour, tontineId: $tontine->id, properties: [
                    'days_since_disbursement' => $days,
                    'beneficiary_id' => $tour->beneficiary_id,
                ]);

                // Notifier le bénéficiaire
                if ($tour->beneficiary) {
                    $notificationService->send(
                        $tour->beneficiary->id,
                        'auto_receipt_confirmed',
                        'Réception confirmée automatiquement',
                        "La réception des fonds du tour #{$tour->tour_number} de {$tontine->name} a été confirmée automatiquement après {$days} jours. Si vous n'avez pas reçu les fonds, contactez l'administrateur.",
                        ['tontine_id' => $tontine->id, 'tour_id' => $tour->id]
                    );
                }

                // Notifier les managers
                $notificationService->notifyTontineManagers(
                    $tontine,
                    'tour_auto_closed',
                    'Tour clôturé automatiquement',
                    "Le tour #{$tour->tour_number} de {$tontine->name} a été clôturé automatiquement après {$days} jours sans confirmation de réception par " . ($tour->beneficiary?->name ?? 'le bénéficiaire') . ".",
                    ['tontine_id' => $tontine->id, 'tour_id' => $tour->id]
                );
            }
        }

        $this->info("Tours clôturés automatiquement : {$totalClosed}.");
        return self::SUCCESS;
    }
}

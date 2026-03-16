<?php

namespace App\Console\Commands;

use App\Enums\TontineStatus;
use App\Models\ActivityLog;
use App\Models\Tontine;
use App\Services\NotificationService;
use App\Services\TontineService;
use Illuminate\Console\Command;

class AutoGenerateTours extends Command
{
    protected $signature = 'tontine:auto-generate-tours';

    protected $description = 'Génère automatiquement les tours pour les tontines actives qui n\'ont pas encore de tours';

    public function handle(TontineService $tontineService, NotificationService $notificationService): int
    {
        $tontines = Tontine::where('status', TontineStatus::ACTIVE)
            ->whereNotNull('start_date')
            ->get();

        $totalGenerated = 0;

        foreach ($tontines as $tontine) {
            if (!$tontine->getSetting('auto_generate_tours', false)) {
                continue;
            }

            // Ne pas regénérer si des tours existent déjà
            if ($tontine->tours()->exists()) {
                continue;
            }

            // Vérifier qu'il y a au moins 2 membres actifs
            $activeMembersCount = $tontine->activeMembers()->count();
            if ($activeMembersCount < 2) {
                continue;
            }

            $tontineService->generateTours($tontine);

            $toursCount = $tontine->tours()->count();
            $totalGenerated += $toursCount;

            ActivityLog::log('auto_generated_tours', $tontine, tontineId: $tontine->id, properties: [
                'tours_count' => $toursCount,
                'members_count' => $activeMembersCount,
            ]);

            $notificationService->notifyTontineManagers(
                $tontine,
                'tours_auto_generated',
                'Tours générés automatiquement',
                "{$toursCount} tours ont été générés automatiquement pour la tontine {$tontine->name}.",
                ['tontine_id' => $tontine->id],
                sendEmail: true
            );
        }

        $this->info("Tours générés automatiquement : {$totalGenerated}.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Models\ActivityLog;
use App\Models\Tontine;
use App\Services\NotificationService;
use App\Services\StatusTransitionService;
use Illuminate\Console\Command;

class UpdateTontineStatuses extends Command
{
    protected $signature = 'tontine:update-statuses';

    protected $description = 'Met à jour automatiquement les statuts des tontines selon leurs conditions';

    public function handle(NotificationService $notificationService): int
    {
        $totalUpdated = 0;

        // 1. DRAFT → PENDING : quand le premier membre (hors créateur) rejoint
        $drafts = Tontine::where('status', TontineStatus::DRAFT)->get();

        foreach ($drafts as $tontine) {
            if (!$tontine->getSetting('auto_status_transitions', false)) {
                continue;
            }

            // Au moins 2 membres actifs (créateur + 1 autre)
            if ($tontine->activeMembers()->count() >= 2) {
                if (!StatusTransitionService::canTransition('tontine', 'DRAFT', 'PENDING')) {
                    $this->warn("Transition DRAFT → PENDING non autorisée pour tontine #{$tontine->id}");
                    continue;
                }
                $tontine->update(['status' => TontineStatus::PENDING]);
                $totalUpdated++;

                ActivityLog::log('auto_status_pending', $tontine, tontineId: $tontine->id, properties: [
                    'from' => 'draft',
                    'to' => 'pending',
                    'reason' => 'Membres suffisants pour passer en attente',
                ]);

                $notificationService->notifyTontineManagers(
                    $tontine,
                    'tontine_status_changed',
                    'Tontine en attente',
                    "La tontine {$tontine->name} est passée en statut \"En attente\" automatiquement (membres inscrits).",
                    ['tontine_id' => $tontine->id]
                );
            }
        }

        // 2. PENDING → ACTIVE : nombre minimum de membres atteint + date de début arrivée
        $pendings = Tontine::where('status', TontineStatus::PENDING)->get();

        foreach ($pendings as $tontine) {
            if (!$tontine->getSetting('auto_status_transitions', false)) {
                continue;
            }

            $minMembers = $tontine->getSetting('min_members_to_start', 3);
            $activeMembersCount = $tontine->activeMembers()->count();
            $startDateReached = $tontine->start_date && $tontine->start_date->lte(now());

            if ($activeMembersCount >= $minMembers && $startDateReached) {
                if (!StatusTransitionService::canTransition('tontine', 'PENDING', 'ACTIVE')) {
                    $this->warn("Transition PENDING → ACTIVE non autorisée pour tontine #{$tontine->id}");
                    continue;
                }
                $tontine->update(['status' => TontineStatus::ACTIVE]);
                $totalUpdated++;

                ActivityLog::log('auto_status_active', $tontine, tontineId: $tontine->id, properties: [
                    'from' => 'pending',
                    'to' => 'active',
                    'members_count' => $activeMembersCount,
                    'reason' => "Nombre minimum de membres ({$minMembers}) atteint et date de début arrivée",
                ]);

                $notificationService->notifyTontineMembers(
                    $tontine,
                    'tontine_activated',
                    'Tontine activée',
                    "La tontine {$tontine->name} est maintenant active ! Les tours vont bientôt commencer.",
                    ['tontine_id' => $tontine->id],
                    sendEmail: true
                );
            }
        }

        // 3. ACTIVE → COMPLETED : tous les tours sont terminés et décaissés
        $actives = Tontine::where('status', TontineStatus::ACTIVE)->get();

        foreach ($actives as $tontine) {
            if (!$tontine->getSetting('auto_status_transitions', false)) {
                continue;
            }

            $totalTours = $tontine->tours()->count();

            // Il faut au moins 1 tour pour pouvoir compléter
            if ($totalTours === 0) {
                continue;
            }

            // Vérifier que TOUS les tours sont en statut COMPLETED (pas upcoming, ongoing ou failed)
            $nonCompletedTours = $tontine->tours()
                ->whereNot('status', TourStatus::COMPLETED)
                ->count();

            if ($nonCompletedTours === 0) {
                // Vérifier que tous les tours ont été décaissés
                $nonDisbursedTours = $tontine->tours()
                    ->whereNull('disbursed_at')
                    ->count();

                if ($nonDisbursedTours === 0) {
                    if (!StatusTransitionService::canTransition('tontine', 'ACTIVE', 'COMPLETED')) {
                        $this->warn("Transition ACTIVE → COMPLETED non autorisée pour tontine #{$tontine->id}");
                        continue;
                    }
                    $tontine->update(['status' => TontineStatus::COMPLETED]);
                    $totalUpdated++;

                    ActivityLog::log('auto_status_completed', $tontine, tontineId: $tontine->id, properties: [
                        'from' => 'active',
                        'to' => 'completed',
                        'total_tours' => $totalTours,
                        'reason' => 'Tous les tours terminés et décaissés',
                    ]);

                    $notificationService->notifyTontineMembers(
                        $tontine,
                        'tontine_completed',
                        'Tontine terminée',
                        "La tontine {$tontine->name} est terminée ! Tous les tours ont été complétés et les fonds décaissés. Merci à tous les membres !",
                        ['tontine_id' => $tontine->id],
                        sendEmail: true
                    );
                }
            }
        }

        $this->info("Statuts de tontines mis à jour : {$totalUpdated}.");

        return self::SUCCESS;
    }
}

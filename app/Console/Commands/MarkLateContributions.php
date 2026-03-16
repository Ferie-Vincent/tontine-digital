<?php

namespace App\Console\Commands;

use App\Enums\ContributionStatus;
use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Models\ActivityLog;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Models\TontineMember;
use App\Services\MemberCleanupService;
use App\Services\NotificationService;
use App\Services\PenaltyService;
use App\Services\StatusTransitionService;
use Illuminate\Console\Command;

class MarkLateContributions extends Command
{
    protected $signature = 'tontine:mark-late';

    protected $description = 'Marque les contributions en retard et applique les pénalités/exclusions automatiques';

    public function handle(NotificationService $notificationService, PenaltyService $penaltyService): int
    {
        $pausedCount = Tontine::where('status', TontineStatus::PAUSED)->count();
        if ($pausedCount > 0) {
            $this->info("{$pausedCount} tontine(s) en pause ignorée(s).");
        }

        $totalMarked = 0;
        $totalExcluded = 0;

        // Charger les tontines par lots avec les tours éligibles pré-chargés
        Tontine::where('status', TontineStatus::ACTIVE)
            ->chunk(50, function ($tontines) use ($notificationService, $penaltyService, &$totalMarked, &$totalExcluded) {
                foreach ($tontines as $tontine) {
                    if (!$tontine->getSetting('late_detection_enabled', false)) {
                        continue;
                    }

                    $thresholdDays = $tontine->getSetting('late_threshold_days', 3);
                    $usePenaltyService = $tontine->getSetting('penalty_enabled', false);
                    $penaltyAmount = $usePenaltyService ? 0 : $tontine->getSetting('late_penalty_amount', 0);

                    // Charger tours + contributions en une seule requête eager
                    $ongoingTours = $tontine->tours()
                        ->where('status', TourStatus::ONGOING)
                        ->whereNotNull('due_date')
                        ->where('due_date', '<=', now()->subDays($thresholdDays))
                        ->with(['contributions' => fn ($q) => $q
                            ->whereIn('status', [ContributionStatus::PENDING, ContributionStatus::DECLARED])
                            ->with('user'),
                        ])
                        ->get();

                    foreach ($ongoingTours as $tour) {
                        $lateContributions = $tour->contributions;

                        if ($lateContributions->isEmpty()) {
                            continue;
                        }

                        // Calculer les pénalités et préparer la mise à jour groupée
                        $bulkUpdateIds = [];
                        $contributionsToNotify = [];

                        foreach ($lateContributions as $contribution) {
                            $fromStatus = StatusTransitionService::resolveEnumName($contribution->status);
                            if (!StatusTransitionService::canTransition('contribution', $fromStatus, 'LATE')) {
                                $this->warn("Transition {$fromStatus} → LATE non autorisée pour contribution #{$contribution->id}");
                                continue;
                            }

                            $contributionPenalty = $usePenaltyService
                                ? $penaltyService->calculatePenalty($tontine, $contribution)
                                : $penaltyAmount;

                            // Si toutes les pénalités sont identiques, on peut faire un bulk update
                            // Sinon on met à jour individuellement (cas PenaltyService dynamique)
                            if ($usePenaltyService) {
                                $contribution->update([
                                    'status' => ContributionStatus::LATE,
                                    'penalty_amount' => $contributionPenalty,
                                ]);
                            } else {
                                $bulkUpdateIds[] = $contribution->id;
                            }

                            $totalMarked++;
                            $contributionsToNotify[] = ['contribution' => $contribution, 'penalty' => $contributionPenalty];

                            ActivityLog::log('marked_late', $contribution, userId: $contribution->user_id, tontineId: $tontine->id, properties: [
                                'penalty_amount' => $contributionPenalty,
                            ]);
                        }

                        // Bulk update pour pénalité fixe (même montant pour tous)
                        if (!empty($bulkUpdateIds)) {
                            Contribution::whereIn('id', $bulkUpdateIds)->update([
                                'status' => ContributionStatus::LATE,
                                'penalty_amount' => $penaltyAmount,
                            ]);
                        }

                        // Envoyer les notifications en batch
                        $notifyUserIds = [];
                        foreach ($contributionsToNotify as $item) {
                            $c = $item['contribution'];
                            $penaltyText = $item['penalty'] > 0
                                ? ' Une pénalité de ' . format_amount($item['penalty']) . ' a été appliquée.'
                                : '';

                            $notifyUserIds[] = $c->user_id;

                            $notificationService->send(
                                $c->user_id,
                                'contribution_late',
                                'Cotisation en retard',
                                "Votre cotisation pour le tour #{$tour->tour_number} de {$tontine->name} est en retard.{$penaltyText}",
                                ['tontine_id' => $tontine->id, 'tour_id' => $tour->id, 'contribution_id' => $c->id],
                                sendEmail: true
                            );
                        }

                        // Exclusion automatique
                        if ($tontine->getSetting('auto_exclusion_enabled', false) && $lateContributions->isNotEmpty()) {
                            $exclusionThreshold = $tontine->getSetting('auto_exclusion_threshold', 3);

                            // Précharger toutes les contributions des membres concernés
                            $affectedUserIds = $lateContributions->pluck('user_id')->unique()->values();
                            $allUserContributions = Contribution::whereIn('user_id', $affectedUserIds)
                                ->where('tontine_id', $tontine->id)
                                ->join('tours', 'contributions.tour_id', '=', 'tours.id')
                                ->orderByDesc('tours.tour_number')
                                ->select('contributions.user_id', 'contributions.status')
                                ->get()
                                ->groupBy('user_id');

                            // Précharger les membres actifs concernés en une seule requête
                            $activeMembers = TontineMember::where('tontine_id', $tontine->id)
                                ->whereIn('user_id', $affectedUserIds)
                                ->where('status', 'active')
                                ->get()
                                ->keyBy('user_id');

                            // Identifier les membres à exclure
                            $usersToExclude = [];
                            foreach ($lateContributions as $contribution) {
                                $userContributions = $allUserContributions->get($contribution->user_id, collect());
                                $lateCount = 0;
                                foreach ($userContributions as $c) {
                                    if ($c->status === ContributionStatus::LATE) {
                                        $lateCount++;
                                    } else {
                                        break;
                                    }
                                }

                                if ($lateCount >= $exclusionThreshold && !isset($usersToExclude[$contribution->user_id])) {
                                    $member = $activeMembers->get($contribution->user_id);
                                    if ($member) {
                                        $usersToExclude[$contribution->user_id] = [
                                            'member' => $member,
                                            'contribution' => $contribution,
                                            'late_count' => $lateCount,
                                        ];
                                    }
                                }
                            }

                            // Bulk update des statuts des membres exclus
                            if (!empty($usersToExclude)) {
                                $excludeUserIds = array_keys($usersToExclude);
                                TontineMember::where('tontine_id', $tontine->id)
                                    ->whereIn('user_id', $excludeUserIds)
                                    ->where('status', 'active')
                                    ->update(['status' => 'excluded']);

                                $totalExcluded += count($usersToExclude);

                                foreach ($usersToExclude as $userId => $data) {
                                    $notificationService->send(
                                        $userId,
                                        'member_excluded',
                                        'Exclusion automatique',
                                        "Vous avez été automatiquement exclu de la tontine {$tontine->name} après {$data['late_count']} retards consécutifs.",
                                        ['tontine_id' => $tontine->id],
                                        sendEmail: true
                                    );

                                    $userName = $data['contribution']->user?->name ?? 'Membre';
                                    $notificationService->notifyTontineManagers(
                                        $tontine,
                                        'member_auto_excluded',
                                        'Membre exclu automatiquement',
                                        "{$userName} a été automatiquement exclu de {$tontine->name} après {$data['late_count']} retards consécutifs.",
                                        ['tontine_id' => $tontine->id, 'user_id' => $userId]
                                    );

                                    app(MemberCleanupService::class)->cleanup($tontine, $data['contribution']->user);

                                    ActivityLog::log('auto_excluded', $data['member'], userId: $userId, tontineId: $tontine->id, properties: [
                                        'consecutive_late_count' => $data['late_count'],
                                    ]);
                                }
                            }
                        }
                    }
                }
            });

        $this->info("Contributions marquées en retard : {$totalMarked}. Membres exclus : {$totalExcluded}.");

        return self::SUCCESS;
    }
}

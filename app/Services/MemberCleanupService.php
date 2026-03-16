<?php

namespace App\Services;

use App\Models\Tontine;
use App\Models\TontineMember;
use App\Models\User;
use App\Models\Contribution;
use App\Models\ActivityLog;
use App\Enums\TourStatus;
use App\Enums\ContributionStatus;
use App\Enums\MemberStatus;

class MemberCleanupService
{
    /**
     * Nettoyer les donnees d'un membre qui part ou est exclu.
     * Supprime les contributions PENDING des tours UPCOMING,
     * rejette les contributions DECLARED des tours ONGOING,
     * auto-reassigne le beneficiaire des tours UPCOMING,
     * et recalcule les expected_amount pour les tours UPCOMING et ONGOING.
     */
    public function cleanup(Tontine $tontine, User $user): array
    {
        $results = [
            'contributions_removed' => 0,
            'contributions_rejected' => 0,
            'beneficiary_reassigned' => 0,
            'beneficiary_alerts' => 0,
        ];

        $notificationService = app(NotificationService::class);

        // 1. Supprimer les contributions PENDING des tours UPCOMING
        $upcomingTourIds = $tontine->tours()
            ->where('status', TourStatus::UPCOMING)
            ->pluck('id');

        if ($upcomingTourIds->isNotEmpty()) {
            $results['contributions_removed'] = Contribution::whereIn('tour_id', $upcomingTourIds)
                ->where('user_id', $user->id)
                ->where('status', ContributionStatus::PENDING)
                ->delete();
        }

        // 2. Rejeter les contributions DECLARED des tours ONGOING
        $ongoingTourIds = $tontine->tours()
            ->where('status', TourStatus::ONGOING)
            ->pluck('id');

        if ($ongoingTourIds->isNotEmpty()) {
            $results['contributions_rejected'] = Contribution::whereIn('tour_id', $ongoingTourIds)
                ->where('user_id', $user->id)
                ->where('status', ContributionStatus::DECLARED)
                ->update([
                    'status' => ContributionStatus::REJECTED,
                    'notes' => 'Rejet automatique : membre ayant quitte la tontine',
                ]);
        }

        // 3. Auto-reassigner le beneficiaire des tours UPCOMING
        $beneficiaryTours = $tontine->tours()
            ->where('status', TourStatus::UPCOMING)
            ->where('beneficiary_id', $user->id)
            ->get();

        if ($beneficiaryTours->isNotEmpty()) {
            // Trouver les membres actifs qui ne sont pas deja beneficiaires d'un tour
            $existingBeneficiaryIds = $tontine->tours()
                ->whereIn('status', [TourStatus::UPCOMING, TourStatus::ONGOING])
                ->where('beneficiary_id', '!=', $user->id)
                ->pluck('beneficiary_id');

            foreach ($beneficiaryTours as $tour) {
                $nextMember = $this->findNextEligibleMember($tontine, $user, $existingBeneficiaryIds);

                if ($nextMember) {
                    $tour->update(['beneficiary_id' => $nextMember->user_id]);
                    $existingBeneficiaryIds->push($nextMember->user_id);
                    $results['beneficiary_reassigned']++;

                    // Notifier les managers de la reassignation automatique
                    $newBeneficiary = $nextMember->user;
                    $notificationService->notifyTontineManagers(
                        $tontine,
                        'beneficiary_reassigned',
                        'Beneficiaire reassigne automatiquement',
                        "Le membre {$user->name} a quitte la tontine. Le beneficiaire du tour #{$tour->tour_number} a ete automatiquement reassigne a {$newBeneficiary->name}.",
                        ['tontine_id' => $tontine->id, 'tour_id' => $tour->id, 'old_user_id' => $user->id, 'new_user_id' => $nextMember->user_id],
                        sendEmail: true
                    );
                } else {
                    // Aucun membre eligible trouve, alerter les managers
                    $results['beneficiary_alerts']++;
                    $notificationService->notifyTontineManagers(
                        $tontine,
                        'member_left_beneficiary',
                        'Beneficiaire a quitte la tontine',
                        "Le membre {$user->name} a quitte la tontine mais etait beneficiaire du tour #{$tour->tour_number}. Aucun membre eligible pour la reassignation automatique. Veuillez reassigner le beneficiaire manuellement.",
                        ['tontine_id' => $tontine->id, 'tour_id' => $tour->id, 'user_id' => $user->id],
                        sendEmail: true
                    );
                }
            }
        }

        // 4. Recalculer expected_amount pour les tours UPCOMING et ONGOING
        $recalcStatuses = [TourStatus::UPCOMING, TourStatus::ONGOING];
        foreach ($tontine->tours()->whereIn('status', $recalcStatuses)->get() as $tour) {
            $expectedAmount = $tour->contributions()->sum('amount');
            $tour->update(['expected_amount' => $expectedAmount]);
        }

        // 5. Logger le nettoyage
        if ($results['contributions_removed'] > 0 || $results['contributions_rejected'] > 0 || $results['beneficiary_reassigned'] > 0 || $results['beneficiary_alerts'] > 0) {
            ActivityLog::log('member_cleanup', $tontine, userId: $user->id, tontineId: $tontine->id, properties: $results);
        }

        return $results;
    }

    /**
     * Trouver le prochain membre eligible en ordre round-robin par position.
     */
    private function findNextEligibleMember(Tontine $tontine, User $departingUser, $existingBeneficiaryIds): ?TontineMember
    {
        return $tontine->members()
            ->where('status', MemberStatus::ACTIVE)
            ->where('user_id', '!=', $departingUser->id)
            ->whereNotIn('user_id', $existingBeneficiaryIds)
            ->orderBy('position')
            ->first();
    }
}

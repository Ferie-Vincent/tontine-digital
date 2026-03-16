<?php

namespace App\Services;

use App\Enums\ContributionStatus;
use App\Enums\TourStatus;
use App\Events\ContributionConfirmed;
use App\Events\ContributionDeclared;
use App\Events\TourCompleted;
use App\Models\ActivityLog;
use App\Models\Contribution;
use App\Models\PaymentProof;
use App\Models\TontineMessage;
use App\Services\StatusTransitionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ContributionService
{
    public function declare(
        Contribution $contribution,
        array $proofData,
        ?string $screenshotPath,
        ?string $notes,
        int $declaredBy
    ): void {
        StatusTransitionService::validateTransition(
            'contribution',
            StatusTransitionService::resolveEnumName($contribution->status),
            'DECLARED'
        );

        DB::transaction(function () use ($contribution, $proofData, $screenshotPath, $notes, $declaredBy) {
            // Validation du montant attendu
            $tontine = $contribution->tontine;
            $member = $tontine->members()->where('user_id', $contribution->user_id)->first();
            $expectedAmount = ($member->parts ?? 1) * $tontine->contribution_amount;
            $requiresReview = false;

            if ($contribution->amount != $expectedAmount) {
                $deviation = abs($contribution->amount - $expectedAmount) / $expectedAmount * 100;
                $amountNote = '[MONTANT INHABITUEL] Montant déclaré : ' . format_amount($contribution->amount)
                    . ' — Montant attendu : ' . format_amount($expectedAmount)
                    . ' (écart de ' . round($deviation, 1) . '%)';
                $notes = ($notes ? $notes . "\n" : '') . $amountNote;

                // Écart > 10% : nécessite une vérification par un gestionnaire
                if ($deviation > 10) {
                    $requiresReview = true;
                }
            }

            $contribution->update([
                'status' => 'declared',
                'declared_at' => now(),
                'notes' => $notes,
                'requires_review' => $requiresReview,
            ]);

            // Check for duplicates
            $detector = app(DuplicatePaymentDetector::class);
            $result = $detector->check(
                $contribution->tontine_id,
                $contribution->id,
                $proofData['transaction_reference'] ?? null,
                $proofData['sender_phone'] ?? null,
                $contribution->amount,
                $proofData['transaction_date'] ?? null
            );

            // Block declaration if exact reference match (HIGH severity)
            if ($result['blocked']) {
                // Revert contribution status back to pending
                $contribution->update([
                    'status' => 'pending',
                    'declared_at' => null,
                    'notes' => $notes,
                ]);

                $blockedMessage = collect($result['warnings'])
                    ->where('severity', 'high')
                    ->pluck('message')
                    ->implode(' ');

                throw new \App\Exceptions\DuplicatePaymentException($blockedMessage);
            }

            $warnings = $result['warnings'];

            if (!empty($warnings)) {
                $warningMessages = collect($warnings)->pluck('message')->implode(' | ');
                $contribution->update([
                    'notes' => ($notes ? $notes . "\n" : '') . '[ALERTE DOUBLON] ' . $warningMessages,
                ]);
            }

            // Supprimer l'ancien justificatif si re-déclaration
            if ($contribution->paymentProof) {
                if ($contribution->paymentProof->screenshot_path) {
                    Storage::disk('public')->delete($contribution->paymentProof->screenshot_path);
                }
                $contribution->paymentProof->delete();
            }

            PaymentProof::create([
                'contribution_id' => $contribution->id,
                'transaction_reference' => $proofData['transaction_reference'] ?? null,
                'payment_method' => $proofData['payment_method'],
                'sender_phone' => $proofData['sender_phone'] ?? null,
                'transaction_date' => $proofData['transaction_date'] ?? null,
                'screenshot_path' => $screenshotPath,
            ]);

            ActivityLog::log('contributed_for_member', $contribution, userId: $declaredBy, tontineId: $contribution->tontine_id);
        });

        // Dispatch event pour usage futur par les listeners
        event(new ContributionDeclared($contribution, $declaredBy));
    }

    public function confirm(Contribution $contribution, int $confirmedBy): void
    {
        StatusTransitionService::validateTransition(
            'contribution',
            StatusTransitionService::resolveEnumName($contribution->status),
            'CONFIRMED'
        );

        $wasLate = $contribution->status === ContributionStatus::LATE;
        $previousPenalty = $contribution->penalty_amount;
        $tontine = $contribution->tontine;

        DB::transaction(function () use ($contribution, $confirmedBy, $wasLate, $previousPenalty, $tontine) {
            $updateData = [
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'confirmed_by' => $confirmedBy,
            ];

            // Auto-remboursement de pénalité si activé et contribution était en retard
            if ($wasLate && $previousPenalty > 0 && $tontine->getSetting('auto_refund_penalty', false)) {
                $updateData['penalty_amount'] = 0;
            }

            $contribution->update($updateData);

            if ($contribution->paymentProof) {
                $contribution->paymentProof->update([
                    'verification_status' => 'verified',
                    'verified_by' => $confirmedBy,
                    'verified_at' => now(),
                ]);
            }

            $this->updateTourCollectedAmount($contribution);

            ActivityLog::log('confirmed', $contribution, tontineId: $contribution->tontine_id);

            // Logger le remboursement de pénalité
            if ($wasLate && $previousPenalty > 0 && $tontine->getSetting('auto_refund_penalty', false)) {
                ActivityLog::log('penalty_refunded', $contribution, userId: $contribution->user_id, tontineId: $tontine->id, properties: [
                    'refunded_amount' => $previousPenalty,
                ]);
            }
        });

        // Notifications APRÈS la transaction
        if ($wasLate && $previousPenalty > 0 && $tontine->getSetting('auto_refund_penalty', false)) {
            app(NotificationService::class)->send(
                $contribution->user_id,
                'penalty_refunded',
                'Pénalité annulée',
                'Votre pénalité de ' . format_amount($previousPenalty)
                    . ' pour le tour #' . $contribution->tour->tour_number
                    . ' de ' . $tontine->name . ' a été annulée suite à la confirmation de votre paiement.',
                ['tontine_id' => $tontine->id, 'contribution_id' => $contribution->id]
            );
        }

        // Notify member
        $tour = $contribution->tour;
        app(NotificationService::class)->send(
            $contribution->user_id,
            'payment_validated',
            'Paiement confirmé',
            'Votre paiement de ' . format_amount($contribution->amount)
                . ' pour le tour #' . $tour->tour_number . ' de ' . $tontine->name . ' a été confirmé.',
            ['tontine_id' => $tontine->id, 'tour_id' => $tour->id, 'contribution_id' => $contribution->id]
        );

        // System message in chat
        TontineMessage::create([
            'tontine_id' => $tontine->id,
            'user_id' => null,
            'type' => 'system',
            'content' => 'Le paiement de ' . $contribution->user->name . ' ('
                . format_amount($contribution->amount) . ') pour le tour #'
                . $tour->tour_number . ' a été confirmé.',
            'metadata' => ['contribution_id' => $contribution->id, 'tour_id' => $tour->id],
        ]);

        // Auto-clôture du tour si toutes les contributions sont confirmées
        $this->autoCompleteTourIfFullyPaid($contribution);

        // Dispatch event pour usage futur par les listeners
        event(new ContributionConfirmed($contribution, $confirmedBy));
    }

    private function autoCompleteTourIfFullyPaid(Contribution $contribution): void
    {
        $tour = $contribution->tour()->first();
        if ($tour->status !== TourStatus::ONGOING) {
            return;
        }

        $totalContributions = $tour->contributions()->count();
        $confirmedContributions = $tour->contributions()->where('status', 'confirmed')->count();

        if ($totalContributions !== $confirmedContributions) {
            return;
        }

        $collectedAmount = $tour->contributions()->where('status', 'confirmed')->sum('amount');

        // Ne PAS changer le status — le tour reste ONGOING jusqu'à confirmation du bénéficiaire
        $tour->update([
            'collected_amount' => $collectedAmount,
            'collection_date' => now(),
        ]);

        ActivityLog::log('all_contributions_confirmed', $tour, tontineId: $tour->tontine_id);

        $tontine = $tour->tontine;
        $beneficiary = $tour->beneficiary;

        // Notifier tous les membres avec email
        app(NotificationService::class)->notifyTontineMembers(
            $tontine,
            'all_contributions_confirmed',
            'Toutes les contributions confirmées',
            'Toutes les contributions du tour #' . $tour->tour_number . ' de ' . $tontine->name
                . ' ont été confirmées. Montant collecté : ' . format_amount($collectedAmount)
                . '. Le versement au bénéficiaire ' . ($beneficiary ? $beneficiary->name : 'N/A') . ' peut maintenant être initié.',
            ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
            sendEmail: true
        );

        // Notification spéciale au bénéficiaire
        if ($beneficiary) {
            app(NotificationService::class)->send(
                $beneficiary->id,
                'tour_ready_for_disbursement',
                'Fonds prêts à être versés',
                'Toutes les contributions du tour #' . $tour->tour_number . ' de ' . $tontine->name
                    . ' ont été collectées. Le montant de ' . format_amount($collectedAmount)
                    . ' vous sera versé prochainement.',
                ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
                sendEmail: true
            );
        }

        // Message système dans le chat
        TontineMessage::create([
            'tontine_id' => $tontine->id,
            'user_id' => null,
            'type' => 'system',
            'content' => 'Toutes les contributions du tour #' . $tour->tour_number . ' sont confirmées. Montant collecté : '
                . format_amount($collectedAmount) . ' pour ' . ($beneficiary ? $beneficiary->name : 'N/A')
                . '. En attente du versement par l\'administrateur.',
            'metadata' => ['tour_id' => $tour->id],
        ]);

        // Dispatch event pour usage futur par les listeners
        event(new TourCompleted($tour, $collectedAmount));
    }

    public function reject(Contribution $contribution, int $rejectedBy, ?string $reason = null): void
    {
        StatusTransitionService::validateTransition(
            'contribution',
            StatusTransitionService::resolveEnumName($contribution->status),
            'REJECTED'
        );

        DB::transaction(function () use ($contribution, $rejectedBy, $reason) {
            $contribution->update([
                'status' => 'rejected',
            ]);

            if ($contribution->paymentProof) {
                $contribution->paymentProof->update([
                    'verification_status' => 'rejected',
                    'rejection_reason' => $reason,
                    'verified_by' => $rejectedBy,
                    'verified_at' => now(),
                ]);
            }

            ActivityLog::log('rejected', $contribution, tontineId: $contribution->tontine_id);
        });

        // Notifications APRÈS la transaction
        $tour = $contribution->tour;
        $tontine = $contribution->tontine;
        $content = 'Votre paiement pour le tour #' . $tour->tour_number . ' de ' . $tontine->name . ' a été rejeté.';
        if ($reason) {
            $content .= ' Motif : ' . $reason;
        }

        app(NotificationService::class)->send(
            $contribution->user_id,
            'payment_rejected',
            'Paiement rejeté',
            $content,
            ['tontine_id' => $tontine->id, 'tour_id' => $tour->id, 'contribution_id' => $contribution->id],
            sendEmail: true
        );

        // System message in chat
        TontineMessage::create([
            'tontine_id' => $tontine->id,
            'user_id' => null,
            'type' => 'system',
            'content' => 'Le paiement de ' . $contribution->user->name . ' pour le tour #'
                . $tour->tour_number . ' a été rejeté.' . ($reason ? ' Motif : ' . $reason : ''),
            'metadata' => ['contribution_id' => $contribution->id, 'tour_id' => $tour->id],
        ]);
    }

    public function getConsecutiveLateCount(int $userId, int $tontineId): int
    {
        $contributions = Contribution::where('user_id', $userId)
            ->where('tontine_id', $tontineId)
            ->join('tours', 'contributions.tour_id', '=', 'tours.id')
            ->orderByDesc('tours.tour_number')
            ->select('contributions.status')
            ->get();

        $count = 0;
        foreach ($contributions as $contribution) {
            if ($contribution->status === ContributionStatus::LATE) {
                $count++;
            } else {
                break;
            }
        }

        return $count;
    }

    private function updateTourCollectedAmount(Contribution $contribution): void
    {
        $tour = $contribution->tour;
        $collectedAmount = $tour->contributions()->where('status', 'confirmed')->sum('amount');
        $tour->update(['collected_amount' => $collectedAmount]);
    }
}

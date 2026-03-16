<?php

namespace App\Services;

use App\Models\PaymentProof;

class DuplicatePaymentDetector
{
    /**
     * Check for potential duplicate payments.
     * Returns array with 'warnings' and 'blocked' flag.
     * blocked=true when an exact reference match (HIGH severity) is found.
     */
    public function check(
        int $tontineId,
        int $excludeContributionId,
        ?string $transactionReference,
        ?string $senderPhone,
        ?int $amount,
        ?string $transactionDate
    ): array {
        $warnings = [];
        $blocked = false;

        // Check 1: Same transaction reference
        if ($transactionReference) {
            $duplicate = PaymentProof::whereHas('contribution', function ($q) use ($tontineId, $excludeContributionId) {
                $q->where('tontine_id', $tontineId)
                  ->where('id', '!=', $excludeContributionId);
            })
            ->where('transaction_reference', $transactionReference)
            ->with('contribution.user', 'contribution.tour')
            ->first();

            if ($duplicate) {
                $c = $duplicate->contribution;
                $warnings[] = [
                    'type' => 'reference',
                    'severity' => 'high',
                    'message' => "Référence de transaction \"{$transactionReference}\" déjà utilisée par {$c->user->name} pour le tour #{$c->tour->tour_number}.",
                ];
                $blocked = true;
            }
        }

        // Check 2: Same sender_phone + similar amount + similar date (±1 day)
        if ($senderPhone && $amount && $transactionDate) {
            $similarQuery = PaymentProof::whereHas('contribution', function ($q) use ($tontineId, $excludeContributionId, $amount) {
                $q->where('tontine_id', $tontineId)
                  ->where('id', '!=', $excludeContributionId)
                  ->where('amount', $amount);
            })
            ->where('sender_phone', $senderPhone)
            ->where('transaction_date', '>=', date('Y-m-d', strtotime($transactionDate . ' -1 day')))
            ->where('transaction_date', '<=', date('Y-m-d', strtotime($transactionDate . ' +1 day')))
            ->with('contribution.user', 'contribution.tour')
            ->get();

            foreach ($similarQuery as $similar) {
                $c = $similar->contribution;
                $warnings[] = [
                    'type' => 'similarity',
                    'severity' => 'medium',
                    'message' => "Paiement similaire détecté : même téléphone, même montant, même date — {$c->user->name}, tour #{$c->tour->tour_number}.",
                ];
            }
        }

        return [
            'warnings' => $warnings,
            'blocked' => $blocked,
        ];
    }
}

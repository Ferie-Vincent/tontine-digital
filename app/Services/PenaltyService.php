<?php

namespace App\Services;

use App\Enums\ContributionStatus;
use App\Models\ActivityLog;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Models\Tour;

class PenaltyService
{
    /**
     * Calcule le montant de la pénalité pour une contribution en retard.
     *
     * Utilise les paramètres de pénalité configurés sur la tontine :
     * - penalty_enabled : si les pénalités sont activées
     * - penalty_type : 'fixed' (montant fixe en FCFA) ou 'percentage' (% du montant de cotisation)
     * - penalty_amount : valeur numérique (FCFA ou %)
     * - penalty_grace_hours : délai de grâce en heures avant application
     *
     * @return int Montant de la pénalité en FCFA (0 si désactivé ou dans le délai de grâce)
     */
    public function calculatePenalty(Tontine $tontine, Contribution $contribution): int
    {
        if (! $tontine->getSetting('penalty_enabled', false)) {
            return 0;
        }

        $penaltyAmount = $tontine->getSetting('penalty_amount', 0);
        if ($penaltyAmount <= 0) {
            return 0;
        }

        // Vérifier le délai de grâce
        $graceHours = $tontine->getSetting('penalty_grace_hours', 24);
        $tour = $contribution->tour;

        if ($tour && $tour->due_date) {
            $graceDeadline = $tour->due_date->copy()->addHours($graceHours);
            if (now()->lt($graceDeadline)) {
                return 0; // Encore dans le délai de grâce
            }
        }

        $penaltyType = $tontine->getSetting('penalty_type', 'fixed');

        if ($penaltyType === 'percentage') {
            // Pourcentage du montant de la cotisation
            return (int) round($contribution->amount * $penaltyAmount / 100);
        }

        // Montant fixe en FCFA
        return (int) $penaltyAmount;
    }

    /**
     * Applique les pénalités à toutes les contributions en retard d'un tour.
     *
     * @return int Montant total des pénalités appliquées
     */
    public function applyPenalties(Tour $tour): int
    {
        $tontine = $tour->tontine;

        if (! $tontine->getSetting('penalty_enabled', false)) {
            return 0;
        }

        $lateContributions = $tour->contributions()
            ->where('status', ContributionStatus::LATE)
            ->where(function ($query) {
                $query->where('penalty_amount', 0)
                    ->orWhereNull('penalty_amount');
            })
            ->get();

        $totalPenalties = 0;

        foreach ($lateContributions as $contribution) {
            $penalty = $this->calculatePenalty($tontine, $contribution);

            if ($penalty > 0) {
                $contribution->update(['penalty_amount' => $penalty]);
                $totalPenalties += $penalty;

                ActivityLog::log('penalty_applied', $contribution, userId: $contribution->user_id, tontineId: $tontine->id, properties: [
                    'penalty_amount' => $penalty,
                    'penalty_type' => $tontine->getSetting('penalty_type', 'fixed'),
                ]);
            }
        }

        return $totalPenalties;
    }
}

<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\Tontine;
use App\Models\Tour;

class FinancialReportService
{
    /**
     * Calcule les KPIs financiers globaux de la tontine.
     */
    public function getGlobalKpis(Tontine $tontine): array
    {
        return [
            'total_collected' => $tontine->contributions()->where('status', 'confirmed')->sum('amount'),
            'total_disbursed' => $tontine->tours()->whereNotNull('disbursed_at')->sum('collected_amount'),
            'total_penalties' => $tontine->contributions()->sum('penalty_amount'),
            'pending_amount' => $tontine->contributions()
                ->whereIn('status', ['pending', 'declared'])
                ->sum('amount'),
        ];
    }

    /**
     * Calcule les statistiques par tour.
     */
    public function getTourStats(Tontine $tontine): \Illuminate\Support\Collection
    {
        return $tontine->tours()->with('beneficiary')
            ->orderBy('tour_number')
            ->get()
            ->map(function ($tour) {
                $confirmed = $tour->contributions()->where('status', 'confirmed')->sum('amount');
                $pending = $tour->contributions()->whereIn('status', ['pending', 'declared'])->sum('amount');
                $late = $tour->contributions()->where('status', 'late')->count();

                return (object) [
                    'tour_number' => $tour->tour_number,
                    'beneficiary_name' => $tour->beneficiary?->name ?? 'N/A',
                    'status' => $tour->status,
                    'due_date' => $tour->due_date,
                    'expected_amount' => $tour->expected_amount,
                    'confirmed_amount' => $confirmed,
                    'pending_amount' => $pending,
                    'late_count' => $late,
                    'disbursed' => $tour->disbursed_at !== null,
                    'collection_percent' => $tour->expected_amount > 0
                        ? round(($confirmed / $tour->expected_amount) * 100, 1)
                        : 0,
                ];
            });
    }

    /**
     * Calcule les statistiques par membre.
     */
    public function getMemberStats(Tontine $tontine): \Illuminate\Support\Collection
    {
        return $tontine->activeMembers()->with('user')->get()->map(function ($member) use ($tontine) {
            $contributed = Contribution::where('user_id', $member->user_id)
                ->where('tontine_id', $tontine->id)
                ->where('status', 'confirmed')
                ->sum('amount');
            $penalties = Contribution::where('user_id', $member->user_id)
                ->where('tontine_id', $tontine->id)
                ->sum('penalty_amount');
            $lateCount = Contribution::where('user_id', $member->user_id)
                ->where('tontine_id', $tontine->id)
                ->where('status', 'late')
                ->count();
            $received = Tour::where('beneficiary_id', $member->user_id)
                ->where('tontine_id', $tontine->id)
                ->whereNotNull('disbursed_at')
                ->sum('collected_amount');

            return (object) [
                'name' => $member->user->name,
                'position' => $member->position,
                'contributed' => $contributed,
                'penalties' => $penalties,
                'late_count' => $lateCount,
                'received' => $received,
                'net' => $received - $contributed - $penalties,
            ];
        })->sortBy('position');
    }

    /**
     * Génère les données du graphique mensuel (6 derniers mois).
     */
    public function getMonthlyChartData(Tontine $tontine): array
    {
        $chartLabels = [];
        $chartAmounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chartLabels[] = $date->translatedFormat('M Y');

            $chartAmounts[] = $tontine->contributions()
                ->where('status', 'confirmed')
                ->whereYear('confirmed_at', $date->year)
                ->whereMonth('confirmed_at', $date->month)
                ->sum('amount');
        }

        return [
            'labels' => collect($chartLabels),
            'amounts' => collect($chartAmounts),
        ];
    }

    /**
     * Analyse la cohérence financière des tours terminés.
     */
    public function getFinancialHealthReport(Tontine $tontine): array
    {
        $completedTours = $tontine->tours()
            ->whereIn('status', ['completed', 'failed'])
            ->with('beneficiary')
            ->orderBy('tour_number')
            ->get();

        $flaggedTours = collect();
        $toursWithNoIssues = 0;

        foreach ($completedTours as $tour) {
            $confirmedAmount = $tour->contributions()->where('status', 'confirmed')->sum('amount');
            $issues = [];

            if ($tour->expected_amount > 0 && $confirmedAmount != $tour->expected_amount) {
                $issues[] = 'Écart de montant';
            }

            if ($tour->status->value === 'completed' && $tour->disbursed_at === null) {
                $issues[] = 'Non décaissé';
            }

            if (empty($issues)) {
                $toursWithNoIssues++;
            } else {
                $flaggedTours->push((object) [
                    'tour_number' => $tour->tour_number,
                    'beneficiary_name' => $tour->beneficiary?->name ?? 'N/A',
                    'expected_amount' => $tour->expected_amount,
                    'confirmed_amount' => $confirmedAmount,
                    'difference' => $confirmedAmount - $tour->expected_amount,
                    'issues' => $issues,
                    'status' => $tour->status,
                ]);
            }
        }

        $totalCompletedTours = $completedTours->count();
        $healthScore = $totalCompletedTours > 0
            ? round(($toursWithNoIssues / $totalCompletedTours) * 100, 1)
            : 100;

        $totalExpectedCompleted = $completedTours->sum('expected_amount');
        $totalCollectedCompleted = 0;
        foreach ($completedTours as $tour) {
            $totalCollectedCompleted += $tour->contributions()->where('status', 'confirmed')->sum('amount');
        }

        return [
            'flagged_tours' => $flaggedTours,
            'health_score' => $healthScore,
            'total_completed_tours' => $totalCompletedTours,
            'tours_with_no_issues' => $toursWithNoIssues,
            'total_expected_completed' => $totalExpectedCompleted,
            'total_collected_completed' => $totalCollectedCompleted,
            'financial_balance' => $totalCollectedCompleted - $totalExpectedCompleted,
        ];
    }
}

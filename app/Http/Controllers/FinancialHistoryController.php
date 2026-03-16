<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialHistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Tontines de l'utilisateur
        $tontineIds = $request->user()->tontineMembers()
            ->whereIn('status', ['active', 'pending'])
            ->pluck('tontine_id');

        // KPI globaux
        $totalContributed = Contribution::where('user_id', $userId)
            ->where('status', 'confirmed')
            ->sum('amount');

        $totalPenalties = Contribution::where('user_id', $userId)
            ->sum('penalty_amount');

        $totalReceived = Tour::where('beneficiary_id', $userId)
            ->whereNotNull('disbursed_at')
            ->sum('collected_amount');

        $netBalance = $totalReceived - $totalContributed - $totalPenalties;

        // Historique mensuel (12 derniers mois)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->translatedFormat('M Y');

            $contributed = Contribution::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->whereYear('confirmed_at', $date->year)
                ->whereMonth('confirmed_at', $date->month)
                ->sum('amount');

            $received = Tour::where('beneficiary_id', $userId)
                ->whereNotNull('disbursed_at')
                ->whereYear('disbursed_at', $date->year)
                ->whereMonth('disbursed_at', $date->month)
                ->sum('collected_amount');

            $monthlyData[] = [
                'label' => $monthLabel,
                'contributed' => $contributed,
                'received' => $received,
            ];
        }

        // Repartition par tontine
        $tontineStats = DB::table('contributions')
            ->join('tontines', 'contributions.tontine_id', '=', 'tontines.id')
            ->where('contributions.user_id', $userId)
            ->whereNull('tontines.deleted_at')
            ->select(
                'tontines.id',
                'tontines.name',
                DB::raw("SUM(CASE WHEN contributions.status = 'confirmed' THEN contributions.amount ELSE 0 END) as total_contributed"),
                DB::raw("SUM(contributions.penalty_amount) as total_penalties"),
                DB::raw("SUM(CASE WHEN contributions.status = 'late' THEN 1 ELSE 0 END) as late_count"),
            )
            ->groupBy('tontines.id', 'tontines.name')
            ->get();

        // Ajouter le montant recu par tontine
        foreach ($tontineStats as $stat) {
            $stat->total_received = Tour::where('beneficiary_id', $userId)
                ->where('tontine_id', $stat->id)
                ->whereNotNull('disbursed_at')
                ->sum('collected_amount');

            $stat->net = $stat->total_received - $stat->total_contributed - $stat->total_penalties;
        }

        // Dernieres transactions
        $recentContributions = Contribution::where('user_id', $userId)
            ->where('status', 'confirmed')
            ->with(['tontine', 'tour'])
            ->orderByDesc('confirmed_at')
            ->take(10)
            ->get();

        $recentDisbursements = Tour::where('beneficiary_id', $userId)
            ->whereNotNull('disbursed_at')
            ->with('tontine')
            ->orderByDesc('disbursed_at')
            ->take(10)
            ->get();

        // Chart data
        $chartLabels = collect($monthlyData)->pluck('label');
        $chartContributed = collect($monthlyData)->pluck('contributed');
        $chartReceived = collect($monthlyData)->pluck('received');

        return view('financial-history.index', compact(
            'totalContributed',
            'totalReceived',
            'totalPenalties',
            'netBalance',
            'tontineStats',
            'recentContributions',
            'recentDisbursements',
            'chartLabels',
            'chartContributed',
            'chartReceived',
        ));
    }
}

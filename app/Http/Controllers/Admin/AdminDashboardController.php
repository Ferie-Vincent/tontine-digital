<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // --- KPI stats ---
        $stats = [
            'users_count' => User::count(),
            'active_users' => User::active()->count(),
            'new_users_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'tontines_count' => Tontine::count(),
            'active_tontines' => Tontine::active()->count(),
            'total_collected' => Contribution::where('status', 'confirmed')->sum('amount'),
            'total_pending' => Contribution::where('status', 'declared')->sum('amount'),
            'late_count' => Contribution::where('status', 'late')->count(),
            'monthly_contributions' => Contribution::where('status', 'confirmed')
                ->whereMonth('confirmed_at', now()->month)
                ->whereYear('confirmed_at', now()->year)
                ->sum('amount'),
        ];

        // --- Contributions mensuelles des 6 derniers mois (confirmees + declarees) ---
        $monthlyData = Contribution::where('status', 'confirmed')
            ->whereDate('confirmed_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(confirmed_at, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $declaredData = Contribution::where('status', 'declared')
            ->whereDate('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartLabels = [];
        $chartConfirmed = [];
        $chartDeclared = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $chartLabels[] = $date->translatedFormat('M Y');
            $chartConfirmed[] = $monthlyData[$key] ?? 0;
            $chartDeclared[] = $declaredData[$key] ?? 0;
        }

        // --- Repartition des tontines par statut ---
        $tontinesByStatus = Tontine::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusConfig = [
            'active' => ['label' => 'Actives', 'color' => '#10B981'],
            'pending' => ['label' => 'En attente', 'color' => '#F59E0B'],
            'draft' => ['label' => 'Brouillon', 'color' => '#94A3B8'],
            'paused' => ['label' => 'En pause', 'color' => '#F97316'],
            'completed' => ['label' => 'Terminees', 'color' => '#3B82F6'],
            'cancelled' => ['label' => 'Annulees', 'color' => '#EF4444'],
        ];

        $doughnutLabels = [];
        $doughnutValues = [];
        $doughnutColors = [];
        foreach ($tontinesByStatus as $status => $count) {
            $cfg = $statusConfig[$status] ?? ['label' => ucfirst($status), 'color' => '#94A3B8'];
            $doughnutLabels[] = $cfg['label'];
            $doughnutValues[] = $count;
            $doughnutColors[] = $cfg['color'];
        }

        // --- Inscriptions mensuelles (6 derniers mois) ---
        $registrationData = User::whereDate('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $registrationLabels = [];
        $registrationValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $registrationLabels[] = $date->translatedFormat('M Y');
            $registrationValues[] = $registrationData[$key] ?? 0;
        }

        // --- Top 5 tontines par montant collecte ---
        $topTontines = Tontine::select('tontines.id', 'tontines.name')
            ->join('contributions', 'contributions.tontine_id', '=', 'tontines.id')
            ->where('contributions.status', 'confirmed')
            ->groupBy('tontines.id', 'tontines.name')
            ->orderByDesc(DB::raw('SUM(contributions.amount)'))
            ->limit(5)
            ->selectRaw('SUM(contributions.amount) as collected')
            ->get();

        $topTontineNames = $topTontines->pluck('name')->toArray();
        $topTontineAmounts = $topTontines->pluck('collected')->map(fn ($v) => (int) $v)->toArray();

        // --- Activite recente (10 dernieres actions) ---
        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // --- Listes recentes ---
        $recentUsers = User::latest()->take(10)->get();
        $recentTontines = Tontine::with('creator')->withCount('activeMembers')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentTontines',
            'chartLabels',
            'chartConfirmed',
            'chartDeclared',
            'doughnutLabels',
            'doughnutValues',
            'doughnutColors',
            'registrationLabels',
            'registrationValues',
            'topTontineNames',
            'topTontineAmounts',
            'recentActivity',
            'tontinesByStatus',
            'statusConfig'
        ));
    }
}

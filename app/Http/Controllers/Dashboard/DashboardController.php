<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Models\TontineMember;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Tontines actives de l'utilisateur
        $activeTontines = Tontine::forUser($user)->active()->count();

        // Contributions du mois
        $monthlyContributions = Contribution::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereMonth('confirmed_at', now()->month)
            ->whereYear('confirmed_at', now()->year)
            ->sum('amount');

        // Contributions du mois précédent (pour le trend)
        $previousMonthContributions = Contribution::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereMonth('confirmed_at', now()->subMonth()->month)
            ->whereYear('confirmed_at', now()->subMonth()->year)
            ->sum('amount');

        // Total cotisé (toutes tontines confondues)
        $totalContributed = Contribution::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->sum('amount');

        // Taux de contribution (confirmées / total attendu)
        $totalExpected = Contribution::where('user_id', $user->id)->count();
        $totalConfirmed = Contribution::where('user_id', $user->id)->where('status', 'confirmed')->count();
        $contributionRate = $totalExpected > 0 ? round(($totalConfirmed / $totalExpected) * 100, 1) : 0;

        // Données graphique : contributions des 6 derniers mois
        $monthlyChartData = Contribution::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereDate('confirmed_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(confirmed_at, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Remplir les mois manquants avec 0
        $chartLabels = [];
        $chartValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $chartLabels[] = $date->translatedFormat('M Y');
            $chartValues[] = $monthlyChartData[$key] ?? 0;
        }

        // Répartition des tontines par statut
        $tontineStatusData = Tontine::forUser($user)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusColors = [
            'active' => '#27AE60',
            'pending' => '#F39C12',
            'draft' => '#95A5A6',
            'paused' => '#F18F01',
            'completed' => '#2E86AB',
            'cancelled' => '#E74C3C',
        ];
        $statusLabels = [
            'active' => 'Active',
            'pending' => 'En attente',
            'draft' => 'Brouillon',
            'paused' => 'En pause',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
        ];

        $doughnutLabels = [];
        $doughnutValues = [];
        $doughnutColors = [];
        foreach ($tontineStatusData as $status => $count) {
            $doughnutLabels[] = $statusLabels[$status] ?? ucfirst($status);
            $doughnutValues[] = $count;
            $doughnutColors[] = $statusColors[$status] ?? '#95A5A6';
        }

        // Répartition des contributions par tontine (montants confirmés)
        $tontineBreakdownData = Contribution::where('contributions.user_id', $user->id)
            ->where('contributions.status', 'confirmed')
            ->join('tontines', 'contributions.tontine_id', '=', 'tontines.id')
            ->selectRaw('tontines.name as tontine_name, SUM(contributions.amount) as total')
            ->groupBy('contributions.tontine_id', 'tontines.name')
            ->orderByDesc('total')
            ->get();

        $tontineBreakdownLabels = $tontineBreakdownData->pluck('tontine_name')->toArray();
        $tontineBreakdownValues = $tontineBreakdownData->pluck('total')->map(fn ($v) => (int) $v)->toArray();
        $tontineBreakdownColors = ['#6366F1', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#06B6D4', '#84CC16'];

        // Statut des cotisations (distribution)
        $statusDistributionData = Contribution::where('user_id', $user->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $contributionStatusLabels = [
            'confirmed' => 'Confirmé',
            'pending' => 'En attente',
            'declared' => 'Déclaré',
            'late' => 'En retard',
            'rejected' => 'Rejeté',
        ];
        $contributionStatusColors = [
            'confirmed' => '#10B981',
            'pending' => '#F59E0B',
            'declared' => '#6366F1',
            'late' => '#EF4444',
            'rejected' => '#6B7280',
        ];

        $statusDistLabels = [];
        $statusDistValues = [];
        $statusDistColors = [];
        foreach ($statusDistributionData as $status => $count) {
            $statusDistLabels[] = $contributionStatusLabels[$status] ?? ucfirst($status);
            $statusDistValues[] = $count;
            $statusDistColors[] = $contributionStatusColors[$status] ?? '#6B7280';
        }

        // Contributions par tontine (pour bar chart horizontal)
        $contributionsByTontine = Tontine::forUser($user)
            ->with(['contributions' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->get()
            ->map(function ($tontine) {
                return [
                    'name' => $tontine->name,
                    'confirmed' => $tontine->contributions->where('status', 'confirmed')->sum('amount'),
                    'pending' => $tontine->contributions->whereIn('status', ['pending', 'declared'])->sum('amount'),
                ];
            })
            ->values();

        // Tours en cours ou l'utilisateur n'a pas encore paye
        $pendingPayments = Tour::whereHas('tontine.members', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'active');
            })
            ->where('status', 'ongoing')
            ->whereDoesntHave('contributions', function ($q) use ($user) {
                $q->where('user_id', $user->id)->whereIn('status', ['declared', 'confirmed']);
            })
            ->with(['tontine', 'beneficiary'])
            ->orderBy('due_date')
            ->get();

        $nextPayment = $pendingPayments->first();

        // Prochain tour à recevoir
        $nextReceive = Tour::where('beneficiary_id', $user->id)
            ->where('status', 'upcoming')
            ->with('tontine')
            ->orderBy('due_date')
            ->first();

        // ── Actions requises ──
        $alerts = collect();

        // 1. Paiements à effectuer (already have $pendingPayments)
        foreach ($pendingPayments as $payment) {
            $daysLeft = now()->diffInDays($payment->due_date, false);
            $alerts->push((object) [
                'type' => 'payment_due',
                'severity' => $daysLeft <= 1 ? 'critical' : ($daysLeft <= 3 ? 'warning' : 'info'),
                'title' => 'Paiement requis',
                'message' => format_amount($payment->tontine->contribution_amount) . ' pour le tour #' . $payment->tour_number . ' de ' . $payment->tontine->name,
                'detail' => $daysLeft > 0 ? 'Échéance dans ' . $daysLeft . ' jour(s)' : ($daysLeft == 0 ? 'Échéance aujourd\'hui' : 'En retard de ' . abs($daysLeft) . ' jour(s)'),
                'url' => route('tontines.tours.show', [$payment->tontine_id, $payment->id]),
                'icon' => 'payment',
                'sort_priority' => $daysLeft <= 0 ? 0 : $daysLeft,
            ]);
        }

        // 2. Paiements à valider (admin/trésorier)
        $managedTontineIds = $user->tontineMembers()
            ->whereIn('role', ['admin', 'treasurer'])
            ->where('status', 'active')
            ->pluck('tontine_id');

        if ($managedTontineIds->isNotEmpty()) {
            // Charger toutes les tontines gérées en une seule requête
            $managedTontines = Tontine::whereIn('id', $managedTontineIds)->get()->keyBy('id');

            $declaredByTontine = Contribution::whereIn('tontine_id', $managedTontineIds)
                ->where('status', 'declared')
                ->selectRaw('tontine_id, count(*) as count')
                ->groupBy('tontine_id')
                ->get();

            foreach ($declaredByTontine as $row) {
                $tontine = $managedTontines[$row->tontine_id] ?? null;
                if ($tontine) {
                    $alerts->push((object) [
                        'type' => 'validate_payments',
                        'severity' => 'warning',
                        'title' => 'Paiements à valider',
                        'message' => $row->count . ' paiement(s) en attente de validation dans ' . $tontine->name,
                        'detail' => 'En tant qu\'administrateur',
                        'url' => route('tontines.contributions.index', ['tontine' => $tontine->id, 'status' => 'declared']),
                        'icon' => 'validate',
                        'sort_priority' => 1,
                    ]);
                }
            }

            // 3. Fonds à décaisser
            $toursToDisburse = Tour::whereIn('tontine_id', $managedTontineIds)
                ->where('status', 'ongoing')
                ->whereNotNull('collection_date')
                ->whereNull('disbursed_at')
                ->with('tontine', 'beneficiary')
                ->get();

            foreach ($toursToDisburse as $tour) {
                $alerts->push((object) [
                    'type' => 'disburse',
                    'severity' => 'warning',
                    'title' => 'Fonds à décaisser',
                    'message' => format_amount($tour->collected_amount) . ' à verser à ' . ($tour->beneficiary?->name ?? 'N/A'),
                    'detail' => 'Tour #' . $tour->tour_number . ' de ' . $tour->tontine->name,
                    'url' => route('tontines.tours.show', [$tour->tontine_id, $tour->id]),
                    'icon' => 'disburse',
                    'sort_priority' => 2,
                ]);
            }

            // 4. Demandes d'adhésion en attente
            $pendingMembers = TontineMember::whereIn('tontine_id', $managedTontineIds)
                ->where('status', 'pending')
                ->selectRaw('tontine_id, count(*) as count')
                ->groupBy('tontine_id')
                ->get();

            foreach ($pendingMembers as $row) {
                $tontine = $managedTontines[$row->tontine_id] ?? null;
                if ($tontine) {
                    $alerts->push((object) [
                        'type' => 'pending_members',
                        'severity' => 'info',
                        'title' => 'Demandes d\'adhésion',
                        'message' => $row->count . ' demande(s) en attente dans ' . $tontine->name,
                        'detail' => '',
                        'url' => route('tontines.members.index', $tontine->id),
                        'icon' => 'members',
                        'sort_priority' => 5,
                    ]);
                }
            }
        }

        // 5. Vous êtes bénéficiaire !
        $beneficiaryTours = Tour::where('beneficiary_id', $user->id)
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->with('tontine')
            ->orderBy('due_date')
            ->get();

        foreach ($beneficiaryTours as $tour) {
            $alerts->push((object) [
                'type' => 'beneficiary',
                'severity' => 'success',
                'title' => 'Vous êtes bénéficiaire !',
                'message' => 'Tour #' . $tour->tour_number . ' de ' . $tour->tontine->name,
                'detail' => $tour->status->value === 'ongoing'
                    ? 'Collecte en cours — ' . format_amount($tour->collected_amount ?? 0) . ' collectés'
                    : 'Prévu le ' . $tour->due_date->format('d/m/Y'),
                'url' => route('tontines.tours.show', [$tour->tontine_id, $tour->id]),
                'icon' => 'beneficiary',
                'sort_priority' => 3,
            ]);
        }

        // Sort: most urgent first
        $alerts = $alerts->sortBy('sort_priority')->values();

        // Mes tontines récentes
        $myTontines = Tontine::forUser($user)
            ->withCount('activeMembers')
            ->latest()
            ->take(5)
            ->get();

        // Activités récentes
        $userTontineIds = $user->tontines()->pluck('tontines.id');
        $recentActivities = \App\Models\ActivityLog::where(function ($q) use ($user, $userTontineIds) {
                $q->where('user_id', $user->id)
                  ->orWhereIn('tontine_id', $userTontineIds);
            })
            ->with(['user', 'tontine'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', compact(
            'activeTontines',
            'monthlyContributions',
            'previousMonthContributions',
            'totalContributed',
            'contributionRate',
            'chartLabels',
            'chartValues',
            'doughnutLabels',
            'doughnutValues',
            'doughnutColors',
            'contributionsByTontine',
            'tontineBreakdownLabels',
            'tontineBreakdownValues',
            'tontineBreakdownColors',
            'statusDistLabels',
            'statusDistValues',
            'statusDistColors',
            'pendingPayments',
            'nextPayment',
            'nextReceive',
            'myTontines',
            'recentActivities',
            'alerts'
        ));
    }
}
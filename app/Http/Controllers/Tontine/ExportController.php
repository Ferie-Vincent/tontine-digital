<?php

namespace App\Http\Controllers\Tontine;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Models\Tour;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    private const MAX_EXPORT_ROWS = 5000;

    /**
     * Export contributions as CSV.
     */
    public function contributionsCsv(Request $request, Tontine $tontine): StreamedResponse
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        $query = Contribution::where('tontine_id', $tontine->id)
            ->with(['user', 'tour', 'paymentProof']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }

        $contributions = $query->latest()->take(self::MAX_EXPORT_ROWS)->get();
        $limitReached = $contributions->count() >= self::MAX_EXPORT_ROWS;

        $filename = 'contributions_' . str_replace(' ', '_', $tontine->name) . '_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($contributions, $limitReached) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header
            fputcsv($handle, [
                'Membre', 'Téléphone', 'Tour', 'Montant', 'Pénalité',
                'Statut', 'Méthode', 'Référence', 'Déclaré le', 'Confirmé le'
            ], ';');

            foreach ($contributions as $c) {
                fputcsv($handle, [
                    $c->user->name ?? 'N/A',
                    $c->user->phone ?? '',
                    'Tour #' . ($c->tour->tour_number ?? '?'),
                    $c->amount,
                    $c->penalty_amount ?? 0,
                    $c->status->value,
                    $c->paymentProof?->payment_method ?? '',
                    $c->paymentProof?->transaction_reference ?? '',
                    $c->declared_at?->format('d/m/Y H:i') ?? '',
                    $c->confirmed_at?->format('d/m/Y H:i') ?? '',
                ], ';');
            }

            if ($limitReached) {
                fputcsv($handle, [
                    '... Export limité à ' . self::MAX_EXPORT_ROWS . ' lignes. Utilisez les filtres pour affiner.'
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export contribution matrix as CSV.
     */
    public function matrixCsv(Tontine $tontine): StreamedResponse
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        $members = $tontine->activeMembers()->with('user')->orderBy('position')->get();
        $tours = $tontine->tours()->orderBy('tour_number')->get();
        $contributions = Contribution::where('tontine_id', $tontine->id)
            ->take(self::MAX_EXPORT_ROWS)->get();
        $limitReached = $contributions->count() >= self::MAX_EXPORT_ROWS;

        $contributions = $contributions->groupBy('tour_id')
            ->map(fn($tc) => $tc->keyBy('user_id'));

        $filename = 'matrice_' . str_replace(' ', '_', $tontine->name) . '_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($members, $tours, $contributions, $limitReached) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            $header = ['Membre', 'Position'];
            foreach ($tours as $tour) {
                $header[] = 'Tour #' . $tour->tour_number;
            }
            fputcsv($handle, $header, ';');

            // Data rows
            foreach ($members as $member) {
                $row = [$member->user->name ?? 'N/A', $member->position];
                foreach ($tours as $tour) {
                    $c = $contributions[$tour->id][$member->user_id] ?? null;
                    if ($c) {
                        $row[] = strtoupper($c->status->value);
                    } elseif ($tour->beneficiary_id === $member->user_id) {
                        $row[] = 'BENEFICIAIRE';
                    } else {
                        $row[] = '-';
                    }
                }
                fputcsv($handle, $row, ';');
            }

            if ($limitReached) {
                fputcsv($handle, [
                    '... Export limité à ' . self::MAX_EXPORT_ROWS . ' lignes de contributions. Certaines données peuvent être incomplètes.'
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export financial overview as PDF.
     */
    public function financesPdf(Tontine $tontine)
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        $tontine->load(['tours.beneficiary', 'activeMembers.user']);

        $totalCollected = $tontine->contributions()->where('status', 'confirmed')->sum('amount');
        $totalDisbursed = $tontine->tours()->whereNotNull('disbursed_at')->sum('collected_amount');
        $totalPenalties = $tontine->contributions()->sum('penalty_amount');
        $pendingAmount = $tontine->contributions()
            ->whereIn('status', ['pending', 'declared'])
            ->sum('amount');

        $tourStats = $tontine->tours()->with('beneficiary')
            ->orderBy('tour_number')
            ->take(self::MAX_EXPORT_ROWS)
            ->get()
            ->map(function ($tour) {
                $confirmed = $tour->contributions()->where('status', 'confirmed')->sum('amount');
                return (object) [
                    'tour_number' => $tour->tour_number,
                    'beneficiary_name' => $tour->beneficiary?->name ?? 'N/A',
                    'status' => $tour->status,
                    'due_date' => $tour->due_date,
                    'expected_amount' => $tour->expected_amount,
                    'confirmed_amount' => $confirmed,
                    'disbursed' => $tour->disbursed_at !== null,
                    'collection_percent' => $tour->expected_amount > 0
                        ? round(($confirmed / $tour->expected_amount) * 100, 1)
                        : 0,
                ];
            });

        $limitReached = $tourStats->count() >= self::MAX_EXPORT_ROWS;

        $memberStats = $tontine->activeMembers()->with('user')->take(self::MAX_EXPORT_ROWS)->get()->map(function ($member) use ($tontine) {
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

        $limitReached = $limitReached || $memberStats->count() >= self::MAX_EXPORT_ROWS;

        $pdf = Pdf::loadView('exports.finances-pdf', compact(
            'tontine', 'totalCollected', 'totalDisbursed', 'totalPenalties',
            'pendingAmount', 'tourStats', 'memberStats', 'limitReached'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('bilan_financier_' . str_replace(' ', '_', $tontine->name) . '_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export contributions as PDF.
     */
    public function contributionsPdf(Request $request, Tontine $tontine)
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        $query = Contribution::where('tontine_id', $tontine->id)
            ->with(['user', 'tour', 'paymentProof']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }

        $contributions = $query->latest()->take(self::MAX_EXPORT_ROWS)->get();
        $limitReached = $contributions->count() >= self::MAX_EXPORT_ROWS;

        $statusCounts = Contribution::where('tontine_id', $tontine->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $pdf = Pdf::loadView('exports.contributions-pdf', compact(
            'tontine', 'contributions', 'statusCounts', 'limitReached'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('contributions_' . str_replace(' ', '_', $tontine->name) . '_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export members list as CSV.
     */
    public function membersCsv(Tontine $tontine): StreamedResponse
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        $members = $tontine->members()->with('user')->orderBy('position')->get();

        $filename = 'membres_' . str_replace(' ', '_', $tontine->name) . '_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($members) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header
            fputcsv($handle, [
                'Nom', 'Téléphone', 'Email', 'Rôle', 'Statut', 'Parts', 'Date d\'adhésion'
            ], ';');

            foreach ($members as $member) {
                fputcsv($handle, [
                    $member->user->name ?? 'N/A',
                    $member->user->phone ?? '',
                    $member->user->email ?? '',
                    $member->role->label(),
                    $member->status->label(),
                    $member->parts,
                    $member->joined_at?->format('d/m/Y') ?? '',
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export full tontine report as CSV.
     */
    public function fullReportCsv(Tontine $tontine): StreamedResponse
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        $tontine->load(['tours.beneficiary', 'activeMembers.user']);

        $members = $tontine->members()->with('user')->orderBy('position')->get();
        $tours = $tontine->tours()->with('beneficiary')->orderBy('tour_number')->get();
        $contributions = Contribution::where('tontine_id', $tontine->id)
            ->with(['user', 'tour'])
            ->latest()
            ->take(self::MAX_EXPORT_ROWS)
            ->get();

        $totalCollected = $tontine->contributions()->where('status', 'confirmed')->sum('amount');
        $totalPending = $tontine->contributions()->whereIn('status', ['pending', 'declared'])->sum('amount');
        $totalLate = $tontine->contributions()->where('status', 'late')->sum('amount');
        $totalPenalties = $tontine->contributions()->sum('penalty_amount');
        $totalDisbursed = $tontine->tours()->whereNotNull('disbursed_at')->sum('collected_amount');

        $filename = 'rapport_complet_' . str_replace(' ', '_', $tontine->name) . '_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($tontine, $members, $tours, $contributions, $totalCollected, $totalPending, $totalLate, $totalPenalties, $totalDisbursed) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // === Section 1: Tontine Info ===
            fputcsv($handle, ['=== INFORMATIONS DE LA TONTINE ==='], ';');
            fputcsv($handle, ['Nom', $tontine->name], ';');
            fputcsv($handle, ['Code', $tontine->code], ';');
            fputcsv($handle, ['Statut', $tontine->status->label()], ';');
            fputcsv($handle, ['Fréquence', $tontine->frequency->label()], ';');
            fputcsv($handle, ['Montant cotisation', $tontine->contribution_amount . ' FCFA'], ';');
            fputcsv($handle, ['Nombre max membres', $tontine->max_members], ';');
            fputcsv($handle, ['Date début', $tontine->start_date?->format('d/m/Y') ?? 'N/A'], ';');
            fputcsv($handle, ['Date fin', $tontine->end_date?->format('d/m/Y') ?? 'N/A'], ';');
            fputcsv($handle, ['Date du rapport', now()->format('d/m/Y H:i')], ';');
            fputcsv($handle, [], ';');

            // === Section 2: Members ===
            fputcsv($handle, ['=== LISTE DES MEMBRES ==='], ';');
            fputcsv($handle, ['Nom', 'Téléphone', 'Email', 'Rôle', 'Statut', 'Parts', 'Date d\'adhésion'], ';');

            foreach ($members as $member) {
                fputcsv($handle, [
                    $member->user->name ?? 'N/A',
                    $member->user->phone ?? '',
                    $member->user->email ?? '',
                    $member->role->label(),
                    $member->status->label(),
                    $member->parts,
                    $member->joined_at?->format('d/m/Y') ?? '',
                ], ';');
            }
            fputcsv($handle, [], ';');

            // === Section 3: Tours ===
            fputcsv($handle, ['=== PLANNING DES TOURS ==='], ';');
            fputcsv($handle, ['Tour', 'Bénéficiaire', 'Statut', 'Date prévue', 'Montant attendu', 'Montant collecté', 'Versé'], ';');

            foreach ($tours as $tour) {
                $confirmedAmount = $tour->contributions()->where('status', 'confirmed')->sum('amount');
                fputcsv($handle, [
                    'Tour #' . $tour->tour_number,
                    $tour->beneficiary?->name ?? 'N/A',
                    $tour->status->label(),
                    $tour->due_date?->format('d/m/Y') ?? '',
                    $tour->expected_amount . ' FCFA',
                    $confirmedAmount . ' FCFA',
                    $tour->disbursed_at ? 'Oui' : 'Non',
                ], ';');
            }
            fputcsv($handle, [], ';');

            // === Section 4: Contributions ===
            fputcsv($handle, ['=== DÉTAIL DES CONTRIBUTIONS ==='], ';');
            fputcsv($handle, ['Membre', 'Tour', 'Montant', 'Pénalité', 'Statut', 'Déclaré le', 'Confirmé le'], ';');

            foreach ($contributions as $c) {
                fputcsv($handle, [
                    $c->user->name ?? 'N/A',
                    'Tour #' . ($c->tour->tour_number ?? '?'),
                    $c->amount . ' FCFA',
                    ($c->penalty_amount ?? 0) . ' FCFA',
                    $c->status->label(),
                    $c->declared_at?->format('d/m/Y H:i') ?? '',
                    $c->confirmed_at?->format('d/m/Y H:i') ?? '',
                ], ';');
            }
            fputcsv($handle, [], ';');

            // === Section 5: Financial Summary ===
            fputcsv($handle, ['=== RÉSUMÉ FINANCIER ==='], ';');
            fputcsv($handle, ['Total collecté (confirmé)', $totalCollected . ' FCFA'], ';');
            fputcsv($handle, ['Total en attente', $totalPending . ' FCFA'], ';');
            fputcsv($handle, ['Total en retard', $totalLate . ' FCFA'], ';');
            fputcsv($handle, ['Total pénalités', $totalPenalties . ' FCFA'], ';');
            fputcsv($handle, ['Total décaissé', $totalDisbursed . ' FCFA'], ';');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

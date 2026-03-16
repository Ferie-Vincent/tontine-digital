<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contributions — {{ $tontine->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1e293b; line-height: 1.5; }
        .header { background: linear-gradient(135deg, #3b82f6, #6366f1); color: white; padding: 24px 30px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin-bottom: 4px; }
        .header p { font-size: 11px; opacity: 0.9; }
        .section { margin: 0 24px 16px; }
        .section-title { font-size: 13px; font-weight: bold; color: #3b82f6; border-bottom: 2px solid #3b82f6; padding-bottom: 4px; margin-bottom: 10px; }
        .summary-grid { display: table; width: 100%; margin-bottom: 16px; }
        .summary-row { display: table-row; }
        .summary-item { display: table-cell; text-align: center; padding: 10px 8px; border: 1px solid #e2e8f0; }
        .summary-value { font-size: 18px; font-weight: bold; }
        .summary-label { font-size: 9px; color: #64748b; margin-top: 2px; text-transform: uppercase; }
        .count-pending .summary-value { color: #64748b; }
        .count-declared .summary-value { color: #d97706; }
        .count-confirmed .summary-value { color: #059669; }
        .count-rejected .summary-value { color: #dc2626; }
        .count-late .summary-value { color: #ea580c; }
        table.data { width: 100%; border-collapse: collapse; font-size: 9px; }
        table.data th { background: #f1f5f9; padding: 6px 6px; text-align: left; font-weight: 600; border: 1px solid #e2e8f0; }
        table.data td { padding: 5px 6px; border: 1px solid #e2e8f0; }
        table.data tr:nth-child(even) { background: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: 600; }
        .badge-pending { background: #f1f5f9; color: #475569; }
        .badge-declared { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-late { background: #ffedd5; color: #9a3412; }
        .footer { text-align: center; color: #94a3b8; font-size: 9px; margin-top: 24px; padding: 12px 24px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $tontine->name }}</h1>
        <p>Liste des contributions</p>
        <p>Exporté le {{ now()->format('d/m/Y à H:i') }} — {{ $contributions->count() }} contribution(s)</p>
    </div>

    {{-- Status summary --}}
    <div class="section">
        <h2 class="section-title">Résumé par Statut</h2>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-item count-pending">
                    <div class="summary-value">{{ $statusCounts['pending'] ?? $statusCounts['PENDING'] ?? 0 }}</div>
                    <div class="summary-label">En attente</div>
                </div>
                <div class="summary-item count-declared">
                    <div class="summary-value">{{ $statusCounts['declared'] ?? $statusCounts['DECLARED'] ?? 0 }}</div>
                    <div class="summary-label">Déclarées</div>
                </div>
                <div class="summary-item count-confirmed">
                    <div class="summary-value">{{ $statusCounts['confirmed'] ?? $statusCounts['CONFIRMED'] ?? 0 }}</div>
                    <div class="summary-label">Confirmées</div>
                </div>
                <div class="summary-item count-rejected">
                    <div class="summary-value">{{ $statusCounts['rejected'] ?? $statusCounts['REJECTED'] ?? 0 }}</div>
                    <div class="summary-label">Rejetées</div>
                </div>
                <div class="summary-item count-late">
                    <div class="summary-value">{{ $statusCounts['late'] ?? $statusCounts['LATE'] ?? 0 }}</div>
                    <div class="summary-label">En retard</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contributions table --}}
    <div class="section">
        <h2 class="section-title">Détail des Contributions</h2>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 16%;">Membre</th>
                    <th style="width: 8%;">Tour</th>
                    <th class="text-right" style="width: 12%;">Montant</th>
                    <th class="text-right" style="width: 10%;">Pénalité</th>
                    <th class="text-center" style="width: 10%;">Statut</th>
                    <th style="width: 12%;">Méthode</th>
                    <th style="width: 12%;">Référence</th>
                    <th class="text-center" style="width: 10%;">Déclaré le</th>
                    <th class="text-center" style="width: 10%;">Confirmé le</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributions as $c)
                <tr>
                    <td class="bold">{{ $c->user->name ?? 'N/A' }}</td>
                    <td>Tour #{{ $c->tour->tour_number ?? '?' }}</td>
                    <td class="text-right">{{ format_amount($c->amount) }}</td>
                    <td class="text-right">{{ format_amount($c->penalty_amount ?? 0) }}</td>
                    <td class="text-center">
                        @php
                            $statusValue = $c->status->value ?? $c->status;
                            $badgeClass = match($statusValue) {
                                'confirmed', 'CONFIRMED' => 'badge-confirmed',
                                'declared', 'DECLARED' => 'badge-declared',
                                'rejected', 'REJECTED' => 'badge-rejected',
                                'late', 'LATE' => 'badge-late',
                                default => 'badge-pending',
                            };
                            $statusLabel = match($statusValue) {
                                'confirmed', 'CONFIRMED' => 'Confirmé',
                                'declared', 'DECLARED' => 'Déclaré',
                                'rejected', 'REJECTED' => 'Rejeté',
                                'late', 'LATE' => 'En retard',
                                'pending', 'PENDING' => 'En attente',
                                default => $statusValue,
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td>{{ $c->paymentProof?->payment_method ? $c->paymentProof->payment_method_label : '-' }}</td>
                    <td style="font-size: 8px;">{{ $c->paymentProof?->transaction_reference ?? '-' }}</td>
                    <td class="text-center">{{ $c->declared_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td class="text-center">{{ $c->confirmed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">Aucune contribution trouvée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        @if(!empty($limitReached) && $limitReached)
            <p style="color: #d97706; font-weight: bold; margin-bottom: 6px;">
                Export limité à {{ $contributions->count() }} lignes. Utilisez les filtres pour affiner les résultats.
            </p>
        @endif
        Généré par DIGI-TONTINE CI — {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>

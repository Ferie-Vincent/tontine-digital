<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bilan Financier — {{ $tontine->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1e293b; line-height: 1.5; }
        .header { background: linear-gradient(135deg, #059669, #10b981); color: white; padding: 24px 30px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin-bottom: 4px; }
        .header p { font-size: 11px; opacity: 0.9; }
        .section { margin: 0 24px 16px; }
        .section-title { font-size: 13px; font-weight: bold; color: #059669; border-bottom: 2px solid #059669; padding-bottom: 4px; margin-bottom: 10px; }
        .kpi-grid { display: table; width: 100%; margin-bottom: 16px; }
        .kpi-row { display: table-row; }
        .kpi-item { display: table-cell; text-align: center; padding: 12px 8px; border: 1px solid #e2e8f0; width: 25%; }
        .kpi-value { font-size: 16px; font-weight: bold; color: #1e293b; }
        .kpi-label { font-size: 9px; color: #64748b; margin-top: 2px; text-transform: uppercase; }
        .kpi-green .kpi-value { color: #059669; }
        .kpi-blue .kpi-value { color: #3b82f6; }
        .kpi-amber .kpi-value { color: #d97706; }
        .kpi-red .kpi-value { color: #dc2626; }
        table.data { width: 100%; border-collapse: collapse; font-size: 9px; }
        table.data th { background: #f1f5f9; padding: 6px 8px; text-align: left; font-weight: 600; border: 1px solid #e2e8f0; }
        table.data td { padding: 5px 8px; border: 1px solid #e2e8f0; }
        table.data tr:nth-child(even) { background: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-amber { color: #d97706; }
        .bold { font-weight: bold; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-slate { background: #f1f5f9; color: #475569; }
        .footer { text-align: center; color: #94a3b8; font-size: 9px; margin-top: 24px; padding: 12px 24px; border-top: 1px solid #e2e8f0; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $tontine->name }}</h1>
        <p>Bilan financier complet</p>
        <p>Exporté le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    {{-- KPIs --}}
    <div class="section">
        <h2 class="section-title">Résumé Financier</h2>
        <div class="kpi-grid">
            <div class="kpi-row">
                <div class="kpi-item kpi-green">
                    <div class="kpi-value">{{ format_amount($totalCollected) }}</div>
                    <div class="kpi-label">Total collecté</div>
                </div>
                <div class="kpi-item kpi-blue">
                    <div class="kpi-value">{{ format_amount($totalDisbursed) }}</div>
                    <div class="kpi-label">Total décaissé</div>
                </div>
                <div class="kpi-item kpi-amber">
                    <div class="kpi-value">{{ format_amount($pendingAmount) }}</div>
                    <div class="kpi-label">En attente</div>
                </div>
                <div class="kpi-item kpi-red">
                    <div class="kpi-value">{{ format_amount($totalPenalties) }}</div>
                    <div class="kpi-label">Pénalités</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tour tracking --}}
    <div class="section">
        <h2 class="section-title">Suivi par Tour</h2>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 5%;">N°</th>
                    <th style="width: 20%;">Bénéficiaire</th>
                    <th class="text-right" style="width: 15%;">Montant attendu</th>
                    <th class="text-right" style="width: 15%;">Montant confirmé</th>
                    <th class="text-center" style="width: 8%;">%</th>
                    <th class="text-center" style="width: 10%;">Échéance</th>
                    <th class="text-center" style="width: 12%;">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tourStats as $tour)
                <tr>
                    <td class="bold">{{ $tour->tour_number }}</td>
                    <td>{{ $tour->beneficiary_name }}</td>
                    <td class="text-right">{{ format_amount($tour->expected_amount ?? 0) }}</td>
                    <td class="text-right bold">{{ format_amount($tour->confirmed_amount) }}</td>
                    <td class="text-center">
                        <span class="{{ $tour->collection_percent >= 100 ? 'text-green' : ($tour->collection_percent >= 50 ? 'text-amber' : 'text-red') }} bold">
                            {{ $tour->collection_percent }}%
                        </span>
                    </td>
                    <td class="text-center">{{ $tour->due_date ? $tour->due_date->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">
                        @if($tour->disbursed)
                            <span class="badge badge-green">Versé</span>
                        @else
                            @php
                                $statusValue = $tour->status->value ?? $tour->status;
                                $badgeClass = match($statusValue) {
                                    'completed', 'COMPLETED' => 'badge-green',
                                    'ongoing', 'ONGOING' => 'badge-blue',
                                    'pending', 'PENDING' => 'badge-slate',
                                    default => 'badge-slate',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $tour->status->label() }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Member tracking --}}
    <div class="section" style="margin-top: 20px;">
        <h2 class="section-title">Suivi par Membre</h2>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 5%;">Pos</th>
                    <th style="width: 22%;">Nom</th>
                    <th class="text-right" style="width: 15%;">Cotisé</th>
                    <th class="text-right" style="width: 15%;">Reçu</th>
                    <th class="text-center" style="width: 8%;">Retards</th>
                    <th class="text-right" style="width: 12%;">Pénalités</th>
                    <th class="text-right" style="width: 15%;">Solde net</th>
                </tr>
            </thead>
            <tbody>
                @foreach($memberStats as $member)
                <tr>
                    <td class="text-center bold">{{ $member->position }}</td>
                    <td>{{ $member->name }}</td>
                    <td class="text-right">{{ format_amount($member->contributed) }}</td>
                    <td class="text-right text-green bold">{{ format_amount($member->received) }}</td>
                    <td class="text-center">
                        @if($member->late_count > 0)
                            <span class="badge {{ $member->late_count >= 3 ? 'badge-red' : 'badge-amber' }}">{{ $member->late_count }}</span>
                        @else
                            0
                        @endif
                    </td>
                    <td class="text-right {{ $member->penalties > 0 ? 'text-red' : '' }}">{{ format_amount($member->penalties) }}</td>
                    <td class="text-right bold {{ $member->net >= 0 ? 'text-green' : 'text-red' }}">
                        {{ $member->net >= 0 ? '+' : '' }}{{ format_amount($member->net) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        @if(!empty($limitReached) && $limitReached)
            <p style="color: #d97706; font-weight: bold; margin-bottom: 6px;">
                Export limité pour des raisons de performance. Certaines données peuvent être tronquées.
            </p>
        @endif
        Généré par DIGI-TONTINE CI — {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>

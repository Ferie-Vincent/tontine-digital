<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Financier — {{ $tontine->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
        .header { background: linear-gradient(135deg, #3b82f6, #6366f1); color: white; padding: 24px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin-bottom: 4px; }
        .header p { font-size: 11px; opacity: 0.9; }
        .section { margin: 0 20px 16px; }
        .section-title { font-size: 13px; font-weight: bold; color: #3b82f6; border-bottom: 2px solid #3b82f6; padding-bottom: 4px; margin-bottom: 10px; }
        .kpi-grid { display: table; width: 100%; margin-bottom: 16px; }
        .kpi-row { display: table-row; }
        .kpi-item { display: table-cell; text-align: center; padding: 12px 8px; border: 1px solid #e2e8f0; width: 25%; }
        .kpi-value { font-size: 18px; font-weight: bold; color: #1e293b; }
        .kpi-label { font-size: 9px; color: #64748b; margin-top: 2px; }
        .kpi-green .kpi-value { color: #059669; }
        .kpi-blue .kpi-value { color: #3b82f6; }
        .kpi-amber .kpi-value { color: #d97706; }
        .kpi-red .kpi-value { color: #dc2626; }
        table.data { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.data th { background: #f1f5f9; padding: 6px 8px; text-align: left; font-weight: 600; border: 1px solid #e2e8f0; }
        table.data td { padding: 5px 8px; border: 1px solid #e2e8f0; }
        table.data tr:nth-child(even) { background: #f8fafc; }
        .progress-bar { background: #e2e8f0; border-radius: 4px; height: 8px; overflow: hidden; }
        .progress-fill { background: #3b82f6; height: 100%; border-radius: 4px; }
        .footer { text-align: center; color: #94a3b8; font-size: 9px; margin-top: 24px; padding: 12px 20px; border-top: 1px solid #e2e8f0; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tontine->name }}</h1>
        <p>Rapport financier — Période : {{ $data['period_label'] }}</p>
        <p>Généré le {{ $generatedAt->format('d/m/Y à H:i') }}</p>
    </div>

    {{-- KPIs --}}
    <div class="section">
        <h2 class="section-title">Résumé Financier</h2>
        <div class="kpi-grid">
            <div class="kpi-row">
                <div class="kpi-item kpi-green">
                    <div class="kpi-value">{{ format_amount($data['total_collected']) }}</div>
                    <div class="kpi-label">COLLECTÉS (TOTAL)</div>
                </div>
                <div class="kpi-item kpi-blue">
                    <div class="kpi-value">{{ format_amount($data['total_disbursed']) }}</div>
                    <div class="kpi-label">DÉCAISSÉS (TOTAL)</div>
                </div>
                <div class="kpi-item kpi-amber">
                    <div class="kpi-value">{{ format_amount($data['total_penalties']) }}</div>
                    <div class="kpi-label">PÉNALITÉS</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-value">{{ $data['active_members_count'] }}</div>
                    <div class="kpi-label">MEMBRES ACTIFS</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Progression --}}
    <div class="section">
        <h2 class="section-title">Progression Globale</h2>
        <p style="margin-bottom: 6px;">Tours complétés : <strong>{{ $data['completed_tours'] }}</strong> / {{ $data['total_tours'] }} ({{ $data['progress'] }}%)</p>
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $data['progress'] }}%"></div>
        </div>
    </div>

    {{-- Collecte de la période --}}
    <div class="section">
        <h2 class="section-title">Activité de la Période</h2>
        <table class="data">
            <tr>
                <th>Indicateur</th>
                <th style="text-align: right;">Montant</th>
            </tr>
            <tr>
                <td>Cotisations collectées (période)</td>
                <td style="text-align: right; font-weight: 600; color: #059669;">{{ format_amount($data['period_collected']) }}</td>
            </tr>
            <tr>
                <td>Fonds décaissés (période)</td>
                <td style="text-align: right; font-weight: 600; color: #3b82f6;">{{ format_amount($data['period_disbursed']) }}</td>
            </tr>
        </table>
    </div>

    {{-- Membres en retard --}}
    @if(!empty($data['late_members']))
    <div class="section">
        <h2 class="section-title">Membres en Retard</h2>
        <table class="data">
            <tr>
                <th>Membre</th>
                <th style="text-align: center;">Retards</th>
                <th style="text-align: right;">Pénalités</th>
            </tr>
            @foreach($data['late_members'] as $member)
            <tr>
                <td>{{ $member['name'] }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $member['late_count'] >= 3 ? 'badge-red' : 'badge-amber' }}">{{ $member['late_count'] }}</span>
                </td>
                <td style="text-align: right;">{{ format_amount($member['total_penalty']) }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    {{-- Prochains tours --}}
    @if(!empty($data['upcoming_tours']))
    <div class="section">
        <h2 class="section-title">Prochains Tours</h2>
        <table class="data">
            <tr>
                <th>Tour</th>
                <th>Bénéficiaire</th>
                <th>Échéance</th>
                <th>Statut</th>
            </tr>
            @foreach($data['upcoming_tours'] as $tour)
            <tr>
                <td>#{{ $tour['number'] }}</td>
                <td>{{ $tour['beneficiary'] }}</td>
                <td>{{ $tour['due_date'] }}</td>
                <td><span class="badge badge-green">{{ $tour['status'] }}</span></td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <div class="footer">
        Rapport généré automatiquement par la plateforme DIGI-TONTINE CI — {{ $generatedAt->format('d/m/Y H:i') }}
    </div>
</body>
</html>

<x-layouts.app title="Historique financier">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <div class="p-2 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Historique financier</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Synthese de vos cotisations et versements</p>
            </div>
        </div>
    </x-slot:header>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total cotise --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-500/10 to-blue-600/5 rounded-bl-full"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 mb-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total cotise</p>
                <p class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white mt-1">{{ format_amount($totalContributed) }}</p>
            </div>
        </div>

        {{-- Total recu --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 rounded-bl-full"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 mb-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total recu</p>
                <p class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white mt-1">{{ format_amount($totalReceived) }}</p>
            </div>
        </div>

        {{-- Solde net --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-violet-500/10 to-violet-600/5 rounded-bl-full"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center shadow-lg shadow-violet-500/30 mb-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Solde net</p>
                <p class="text-lg sm:text-xl font-bold {{ $netBalance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                    {{ $netBalance >= 0 ? '+' : '' }}{{ format_amount($netBalance) }}
                </p>
            </div>
        </div>

        {{-- Penalites --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-500/10 to-red-600/5 rounded-bl-full"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg shadow-red-500/30 mb-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Penalites</p>
                <p class="text-lg sm:text-xl font-bold text-red-600 dark:text-red-400 mt-1">{{ format_amount($totalPenalties) }}</p>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 sm:p-6 mb-6">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <div>
                <h3 class="text-base font-semibold text-slate-800 dark:text-white">Évolution mensuelle</h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Cotisations vs versements sur 12 mois</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <div class="relative h-[220px] sm:h-[300px]">
            <canvas id="financialChart"></canvas>
        </div>
    </div>

    {{-- Répartition par tontine --}}
    @if($tontineStats->count() > 0)
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Répartition par tontine</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Détail de vos finances par groupe</p>
        </div>

        {{-- Version mobile : cartes --}}
        <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($tontineStats as $stat)
            <a href="{{ route('tontines.show', $stat->id) }}" class="block p-4 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $stat->name }}</p>
                    <span class="text-sm font-bold {{ $stat->net >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $stat->net >= 0 ? '+' : '' }}{{ format_amount($stat->net) }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Cotisé</span>
                        <span class="text-slate-700 dark:text-slate-300 font-medium">{{ format_amount($stat->total_contributed) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Reçu</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ format_amount($stat->total_received) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Pénalités</span>
                        <span class="{{ $stat->total_penalties > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-400 dark:text-slate-500' }} font-medium">{{ format_amount($stat->total_penalties) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Retards</span>
                        @if($stat->late_count > 0)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400">{{ $stat->late_count }}</span>
                        @else
                            <span class="text-slate-400 dark:text-slate-500 font-medium">0</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Version desktop : tableau --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-700/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Tontine</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Cotisé</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Reçu</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Pénalités</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Retards</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Solde</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($tontineStats as $stat)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('tontines.show', $stat->id) }}" class="text-sm font-medium text-slate-800 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                {{ $stat->name }}
                            </a>
                        </td>
                        <td class="px-5 py-4 text-right text-sm text-slate-600 dark:text-slate-300 whitespace-nowrap">
                            {{ format_amount($stat->total_contributed) }}
                        </td>
                        <td class="px-5 py-4 text-right text-sm text-emerald-600 dark:text-emerald-400 font-medium whitespace-nowrap">
                            {{ format_amount($stat->total_received) }}
                        </td>
                        <td class="px-5 py-4 text-right text-sm {{ $stat->total_penalties > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-400 dark:text-slate-500' }} whitespace-nowrap">
                            {{ format_amount($stat->total_penalties) }}
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if($stat->late_count > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400">
                                    {{ $stat->late_count }}
                                </span>
                            @else
                                <span class="text-sm text-slate-400 dark:text-slate-500">0</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right text-sm font-semibold {{ $stat->net >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} whitespace-nowrap">
                            {{ $stat->net >= 0 ? '+' : '' }}{{ format_amount($stat->net, false) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Dernieres transactions --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Dernières cotisations --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-500/5 to-transparent">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                        </svg>
                    </div>
                    <h3 class="text-sm sm:text-base font-semibold text-slate-800 dark:text-white">Dernières cotisations</h3>
                </div>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($recentContributions as $contribution)
                <div class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-slate-800 dark:text-white truncate">{{ $contribution->tontine->name }}</p>
                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">
                                Tour #{{ $contribution->tour->tour_number }}
                                &bull; {{ $contribution->confirmed_at?->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-white shrink-0 ml-3">
                        -{{ format_amount($contribution->amount, false) }}
                    </p>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Aucune cotisation</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Derniers versements reçus --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-emerald-500/5 to-transparent">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                        </svg>
                    </div>
                    <h3 class="text-sm sm:text-base font-semibold text-slate-800 dark:text-white">Versements reçus</h3>
                </div>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($recentDisbursements as $tour)
                <div class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-slate-800 dark:text-white truncate">{{ $tour->tontine->name }}</p>
                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">
                                Tour #{{ $tour->tour_number }}
                                &bull; {{ $tour->disbursed_at?->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-semibold text-emerald-600 dark:text-emerald-400 shrink-0 ml-3">
                        +{{ format_amount($tour->collected_amount, false) }}
                    </p>
                </div>
                @empty
                <div class="p-8 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Aucun versement recu</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94A3B8' : '#64748B';
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(100, 116, 139, 0.1)';
            const tooltipBg = isDark ? '#1E293B' : '#fff';
            const tooltipTitle = isDark ? '#fff' : '#1E293B';
            const tooltipBody = isDark ? '#94A3B8' : '#64748B';
            const tooltipBorder = isDark ? '#334155' : '#E2E8F0';

            function formatMoney(value) {
                return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
            }

            const ctx = document.getElementById('financialChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Cotisations',
                                data: @json($chartContributed),
                                backgroundColor: function(context) {
                                    const chart = context.chart;
                                    const {ctx, chartArea} = chart;
                                    if (!chartArea) return '#3B82F6';
                                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                    gradient.addColorStop(0, '#2563EB');
                                    gradient.addColorStop(1, '#3B82F6');
                                    return gradient;
                                },
                                borderRadius: 6,
                                maxBarThickness: 28,
                            },
                            {
                                label: 'Versements recus',
                                data: @json($chartReceived),
                                backgroundColor: function(context) {
                                    const chart = context.chart;
                                    const {ctx, chartArea} = chart;
                                    if (!chartArea) return '#10B981';
                                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                    gradient.addColorStop(0, '#059669');
                                    gradient.addColorStop(1, '#10B981');
                                    return gradient;
                                },
                                borderRadius: 6,
                                maxBarThickness: 28,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    color: textColor,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 16,
                                    font: { weight: '500', size: 12 }
                                }
                            },
                            tooltip: {
                                backgroundColor: tooltipBg,
                                titleColor: tooltipTitle,
                                bodyColor: tooltipBody,
                                borderColor: tooltipBorder,
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                callbacks: {
                                    label: function(ctx) {
                                        return ctx.dataset.label + ' : ' + formatMoney(ctx.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: textColor,
                                    font: { weight: '500', size: window.innerWidth < 640 ? 9 : 12 },
                                    maxRotation: window.innerWidth < 640 ? 45 : 0,
                                    autoSkip: true,
                                    maxTicksToDisplay: window.innerWidth < 640 ? 6 : 12
                                },
                                grid: { display: false },
                                border: { display: false }
                            },
                            y: {
                                ticks: {
                                    color: textColor,
                                    font: { size: window.innerWidth < 640 ? 10 : 12 },
                                    callback: function(v) {
                                        return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v);
                                    }
                                },
                                grid: { color: gridColor, drawBorder: false },
                                border: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>

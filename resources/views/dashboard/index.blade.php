<x-layouts.app title="Tableau de bord">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/25">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Tableau de bord</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Vue d'ensemble de vos tontines</p>
            </div>
        </div>
    </x-slot:header>

    {{-- Actions requises --}}
    @if($alerts->count() > 0)
    <div class="mb-6 space-y-3">
        <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions requises ({{ $alerts->count() }})</h3>
        @foreach($alerts as $alert)
        @php
            $colors = match($alert->severity) {
                'critical' => 'from-red-600 to-red-700 dark:from-red-700 dark:to-red-800 shadow-red-500/20',
                'warning' => 'from-amber-500 to-amber-600 dark:from-amber-600 dark:to-amber-700 shadow-amber-500/20',
                'success' => 'from-emerald-500 to-emerald-600 dark:from-emerald-600 dark:to-emerald-700 shadow-emerald-500/20',
                default => 'from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 shadow-blue-500/20',
            };
            $iconSvg = match($alert->icon) {
                'payment' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'validate' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'disburse' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                'members' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>',
                'beneficiary' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>',
                default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            };
        @endphp
        <div class="relative overflow-hidden bg-gradient-to-r {{ $colors }} rounded-xl shadow-lg">
            <div class="relative p-4 sm:p-5">
                <div class="flex items-center gap-4">
                    <div class="shrink-0 w-10 h-10 rounded-lg bg-white/15 backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconSvg !!}</svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <h4 class="text-white font-bold text-sm">{{ $alert->title }}</h4>
                            @if($alert->severity === 'critical')
                            <span class="px-1.5 py-0.5 bg-white/20 rounded text-[10px] font-bold text-white uppercase">Urgent</span>
                            @endif
                        </div>
                        <p class="text-white/90 text-sm">{{ $alert->message }}</p>
                        @if($alert->detail)
                        <p class="text-white/70 text-xs mt-0.5">{{ $alert->detail }}</p>
                        @endif
                    </div>
                    <a href="{{ $alert->url }}" class="shrink-0 inline-flex items-center gap-1 px-3 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition">
                        <span class="hidden sm:inline">Voir</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Stats principaux --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        {{-- Tontines actives --}}
        <div class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-lg hover:shadow-primary-500/5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tontines actives</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mt-2">{{ $activeTontines }}</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Contributions du mois --}}
        @php
            $contributionTrend = $previousMonthContributions > 0
                ? round((($monthlyContributions - $previousMonthContributions) / $previousMonthContributions) * 100, 1)
                : ($monthlyContributions > 0 ? 100 : 0);
            $contributionTrendUp = $contributionTrend >= 0;
        @endphp
        <div class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-lg hover:shadow-emerald-500/5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Contributions du mois</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mt-2 truncate">{{ format_amount($monthlyContributions) }}</p>
                    @if($contributionTrend != 0)
                    <div class="flex items-center gap-1.5 mt-3">
                        @if($contributionTrendUp)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            {{ abs($contributionTrend) }}%
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">vs mois dernier</span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            {{ abs($contributionTrend) }}%
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">vs mois dernier</span>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300 flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        {{-- Prochain paiement --}}
        <div class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-lg hover:shadow-amber-500/5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Prochain paiement</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white mt-2 truncate">{{ $nextPayment ? $nextPayment->tontine->name : 'Aucun' }}</p>
                    @if($nextPayment)
                    <p class="text-sm text-amber-600 dark:text-amber-400 mt-1 font-medium">{{ $nextPayment->due_date->format('d/m/Y') }}</p>
                    @endif
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300 flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>

        {{-- Prochain encaissement --}}
        <div class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-lg hover:shadow-violet-500/5 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Prochain encaissement</p>
                    <p class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white mt-2 truncate">{{ $nextReceive ? $nextReceive->tontine->name : 'Aucun' }}</p>
                    @if($nextReceive)
                    <p class="text-sm text-violet-600 dark:text-violet-400 mt-1 font-medium">{{ $nextReceive->due_date->format('d/m/Y') }}</p>
                    @endif
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-500/30 group-hover:scale-110 transition-transform duration-300 flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- KPIs supplementaires --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
        {{-- Total cotise --}}
        <div class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-lg transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total cotise</p>
                    <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mt-2">{{ format_amount($totalContributed) }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Toutes tontines confondues</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300 flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
            </div>
        </div>

        {{-- Taux de contribution --}}
        @php
            $rateColor = $contributionRate >= 75 ? 'emerald' : ($contributionRate >= 50 ? 'amber' : 'red');
        @endphp
        <div class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-lg transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-{{ $rateColor }}-500/10 to-{{ $rateColor }}-600/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Taux de contribution</p>
                    <div class="flex items-baseline gap-2 mt-2">
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white">{{ $contributionRate }}%</p>
                        <div class="flex-1 max-w-[120px] h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-{{ $rateColor }}-500 to-{{ $rateColor }}-600 rounded-full transition-all duration-500" style="width: {{ $contributionRate }}%"></div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Contributions confirmees / attendues</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-{{ $rateColor }}-500 to-{{ $rateColor }}-600 flex items-center justify-center shadow-lg shadow-{{ $rateColor }}-500/30 group-hover:scale-110 transition-transform duration-300 flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
        {{-- Bar Chart : Contributions mensuelles --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Contributions des 6 derniers mois</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Evolution de vos cotisations</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="relative" style="height: 300px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Doughnut Chart : Repartition des tontines --}}
        <div>
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden h-full">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Repartition</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Par statut</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if(count($doughnutValues) > 0)
                    <div class="relative flex items-center justify-center" style="height: 280px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center text-slate-500 dark:text-slate-400 text-sm py-12">
                        <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        </svg>
                        <p>Aucune tontine</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques supplementaires : Repartition par tontine + Statut des cotisations --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
        {{-- Doughnut : Repartition par tontine --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-white">Répartition par tontine</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Montants cotisés par tontine</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if(count($tontineBreakdownValues) > 0)
                <div class="relative flex items-center justify-center" style="height: 300px;">
                    <canvas id="tontineBreakdownChart"></canvas>
                </div>
                @else
                <div class="flex flex-col items-center justify-center text-slate-500 dark:text-slate-400 text-sm py-12">
                    <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    </svg>
                    <p>Aucune cotisation confirmée</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Doughnut : Statut des cotisations --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-white">Statut des cotisations</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Distribution par statut</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if(count($statusDistValues) > 0)
                <div class="relative flex items-center justify-center" style="height: 300px;">
                    <canvas id="statusDistChart"></canvas>
                </div>
                @else
                <div class="flex flex-col items-center justify-center text-slate-500 dark:text-slate-400 text-sm py-12">
                    <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p>Aucune cotisation enregistrée</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions rapides --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
        <a href="{{ route('requests.create') }}" class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:border-violet-300 dark:hover:border-violet-500/50 transition-all duration-300 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-500/30 group-hover:scale-110 transition-transform duration-300 shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-slate-800 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">Soumettre une requête</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Paiement, reclamation, question...</p>
            </div>
        </a>

        <a href="{{ route('requests.index') }}" class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-500/50 transition-all duration-300 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300 shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <p class="font-semibold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Mes requêtes</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Suivre vos demandes en cours</p>
            </div>
        </a>

        <a href="{{ route('tontines.join') }}" class="group bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-500/50 transition-all duration-300 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300 shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-slate-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Rejoindre une tontine</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Avec un code d'invitation</p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Mes Tontines --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-md shadow-primary-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Mes Tontines</h3>
                </div>
                <div class="flex gap-2">
                    @if(auth()->user()->is_admin)
                    <x-button variant="primary" size="sm" @click="$dispatch('open-modal-create-tontine')">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Creer
                    </x-button>
                    @endif
                    <x-button variant="outline" size="sm" @click="$dispatch('open-modal-join-tontine')">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Rejoindre
                    </x-button>
                </div>
            </div>

            @forelse($myTontines as $tontine)
            <a href="{{ route('tontines.show', $tontine) }}" class="block group">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-500/50 transition-all duration-300 p-5 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 via-primary-400 to-primary-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-500/20 dark:to-primary-600/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <span class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ strtoupper(substr($tontine->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <h4 class="text-slate-800 dark:text-white font-semibold group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $tontine->name }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                    <span>{{ $tontine->frequency->label() }}</span>
                                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                    <span>{{ $tontine->active_members_count }} membre(s)</span>
                                </p>
                            </div>
                        </div>
                        <x-badge :color="$tontine->status->color()">{{ $tontine->status->label() }}</x-badge>
                    </div>

                    <div class="flex items-center justify-between text-sm mb-3">
                        <span class="text-slate-600 dark:text-slate-300 font-medium">{{ $tontine->formatted_amount }} / tour</span>
                        <span class="text-primary-600 dark:text-primary-400 font-bold">{{ $tontine->progress }}%</span>
                    </div>

                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-primary-500 to-primary-600 rounded-full transition-all duration-500 ease-out" style="width: {{ $tontine->progress }}%"></div>
                    </div>

                    <div class="absolute right-5 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
            @empty
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-8">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Aucune tontine</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Vous n'avez pas encore de tontine. Creez-en une ou rejoignez une existante.</p>
                    <div class="flex gap-3">
                        @if(auth()->user()->is_admin)
                        <x-button variant="primary" size="sm" @click="$dispatch('open-modal-create-tontine')">Creer une tontine</x-button>
                        @endif
                        <x-button variant="outline" size="sm" @click="$dispatch('open-modal-join-tontine')">Rejoindre</x-button>
                    </div>
                </div>
            </div>
            @endforelse

            @if($myTontines->count() > 0)
            <div class="text-center pt-2">
                <a href="{{ route('tontines.index') }}" class="inline-flex items-center gap-2 text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium group">
                    Voir toutes mes tontines
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endif

            @if($contributionsByTontine->isNotEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mt-6">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Contributions par tontine</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Repartition detaillee</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="relative" style="height: {{ max(200, $contributionsByTontine->count() * 50) }}px;">
                        <canvas id="tontineChart"></canvas>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Activite recente --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md shadow-emerald-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Activite recente</h3>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($recentActivities as $activity)
                    <div class="flex items-start gap-3 p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
                        <div class="relative">
                            <x-avatar :user="$activity->user ?? null" :name="$activity->user->name ?? 'Système'" size="sm" />
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-slate-600 dark:text-slate-300">
                                <span class="text-slate-800 dark:text-white font-semibold">{{ $activity->user->name ?? 'Systeme' }}</span>
                                {{ $activity->action_label }}
                            </p>
                            @if($activity->tontine)
                            <p class="text-xs text-primary-600 dark:text-primary-400 font-medium mt-0.5">{{ $activity->tontine->name }}</p>
                            @endif
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-12 px-4">
                        <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center">Aucune activite recente</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create --}}
    @if(auth()->user()->is_admin)
    <x-modal id="create-tontine" maxWidth="2xl" title="Creer une tontine">
        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1">Nouvelle tontine</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Remplissez les informations pour creer votre tontine</p>
        </div>
        <form method="POST" action="{{ route('tontines.store') }}" class="space-y-5">
            @csrf
            <x-input name="name" label="Nom de la tontine" placeholder="Ex: Tontine des collegues" :error="$errors->createTontine->first('name')" required />
            <x-textarea name="description" label="Description" placeholder="Décrivez votre tontine..." rows="3" :error="$errors->createTontine->first('description')" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input name="contribution_amount" label="Montant de cotisation (FCFA)" type="number" min="1000" step="500" placeholder="50000" :error="$errors->createTontine->first('contribution_amount')" required />
                <x-select name="frequency" label="Fréquence" :error="$errors->createTontine->first('frequency')" required>
                    <option value="">Choisir...</option>
                    <option value="weekly" {{ old('frequency') === 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                    <option value="biweekly" {{ old('frequency') === 'biweekly' ? 'selected' : '' }}>Bimensuelle</option>
                    <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                </x-select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input name="max_members" label="Nombre max de membres" type="number" min="2" max="100" placeholder="10" :error="$errors->createTontine->first('max_members')" required />
                <x-input name="start_date" label="Date de debut" type="date" :error="$errors->createTontine->first('start_date')" required />
            </div>
            <x-textarea name="rules" label="Reglement (optionnel)" placeholder="Regles de la tontine..." rows="4" :error="$errors->createTontine->first('rules')" />
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                <x-button type="button" variant="ghost" @click="$dispatch('close-modal-create-tontine')">Annuler</x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Creer la tontine
                </x-button>
            </div>
        </form>
    </x-modal>
    @endif

    {{-- Modal Join --}}
    <x-modal id="join-tontine" maxWidth="sm" title="Rejoindre une tontine">
        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1">Rejoindre une tontine</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Entrez le code d'invitation fourni par l'administrateur</p>
        </div>
        <form method="POST" action="{{ route('tontines.join.submit') }}" class="space-y-5">
            @csrf
            <x-input name="code" label="Code d'invitation" placeholder="XXXXXXXX" maxlength="8" class="text-center text-2xl font-mono tracking-widest uppercase" :error="$errors->joinTontine->first('code')" required />
            <x-button type="submit" variant="primary" class="w-full">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Rejoindre
            </x-button>
        </form>
    </x-modal>

    @if($errors->createTontine->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new Event('open-modal-create-tontine'));
        });
    </script>
    @endif

    @if($errors->joinTontine->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new Event('open-modal-join-tontine'));
        });
    </script>
    @endif

    @push('scripts')
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94A3B8' : '#64748B';
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(100, 116, 139, 0.1)';

            function formatMoney(value) {
                return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
            }

            // Bar Chart
            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            data: @json($chartValues),
                            backgroundColor: function(context) {
                                const chart = context.chart;
                                const {ctx, chartArea} = chart;
                                if (!chartArea) return '#3C50E0';
                                const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                gradient.addColorStop(0, '#6366F1');
                                gradient.addColorStop(1, '#3C50E0');
                                return gradient;
                            },
                            borderRadius: 6,
                            maxBarThickness: 45,
                            hoverBackgroundColor: '#4F46E5',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: isDark ? '#1E293B' : '#fff',
                                titleColor: isDark ? '#fff' : '#1E293B',
                                bodyColor: isDark ? '#94A3B8' : '#64748B',
                                borderColor: isDark ? '#334155' : '#E2E8F0',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                callbacks: {
                                    label: function(ctx) {
                                        return formatMoney(ctx.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: textColor,
                                    font: { weight: '500' }
                                },
                                grid: { display: false },
                                border: { display: false }
                            },
                            y: {
                                ticks: {
                                    color: textColor,
                                    callback: function(value) {
                                        return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(value);
                                    }
                                },
                                grid: {
                                    color: gridColor,
                                    drawBorder: false
                                },
                                border: { display: false }
                            }
                        }
                    }
                });
            }

            // Doughnut Chart
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                const doughnutLabels = @json($doughnutLabels);
                const doughnutValues = @json($doughnutValues);
                const doughnutColors = @json($doughnutColors);
                const total = doughnutValues.reduce((a, b) => a + b, 0);

                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: doughnutLabels,
                        datasets: [{
                            data: doughnutValues,
                            backgroundColor: doughnutColors,
                            borderWidth: 0,
                            hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: textColor,
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: { weight: '500' }
                                }
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#1E293B' : '#fff',
                                titleColor: isDark ? '#fff' : '#1E293B',
                                bodyColor: isDark ? '#94A3B8' : '#64748B',
                                borderColor: isDark ? '#334155' : '#E2E8F0',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                            }
                        }
                    },
                    plugins: [{
                        id: 'centerText',
                        beforeDraw: function(chart) {
                            const { ctx: c } = chart;
                            c.save();
                            c.font = 'bold 28px Inter, system-ui, sans-serif';
                            c.fillStyle = isDark ? '#F1F5F9' : '#1E293B';
                            c.textAlign = 'center';
                            c.textBaseline = 'middle';
                            const chartArea = chart.chartArea;
                            const centerX = (chartArea.left + chartArea.right) / 2;
                            const centerY = (chartArea.top + chartArea.bottom) / 2;
                            c.fillText(total, centerX, centerY - 10);
                            c.font = '500 13px Inter, system-ui, sans-serif';
                            c.fillStyle = textColor;
                            c.fillText('tontine' + (total > 1 ? 's' : ''), centerX, centerY + 16);
                            c.restore();
                        }
                    }]
                });
            }

            // Doughnut Chart : Repartition par tontine
            const tontineBreakdownCtx = document.getElementById('tontineBreakdownChart');
            if (tontineBreakdownCtx) {
                const tbLabels = @json($tontineBreakdownLabels);
                const tbValues = @json($tontineBreakdownValues);
                const tbAllColors = @json($tontineBreakdownColors);
                const tbColors = tbLabels.map((_, i) => tbAllColors[i % tbAllColors.length]);
                const tbTotal = tbValues.reduce((a, b) => a + b, 0);

                new Chart(tontineBreakdownCtx, {
                    type: 'doughnut',
                    data: {
                        labels: tbLabels,
                        datasets: [{
                            data: tbValues,
                            backgroundColor: tbColors,
                            borderWidth: 0,
                            hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: textColor,
                                    padding: 16,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: { weight: '500', size: 12 }
                                }
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#1E293B' : '#fff',
                                titleColor: isDark ? '#fff' : '#1E293B',
                                bodyColor: isDark ? '#94A3B8' : '#64748B',
                                borderColor: isDark ? '#334155' : '#E2E8F0',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                callbacks: {
                                    label: function(ctx) {
                                        const pct = tbTotal > 0 ? Math.round((ctx.parsed / tbTotal) * 100) : 0;
                                        return ctx.label + ' : ' + formatMoney(ctx.parsed) + ' (' + pct + '%)';
                                    }
                                }
                            }
                        }
                    },
                    plugins: [{
                        id: 'centerTextBreakdown',
                        beforeDraw: function(chart) {
                            const { ctx: c } = chart;
                            c.save();
                            c.font = 'bold 22px Inter, system-ui, sans-serif';
                            c.fillStyle = isDark ? '#F1F5F9' : '#1E293B';
                            c.textAlign = 'center';
                            c.textBaseline = 'middle';
                            const chartArea = chart.chartArea;
                            const centerX = (chartArea.left + chartArea.right) / 2;
                            const centerY = (chartArea.top + chartArea.bottom) / 2;
                            c.fillText(new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(tbTotal), centerX, centerY - 10);
                            c.font = '500 12px Inter, system-ui, sans-serif';
                            c.fillStyle = textColor;
                            c.fillText('FCFA', centerX, centerY + 14);
                            c.restore();
                        }
                    }]
                });
            }

            // Doughnut Chart : Statut des cotisations
            const statusDistCtx = document.getElementById('statusDistChart');
            if (statusDistCtx) {
                const sdLabels = @json($statusDistLabels);
                const sdValues = @json($statusDistValues);
                const sdColors = @json($statusDistColors);
                const sdTotal = sdValues.reduce((a, b) => a + b, 0);

                new Chart(statusDistCtx, {
                    type: 'doughnut',
                    data: {
                        labels: sdLabels,
                        datasets: [{
                            data: sdValues,
                            backgroundColor: sdColors,
                            borderWidth: 0,
                            hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: textColor,
                                    padding: 16,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: { weight: '500', size: 12 }
                                }
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#1E293B' : '#fff',
                                titleColor: isDark ? '#fff' : '#1E293B',
                                bodyColor: isDark ? '#94A3B8' : '#64748B',
                                borderColor: isDark ? '#334155' : '#E2E8F0',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                callbacks: {
                                    label: function(ctx) {
                                        const pct = sdTotal > 0 ? Math.round((ctx.parsed / sdTotal) * 100) : 0;
                                        return ctx.label + ' : ' + ctx.parsed + ' (' + pct + '%)';
                                    }
                                }
                            }
                        }
                    },
                    plugins: [{
                        id: 'centerTextStatus',
                        beforeDraw: function(chart) {
                            const { ctx: c } = chart;
                            c.save();
                            c.font = 'bold 28px Inter, system-ui, sans-serif';
                            c.fillStyle = isDark ? '#F1F5F9' : '#1E293B';
                            c.textAlign = 'center';
                            c.textBaseline = 'middle';
                            const chartArea = chart.chartArea;
                            const centerX = (chartArea.left + chartArea.right) / 2;
                            const centerY = (chartArea.top + chartArea.bottom) / 2;
                            c.fillText(sdTotal, centerX, centerY - 10);
                            c.font = '500 12px Inter, system-ui, sans-serif';
                            c.fillStyle = textColor;
                            c.fillText('cotisation' + (sdTotal > 1 ? 's' : ''), centerX, centerY + 14);
                            c.restore();
                        }
                    }]
                });
            }

            // Horizontal Bar Chart
            const tontineCtx = document.getElementById('tontineChart');
            if (tontineCtx) {
                const tontineData = @json($contributionsByTontine);
                new Chart(tontineCtx, {
                    type: 'bar',
                    data: {
                        labels: tontineData.map(t => t.name),
                        datasets: [
                            {
                                label: 'Confirmées',
                                data: tontineData.map(t => t.confirmed),
                                backgroundColor: '#10B981',
                                borderRadius: 4,
                                hoverBackgroundColor: '#059669',
                            },
                            {
                                label: 'En attente',
                                data: tontineData.map(t => t.pending),
                                backgroundColor: '#F59E0B',
                                borderRadius: 4,
                                hoverBackgroundColor: '#D97706',
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
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
                                    padding: 20,
                                    font: { weight: '500' }
                                }
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#1E293B' : '#fff',
                                titleColor: isDark ? '#fff' : '#1E293B',
                                bodyColor: isDark ? '#94A3B8' : '#64748B',
                                borderColor: isDark ? '#334155' : '#E2E8F0',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                callbacks: {
                                    label: function(ctx) {
                                        return ctx.dataset.label + ' : ' + formatMoney(ctx.parsed.x);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                ticks: {
                                    color: textColor,
                                    callback: function(value) {
                                        return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(value);
                                    }
                                },
                                grid: {
                                    color: gridColor,
                                    drawBorder: false
                                },
                                border: { display: false }
                            },
                            y: {
                                stacked: true,
                                ticks: {
                                    color: textColor,
                                    font: { weight: '500' }
                                },
                                grid: { display: false },
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

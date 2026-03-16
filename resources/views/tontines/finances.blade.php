<x-layouts.app :title="'Finances - ' . $tontine->name">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <a href="{{ route('tontines.show', $tontine) }}" class="p-2 -ml-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="p-2 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Tableau de bord financier</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $tontine->name }}</p>
            </div>
        </div>
    </x-slot:header>

    {{-- Export button --}}
    @if(auth()->user()->canManage($tontine))
    <div class="flex gap-2 mb-4">
        <a href="{{ route('tontines.finances.export.pdf', ['tontine' => $tontine->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-red-300 dark:border-red-600/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exporter le bilan PDF
        </a>
        <a href="{{ route('tontines.export.full-report.csv', ['tontine' => $tontine->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-emerald-300 dark:border-emerald-600/50 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition" style="background-color: transparent;">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Rapport complet CSV
        </a>
    </div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 rounded-bl-full"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 mb-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total collecte</p>
                <p class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white mt-1">{{ format_amount($totalCollected) }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500">confirmes</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-500/10 to-blue-600/5 rounded-bl-full"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 mb-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total verse</p>
                <p class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white mt-1">{{ format_amount($totalDisbursed) }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500">decaisses</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-500/10 to-amber-600/5 rounded-bl-full"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30 mb-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">En attente</p>
                <p class="text-lg sm:text-xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ format_amount($pendingAmount) }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500">non confirmes</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-500/10 to-red-600/5 rounded-bl-full"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg shadow-red-500/30 mb-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Penalites</p>
                <p class="text-lg sm:text-xl font-bold text-red-600 dark:text-red-400 mt-1">{{ format_amount($totalPenalties) }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500">de penalites</p>
            </div>
        </div>
    </div>

    {{-- Graphique mensuel --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-semibold text-slate-800 dark:text-white">Collecte mensuelle</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Montants confirmes sur 6 mois</p>
            </div>
        </div>
        <div class="relative" style="height: 280px;">
            <canvas id="financeChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Suivi par tour --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden" x-data="{ showAll: false }">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-base font-semibold text-slate-800 dark:text-white">Suivi par tour</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Bénéficiaire</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Collecte</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">%</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($tourStats as $index => $tour)
                        <tr x-show="showAll || {{ $index }} < 10" class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3 font-bold text-slate-700 dark:text-slate-300">{{ $tour->tour_number }}</td>
                            <td class="px-4 py-3 text-slate-800 dark:text-white font-medium">{{ $tour->beneficiary_name }}</td>
                            <td class="px-4 py-3 text-right text-slate-600 dark:text-slate-300">
                                {{ format_amount($tour->confirmed_amount, false) }}
                                @if($tour->pending_amount > 0)
                                <span class="text-xs text-amber-500">(+{{ format_amount($tour->pending_amount, false) }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs font-semibold {{ $tour->collection_percent >= 100 ? 'text-emerald-600 dark:text-emerald-400' : ($tour->collection_percent >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                    {{ $tour->collection_percent }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-badge :color="$tour->status->color()" size="sm">
                                    {{ $tour->disbursed ? 'Verse' : $tour->status->label() }}
                                </x-badge>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(count($tourStats) > 10)
            <div class="text-center py-3 border-t border-slate-100 dark:border-slate-700/50">
                <button @click="showAll = !showAll" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    <span x-show="!showAll">Voir les {{ count($tourStats) - 10 }} autres lignes</span>
                    <span x-show="showAll" x-cloak>Reduire</span>
                </button>
            </div>
            @endif
        </div>

        {{-- Suivi par membre --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden" x-data="{ showAll: false }">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-base font-semibold text-slate-800 dark:text-white">Suivi par membre</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Membre</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Cotise</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Recu</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Retards</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Solde</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($memberStats as $index => $member)
                        <tr x-show="showAll || {{ $index }} < 10" class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded bg-slate-200 dark:bg-slate-600 flex items-center justify-center text-[10px] font-bold text-slate-500 dark:text-slate-400">{{ $member->position }}</span>
                                    <span class="text-slate-800 dark:text-white font-medium">{{ $member->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right text-slate-600 dark:text-slate-300">{{ format_amount($member->contributed, false) }}</td>
                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-medium">{{ format_amount($member->received, false) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($member->late_count > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-500/20 text-orange-700 dark:text-orange-400">
                                    {{ $member->late_count }}
                                </span>
                                @else
                                <span class="text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold {{ $member->net >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $member->net >= 0 ? '+' : '' }}{{ format_amount($member->net, false) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(count($memberStats) > 10)
            <div class="text-center py-3 border-t border-slate-100 dark:border-slate-700/50">
                <button @click="showAll = !showAll" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    <span x-show="!showAll">Voir les {{ count($memberStats) - 10 }} autres lignes</span>
                    <span x-show="showAll" x-cloak>Reduire</span>
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Cohérence financière (admin/trésorier uniquement) --}}
    @if(auth()->user()->canManage($tontine))
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-lg
                    @if($healthScore >= 90) bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-emerald-500/30
                    @elseif($healthScore >= 70) bg-gradient-to-br from-amber-500 to-amber-600 shadow-amber-500/30
                    @else bg-gradient-to-br from-red-500 to-red-600 shadow-red-500/30
                    @endif">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white">Cohérence financière</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Analyse des tours terminés ({{ $totalCompletedTours }} tours)</p>
                </div>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold
                    @if($healthScore >= 90) bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400
                    @elseif($healthScore >= 70) bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400
                    @else bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400
                    @endif">
                    @if($healthScore >= 90)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                    @elseif($healthScore >= 70)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                    {{ $healthScore }}% de santé
                </span>
            </div>
        </div>

        {{-- Résumé financier --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="text-center">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total attendu</p>
                <p class="text-lg font-bold text-slate-800 dark:text-white">{{ format_amount($totalExpectedCompleted) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total collecté</p>
                <p class="text-lg font-bold text-slate-800 dark:text-white">{{ format_amount($totalCollectedCompleted) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Pénalités collectées</p>
                <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ format_amount($totalPenalties) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Solde</p>
                <p class="text-lg font-bold {{ $financialBalance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $financialBalance >= 0 ? '+' : '' }}{{ format_amount($financialBalance) }}
                </p>
            </div>
        </div>

        {{-- Tours signalés --}}
        @if($flaggedTours->count() > 0)
        <div class="px-6 py-3 bg-amber-50 dark:bg-amber-500/10 border-b border-slate-200 dark:border-slate-700">
            <p class="text-sm font-medium text-amber-700 dark:text-amber-400">
                <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ $flaggedTours->count() }} tour(s) avec anomalie(s) détectée(s)
            </p>
        </div>
        <div class="overflow-x-auto" x-data="{ showAllFlagged: false }">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Tour</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Bénéficiaire</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Attendu</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Collecté</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Écart</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">Anomalie(s)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($flaggedTours as $index => $flagged)
                    <tr x-show="showAllFlagged || {{ $index }} < 10" class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3 font-bold text-slate-700 dark:text-slate-300">#{{ $flagged->tour_number }}</td>
                        <td class="px-4 py-3 text-slate-800 dark:text-white font-medium">{{ $flagged->beneficiary_name }}</td>
                        <td class="px-4 py-3 text-right text-slate-600 dark:text-slate-300">{{ format_amount($flagged->expected_amount, false) }}</td>
                        <td class="px-4 py-3 text-right text-slate-600 dark:text-slate-300">{{ format_amount($flagged->confirmed_amount, false) }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $flagged->difference >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $flagged->difference >= 0 ? '+' : '' }}{{ format_amount($flagged->difference, false) }}
                        </td>
                        <td class="px-4 py-3">
                            @foreach($flagged->issues as $issue)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mr-1 mb-0.5
                                {{ $issue === 'Non décaissé' ? 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400' : 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400' }}">
                                {{ $issue }}
                            </span>
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($flaggedTours->count() > 10)
            <div class="text-center py-3 border-t border-slate-100 dark:border-slate-700/50">
                <button @click="showAllFlagged = !showAllFlagged" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    <span x-show="!showAllFlagged">Voir les {{ $flaggedTours->count() - 10 }} autres anomalies</span>
                    <span x-show="showAllFlagged" x-cloak>Réduire</span>
                </button>
            </div>
            @endif
        </div>
        @else
        <div class="px-6 py-8 text-center">
            <svg class="w-12 h-12 mx-auto text-emerald-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucune anomalie détectée</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Tous les tours terminés sont cohérents</p>
        </div>
        @endif
    </div>
    @endif

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

            const ctx = document.getElementById('financeChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Collecte confirmee',
                            data: @json($chartAmounts),
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
                            maxBarThickness: 40,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
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
                                        return new Intl.NumberFormat('fr-FR').format(ctx.parsed.y) + ' FCFA';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { weight: '500' } },
                                grid: { display: false },
                                border: { display: false }
                            },
                            y: {
                                ticks: {
                                    color: textColor,
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

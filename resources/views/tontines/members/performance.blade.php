<x-layouts.app :title="'Performance - ' . $member->user->name">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <a href="{{ route('tontines.members.index', $tontine) }}" class="p-2 -ml-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <span class="font-semibold">Performance</span>
                <span class="text-slate-400 mx-2">/</span>
                <span class="text-slate-500 dark:text-slate-400">{{ $member->user->name }}</span>
                <span class="text-slate-400 mx-2">/</span>
                <span class="text-slate-500 dark:text-slate-400">{{ $tontine->name }}</span>
            </div>
        </div>
    </x-slot:header>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Punctuality Rate --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg"
                     style="background-color: {{ $punctualityRate >= 80 ? '#10b981' : ($punctualityRate >= 50 ? '#f59e0b' : '#ef4444') }};">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $punctualityRate }}%</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Taux de ponctualite</p>
                </div>
            </div>
        </div>

        {{-- Total Contributed --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background-color: #6366f1;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ format_amount($totalAmount) }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total cotise</p>
                </div>
            </div>
        </div>

        {{-- Late Count --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background-color: {{ $totalLate > 0 ? '#f97316' : '#10b981' }};">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $totalLate }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Retards</p>
                </div>
            </div>
        </div>

        {{-- Consecutive Streak --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background-color: #0ea5e9;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $streak }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Serie ponctuelle</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">Total contributions</span>
                <span class="font-semibold text-slate-800 dark:text-white">{{ $totalContributions }}</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">A l'heure</span>
                <span class="font-semibold" style="color: #10b981;">{{ $onTimeCount }}</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">Delai moyen</span>
                <span class="font-semibold text-slate-800 dark:text-white">
                    @if($averageDelay > 0)
                        +{{ $averageDelay }} jour(s)
                    @elseif($averageDelay < 0)
                        {{ $averageDelay }} jour(s)
                    @else
                        0 jour
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- Contribution History Table --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full" style="background-color: #6366f1;"></div>
                <h3 class="font-semibold text-slate-800 dark:text-white">Historique des contributions</h3>
            </div>
        </div>

        @if($history->isEmpty())
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">Aucune contribution</h3>
                <p class="text-slate-500 dark:text-slate-400">Ce membre n'a pas encore de contributions enregistrees.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-700/50">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tour</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Echeance</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Montant</th>
                            <th class="text-center px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Statut</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Delai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($history as $row)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">
                                {{ $row->date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">
                                @if($row->tour_number)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold text-white" style="background-color: #6366f1;">
                                        {{ $row->tour_number }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                {{ $row->tour_due_date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-white text-right">
                                {{ format_amount($row->amount) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusConfig = match($row->status) {
                                        \App\Enums\ContributionStatus::CONFIRMED => ['label' => 'Confirme', 'bg' => '#dcfce7', 'text' => '#166534', 'dark_bg' => 'rgba(16,185,129,0.2)', 'dark_text' => '#6ee7b7'],
                                        \App\Enums\ContributionStatus::DECLARED => ['label' => 'Declare', 'bg' => '#fef9c3', 'text' => '#854d0e', 'dark_bg' => 'rgba(234,179,8,0.2)', 'dark_text' => '#fde047'],
                                        \App\Enums\ContributionStatus::LATE => ['label' => 'En retard', 'bg' => '#ffedd5', 'text' => '#9a3412', 'dark_bg' => 'rgba(249,115,22,0.2)', 'dark_text' => '#fdba74'],
                                        \App\Enums\ContributionStatus::REJECTED => ['label' => 'Rejete', 'bg' => '#fee2e2', 'text' => '#991b1b', 'dark_bg' => 'rgba(239,68,68,0.2)', 'dark_text' => '#fca5a5'],
                                        \App\Enums\ContributionStatus::PENDING => ['label' => 'En attente', 'bg' => '#f1f5f9', 'text' => '#475569', 'dark_bg' => 'rgba(100,116,139,0.2)', 'dark_text' => '#cbd5e1'],
                                        default => ['label' => '-', 'bg' => '#f1f5f9', 'text' => '#475569', 'dark_bg' => 'rgba(100,116,139,0.2)', 'dark_text' => '#cbd5e1'],
                                    };
                                @endphp
                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full"
                                      style="background-color: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['text'] }};">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                @if($row->delay !== null)
                                    @if($row->delay > 0)
                                        <span style="color: #f97316;">+{{ $row->delay }}j</span>
                                    @elseif($row->delay < 0)
                                        <span style="color: #10b981;">{{ $row->delay }}j</span>
                                    @else
                                        <span class="text-slate-500 dark:text-slate-400">0j</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Back Button --}}
    <div class="mt-6">
        <a href="{{ route('tontines.members.index', $tontine) }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Retour aux membres
        </a>
    </div>
</x-layouts.app>

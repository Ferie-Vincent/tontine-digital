<x-layouts.app :title="'Tours - ' . $tontine->name">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <a href="{{ route('tontines.show', $tontine) }}" class="p-2 -ml-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <span class="font-semibold">Tours</span>
                <span class="text-slate-400 mx-2">/</span>
                <span class="text-slate-500 dark:text-slate-400">{{ $tontine->name }}</span>
            </div>
        </div>
    </x-slot:header>

    {{-- Stats rapides --}}
    @php
        $totalTours = $tours->total();
        $completedTours = $tontine->tours()->where('status', 'completed')->count();
        $ongoingTour = $tontine->tours()->where('status', 'ongoing')->first();
        $totalCollected = $tontine->tours()->where('status', 'completed')->sum('collected_amount');
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total tours</p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $totalTours }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Complétés</p>
                    <p class="text-2xl font-bold text-emerald-500 mt-1">{{ $completedTours }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">En cours</p>
                    <p class="text-2xl font-bold text-amber-500 mt-1">{{ $ongoingTour ? 'Tour #'.$ongoingTour->tour_number : 'Aucun' }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total collecté</p>
                    <p class="text-xl font-bold text-slate-800 dark:text-white mt-1">{{ format_amount($totalCollected) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-violet-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Liste des tours --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Calendrier des tours</h3>
                <a href="{{ route('tontines.contributions.matrix', $tontine) }}" class="text-sm primary-text hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Voir la matrice
                </a>
            </div>

            @forelse($tours as $tour)
            @php
                $progress = $tour->expected_amount > 0 ? round(($tour->collected_amount / $tour->expected_amount) * 100) : 0;
                $statusColors = [
                    'upcoming' => ['bg' => 'bg-slate-100 dark:bg-slate-700', 'border' => 'border-slate-300 dark:border-slate-600', 'icon' => 'text-slate-400'],
                    'ongoing' => ['bg' => 'bg-amber-50 dark:bg-amber-500/10', 'border' => 'border-amber-200 dark:border-amber-500/30', 'icon' => 'text-amber-500'],
                    'completed' => ['bg' => 'bg-emerald-50 dark:bg-emerald-500/10', 'border' => 'border-emerald-200 dark:border-emerald-500/30', 'icon' => 'text-emerald-500'],
                    'failed' => ['bg' => 'bg-red-50 dark:bg-red-500/10', 'border' => 'border-red-200 dark:border-red-500/30', 'icon' => 'text-red-500'],
                ];
                $colors = $statusColors[$tour->status->value] ?? $statusColors['upcoming'];
            @endphp
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-lg transition-shadow">
                <div class="flex items-stretch">
                    {{-- Numéro du tour --}}
                    <div class="w-20 {{ $colors['bg'] }} flex flex-col items-center justify-center border-r {{ $colors['border'] }}">
                        <span class="text-xs text-slate-500 dark:text-slate-400 uppercase">Tour</span>
                        <span class="text-3xl font-bold {{ $colors['icon'] }}">{{ $tour->tour_number }}</span>
                    </div>

                    {{-- Contenu principal --}}
                    <div class="flex-1 p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $tour->beneficiary->avatar_url }}" alt="{{ $tour->beneficiary->name }}" class="w-10 h-10 rounded-full object-cover shadow-lg" />
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-white">{{ $tour->beneficiary->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $tour->due_date->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <x-badge :color="$tour->status->color()" size="sm">{{ $tour->status->label() }}</x-badge>
                        </div>

                        {{-- Barre de progression --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-slate-500 dark:text-slate-400">Collecté</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ format_amount($tour->collected_amount, false) }} / {{ format_amount($tour->expected_amount) }}</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all {{ $tour->status->value === 'completed' ? 'bg-emerald-500' : 'bg-gradient-to-r from-blue-500 to-violet-500' }}" style="width: {{ min(100, $progress) }}%"></div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold {{ $progress >= 100 ? 'text-emerald-500' : 'text-slate-600 dark:text-slate-400' }}">{{ $progress }}%</span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tontines.tours.show', [$tontine, $tour]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Détails
                                </a>
                                @if($userMember && $userMember->canManage())
                                    @if($tour->status->value === 'upcoming')
                                    <div x-data="{ showStartConfirm: false }">
                                        <button type="button" @click="showStartConfirm = true" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500 text-white hover:bg-emerald-600 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Démarrer
                                        </button>
                                        <div x-show="showStartConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                            <div class="fixed inset-0 bg-black/50" @click="showStartConfirm = false"></div>
                                            <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95">
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Démarrer ce tour ?</h3>
                                                </div>
                                                <p class="text-slate-600 dark:text-slate-400 mb-6">Les membres seront notifiés et devront commencer à contribuer.</p>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="showStartConfirm = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                                        Annuler
                                                    </button>
                                                    <form method="POST" action="{{ route('tontines.tours.start', [$tontine, $tour]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">
                                                            Démarrer
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @elseif($tour->status->value === 'ongoing')
                                    <div x-data="{ showCompleteConfirm: false }">
                                        <button type="button" @click="showCompleteConfirm = true" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-500 text-white hover:bg-amber-600 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Clôturer
                                        </button>
                                        <div x-show="showCompleteConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                            <div class="fixed inset-0 bg-black/50" @click="showCompleteConfirm = false"></div>
                                            <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95">
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Clôturer la collecte ?</h3>
                                                </div>
                                                <p class="text-slate-600 dark:text-slate-400 mb-6">Le montant collecté sera figé et le versement pourra être effectué.</p>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="showCompleteConfirm = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                                        Annuler
                                                    </button>
                                                    <form method="POST" action="{{ route('tontines.tours.complete', [$tontine, $tour]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition">
                                                            Clôturer
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @elseif($tour->status->value === 'failed')
                                    <div x-data="{ showRetry: false }">
                                        <button @click="showRetry = true" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-900/20 dark:border-amber-800 dark:hover:bg-amber-900/40 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Relancer
                                        </button>
                                        <div x-show="showRetry" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                            <div class="fixed inset-0 bg-black/50" @click="showRetry = false"></div>
                                            <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95">
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Relancer le tour #{{ $tour->tour_number }}</h3>
                                                </div>
                                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Les contributions confirmées seront conservées. Les contributions en retard ou rejetées seront réinitialisées.</p>
                                                <form method="POST" action="{{ route('tontines.tours.retry', [$tontine, $tour]) }}">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nouvelle date d'échéance</label>
                                                        <input type="date" name="new_due_date" min="{{ now()->addDay()->format('Y-m-d') }}" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                                                    </div>
                                                    <div class="flex justify-end gap-3">
                                                        <button type="button" @click="showRetry = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                                            Annuler
                                                        </button>
                                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition">
                                                            Relancer
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">Aucun tour planifié</h3>
                <p class="text-slate-500 dark:text-slate-400">Les tours seront générés automatiquement ou peuvent être créés manuellement.</p>
            </div>
            @endforelse

            @if($tours->hasPages())
            <div class="mt-4">{{ $tours->links() }}</div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Créer un tour (admin/trésorier) --}}
            @if($userMember && $userMember->canManage())
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-500 to-violet-500">
                    <h3 class="font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Planifier un tour
                    </h3>
                </div>
                <form method="POST" action="{{ route('tontines.tours.store', $tontine) }}" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bénéficiaire</label>
                        <select name="beneficiary_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="">Choisir un membre...</option>
                            @foreach($tontine->activeMembers()->with('user')->orderBy('position')->get() as $member)
                            @php
                                $hasReceivedTour = $tontine->tours()->where('beneficiary_id', $member->user_id)->exists();
                            @endphp
                            <option value="{{ $member->user_id }}" {{ $hasReceivedTour ? 'disabled' : '' }}>
                                {{ $member->position }}. {{ $member->user->name }} {{ $hasReceivedTour ? '(déjà bénéficiaire)' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Date d'échéance</label>
                        <input type="date" name="due_date" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <button type="submit" class="w-full btn-primary py-2.5 rounded-lg text-sm font-medium">
                        Créer le tour
                    </button>
                </form>
            </div>
            @endif

            {{-- Tour en cours --}}
            @if($ongoingTour)
            <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl p-5 text-white">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-white/80">Tour en cours</span>
                </div>
                <p class="text-2xl font-bold mb-1">Tour #{{ $ongoingTour->tour_number }}</p>
                <p class="text-white/80 text-sm mb-3">{{ $ongoingTour->beneficiary->name }}</p>
                <div class="bg-white/20 rounded-full h-2 mb-2">
                    <div class="bg-white h-2 rounded-full" style="width: {{ $ongoingTour->collection_progress }}%"></div>
                </div>
                <p class="text-xs text-white/70">{{ $ongoingTour->collection_progress }}% collecté</p>
                <a href="{{ route('tontines.tours.show', [$tontine, $ongoingTour]) }}" class="mt-4 block w-full text-center bg-white/20 hover:bg-white/30 rounded-lg py-2 text-sm font-medium transition">
                    Voir les détails
                </a>
            </div>
            @endif

            {{-- Prochain tour --}}
            @php
                $nextTour = $tontine->tours()->where('status', 'upcoming')->orderBy('tour_number')->first();
            @endphp
            @if($nextTour && !$ongoingTour)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Prochain tour</span>
                </div>
                <p class="text-xl font-bold text-slate-800 dark:text-white mb-1">Tour #{{ $nextTour->tour_number }}</p>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-2">{{ $nextTour->beneficiary->name }}</p>
                <p class="text-xs text-slate-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Prévu le {{ $nextTour->due_date->format('d/m/Y') }}
                </p>
            </div>
            @endif

            {{-- Calendrier des échéances --}}
            @php
                $allTours = $tontine->tours()->with('beneficiary')->orderBy('tour_number')->get();
            @endphp
            @if($allTours->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden" x-data="{ showAll: false }">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h4 class="font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Calendrier des échéances
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-700/50">
                                <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">#</th>
                                <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Bénéficiaire</th>
                                <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Échéance</th>
                                <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            @foreach($allTours as $index => $t)
                            @php
                                $isOverdue = $t->status->value === 'upcoming' && $t->due_date->isPast();
                                $disbursementLabel = null;
                                if ($t->status->value === 'completed') {
                                    if ($t->beneficiary_confirmed_at) {
                                        $disbursementLabel = 'Confirme';
                                    } elseif ($t->disbursed_at) {
                                        $disbursementLabel = 'Verse';
                                    }
                                }
                            @endphp
                            <tr x-show="showAll || {{ $index }} < 10" class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition {{ $t->status->value === 'ongoing' ? 'bg-amber-50/50 dark:bg-amber-500/5' : '' }}">
                                <td class="px-4 py-2.5">
                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $t->tour_number }}</span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-slate-800 dark:text-white font-medium truncate block max-w-[120px]">{{ $t->beneficiary->name }}</span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="{{ $isOverdue ? 'text-red-500 font-medium' : 'text-slate-600 dark:text-slate-400' }}">
                                        {{ $t->due_date->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    @if($disbursementLabel)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $disbursementLabel === 'Confirme' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' }}">
                                            {{ $disbursementLabel }}
                                        </span>
                                    @else
                                        <x-badge :color="$t->status->color()" size="sm">{{ $t->status->label() }}</x-badge>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($allTours->count() > 10)
                <div class="text-center py-3 border-t border-slate-100 dark:border-slate-700/50">
                    <button @click="showAll = !showAll" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        <span x-show="!showAll">Voir les {{ $allTours->count() - 10 }} autres lignes</span>
                        <span x-show="showAll" x-cloak>Reduire</span>
                    </button>
                </div>
                @endif
            </div>
            @endif

            {{-- Legende --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h4 class="font-medium text-slate-800 dark:text-white mb-3">Legende</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                        <span class="text-slate-600 dark:text-slate-400">A venir</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">En cours</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">Complete</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">Echoue</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">Verse</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

<x-layouts.app title="Mes Tontines">
    <x-slot:header>
        Mes Tontines
    </x-slot:header>

    {{-- Stats Cards --}}
    @php
        $totalTontines = $tontines->total();
        $activeTontines = $tontines->where('status', \App\Enums\TontineStatus::ACTIVE)->count();
        $pendingTontines = $tontines->where('status', \App\Enums\TontineStatus::PENDING)->count();
        $totalContributed = $tontines->sum(function($t) {
            return $t->contribution_amount * $t->active_members_count;
        });
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total tontines</p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $totalTontines }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Actives</p>
                    <p class="text-2xl font-bold text-emerald-500 mt-1">{{ $activeTontines }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">En attente</p>
                    <p class="text-2xl font-bold text-amber-500 mt-1">{{ $pendingTontines }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Contributions</p>
                    <p class="text-xl font-bold text-slate-800 dark:text-white mt-1">{{ format_amount($totalContributed) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-violet-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content: Tontine Cards --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Search & Filter --}}
            <form method="GET" action="{{ route('tontines.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une tontine..." aria-label="Rechercher une tontine" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 min-h-[44px]">
                </div>
                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-800 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="all">Tous les statuts</option>
                    @foreach(\App\Enums\TontineStatus::cases() as $s)
                    <option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary px-4 py-2.5 rounded-lg text-sm font-medium sm:hidden">Rechercher</button>
            </form>

            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $tontines->total() }} tontine(s) au total</p>
                @if(request('search') || (request('status') && request('status') !== 'all'))
                <a href="{{ route('tontines.index') }}" class="text-sm text-blue-500 hover:underline">Effacer les filtres</a>
                @endif
            </div>

            @forelse($tontines as $tontine)
            @php
                $progress = $tontine->max_members > 0 ? round(($tontine->active_members_count / $tontine->max_members) * 100) : 0;
                $statusColors = [
                    'pending' => ['bg' => 'bg-amber-50 dark:bg-amber-500/10', 'border' => 'border-amber-200 dark:border-amber-500/30', 'icon' => 'text-amber-500'],
                    'active' => ['bg' => 'bg-emerald-50 dark:bg-emerald-500/10', 'border' => 'border-emerald-200 dark:border-emerald-500/30', 'icon' => 'text-emerald-500'],
                    'completed' => ['bg' => 'bg-slate-100 dark:bg-slate-700', 'border' => 'border-slate-300 dark:border-slate-600', 'icon' => 'text-slate-400'],
                    'cancelled' => ['bg' => 'bg-red-50 dark:bg-red-500/10', 'border' => 'border-red-200 dark:border-red-500/30', 'icon' => 'text-red-500'],
                ];
                $colors = $statusColors[$tontine->status->value] ?? $statusColors['pending'];
            @endphp
            <a href="{{ route('tontines.show', $tontine) }}" class="block">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="flex items-stretch">
                        {{-- Left accent bar --}}
                        <div class="w-1.5 bg-gradient-to-b from-blue-500 to-violet-500"></div>

                        {{-- Main content --}}
                        <div class="flex-1 p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-violet-500 flex items-center justify-center text-white text-lg font-bold shadow-lg">
                                        {{ strtoupper(substr($tontine->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-slate-800 dark:text-white text-lg">{{ $tontine->name }}</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $tontine->frequency->label() }}
                                            </span>
                                            <span class="text-slate-300 dark:text-slate-600">|</span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $tontine->active_members_count }}/{{ $tontine->max_members }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <x-badge :color="$tontine->status->color()" size="sm" dot>{{ $tontine->status->label() }}</x-badge>
                            </div>

                            {{-- Description --}}
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">{{ $tontine->description ?? 'Aucune description disponible pour cette tontine.' }}</p>

                            {{-- Amount and Progress --}}
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-slate-500 dark:text-slate-400">Remplissage</span>
                                <span class="text-sm font-semibold primary-text">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 mb-4">
                                <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-violet-500 transition-all" style="width: {{ min(100, $progress) }}%"></div>
                            </div>

                            {{-- Footer with amount --}}
                            <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-700">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400">Cotisation</p>
                                        <p class="font-bold text-slate-800 dark:text-white">{{ $tontine->formatted_amount }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 text-sm primary-text font-medium">
                                    Voir les details
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-12 text-center">
                <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h4 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Aucune tontine</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Vous n'avez pas encore de tontine. {{ $canCreateTontine ? 'Creez-en une ou rejoignez' : 'Rejoignez' }} une existante.</p>
                <div class="flex items-center justify-center gap-3">
                    @if($canCreateTontine)
                    <button @click="$dispatch('open-modal-create-tontine')" class="btn-primary px-5 py-2.5 rounded-lg text-sm font-medium inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Creer une tontine
                    </button>
                    @endif
                    <button @click="$dispatch('open-modal-join-tontine')" class="px-5 py-2.5 rounded-lg text-sm font-medium border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Rejoindre
                    </button>
                </div>
            </div>
            @endforelse

            @if($tontines->hasPages())
            <div class="mt-6">
                {{ $tontines->links() }}
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Create Tontine Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-500 to-violet-500">
                    <h3 class="font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Actions rapides
                    </h3>
                </div>
                <div class="p-5 space-y-3">
                    @if($canCreateTontine)
                    <button @click="$dispatch('open-modal-create-tontine')" class="w-full btn-primary py-3 rounded-lg text-sm font-medium inline-flex items-center justify-center gap-2 hover:shadow-lg transition-shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Nouvelle tontine
                    </button>
                    @endif
                    <button @click="$dispatch('open-modal-join-tontine')" class="w-full py-3 rounded-lg text-sm font-medium border-2 border-dashed border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:border-blue-500 hover:text-blue-500 dark:hover:border-blue-500 dark:hover:text-blue-500 transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Rejoindre une tontine
                    </button>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h4 class="font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Résumé
                </h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Tontines actives</span>
                        <span class="font-bold text-emerald-500">{{ $activeTontines }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">En attente</span>
                        <span class="font-bold text-amber-500">{{ $pendingTontines }}</span>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Total membres</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ $tontines->sum('active_members_count') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tips Card --}}
            <div class="bg-gradient-to-br from-blue-500 to-violet-500 rounded-xl p-5 text-white">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="font-medium">Conseil</span>
                </div>
                <p class="text-sm text-white/90 leading-relaxed">
                    Pour inviter des membres a rejoindre votre tontine, partagez le code d'invitation disponible sur la page de details de chaque tontine.
                </p>
            </div>

            {{-- Legend --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h4 class="font-medium text-slate-800 dark:text-white mb-3">Legende des statuts</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">Active - En cours</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">En attente - Pas encore demarree</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                        <span class="text-slate-600 dark:text-slate-400">Terminee</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">Annulee</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create --}}
    <x-modal id="create-tontine" maxWidth="2xl" title="Creer une tontine">
        <form method="POST" action="{{ route('tontines.store') }}" class="space-y-4">
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
            <div class="flex justify-end gap-3">
                <x-button type="button" variant="ghost" @click="$dispatch('close-modal-create-tontine')">Annuler</x-button>
                <x-button type="submit" variant="primary">Creer la tontine</x-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Join --}}
    <x-modal id="join-tontine" maxWidth="sm" title="Rejoindre une tontine">
        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-violet-500 flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-1">Entrez le code d'invitation</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Demandez le code a l'administrateur de la tontine</p>
        </div>
        <form method="POST" action="{{ route('tontines.join.submit') }}" class="space-y-4">
            @csrf
            <x-input name="code" label="Code d'invitation" placeholder="XXXXXXXX" maxlength="8" class="text-center text-2xl font-mono tracking-widest uppercase" :error="$errors->joinTontine->first('code')" required />
            <x-button type="submit" variant="primary" class="w-full">Rejoindre</x-button>
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
</x-layouts.app>

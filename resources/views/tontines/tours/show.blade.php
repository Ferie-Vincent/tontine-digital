<x-layouts.app :title="'Tour #' . $tour->tour_number . ' - ' . $tontine->name">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <a href="{{ route('tontines.tours.index', $tontine) }}" class="p-2 -ml-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <span class="font-semibold">Tour #{{ $tour->tour_number }}</span>
                <span class="text-slate-400 mx-2">/</span>
                <span class="text-slate-500 dark:text-slate-400">{{ $tontine->name }}</span>
            </div>
        </div>
    </x-slot:header>

    <x-breadcrumb :items="[
        ['label' => 'Accueil', 'url' => route('dashboard')],
        ['label' => $tontine->name, 'url' => route('tontines.show', $tontine)],
        ['label' => 'Tours', 'url' => route('tontines.tours.index', $tontine)],
        ['label' => 'Tour #' . $tour->tour_number],
    ]" />

    @php
        $progress = $tour->expected_amount > 0 ? round(($tour->collected_amount / $tour->expected_amount) * 100) : 0;
    @endphp

    {{-- Hero Card du bénéficiaire --}}
    <div class="bg-gradient-to-r from-blue-600 via-violet-600 to-purple-600 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ $tour->beneficiary->avatar_url }}" alt="{{ $tour->beneficiary->name }}" class="w-16 h-16 rounded-2xl object-cover shadow-lg" />
                <div>
                    <p class="text-white/70 text-sm">Bénéficiaire du tour</p>
                    <h2 class="text-2xl font-bold">{{ $tour->beneficiary->name }}</h2>
                    <p class="text-white/70 text-sm flex items-center gap-1 mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Échéance: {{ $tour->due_date->format('d M Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <x-badge :color="$tour->status->color()" size="md">{{ $tour->status->label() }}</x-badge>
                <span class="text-3xl font-bold">#{{ $tour->tour_number }}</span>
            </div>
        </div>

        {{-- Barre de progression --}}
        <div class="relative mt-6">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-white/80">Progression de la collecte</span>
                <span class="font-bold">{{ $progress }}%</span>
            </div>
            <div class="w-full bg-white/20 rounded-full h-3">
                <div class="bg-white h-3 rounded-full transition-all shadow-lg" style="width: {{ min(100, $progress) }}%"></div>
            </div>
            <div class="flex items-center justify-between text-sm mt-2">
                <span class="text-white/70">{{ format_amount($tour->collected_amount) }} collectés</span>
                <span class="text-white/70">sur {{ format_amount($tour->expected_amount) }}</span>
            </div>
        </div>
    </div>

    {{-- Carte Statut du versement (visible quand toutes les contributions sont confirmées) --}}
    @if($tour->collection_date || $tour->status->value === 'completed')
    <div class="mb-6">
        @if(!$tour->disbursed_at)
        {{-- Etat 1 : En attente de versement --}}
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-5 text-white relative overflow-hidden">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-lg">En attente du versement</p>
                        <p class="text-white/80 text-sm">Les fonds de {{ format_amount($tour->collected_amount) }} doivent etre verses a {{ $tour->beneficiary->name }}</p>
                    </div>
                </div>
                @if($userMember && $userMember->canManage())
                <button type="button" @click="$dispatch('open-modal-disburse')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-amber-600 font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Marquer comme verse
                </button>
                @endif
            </div>
        </div>

        @elseif($tour->disbursed_at && !$tour->beneficiary_confirmed_at)
        {{-- Etat 2 : Verse, en attente de confirmation --}}
        <div class="bg-gradient-to-r from-blue-500 to-violet-500 rounded-xl p-5 text-white relative overflow-hidden">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-lg">Fonds verses</p>
                        <p class="text-white/80 text-sm">
                            {{ format_amount($tour->collected_amount) }} verses le {{ $tour->disbursed_at->format('d/m/Y a H:i') }}
                            via {{ ucfirst(str_replace('_', ' ', $tour->disbursement_method)) }}
                        </p>
                        <p class="text-white/60 text-xs mt-1">En attente de confirmation du bénéficiaire</p>
                    </div>
                </div>
                @if($tour->beneficiary_id === auth()->id())
                <div x-data="{ showReceiptConfirm: false }">
                    <button type="button" @click="showReceiptConfirm = true" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-blue-600 font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Confirmer la reception
                    </button>
                    <div x-show="showReceiptConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="fixed inset-0 bg-black/50" @click="showReceiptConfirm = false"></div>
                        <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Confirmer la réception ?</h3>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 mb-6">Confirmez-vous avoir reçu les fonds ? Cette action est définitive et clôturera le tour.</p>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="showReceiptConfirm = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">Annuler</button>
                                <form method="POST" action="{{ route('tontines.tours.confirmReceipt', [$tontine, $tour]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">Confirmer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @else
        {{-- Etat 3 : Verse et confirme --}}
        <div class="bg-gradient-to-r from-emerald-500 to-green-500 rounded-xl p-5 text-white relative overflow-hidden">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-lg">Versement confirme</p>
                    <p class="text-white/80 text-sm">
                        {{ format_amount($tour->collected_amount) }} verses a {{ $tour->beneficiary->name }}
                        le {{ $tour->disbursed_at->format('d/m/Y') }}
                    </p>
                    <p class="text-white/60 text-xs mt-1">
                        Réception confirmée par le bénéficiaire le {{ $tour->beneficiary_confirmed_at->format('d/m/Y a H:i') }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Bandeau Tour échoué --}}
    @if($tour->status->value === 'failed')
    <div class="mb-6">
        <div class="bg-gradient-to-r from-red-600 to-rose-600 rounded-xl p-5 text-white relative overflow-hidden">
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-lg">Tour échoué</p>
                    <p class="text-white/80 text-sm">
                        Ce tour n'a pas atteint le seuil minimal de collecte dans les délais impartis.
                        Collecte finale : {{ format_amount($tour->collected_amount) }} sur {{ format_amount($tour->expected_amount) }} attendus ({{ $progress }}%).
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Liste des contributions --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 dark:text-white">Contributions des membres</h3>
                    <span class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $tour->contributions->where('status.value', 'confirmed')->count() }} / {{ $tour->contributions->count() }} confirmées
                    </span>
                </div>
                <div class="divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($tour->contributions->sortBy('user.name') as $contribution)
                    @php
                        $statusConfig = [
                            'pending' => ['bg' => 'bg-slate-100 dark:bg-slate-700', 'text' => 'text-slate-500', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'declared' => ['bg' => 'bg-amber-100 dark:bg-amber-500/20', 'text' => 'text-amber-600 dark:text-amber-400', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'confirmed' => ['bg' => 'bg-emerald-100 dark:bg-emerald-500/20', 'text' => 'text-emerald-600 dark:text-emerald-400', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'rejected' => ['bg' => 'bg-red-100 dark:bg-red-500/20', 'text' => 'text-red-600 dark:text-red-400', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'late' => ['bg' => 'bg-orange-100 dark:bg-orange-500/20', 'text' => 'text-orange-600 dark:text-orange-400', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                        ];
                        $config = $statusConfig[$contribution->status->value] ?? $statusConfig['pending'];
                    @endphp
                    <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full {{ $config['bg'] }} flex items-center justify-center {{ $config['text'] }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/></svg>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800 dark:text-white">{{ $contribution->user->name }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ format_amount($contribution->amount) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-badge :color="$contribution->status->color()" size="sm">{{ $contribution->status->label() }}</x-badge>

                                @if($userMember && $userMember->canManage())
                                    {{-- PENDING : Déclarer pour ce membre (seulement si tontine active et tour en cours) --}}
                                    @if($contribution->status->value === 'pending' && $tontine->status->value === 'active' && $tour->status->value === 'ongoing')
                                    <button type="button" @click="$dispatch('open-modal-declare-{{ $contribution->id }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-500/30 transition"
                                        title="Déclarer pour ce membre">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        Déclarer
                                    </button>
                                    @endif

                                    {{-- DECLARED : Voir justificatif + Confirmer + Rejeter --}}
                                    @if($contribution->status->value === 'declared')
                                    <div class="flex gap-1">
                                        @if($contribution->paymentProof)
                                        <button type="button" @click="$dispatch('open-modal-proof-{{ $contribution->id }}')" class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-500/30 flex items-center justify-center transition" title="Voir le justificatif">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        @endif
                                        <form method="POST" action="{{ route('tontines.contributions.confirm', [$tontine, $contribution]) }}">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-500/30 flex items-center justify-center transition" title="Confirmer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                        <button type="button" @click="$dispatch('open-modal-reject-{{ $contribution->id }}')" class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-500/30 flex items-center justify-center transition" title="Rejeter">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    @endif

                                    {{-- REJECTED : Re-declarer (seulement si tontine active et tour en cours) --}}
                                    @if($contribution->status->value === 'rejected' && $tontine->status->value === 'active' && $tour->status->value === 'ongoing')
                                    <button type="button" @click="$dispatch('open-modal-declare-{{ $contribution->id }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-500/30 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Re-declarer
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Infos du justificatif si déclaré --}}
                        @if($contribution->paymentProof && in_array($contribution->status->value, ['declared', 'confirmed']))
                        <div class="mt-3 ml-13 pl-10 text-xs text-slate-500 dark:text-slate-400 flex flex-wrap gap-x-4 gap-y-1">
                            @if($contribution->paymentProof->payment_method)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                {{ $contribution->paymentProof->payment_method_label }}
                            </span>
                            @endif
                            @if($contribution->paymentProof->transaction_reference)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                {{ $contribution->paymentProof->transaction_reference }}
                            </span>
                            @endif
                            @if($contribution->paymentProof->transaction_date)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $contribution->paymentProof->transaction_date->format('d/m/Y H:i') }}
                            </span>
                            @endif
                        </div>
                        @endif
                    </div>

                    {{-- Modal justificatif --}}
                    @if($contribution->paymentProof)
                    <x-modal id="proof-{{ $contribution->id }}" maxWidth="lg" title="Justificatif de paiement">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-200 dark:border-slate-700">
                                <img src="{{ $contribution->user->avatar_url }}" alt="{{ $contribution->user->name }}" class="w-12 h-12 rounded-full object-cover" />
                                <div>
                                    <p class="font-semibold text-slate-800 dark:text-white">{{ $contribution->user->name }}</p>
                                    <p class="text-sm text-slate-500">{{ format_amount($contribution->amount) }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-slate-500 dark:text-slate-400">Méthode</p>
                                    <p class="font-medium text-slate-800 dark:text-white">{{ $contribution->paymentProof->payment_method_label }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500 dark:text-slate-400">Référence</p>
                                    <p class="font-medium text-slate-800 dark:text-white font-mono">{{ $contribution->paymentProof->transaction_reference ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500 dark:text-slate-400">Numéro émetteur</p>
                                    <p class="font-medium text-slate-800 dark:text-white">{{ $contribution->paymentProof->sender_phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500 dark:text-slate-400">Date transaction</p>
                                    <p class="font-medium text-slate-800 dark:text-white">{{ $contribution->paymentProof->transaction_date ? $contribution->paymentProof->transaction_date->format('d/m/Y H:i') : '-' }}</p>
                                </div>
                            </div>

                            @if($contribution->paymentProof->screenshot_path)
                            <div>
                                <p class="text-slate-500 dark:text-slate-400 text-sm mb-2">Capture d'écran</p>
                                <img src="{{ Storage::url($contribution->paymentProof->screenshot_path) }}" alt="Justificatif" class="w-full rounded-lg border border-slate-200 dark:border-slate-700">
                            </div>
                            @endif

                            @if($contribution->requires_review)
                            <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800">
                                <p class="text-sm font-semibold text-amber-800 dark:text-amber-200 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    Vérification requise — Écart de montant supérieur à 10%
                                </p>
                            </div>
                            @endif

                            @if($contribution->notes)
                            <div>
                                <p class="text-slate-500 dark:text-slate-400 text-sm mb-1">Notes</p>
                                <p class="text-slate-800 dark:text-white text-sm whitespace-pre-line">{{ $contribution->notes }}</p>
                            </div>
                            @endif

                            @if($contribution->status->value === 'declared' && $userMember && $userMember->canManage())
                            <div class="flex gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                                <form method="POST" action="{{ route('tontines.contributions.confirm', [$tontine, $contribution]) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 rounded-lg text-sm font-medium bg-emerald-500 hover:bg-emerald-600 text-white transition">
                                        Confirmer le paiement
                                    </button>
                                </form>
                                <button type="button" @click="$dispatch('close-modal-proof-{{ $contribution->id }}'); setTimeout(() => $dispatch('open-modal-reject-{{ $contribution->id }}'), 300)" class="flex-1 py-2.5 rounded-lg text-sm font-medium bg-red-500 hover:bg-red-600 text-white transition">
                                    Rejeter
                                </button>
                            </div>
                            @endif
                        </div>
                    </x-modal>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Carte bénéficiaire --}}
            @if($tour->beneficiary_id === auth()->id())
            <div class="bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl p-5 text-white">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-lg font-bold mb-1">Vous êtes le bénéficiaire</p>
                    <p class="text-white/80 text-sm">de ce tour</p>
                    <div class="mt-4 pt-4 border-t border-white/20">
                        <p class="text-white/70 text-sm">Montant attendu</p>
                        <p class="text-2xl font-bold">{{ format_amount($tour->expected_amount) }}</p>
                    </div>

                    @if($tour->collection_date || $tour->status->value === 'completed')
                    <div class="mt-4 pt-4 border-t border-white/20">
                        @if($tour->disbursed_at && $tour->beneficiary_confirmed_at)
                            <div class="flex items-center justify-center gap-2 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium">Fonds recus</span>
                            </div>
                            <p class="text-white/70 text-xs">Verse le {{ $tour->disbursed_at->format('d/m/Y') }} via {{ ucfirst(str_replace('_', ' ', $tour->disbursement_method)) }}</p>
                        @elseif($tour->disbursed_at)
                            <p class="text-white/80 text-sm font-medium mb-2">Fonds verses le {{ $tour->disbursed_at->format('d/m/Y') }}</p>
                            <div x-data="{ showReceiptConfirm2: false }">
                                <button type="button" @click="showReceiptConfirm2 = true" class="w-full mt-2 px-4 py-2.5 bg-white text-emerald-600 font-bold rounded-xl shadow hover:shadow-lg transition text-sm">
                                    Confirmer que j'ai recu les fonds
                                </button>
                                <div x-show="showReceiptConfirm2" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                    <div class="fixed inset-0 bg-black/50" @click="showReceiptConfirm2 = false"></div>
                                    <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Confirmer la réception ?</h3>
                                        </div>
                                        <p class="text-slate-600 dark:text-slate-400 mb-6">Confirmez-vous avoir reçu les fonds ? Cette action est définitive et clôturera le tour.</p>
                                        <div class="flex justify-end gap-3">
                                            <button type="button" @click="showReceiptConfirm2 = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">Annuler</button>
                                            <form method="POST" action="{{ route('tontines.tours.confirmReceipt', [$tontine, $tour]) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">Confirmer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-white/70 text-sm">En attente du versement</span>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Ma contribution --}}
            @if($userContribution)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-semibold text-slate-800 dark:text-white">Ma contribution</h3>
                </div>
                <div class="p-5">
                    <div class="text-center mb-4">
                        <x-badge :color="$userContribution->status->color()" size="md">{{ $userContribution->status->label() }}</x-badge>
                        <p class="text-3xl font-bold text-slate-800 dark:text-white mt-3">{{ format_amount($userContribution->amount) }}</p>
                    </div>
                    @if(in_array($userContribution->status->value, ['pending', 'rejected']) && $tontine->status->value === 'active' && $tour->status->value === 'ongoing')
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                        @livewire('tontine.contribution-declare', ['contribution' => $userContribution])
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Actions admin --}}
            @if($userMember && $userMember->canManage())
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-semibold text-slate-800 dark:text-white">Actions</h3>
                </div>
                <div class="p-5 space-y-3">
                    @if($tour->status->value === 'upcoming')
                    <form method="POST" action="{{ route('tontines.tours.start', [$tontine, $tour]) }}">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium bg-emerald-500 text-white hover:bg-emerald-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Demarrer le tour
                        </button>
                    </form>
                    @elseif($tour->collection_date && !$tour->disbursed_at)
                    <button type="button" @click="$dispatch('open-modal-disburse')" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium bg-blue-500 text-white hover:bg-blue-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Marquer comme verse
                    </button>
                    @endif

                    @if($tour->status->value === 'upcoming')
                    <button type="button" @click="$dispatch('open-modal-reassign')" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition" style="background-color: #f59e0b; color: white;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Réassigner le bénéficiaire
                    </button>
                    @endif

                    <a href="{{ route('tontines.contributions.matrix', $tontine) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Voir la matrice
                    </a>
                </div>
            </div>
            @endif

            {{-- Statistiques --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h4 class="font-semibold text-slate-800 dark:text-white mb-4">Statistiques</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Confirmées</span>
                        <span class="text-sm font-semibold text-emerald-500">{{ $tour->contributions->where('status.value', 'confirmed')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">En attente de validation</span>
                        <span class="text-sm font-semibold text-amber-500">{{ $tour->contributions->where('status.value', 'declared')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Non déclarées</span>
                        <span class="text-sm font-semibold text-slate-500">{{ $tour->contributions->where('status.value', 'pending')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">En retard</span>
                        <span class="text-sm font-semibold text-orange-500">{{ $tour->contributions->where('status.value', 'late')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Rejetées</span>
                        <span class="text-sm font-semibold text-red-500">{{ $tour->contributions->where('status.value', 'rejected')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== MODALES ADMIN ==================== --}}
    @if($userMember && $userMember->canManage())
        {{-- Modales de declaration (pour contributions pending ou rejected) --}}
        @foreach($tour->contributions as $contribution)
            @if(in_array($contribution->status->value, ['pending', 'rejected']))
            <x-modal id="declare-{{ $contribution->id }}" maxWidth="lg" title="Déclarer le paiement">
                <form method="POST" action="{{ route('tontines.contributions.declare', [$tontine, $contribution]) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-200 dark:border-slate-700">
                        <img src="{{ $contribution->user->avatar_url }}" alt="{{ $contribution->user->name }}" class="w-12 h-12 rounded-full object-cover" />
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-white">{{ $contribution->user->name }}</p>
                            <p class="text-sm text-slate-500">{{ format_amount($contribution->amount) }} - Tour #{{ $tour->tour_number }}</p>
                        </div>
                    </div>

                    <x-select name="payment_method" label="Méthode de paiement" required>
                        <option value="">Choisir...</option>
                        @foreach(\App\Enums\PaymentMethod::cases() as $method)
                            <option value="{{ $method->value }}">{{ $method->label() }}</option>
                        @endforeach
                    </x-select>

                    <x-input name="transaction_reference" label="Référence de transaction" placeholder="Ex: CI240101XXXX" />
                    <x-input name="sender_phone" label="Numéro émetteur" placeholder="07 XX XX XX XX" />
                    <x-input name="transaction_date" label="Date de la transaction" type="datetime-local" />
                    <x-file-upload name="screenshot" label="Capture d'écran (optionnel)" accept="image/*" hint="Max 5 Mo" />
                    <x-textarea name="notes" label="Notes (optionnel)" placeholder="Informations complémentaires..." rows="2" />

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <x-button type="button" variant="ghost" @click="$dispatch('close-modal-declare-{{ $contribution->id }}')">Annuler</x-button>
                        <x-button type="submit" variant="primary">Déclarer le paiement</x-button>
                    </div>
                </form>
            </x-modal>
            @endif
        @endforeach

        {{-- Modales de rejet (pour contributions declared) --}}
        @foreach($tour->contributions as $contribution)
            @if($contribution->status->value === 'declared')
            <x-modal id="reject-{{ $contribution->id }}" maxWidth="md" title="Rejeter la contribution">
                <form method="POST" action="{{ route('tontines.contributions.reject', [$tontine, $contribution]) }}" class="space-y-4">
                    @csrf
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-200 dark:border-slate-700">
                        <img src="{{ $contribution->user->avatar_url }}" alt="{{ $contribution->user->name }}" class="w-12 h-12 rounded-full object-cover" />
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-white">{{ $contribution->user->name }}</p>
                            <p class="text-sm text-slate-500">{{ format_amount($contribution->amount) }}</p>
                        </div>
                    </div>

                    <x-alert type="warning">
                        Cette action rejettera la contribution déclarée. Le membre devra soumettre un nouveau justificatif.
                    </x-alert>

                    <x-textarea name="reason" label="Motif du rejet" placeholder="Précisez la raison du rejet..." rows="3" />

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <x-button type="button" variant="ghost" @click="$dispatch('close-modal-reject-{{ $contribution->id }}')">Annuler</x-button>
                        <x-button type="submit" variant="danger">Rejeter la contribution</x-button>
                    </div>
                </form>
            </x-modal>
            @endif
        @endforeach
    @endif

    {{-- Modal réassignation du bénéficiaire (admin) --}}
    @if($userMember && $userMember->canManage() && $tour->status->value === 'upcoming')
    <x-modal id="reassign" maxWidth="md" title="Réassigner le bénéficiaire">
        <form method="POST" action="{{ route('tontines.tours.reassign', [$tontine, $tour]) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="flex items-center gap-3 pb-4 border-b border-slate-200 dark:border-slate-700">
                <img src="{{ $tour->beneficiary->avatar_url }}" alt="{{ $tour->beneficiary->name }}" class="w-12 h-12 rounded-full object-cover" />
                <div>
                    <p class="font-semibold text-slate-800 dark:text-white">{{ $tour->beneficiary->name }}</p>
                    <p class="text-sm text-slate-500">Bénéficiaire actuel - Tour #{{ $tour->tour_number }}</p>
                </div>
            </div>

            <x-alert type="warning">
                Cette action changera le bénéficiaire de ce tour. Le nouveau bénéficiaire recevra la cagnotte à la place du bénéficiaire actuel.
            </x-alert>

            <x-select name="beneficiary_id" label="Nouveau bénéficiaire" required>
                <option value="">Sélectionner un membre...</option>
                @foreach($tontine->activeMembers()->with('user')->get()->sortBy('user.name') as $member)
                    @if($member->user_id !== $tour->beneficiary_id)
                    <option value="{{ $member->user_id }}">{{ $member->user->name }}</option>
                    @endif
                @endforeach
            </x-select>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                <x-button type="button" variant="ghost" @click="$dispatch('close-modal-reassign')">Annuler</x-button>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition" style="background-color: #f59e0b; color: white;">
                    Réassigner
                </button>
            </div>
        </form>
    </x-modal>
    @endif

    {{-- Modal versement (admin) --}}
    @if($userMember && $userMember->canManage() && $tour->collection_date && !$tour->disbursed_at)
    <x-modal id="disburse" maxWidth="lg" title="Verser les fonds au bénéficiaire">
        <form method="POST" action="{{ route('tontines.tours.disburse', [$tontine, $tour]) }}" class="space-y-4">
            @csrf
            <div class="flex items-center gap-3 pb-4 border-b border-slate-200 dark:border-slate-700">
                <img src="{{ $tour->beneficiary->avatar_url }}" alt="{{ $tour->beneficiary->name }}" class="w-12 h-12 rounded-full object-cover" />
                <div>
                    <p class="font-semibold text-slate-800 dark:text-white">{{ $tour->beneficiary->name }}</p>
                    <p class="text-sm text-slate-500">{{ format_amount($tour->collected_amount) }} - Tour #{{ $tour->tour_number }}</p>
                </div>
            </div>

            <x-alert type="info">
                Confirmez que les fonds ont été versés au bénéficiaire. Ce dernier devra ensuite confirmer la réception.
            </x-alert>

            <x-select name="disbursement_method" label="Méthode de versement" required>
                <option value="">Choisir...</option>
                <option value="orange_money">Orange Money</option>
                <option value="mtn_momo">MTN MoMo</option>
                <option value="wave">Wave</option>
                <option value="cash">Espèces</option>
                <option value="bank_transfer">Virement bancaire</option>
                <option value="other">Autre</option>
            </x-select>

            <x-input name="disbursement_reference" label="Référence de transaction" placeholder="Ex: CI240101XXXX" />
            <x-textarea name="disbursement_notes" label="Notes (optionnel)" placeholder="Informations complémentaires..." rows="2" />

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                <x-button type="button" variant="ghost" @click="$dispatch('close-modal-disburse')">Annuler</x-button>
                <x-button type="submit" variant="primary">Confirmer le versement</x-button>
            </div>
        </form>
    </x-modal>
    @endif
</x-layouts.app>

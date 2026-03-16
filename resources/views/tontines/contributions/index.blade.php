<x-layouts.app :title="'Contributions - ' . $tontine->name">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <a href="{{ route('tontines.show', $tontine) }}" class="p-2 -ml-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <span class="font-semibold">Gestion des contributions</span>
                <span class="text-slate-400 mx-2">/</span>
                <span class="text-slate-500 dark:text-slate-400">{{ $tontine->name }}</span>
            </div>
        </div>
    </x-slot:header>

    {{-- Flash messages --}}
    @if(session('success'))
        <x-alert type="success" dismissible class="mb-6">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert type="danger" dismissible class="mb-6">{{ session('error') }}</x-alert>
    @endif
    @if(session('warning'))
        <x-alert type="warning" dismissible class="mb-6">{{ session('warning') }}</x-alert>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">En attente</p>
                    <p class="text-2xl font-bold text-slate-600 dark:text-slate-300 mt-1">{{ $statusCounts['pending'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-slate-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Déclarées</p>
                    <p class="text-2xl font-bold text-amber-500 mt-1">{{ $statusCounts['declared'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-amber-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Confirmées</p>
                    <p class="text-2xl font-bold text-emerald-500 mt-1">{{ $statusCounts['confirmed'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Rejetées</p>
                    <p class="text-2xl font-bold text-red-500 mt-1">{{ $statusCounts['rejected'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Bannière résumé des pénalités --}}
    @php
        $totalPenalties = $contributions->sum('penalty_amount');
        $lateWithPenaltyCount = $contributions->where('penalty_amount', '>', 0)->count();
    @endphp
    @if($lateWithPenaltyCount > 0)
    <div class="rounded-xl border border-rose-200 dark:border-rose-800/50 p-4 mb-6" style="background-color: #fff1f2;">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #ffe4e6;">
                <svg class="w-5 h-5" style="color: #e11d48;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold" style="color: #9f1239;">Pénalités sur cette page</p>
                <p class="text-xs" style="color: #be123c;">
                    {{ $lateWithPenaltyCount }} contribution(s) avec pénalité pour un total de <strong>{{ format_amount($totalPenalties) }}</strong>
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Export buttons --}}
    @if($userMember && in_array($userMember->role->value, ['admin', 'treasurer']))
    <div class="flex gap-2 mb-4">
        <a href="{{ route('tontines.contributions.export.csv', array_merge(['tontine' => $tontine->id], request()->only(['status', 'tour_id']))) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exporter CSV
        </a>
        <a href="{{ route('tontines.contributions.export.pdf', array_merge(['tontine' => $tontine->id], request()->only(['status', 'tour_id']))) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-red-300 dark:border-red-600/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exporter PDF
        </a>
    </div>
    @endif

    {{-- Filtres --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex flex-wrap gap-2">
            @php
                $currentStatus = request('status', 'all');
                $statuses = [
                    'all' => 'Toutes',
                    'pending' => 'En attente',
                    'declared' => 'Déclarées',
                    'confirmed' => 'Confirmées',
                    'rejected' => 'Rejetées',
                    'late' => 'En retard',
                ];
            @endphp
            @foreach($statuses as $value => $label)
                <a href="{{ route('tontines.contributions.index', array_merge(['tontine' => $tontine->id], request()->only('tour_id'), $value !== 'all' ? ['status' => $value] : [])) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentStatus === $value ? 'bg-blue-500 text-white' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                    {{ $label }}
                    @if($value !== 'all')
                        <span class="ml-1 text-xs opacity-75">({{ $statusCounts[$value] ?? 0 }})</span>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('tontines.contributions.index', $tontine) }}" class="flex items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <select name="tour_id" onchange="this.form.submit()" class="rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-slate-800 dark:text-white">
                    <option value="">Tous les tours</option>
                    @foreach($tours as $tour)
                        <option value="{{ $tour->id }}" @selected(request('tour_id') == $tour->id)>Tour #{{ $tour->tour_number }}</option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('tontines.contributions.matrix', $tontine) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Matrice
            </a>
        </div>
    </div>

    {{-- Liste des contributions --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800 dark:text-white">Liste des contributions</h3>
            <span class="text-sm text-slate-500 dark:text-slate-400">{{ $contributions->total() }} résultat(s)</span>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
            @forelse($contributions as $contribution)
            @php
                $statusConfig = [
                    'pending' => ['bg' => 'bg-slate-100 dark:bg-slate-700', 'text' => 'text-slate-500', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'declared' => ['bg' => 'bg-amber-100 dark:bg-amber-500/20', 'text' => 'text-amber-600 dark:text-amber-400', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
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
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                {{ format_amount($contribution->amount) }}
                                <span class="text-slate-300 dark:text-slate-600 mx-1">|</span>
                                Tour #{{ $contribution->tour->tour_number }}
                            </p>
                            @if($contribution->penalty_amount > 0)
                            <p class="text-xs font-medium mt-0.5" style="color: #e11d48;">
                                <svg class="w-3 h-3 inline-block mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Pénalité : {{ format_amount($contribution->penalty_amount) }}
                            </p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge :color="$contribution->status->color()" size="sm">{{ $contribution->status->label() }}</x-badge>
                        @if($contribution->notes && str_contains($contribution->notes, '[ALERTE DOUBLON]'))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400" title="{{ $contribution->notes }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.072 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                Doublon?
                            </span>
                        @endif
                        @if($contribution->requires_review)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400" title="Écart de montant supérieur à 10%">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Vérification requise
                            </span>
                        @endif

                        @if($userMember && $userMember->canManage())
                            {{-- PENDING : Déclarer pour ce membre (seulement si tontine active et tour en cours) --}}
                            @if($contribution->status->value === 'pending' && $tontine->status->value === 'active' && $contribution->tour->status->value === 'ongoing')
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

                            {{-- REJECTED : Re-déclarer (seulement si tontine active et tour en cours) --}}
                            @if($contribution->status->value === 'rejected' && $tontine->status->value === 'active' && $contribution->tour->status->value === 'ongoing')
                                <button type="button" @click="$dispatch('open-modal-declare-{{ $contribution->id }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-500/30 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Re-déclarer
                                </button>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Info justificatif si existe --}}
                @if($contribution->paymentProof && in_array($contribution->status->value, ['declared', 'confirmed']))
                <div class="mt-3 pl-13 ml-13 text-xs text-slate-500 dark:text-slate-400 flex flex-wrap gap-x-4 gap-y-1" style="padding-left: 3.25rem;">
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

                {{-- Motif de rejet --}}
                @if($contribution->status->value === 'rejected' && $contribution->paymentProof && $contribution->paymentProof->rejection_reason)
                <div class="mt-3 text-xs text-red-500 dark:text-red-400 flex items-start gap-1" style="padding-left: 3.25rem;">
                    <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Motif : {{ $contribution->paymentProof->rejection_reason }}</span>
                </div>
                @endif
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h4 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Aucune contribution</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400">Aucune contribution ne correspond aux filtres sélectionnés.</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($contributions->hasPages())
    <div class="mt-6">
        {{ $contributions->links() }}
    </div>
    @endif

    {{-- ==================== MODALES ==================== --}}

    @if($userMember && $userMember->canManage())
        {{-- Modales de declaration (pour contributions pending ou rejected) --}}
        @foreach($contributions as $contribution)
            @if(in_array($contribution->status->value, ['pending', 'rejected']))
            <x-modal id="declare-{{ $contribution->id }}" maxWidth="lg" title="Déclarer le paiement">
                <form method="POST" action="{{ route('tontines.contributions.declare', [$tontine, $contribution]) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    {{-- Info membre --}}
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-200 dark:border-slate-700">
                        <img src="{{ $contribution->user->avatar_url }}" alt="{{ $contribution->user->name }}" class="w-12 h-12 rounded-full object-cover" />
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-white">{{ $contribution->user->name }}</p>
                            <p class="text-sm text-slate-500">{{ format_amount($contribution->amount) }} - Tour #{{ $contribution->tour->tour_number }}</p>
                        </div>
                    </div>

                    <x-select name="payment_method" label="Méthode de paiement" required>
                        <option value="">Choisir...</option>
                        @foreach(\App\Enums\PaymentMethod::cases() as $method)
                            <option value="{{ $method->value }}" @selected(old('payment_method') === $method->value)>{{ $method->label() }}</option>
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

        {{-- Modales de justificatif (pour contributions avec proof) --}}
        @foreach($contributions as $contribution)
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

                    @if($contribution->status->value === 'declared')
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

        {{-- Modales de rejet (pour contributions declared) --}}
        @foreach($contributions as $contribution)
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
</x-layouts.app>

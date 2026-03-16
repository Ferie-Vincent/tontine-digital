<x-layouts.app :title="$tontine->name">
    <x-breadcrumb :items="[
        ['label' => 'Accueil', 'url' => route('dashboard')],
        ['label' => 'Mes tontines', 'url' => route('tontines.index')],
        ['label' => $tontine->name],
    ]" />

    {{-- Hero Section with Gradient Background --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-violet-600 to-purple-600 rounded-2xl mb-8 p-4 sm:p-6 md:p-8">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)"/>
            </svg>
        </div>

        {{-- Decorative Circles --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative">
            {{-- Back Button & Actions --}}
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('tontines.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-white/90 hover:text-white transition-all duration-200 backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span class="text-sm font-medium">Retour</span>
                </a>

                @if($userMember && $userMember->isAdmin())
                <div class="flex items-center gap-2">
                    <button @click="$dispatch('open-modal-edit-tontine')" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-white/90 hover:text-white transition-all duration-200 backdrop-blur-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span class="text-sm font-medium">Modifier</span>
                    </button>
                    <form method="POST" action="{{ route('tontines.clone', $tontine) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-white/90 hover:text-white transition-all duration-200 backdrop-blur-sm" title="Dupliquer cette tontine">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span class="text-sm font-medium">Dupliquer</span>
                        </button>
                    </form>
                    @if($tontine->status === \App\Enums\TontineStatus::ACTIVE)
                    <form method="POST" action="{{ route('tontines.pause', $tontine) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white/90 hover:text-white transition-all duration-200 backdrop-blur-sm" style="background-color: rgba(245, 158, 11, 0.3);" title="Mettre en pause">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium">Pause</span>
                        </button>
                    </form>
                    @elseif($tontine->status === \App\Enums\TontineStatus::PAUSED)
                    <form method="POST" action="{{ route('tontines.resume', $tontine) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white/90 hover:text-white transition-all duration-200 backdrop-blur-sm" style="background-color: rgba(22, 163, 74, 0.3);" title="Reprendre la tontine">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium">Reprendre</span>
                        </button>
                    </form>
                    @endif
                    @if($tontine->creator_id === auth()->id())
                    <button @click="$dispatch('open-modal-delete-tontine')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500/20 hover:bg-red-500/30 rounded-xl text-white/90 hover:text-white transition-all duration-200 backdrop-blur-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                    @endif
                </div>
                @endif
            </div>

            {{-- Tontine Name & Code --}}
            <div class="text-center mb-6">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 rounded-full text-white/80 text-sm mb-4 backdrop-blur-sm">
                    <x-badge :color="$tontine->status->color()">{{ $tontine->status->label() }}</x-badge>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-4xl font-bold text-white mb-3">{{ $tontine->name }}</h1>
                @if($userMember && $userMember->isAdmin())
                <div class="flex items-center justify-center gap-3">
                    <span class="font-mono text-xl text-white/90 bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm cursor-pointer hover:bg-white/20 transition-all" onclick="copyToClipboard('{{ $tontine->code }}')" title="Cliquer pour copier">
                        {{ $tontine->code }}
                    </span>
                    <button onclick="copyToClipboard('{{ $tontine->code }}')" class="p-2 bg-white/10 hover:bg-white/20 rounded-lg text-white/80 hover:text-white transition-all" title="Copier le code">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                    <button @click="$dispatch('open-modal-qrcode')" class="p-2 bg-white/10 hover:bg-white/20 rounded-lg text-white/80 hover:text-white transition-all" title="QR Code">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </button>
                </div>
                @endif
            </div>

            {{-- Key Info Pills --}}
            <div class="flex flex-wrap items-center justify-center gap-4 text-white/80 text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Créé par {{ $tontine->creator->name }}</span>
                </div>
                <span class="w-1 h-1 bg-white/40 rounded-full"></span>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Début le {{ $tontine->start_date->format('d/m/Y') }}</span>
                </div>
                @if($tontine->end_date)
                <span class="w-1 h-1 bg-white/40 rounded-full"></span>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Fin le {{ $tontine->end_date->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Expired Period Warning --}}
    @if($tontine->end_date && $tontine->end_date->isPast() && $tontine->status->value !== 'completed')
    <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-xl flex items-start gap-3">
        <svg class="w-6 h-6 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Période expirée</p>
            <p class="text-sm text-amber-700 dark:text-amber-400 mt-0.5">La date de fin de cette tontine ({{ $tontine->end_date->format('d/m/Y') }}) est dépassée. Veuillez contacter l'administrateur.</p>
        </div>
    </div>
    @endif

    {{-- Paused Banner --}}
    @if($tontine->status === \App\Enums\TontineStatus::PAUSED)
    <div class="mb-6 p-4 border rounded-xl flex flex-col sm:flex-row items-start gap-3" style="background-color: #fffbeb; border-color: #fbbf24;">
        <svg class="w-6 h-6 shrink-0 mt-0.5" style="color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="flex-1">
            <p class="text-sm font-semibold" style="color: #92400e;">Tontine en pause</p>
            <p class="text-sm mt-0.5" style="color: #b45309;">Cette tontine est actuellement en pause. Les contributions ne peuvent pas être déclarées et aucun nouveau tour ne peut démarrer.</p>
        </div>
        @if($userMember && $userMember->isAdmin())
        <form method="POST" action="{{ route('tontines.resume', $tontine) }}" class="shrink-0">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg transition hover:opacity-90" style="background-color: #16a34a;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Reprendre
            </button>
        </form>
        @endif
    </div>
    @endif

    {{-- Stats Cards (4 columns) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Members Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded-lg">Membres</span>
            </div>
            <p class="text-3xl font-bold text-slate-800 dark:text-white mb-1">{{ $tontine->active_members_count }}<span class="text-lg text-slate-400 font-normal">/{{ $tontine->max_members }}</span></p>
            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2 mt-3">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ ($tontine->active_members_count / $tontine->max_members) * 100 }}%"></div>
            </div>
        </div>

        {{-- Amount Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/30 px-2 py-1 rounded-lg">Cotisation</span>
            </div>
            <p class="text-2xl font-bold text-slate-800 dark:text-white mb-1">{{ $tontine->formatted_amount }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">par {{ strtolower($tontine->frequency->label()) }}</p>
            @if($tontine->total_parts > 0)
            <p class="text-xs text-violet-600 dark:text-violet-400 mt-1">Cagnotte : {{ $tontine->formatted_pot_amount }}</p>
            @endif
            @if($tontine->formatted_target_per_tour)
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Visée/tour : {{ $tontine->formatted_target_per_tour }}</p>
            @endif
        </div>

        {{-- Frequency Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-1 rounded-lg">Fréquence</span>
            </div>
            <p class="text-2xl font-bold text-slate-800 dark:text-white mb-1">{{ $tontine->frequency->label() }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">cycle de paiement</p>
        </div>

        {{-- Progress Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded-lg">Progression</span>
            </div>
            <p class="text-3xl font-bold text-slate-800 dark:text-white mb-1">{{ $tontine->progress }}%</p>
            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2 mt-3">
                <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-2 rounded-full transition-all duration-500" style="width: {{ $tontine->progress }}%"></div>
            </div>
        </div>
    </div>

    {{-- Progression globale --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 mb-8">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300">Progression globale</h3>
            <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $tontine->progress }}%</span>
        </div>
        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-3 rounded-full transition-all duration-500" style="width: {{ $tontine->progress }}%"></div>
        </div>
        <div class="flex items-center justify-between mt-3 text-sm text-slate-500 dark:text-slate-400">
            <span>{{ $tontine->tours()->where('status', 'completed')->count() }} / {{ $tontine->tours()->count() }} tours complétés</span>
            @if($tontine->tours()->where('status', 'ongoing')->exists())
                <span class="text-amber-600 dark:text-amber-400 font-medium">Tour en cours</span>
            @endif
        </div>
    </div>

    {{-- Statistiques financières --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Total collecté</p>
            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ format_amount($tontine->contributions()->where('status', 'confirmed')->sum('amount')) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Total versé</p>
            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ format_amount($tontine->tours()->whereNotNull('disbursed_at')->sum('collected_amount')) }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Membres actifs</p>
            <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $tontine->active_members_count }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Tours restants</p>
            <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $tontine->tours()->whereIn('status', ['upcoming', 'ongoing'])->count() }}</p>
        </div>
    </div>

    {{-- Banniere Mon Paiement - visible pour les membres quand un tour est en cours --}}
    @if($currentTour && $userContribution && $currentTour->beneficiary_id !== auth()->id())
    <div class="mb-8">
        @if(in_array($userContribution->status->value, ['pending', 'rejected']))
        {{-- Paiement en attente ou rejete --}}
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-6 shadow-lg shadow-amber-500/20 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm shrink-0">
                        @if($userContribution->status->value === 'rejected')
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @else
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="text-white">
                        @if($userContribution->status->value === 'rejected')
                        <p class="text-lg font-bold">Paiement rejeté - Veuillez re-soumettre</p>
                        <p class="text-white/80 text-sm mt-0.5">Tour #{{ $currentTour->tour_number }} - Votre paiement a été rejeté par l'administrateur</p>
                        @else
                        <p class="text-lg font-bold">Paiement en attente</p>
                        <p class="text-white/80 text-sm mt-0.5">Tour #{{ $currentTour->tour_number }} - Montant : {{ $userContribution->formatted_amount }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('tontines.tours.show', [$tontine, $currentTour]) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-amber-600 font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Déclarer mon paiement
                </a>
            </div>
        </div>
        @elseif($userContribution->status->value === 'declared')
        {{-- Paiement déclaré, en attente de validation --}}
        <div class="bg-gradient-to-r from-blue-500 to-violet-500 rounded-2xl p-6 shadow-lg shadow-blue-500/20 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="text-white">
                        <p class="text-lg font-bold">Paiement soumis - En attente de validation</p>
                        <p class="text-white/80 text-sm mt-0.5">Tour #{{ $currentTour->tour_number }} - {{ $userContribution->formatted_amount }} - L'administrateur va vérifier votre paiement</p>
                    </div>
                </div>
                <a href="{{ route('tontines.tours.show', [$tontine, $currentTour]) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-xl backdrop-blur-sm transition-all duration-200 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Voir les détails
                </a>
            </div>
        </div>
        @elseif($userContribution->status->value === 'confirmed')
        {{-- Paiement confirmé --}}
        <div class="bg-gradient-to-r from-emerald-500 to-green-500 rounded-2xl p-6 shadow-lg shadow-emerald-500/20 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-white">
                    <p class="text-lg font-bold">Paiement confirmé</p>
                    <p class="text-white/80 text-sm mt-0.5">Tour #{{ $currentTour->tour_number }} - {{ $userContribution->formatted_amount }} - Votre paiement a été validé</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content Area --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Current Tour Card --}}
            @if($currentTour)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-emerald-500/10 to-green-500/10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Tour en cours</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Tour #{{ $currentTour->tour_number }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Actif</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <x-avatar :user="$currentTour->beneficiary" size="lg" />
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-gradient-to-r from-emerald-500 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-white text-lg">{{ $currentTour->beneficiary->name }}</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Bénéficiaire du tour</p>
                            </div>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-2xl font-bold bg-gradient-to-r from-emerald-500 to-green-600 bg-clip-text text-transparent">{{ $currentTour->formatted_collected_amount }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">sur {{ $currentTour->formatted_expected_amount }}</p>
                            <div class="w-40 bg-slate-200 dark:bg-slate-600 rounded-full h-2">
                                <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-2 rounded-full transition-all duration-500" style="width: {{ $currentTour->collection_progress }}%"></div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $currentTour->collection_progress }}% collecté</p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <x-button :href="route('tontines.tours.show', [$tontine, $currentTour])" variant="primary" size="sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Voir details
                        </x-button>
                        @if($userMember && $userMember->canManage())
                        <div x-data="{ showCompleteConfirm: false }">
                            <x-button type="button" variant="success" size="sm" @click="showCompleteConfirm = true">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Clôturer
                            </x-button>
                            <div x-show="showCompleteConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                <div class="fixed inset-0 bg-black/50" @click="showCompleteConfirm = false"></div>
                                <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Clôturer la collecte ?</h3>
                                    </div>
                                    <p class="text-slate-600 dark:text-slate-400 mb-6">Le montant collecté sera figé et le versement pourra être effectué.</p>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="showCompleteConfirm = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">Annuler</button>
                                        <form method="POST" action="{{ route('tontines.tours.complete', [$tontine, $currentTour]) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition">Clôturer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Tours Calendar Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Calendrier des tours</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Prochains tours planifiés</p>
                        </div>
                        <a href="{{ route('tontines.tours.index', $tontine) }}" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                            Voir tous
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($upcomingTours->isEmpty() && !$currentTour)
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-slate-600 dark:text-slate-300 font-medium">Aucun tour planifié</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Les tours n'ont pas encore été planifiés.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($upcomingTours as $tour)
                            <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-violet-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-blue-500/20">
                                        #{{ $tour->tour_number }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $tour->beneficiary->name }}</p>
                                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $tour->due_date->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                                <x-badge :color="$tour->status->color()">{{ $tour->status->label() }}</x-badge>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Contribution Matrix Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Suivi des contributions</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Matrice de suivi détaillée</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <p class="text-sm text-slate-600 dark:text-slate-300 mb-3">Consultez la matrice complète pour visualiser l'état des contributions de tous les membres pour chaque tour.</p>
                        </div>
                        <x-button :href="route('tontines.contributions.matrix', $tontine)" variant="outline" size="sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Voir la matrice
                        </x-button>
                    </div>
                </div>
            </div>

            {{-- Discussion Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Discussion</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Espace de communication de la tontine</p>
                        </div>
                        <a href="{{ route('tontines.messages.index', $tontine) }}" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                            Ouvrir
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-slate-600 dark:text-slate-300">Échangez avec les membres et l'administrateur de la tontine. Les soumissions de paiement y apparaissent automatiquement.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Tontine Info Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-500/5 to-violet-500/5">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Informations</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Statut</dt>
                            <dd><x-badge :color="$tontine->status->color()">{{ $tontine->status->label() }}</x-badge></dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Créateur</dt>
                            <dd class="text-sm text-slate-800 dark:text-white font-medium">{{ $tontine->creator->name }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Date de début</dt>
                            <dd class="text-sm text-slate-800 dark:text-white">{{ $tontine->start_date->format('d/m/Y') }}</dd>
                        </div>
                        @if($tontine->end_date)
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Date de fin</dt>
                            <dd class="text-sm text-slate-800 dark:text-white">{{ $tontine->end_date->format('d/m/Y') }}</dd>
                        </div>
                        @endif
                        @if($userMember)
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Mon rôle</dt>
                            <dd><x-badge :color="$userMember->role->color()">{{ $userMember->role->label() }}</x-badge></dd>
                        </div>
                        @endif
                        @if($tontine->total_parts > 0)
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Total parts</dt>
                            <dd class="text-sm text-slate-800 dark:text-white font-medium">{{ $tontine->total_parts }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Cagnotte/tour</dt>
                            <dd class="text-sm font-medium text-violet-600 dark:text-violet-400">{{ $tontine->formatted_pot_amount }}</dd>
                        </div>
                        @endif
                        @if($tontine->formatted_target_per_tour)
                        <div class="flex justify-between items-center">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Visée/tour</dt>
                            <dd class="text-sm font-medium text-violet-600 dark:text-violet-400">{{ $tontine->formatted_target_per_tour }}</dd>
                        </div>
                        @endif
                        @if($tontine->formatted_target_total)
                        <div>
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-slate-500 dark:text-slate-400">Objectif global</dt>
                                <dd class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ $tontine->formatted_target_total }}</dd>
                            </div>
                            @php
                                $totalCollected = $tontine->contributions()->where('status', 'validated')->sum('amount');
                                $progressGlobal = $tontine->target_amount_total > 0 ? min(round(($totalCollected / $tontine->target_amount_total) * 100, 1), 100) : 0;
                            @endphp
                            <div class="mt-2">
                                <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                                    <span>{{ format_amount($totalCollected) }}</span>
                                    <span>{{ $progressGlobal }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-2 rounded-full transition-all duration-500" style="width: {{ $progressGlobal }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </dl>

                    @if($tontine->description)
                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <dt class="text-sm text-slate-500 dark:text-slate-400 mb-2">Description</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-300">{{ $tontine->description }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Members Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Membres</h3>
                        <span class="text-sm text-slate-500 dark:text-slate-400">{{ $tontine->active_members_count }}/{{ $tontine->max_members }}</span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        @foreach($tontine->members->where('status.value', 'active')->sortBy('position') as $member)
                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <span class="w-6 h-6 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 flex items-center justify-center text-xs font-bold text-slate-500 dark:text-slate-400">{{ $member->position }}</span>
                            <div class="relative">
                                <x-avatar :user="$member->user" size="sm" />
                                @if($member->role->value === 'admin')
                                <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-gradient-to-r from-amber-500 to-orange-500 rounded-full flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 01.894.553l1.809 3.666 4.045.588a1 1 0 01.554 1.706l-2.928 2.854.691 4.032a1 1 0 01-1.451 1.054L10 14.347l-3.614 1.9a1 1 0 01-1.451-1.054l.691-4.032-2.928-2.854a1 1 0 01.554-1.706l4.045-.588 1.809-3.666A1 1 0 0110 2z" clip-rule="evenodd"/></svg>
                                </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-sm text-slate-800 dark:text-white font-medium truncate">{{ $member->user->name }}</p>
                                    @if($member->parts > 1)
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-400">{{ $member->parts }}p</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $member->role->label() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <x-button :href="route('tontines.members.index', $tontine)" variant="ghost" size="sm" class="w-full">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Gérer les membres
                        </x-button>
                    </div>
                </div>
            </div>

            {{-- Position Swap Card --}}
            @if($userMember && $userMember->status->value === 'active')
            @php
                $pendingSwapCount = \App\Models\PositionSwapRequest::where('tontine_id', $tontine->id)
                    ->where('target_id', auth()->id())
                    ->where('status', 'pending')
                    ->count();
                $myPendingSwap = \App\Models\PositionSwapRequest::where('tontine_id', $tontine->id)
                    ->where('requester_id', auth()->id())
                    ->where('status', 'pending')
                    ->first();
            @endphp
            <div x-data="{ showSwapModal: false }" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-indigo-500/5 to-blue-500/5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Échange de position</h3>
                        @if($pendingSwapCount > 0)
                            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full">{{ $pendingSwapCount }}</span>
                        @endif
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                        <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-sm font-bold text-white">{{ $userMember->position }}</span>
                        <div>
                            <p class="text-sm font-medium text-slate-800 dark:text-white">Votre position actuelle</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Position #{{ $userMember->position }} dans l'ordre de passage</p>
                        </div>
                    </div>

                    @if($myPendingSwap)
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                            <p class="text-sm text-amber-700 dark:text-amber-400">
                                Demande en cours vers <strong>{{ $myPendingSwap->target->name }}</strong> (position #{{ $myPendingSwap->target_position }})
                            </p>
                        </div>
                    @else
                        <button @click="showSwapModal = true" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Demander un échange de position
                        </button>
                    @endif

                    @if($pendingSwapCount > 0)
                        <a href="{{ route('tontines.swap.pending', $tontine) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 rounded-xl shadow-lg shadow-indigo-500/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            Voir les demandes reçues ({{ $pendingSwapCount }})
                        </a>
                    @endif
                </div>

                {{-- Modal d'échange --}}
                <div x-show="showSwapModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                    <div @click.away="showSwapModal = false" class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">Demander un échange de position</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Votre position actuelle : <strong class="text-indigo-600 dark:text-indigo-400">#{{ $userMember->position }}</strong></p>

                        <form method="POST" action="{{ route('tontines.swap.store', $tontine) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Échanger avec</label>
                                <select name="target_user_id" required class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-white text-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all duration-200">
                                    <option value="">-- Sélectionner un membre --</option>
                                    @foreach($tontine->members->where('status.value', 'active')->where('user_id', '!=', auth()->id())->sortBy('position') as $member)
                                        <option value="{{ $member->user_id }}">{{ $member->user->name }} (position #{{ $member->position }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Motif (optionnel)</label>
                                <textarea name="reason" rows="3" maxlength="500" placeholder="Expliquez pourquoi vous souhaitez échanger..." class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-white text-sm focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all duration-200"></textarea>
                            </div>
                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="showSwapModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                    Annuler
                                </button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-lg shadow-indigo-500/20 transition">
                                    Envoyer la demande
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- Admin Quick Actions Card --}}
            @if($userMember && $userMember->isAdmin())
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-emerald-500/5 to-green-500/5">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Administration</h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('tontines.finances', $tontine) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Tableau de bord financier</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Collectes, versements, pénalités</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('tontines.contributions.matrix', $tontine) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-violet-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Matrice des contributions</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Vue globale par membre et tour</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('tontines.activity', $tontine) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-800 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Historique d'activite</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Journal des actions et evenements</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            @endif

            {{-- Invite Code + QR Card --}}
            @if($userMember && $userMember->isAdmin())
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-violet-500/5 to-purple-500/5">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Inviter des membres</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        {{-- Code d'invitation --}}
                        <div class="inline-block p-4 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-600 rounded-2xl mb-4">
                            <p class="text-3xl font-mono font-bold bg-gradient-to-r from-blue-600 via-violet-600 to-purple-600 bg-clip-text text-transparent tracking-widest">{{ $tontine->code }}</p>
                        </div>

                        {{-- QR Code --}}
                        <div class="mb-4">
                            <div class="inline-flex items-center justify-center p-3 bg-white rounded-2xl shadow-inner border border-slate-200">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('tontines.join') . '?code=' . $tontine->code) }}" alt="QR Code {{ $tontine->code }}" width="180" height="180" class="rounded-lg">
                            </div>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Scannez pour rejoindre</p>
                        </div>

                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Partagez le code ou le QR code pour inviter de nouveaux membres</p>

                        <div class="flex flex-col gap-2">
                            <button onclick="copyToClipboard('{{ $tontine->code }}')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 via-violet-600 to-purple-600 hover:from-blue-700 hover:via-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-violet-500/30 transition-all duration-200 hover:shadow-xl hover:shadow-violet-500/40 hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Copier le code
                            </button>
                            <button onclick="downloadQR()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-xl transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Télécharger le QR code
                            </button>
                            @if(auth()->user()->canManage($tontine))
                            <button @click="$dispatch('open-modal-sms-invite')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl shadow-lg shadow-emerald-500/30 transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Inviter par SMS
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Quitter la tontine (non-admin uniquement) --}}
            @if($userMember && $userMember->role->value !== 'admin')
            <div x-data="{ showLeaveModal: false }" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-red-500/5 to-orange-500/5">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Zone de danger</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Si vous quittez cette tontine, vous ne pourrez plus accéder aux tours et contributions.</p>
                    <button @click="showLeaveModal = true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Quitter la tontine
                    </button>

                    {{-- Modal de confirmation --}}
                    <div x-show="showLeaveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                        <div @click.away="showLeaveModal = false" class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Quitter la tontine</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                                Êtes-vous sûr de vouloir quitter <strong>{{ $tontine->name }}</strong> ?
                                Cette action est irréversible. Vous ne pourrez plus accéder aux tours et contributions de cette tontine.
                            </p>
                            <div class="flex justify-end gap-3">
                                <button @click="showLeaveModal = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                    Annuler
                                </button>
                                <form method="POST" action="{{ route('tontines.leave', $tontine) }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                        Confirmer le départ
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Modal Edit --}}
    @if($userMember && $userMember->isAdmin())
    <x-modal id="edit-tontine" maxWidth="2xl" title="Modifier la tontine">
        <form method="POST" action="{{ route('tontines.update', $tontine) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <x-input name="name" label="Nom de la tontine" :value="old('name', $tontine->name)" :error="$errors->editTontine->first('name')" required />
            <x-textarea name="description" label="Description" :value="old('description', $tontine->description)" rows="3" :error="$errors->editTontine->first('description')" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input name="contribution_amount" label="Montant de cotisation (FCFA)" type="number" min="1000" step="500" :value="old('contribution_amount', $tontine->contribution_amount)" :error="$errors->editTontine->first('contribution_amount')" required />
                <x-select name="frequency" label="Fréquence" :error="$errors->editTontine->first('frequency')" required>
                    <option value="weekly" {{ old('frequency', $tontine->frequency->value) === 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                    <option value="biweekly" {{ old('frequency', $tontine->frequency->value) === 'biweekly' ? 'selected' : '' }}>Bimensuelle</option>
                    <option value="monthly" {{ old('frequency', $tontine->frequency->value) === 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                </x-select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input name="target_amount_per_tour" label="Cagnotte visée/tour (FCFA)" type="number" min="1000" step="500" :value="old('target_amount_per_tour', $tontine->target_amount_per_tour)" :error="$errors->editTontine->first('target_amount_per_tour')" />
                <x-input name="target_amount_total" label="Objectif global (FCFA)" type="number" min="1000" step="500" :value="old('target_amount_total', $tontine->target_amount_total)" :error="$errors->editTontine->first('target_amount_total')" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input name="max_members" label="Nombre max de membres" type="number" min="2" max="100" :value="old('max_members', $tontine->max_members)" :error="$errors->editTontine->first('max_members')" required />
                <x-input name="end_date" label="Date de fin (optionnel)" type="date" :value="old('end_date', $tontine->end_date?->format('Y-m-d'))" :error="$errors->editTontine->first('end_date')" />
            </div>
            <x-select name="status" label="Statut" :error="$errors->editTontine->first('status')">
                @foreach(\App\Enums\TontineStatus::cases() as $status)
                <option value="{{ $status->value }}" {{ old('status', $tontine->status->value) === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                @endforeach
            </x-select>
            <x-textarea name="rules" label="Règlement" :value="old('rules', $tontine->rules)" rows="4" :error="$errors->editTontine->first('rules')" />
            <div class="flex justify-end gap-3">
                <x-button type="button" variant="ghost" @click="$dispatch('close-modal-edit-tontine')">Annuler</x-button>
                <x-button type="submit" variant="primary">Enregistrer</x-button>
            </div>
        </form>
    </x-modal>
    @endif

    {{-- Modal Delete --}}
    @if($tontine->creator_id === auth()->id())
    <x-modal id="delete-tontine" maxWidth="sm" title="Supprimer la tontine">
        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <p class="text-slate-800 dark:text-white font-semibold mb-1">Supprimer "{{ $tontine->name }}" ?</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Cette action est irréversible.</p>
        </div>
        <form method="POST" action="{{ route('tontines.destroy', $tontine) }}">
            @csrf
            @method('DELETE')
            <div class="flex justify-center gap-3">
                <x-button type="button" variant="ghost" @click="$dispatch('close-modal-delete-tontine')">Annuler</x-button>
                <x-button type="submit" variant="danger">Confirmer</x-button>
            </div>
        </form>
    </x-modal>
    @endif

    {{-- Modal SMS Invitation --}}
    @if(auth()->user()->canManage($tontine))
    <x-modal id="sms-invite" maxWidth="lg" title="Inviter par SMS">
        <form method="POST" action="{{ route('tontines.members.invite', $tontine) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Numéros de téléphone</label>
                <textarea name="phones" rows="4" placeholder="Entrez les numéros séparés par des virgules, points-virgules ou retours à la ligne.&#10;Ex: 0701020304, 0705060708&#10;0709101112" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-white text-sm focus:border-violet-500 dark:focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 outline-none transition-all duration-200" required>{{ old('phones') }}</textarea>
                @error('phones')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2">Aperçu du message :</p>
                <p class="text-sm text-slate-700 dark:text-slate-300 italic">
                    {{ \App\Models\SiteSettings::get('platform_name', 'DIGI-TONTINE CI') }} - Vous etes invite(e) a rejoindre la tontine "{{ $tontine->name }}".
                    Cotisation : {{ format_amount($tontine->contribution_amount) }} / {{ $tontine->frequency->label() }}.
                    Code d'invitation : {{ $tontine->code }}.
                    Rendez-vous sur la plateforme pour rejoindre !
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                <x-button type="button" variant="ghost" @click="$dispatch('close-modal-sms-invite')">Annuler</x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Envoyer les invitations
                </x-button>
            </div>
        </form>
    </x-modal>
    @endif

    {{-- Modal QR Code --}}
    <x-modal id="qrcode" maxWidth="sm">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-violet-500/10 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">QR Code d'invitation</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">Scannez ce code pour rejoindre la tontine <strong>{{ $tontine->name }}</strong></p>

            {{-- QR Code Image --}}
            <div class="inline-flex items-center justify-center p-4 bg-white rounded-2xl shadow-inner border border-slate-200 mb-4">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('tontines.join') . '?code=' . $tontine->code) }}" alt="QR Code {{ $tontine->code }}" width="200" height="200" class="rounded-lg">
            </div>

            {{-- Code d'invitation --}}
            <p class="text-sm font-mono text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg px-4 py-2 mb-5 tracking-widest">{{ $tontine->code }}</p>

            {{-- Actions --}}
            <div class="flex flex-col gap-2">
                <button onclick="downloadQR()" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gradient-to-r from-blue-600 via-violet-600 to-purple-600 hover:from-blue-700 hover:via-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-violet-500/30 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Télécharger le QR code
                </button>
                <button onclick="shareQR()" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    Partager le lien
                </button>
            </div>
        </div>
    </x-modal>

    @if($errors->editTontine->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new Event('open-modal-edit-tontine'));
        });
    </script>
    @endif

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 px-6 py-3 bg-slate-800 dark:bg-white text-white dark:text-slate-800 rounded-xl shadow-2xl transform transition-all duration-300 translate-y-full opacity-0 z-50 flex items-center gap-3';
                toast.innerHTML = `
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="font-medium">Code copié !</span>
                `;
                document.body.appendChild(toast);
                setTimeout(() => toast.classList.remove('translate-y-full', 'opacity-0'), 10);
                setTimeout(() => {
                    toast.classList.add('translate-y-full', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, 2500);
            });
        }

        function downloadQR() {
            const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data={{ urlencode(route('tontines.join') . '?code=' . $tontine->code) }}";
            fetch(qrUrl)
                .then(response => response.blob())
                .then(blob => {
                    const link = document.createElement('a');
                    link.download = 'qr-{{ $tontine->code }}.png';
                    link.href = URL.createObjectURL(blob);
                    link.click();
                    URL.revokeObjectURL(link.href);
                });
        }

        function shareQR() {
            const shareUrl = "{{ route('tontines.join') }}?code={{ $tontine->code }}";
            if (navigator.share) {
                navigator.share({
                    title: 'Rejoindre la tontine {{ $tontine->name }}',
                    text: 'Utilisez ce lien pour rejoindre la tontine "{{ $tontine->name }}". Code : {{ $tontine->code }}',
                    url: shareUrl,
                }).catch(() => {});
            } else {
                copyToClipboard(shareUrl);
            }
        }
    </script>
</x-layouts.app>

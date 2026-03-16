<x-layouts.app :title="$user->name">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users') }}" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Profil utilisateur</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Details et activites</p>
            </div>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        {{-- Hero Section - User Profile Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            {{-- Gradient Banner --}}
            <div class="h-32 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 relative">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white dark:from-slate-800 to-transparent"></div>
            </div>

            {{-- Profile Content --}}
            <div class="px-6 pb-6 -mt-16 relative">
                <div class="flex flex-col lg:flex-row items-start lg:items-end gap-6">
                    {{-- Large Avatar --}}
                    <div class="relative">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-28 h-28 rounded-2xl object-cover shadow-xl ring-4 ring-white dark:ring-slate-800" />
                        @if($user->status === 'active')
                        <span class="absolute -bottom-1 -right-1 w-7 h-7 bg-emerald-500 border-4 border-white dark:border-slate-800 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        @else
                        <span class="absolute -bottom-1 -right-1 w-7 h-7 bg-red-500 border-4 border-white dark:border-slate-800 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        @endif
                    </div>

                    {{-- User Info --}}
                    <div class="flex-1 pt-4 lg:pt-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-3">
                            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $user->name }}</h1>
                            <div class="flex items-center gap-2">
                                @if($user->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                    Actif
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                    Suspendu
                                </span>
                                @endif
                                @if($user->is_admin)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-400 text-xs font-semibold rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Administrateur
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- Contact Info Row --}}
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ $user->formatted_phone }}</span>
                            </div>
                            @if($user->email)
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span>{{ $user->email }}</span>
                            </div>
                            @endif
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span>Inscrit le {{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col items-end gap-2 pt-4 lg:pt-0">
                        <div class="flex items-center gap-2">
                            @if(!$user->is_admin)
                            <form method="POST" action="{{ route('admin.users.impersonate', $user) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-sm font-medium rounded-xl border border-amber-200 dark:border-amber-500/30 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Se connecter en tant que
                                </button>
                            </form>
                            @endif
                            @if($user->status === 'active' && !$user->is_admin)
                            <div x-data="{ showSuspendConfirm: false }">
                                <button type="button" @click="showSuspendConfirm = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 text-sm font-medium rounded-xl border border-red-200 dark:border-red-500/30 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    Suspendre
                                </button>
                                <div x-show="showSuspendConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                    <div class="fixed inset-0 bg-black/50" @click="showSuspendConfirm = false"></div>
                                    <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Suspendre cet utilisateur ?</h3>
                                        </div>
                                        <p class="text-slate-600 dark:text-slate-400 mb-6">L'utilisateur ne pourra plus se connecter ni accéder à la plateforme.</p>
                                        <div class="flex justify-end gap-3">
                                            <button type="button" @click="showSuspendConfirm = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">Annuler</button>
                                            <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Suspendre</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @elseif($user->status === 'suspended')
                            <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-medium rounded-xl border border-emerald-200 dark:border-emerald-500/30 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Reactiver
                                </button>
                            </form>
                            @endif
                        </div>
                        @if($user->locked_until && $user->locked_until->isFuture())
                        <div class="flex flex-col items-end">
                            <form method="POST" action="{{ route('admin.users.unlock', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-sm font-medium rounded-xl border border-amber-200 dark:border-amber-500/30 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    Déverrouiller le compte
                                </button>
                            </form>
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Verrouillé jusqu'au {{ $user->locked_until->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Tontines Count --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 relative overflow-hidden group hover:shadow-lg transition-shadow duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-blue-600/5 rounded-bl-full"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400">
                            Membre
                        </span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tontines</p>
                    <p class="text-3xl font-bold text-slate-800 dark:text-white mt-1">{{ $user->tontines->count() }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Groupes rejoints</p>
                </div>
            </div>

            {{-- Total Contributions --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 relative overflow-hidden group hover:shadow-lg transition-shadow duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 rounded-bl-full"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                            Total
                        </span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Contributions</p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ format_amount($contributionsTotal) }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">contribues</p>
                </div>
            </div>

            {{-- Created Tontines --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 relative overflow-hidden group hover:shadow-lg transition-shadow duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-violet-500/10 to-violet-600/5 rounded-bl-full"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center shadow-lg shadow-violet-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-500/20 text-violet-600 dark:text-violet-400">
                            Createur
                        </span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Tontines créées</p>
                    <p class="text-3xl font-bold text-slate-800 dark:text-white mt-1">{{ $user->createdTontines->count() }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Groupes inities</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Tontines List --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-500/5 to-transparent">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-slate-800 dark:text-white">Tontines de l'utilisateur</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->tontines->count() }} groupe(s) au total</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($user->tontines as $tontine)
                        <div class="flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                    {{ strtoupper(substr($tontine->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $tontine->name }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        {{-- Role Badge --}}
                                        @if($tontine->pivot->role === 'admin')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-xs font-medium rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Admin
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300 text-xs font-medium rounded-full">
                                            Membre
                                        </span>
                                        @endif
                                        <span class="text-xs text-slate-400 dark:text-slate-500">
                                            Rejoint le {{ \Carbon\Carbon::parse($tontine->pivot->joined_at)?->format('d/m/Y') ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($tontine->status->value === 'active') bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400
                                    @elseif($tontine->status->value === 'pending') bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400
                                    @else bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-300
                                    @endif">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        @if($tontine->status->value === 'active') bg-emerald-500
                                        @elseif($tontine->status->value === 'pending') bg-amber-500
                                        @else bg-slate-400
                                        @endif mr-1.5"></span>
                                    {{ $tontine->status->label() }}
                                </span>
                                <a href="{{ route('tontines.show', $tontine) }}" class="p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="text-slate-600 dark:text-slate-300 font-medium mb-1">Aucune tontine</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Cet utilisateur n'a rejoint aucune tontine</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Account Details Card --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-violet-500/5 to-transparent">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Details du compte</h3>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50">
                            <span class="text-sm text-slate-500 dark:text-slate-400">ID Utilisateur</span>
                            <span class="text-sm font-mono font-medium text-slate-700 dark:text-slate-200">#{{ $user->id }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Téléphone</span>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $user->formatted_phone }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Email</span>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $user->email ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Inscription</span>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700/50">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Derniere connexion</span>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $user->last_login_at?->diffForHumans() ?? 'Jamais' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Type de compte</span>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $user->is_admin ? 'Administrateur' : 'Utilisateur' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Activity Timeline --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-indigo-500/5 to-transparent">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Activite recente</h3>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="space-y-4">
                            {{-- Account created --}}
                            <div class="flex gap-3">
                                <div class="relative">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                    </div>
                                    @if($user->tontines->count() > 0)
                                    <div class="absolute top-8 left-1/2 -translate-x-1/2 w-0.5 h-6 bg-slate-200 dark:bg-slate-700"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-800 dark:text-white">
                                        <span class="font-medium">Compte cree</span>
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            {{-- Show latest tontine joined --}}
                            @if($user->tontines->count() > 0)
                            @php $latestTontine = $user->tontines->sortByDesc('pivot.joined_at')->first(); @endphp
                            <div class="flex gap-3">
                                <div class="relative">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-800 dark:text-white">
                                        A rejoint <span class="font-medium">{{ $latestTontine->name }}</span>
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($latestTontine->pivot->joined_at)?->diffForHumans() ?? 'Date inconnue' }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick Actions Card --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Actions rapides</h3>
                        </div>
                    </div>
                    <div class="p-4 space-y-2">
                        @if(!$user->is_admin)
                        <form method="POST" action="{{ route('admin.users.impersonate', $user) }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 transition-colors group">
                                <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 text-left">
                                    <p class="text-sm font-medium text-amber-700 dark:text-amber-400">Se connecter en tant que</p>
                                </div>
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors group">
                            <div class="w-9 h-9 rounded-lg bg-slate-200 dark:bg-slate-600 group-hover:bg-blue-500/20 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Retour a la liste</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        @if($user->locked_until && $user->locked_until->isFuture())
                        <form method="POST" action="{{ route('admin.users.unlock', $user) }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 transition-colors group">
                                <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 text-left">
                                    <p class="text-sm font-medium text-amber-700 dark:text-amber-400">Déverrouiller le compte</p>
                                    <p class="text-xs text-amber-500 dark:text-amber-500">Verrouillé jusqu'au {{ $user->locked_until->format('d/m/Y H:i') }}</p>
                                </div>
                            </button>
                        </form>
                        @endif

                        @if(!$user->is_admin)
                        @if($user->status === 'active')
                        <div x-data="{ showSuspendConfirm2: false }">
                            <button type="button" @click="showSuspendConfirm2 = true" class="w-full flex items-center gap-3 p-3 rounded-xl bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors group">
                                <div class="w-9 h-9 rounded-lg bg-red-100 dark:bg-red-500/20 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </div>
                                <div class="flex-1 text-left">
                                    <p class="text-sm font-medium text-red-700 dark:text-red-400">Suspendre le compte</p>
                                </div>
                            </button>
                            <div x-show="showSuspendConfirm2" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                <div class="fixed inset-0 bg-black/50" @click="showSuspendConfirm2 = false"></div>
                                <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Suspendre cet utilisateur ?</h3>
                                    </div>
                                    <p class="text-slate-600 dark:text-slate-400 mb-6">L'utilisateur ne pourra plus se connecter ni accéder à la plateforme.</p>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="showSuspendConfirm2 = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">Annuler</button>
                                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Suspendre</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors group">
                                <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 text-left">
                                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Reactiver le compte</p>
                                </div>
                            </button>
                        </form>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

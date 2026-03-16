<x-layouts.app :title="'Membres - ' . $tontine->name">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <a href="{{ route('tontines.show', $tontine) }}" class="p-2 -ml-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <span class="font-semibold">Membres</span>
                <span class="text-slate-400 mx-2">/</span>
                <span class="text-slate-500 dark:text-slate-400">{{ $tontine->name }}</span>
            </div>
        </div>
    </x-slot:header>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        {{-- Total Members --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $activeMembers->count() + $pendingMembers->count() }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total membres</p>
                </div>
            </div>
        </div>

        {{-- Active Members --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $activeMembers->count() }}<span class="text-base font-normal text-slate-400">/{{ $tontine->max_members }}</span></p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Membres actifs</p>
                </div>
            </div>
        </div>

        {{-- Pending Requests --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $pendingMembers->count() }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">En attente</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Export button --}}
    @if(auth()->user()->canManage($tontine))
    <div class="flex gap-2 mb-6">
        <a href="{{ route('tontines.export.members.csv', ['tontine' => $tontine->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-emerald-300 dark:border-emerald-600/50 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition" style="background-color: transparent;">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exporter membres CSV
        </a>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Pending Requests Section --}}
            @if($pendingMembers->isNotEmpty() && $userMember && $userMember->canManage())
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                            <h3 class="font-semibold text-slate-800 dark:text-white">Demandes en attente</h3>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">{{ $pendingMembers->count() }} demande(s)</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($pendingMembers as $member)
                        <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700/50 dark:to-slate-700/30 rounded-xl p-4 border border-slate-200 dark:border-slate-600/50 hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="relative">
                                    <x-avatar :user="$member->user" size="lg" />
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-amber-500 border-2 border-white dark:border-slate-700"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 dark:text-white truncate">{{ $member->user->name }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $member->user->phone }}</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $member->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mt-4">
                                <form method="POST" action="{{ route('tontines.members.accept', [$tontine, $member]) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 text-white hover:from-emerald-600 hover:to-emerald-700 shadow-md shadow-emerald-500/30 transition-all duration-300">
                                        <span class="flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Accepter
                                        </span>
                                    </button>
                                </form>
                                <div x-data="{ showRejectConfirm: false }">
                                    <button type="button" @click="showRejectConfirm = true" class="px-4 py-2 text-sm font-medium rounded-lg bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 hover:bg-red-100 hover:text-red-600 dark:hover:bg-red-500/20 dark:hover:text-red-400 transition-all duration-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <div x-show="showRejectConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                        <div class="fixed inset-0 bg-black/50" @click="showRejectConfirm = false"></div>
                                        <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </div>
                                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Refuser cette demande ?</h3>
                                            </div>
                                            <p class="text-slate-600 dark:text-slate-400 mb-6">La demande d'adhésion de ce membre sera refusée. Il pourra soumettre une nouvelle demande ultérieurement.</p>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="showRejectConfirm = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">Annuler</button>
                                                <form method="POST" action="{{ route('tontines.members.reject', [$tontine, $member]) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Refuser</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Active Members Section --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <h3 class="font-semibold text-slate-800 dark:text-white">Membres actifs</h3>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">{{ $activeMembers->count() }}/{{ $tontine->max_members }}</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($activeMembers->sortBy('position') as $member)
                        <div class="group bg-white dark:bg-slate-700/30 rounded-xl p-4 border border-slate-200 dark:border-slate-600/50 hover:border-blue-300 dark:hover:border-blue-500/50 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-300">
                            <div class="flex items-center gap-4">
                                {{-- Position Number with Gradient --}}
                                <div class="relative">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                                        <span class="text-sm font-bold text-white">{{ $member->position }}</span>
                                    </div>
                                </div>

                                {{-- Avatar --}}
                                <div class="relative">
                                    <x-avatar :user="$member->user" size="md" />
                                    @if($member->user_id === auth()->id())
                                    <div class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-blue-500 border-2 border-white dark:border-slate-700 flex items-center justify-center">
                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                    </div>
                                    @endif
                                </div>

                                {{-- Member Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-slate-800 dark:text-white truncate">{{ $member->user->name }}</p>
                                        @if($member->user_id === auth()->id())
                                        <span class="text-xs text-blue-500">(Vous)</span>
                                        @endif
                                        @if($member->parts > 1)
                                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-400">{{ $member->parts }} parts</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $member->user->phone }}</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ format_amount($member->parts * $tontine->contribution_amount) }}/tour</p>
                                </div>

                                {{-- Role Badge & Actions --}}
                                <div class="flex items-center gap-2">
                                    @php
                                        $roleColors = [
                                            'admin' => 'bg-gradient-to-r from-purple-500 to-indigo-500 text-white shadow-purple-500/30',
                                            'treasurer' => 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-amber-500/30',
                                            'member' => 'bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300',
                                        ];
                                        $roleColor = $roleColors[$member->role->value] ?? $roleColors['member'];
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full shadow-sm {{ $roleColor }}">{{ $member->role->label() }}</span>

                                    <a href="{{ route('tontines.members.performance', [$tontine, $member]) }}" class="p-2 rounded-xl text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-all duration-200" title="Performance">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    </a>

                                    @if($userMember && $userMember->isAdmin() && $member->user_id !== auth()->id())
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-600 transition-all duration-200 opacity-0 group-hover:opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                        </button>

                                        {{-- Dropdown Menu --}}
                                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-200/50 dark:shadow-slate-900/50 py-2 z-50" x-cloak>
                                            <div class="px-3 py-2 border-b border-slate-200 dark:border-slate-700">
                                                <p class="text-xs font-medium text-slate-400 uppercase">Actions</p>
                                            </div>
                                            <form method="POST" action="{{ route('tontines.members.updateRole', [$tontine, $member]) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="role" value="treasurer">
                                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Nommer trésorier
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('tontines.members.updateRole', [$tontine, $member]) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="role" value="member">
                                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    Rétrograder en membre
                                                </button>
                                            </form>
                                            <button type="button" @click="open = false; $dispatch('open-modal-edit-parts-{{ $member->id }}')" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                                Modifier les parts
                                            </button>
                                            <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
                                            <div x-data="{ showExcludeConfirm: false }">
                                                <button type="button" @click="showExcludeConfirm = true" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
                                                    Exclure
                                                </button>
                                                <div x-show="showExcludeConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                                    <div class="fixed inset-0 bg-black/50" @click="showExcludeConfirm = false"></div>
                                                    <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                                        <div class="flex items-center gap-3 mb-4">
                                                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
                                                            </div>
                                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Exclure ce membre ?</h3>
                                                        </div>
                                                        <p class="text-slate-600 dark:text-slate-400 mb-6">Le membre sera retiré de la tontine et ne pourra plus participer aux tours en cours.</p>
                                                        <div class="flex justify-end gap-3">
                                                            <button type="button" @click="showExcludeConfirm = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">Annuler</button>
                                                            <form method="POST" action="{{ route('tontines.members.exclude', [$tontine, $member]) }}" class="inline">
                                                                @csrf
                                                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Exclure</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($activeMembers->isEmpty())
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">Aucun membre</h3>
                        <p class="text-slate-500 dark:text-slate-400">Cette tontine n'a pas encore de membres. Invitez des personnes à rejoindre !</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Invitation Code Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </div>
                        <h3 class="font-semibold text-slate-800 dark:text-white mb-2">Code d'invitation</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Partagez ce code pour inviter de nouveaux membres</p>

                        {{-- Large Invitation Code --}}
                        <div class="relative bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-700/50 rounded-xl p-6 border-2 border-dashed border-slate-300 dark:border-slate-600 mb-4">
                            <p class="text-3xl font-mono font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent tracking-[0.3em]">{{ $tontine->code }}</p>
                        </div>

                        <button onclick="copyToClipboard('{{ $tontine->code }}')" class="w-full px-4 py-3 text-sm font-medium rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-all duration-300 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Copier le code
                        </button>
                        @if($userMember && $userMember->canManage())
                        <button @click="$dispatch('open-modal-sms-invite-members')" class="w-full px-4 py-3 text-sm font-medium rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-500/30 transition-all duration-300 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Inviter par SMS
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Add Member Card --}}
            @if($userMember && $userMember->canManage() && !$tontine->isFull())
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <h3 class="font-semibold text-slate-800 dark:text-white mb-2">Ajout direct</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Ajouter un membre par recherche ou créer un nouveau compte</p>

                        <button @click="$dispatch('open-modal-add-member')" class="w-full px-4 py-3 text-sm font-medium rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white hover:from-emerald-600 hover:to-teal-700 shadow-lg shadow-emerald-500/30 transition-all duration-300 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Ajouter un membre
                        </button>
                        <button @click="$dispatch('open-modal-import-csv')" style="background-color: #7c3aed; color: white;" class="w-full mt-2 px-4 py-3 text-sm font-medium rounded-xl hover:opacity-90 shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Importer CSV
                        </button>
                    </div>
                </div>
            </div>
            @elseif($tontine->isFull())
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-slate-400 to-slate-500 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="font-semibold text-slate-800 dark:text-white mb-2">Tontine complète</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Le nombre maximum de membres a été atteint</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Quick Stats --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="font-semibold text-slate-800 dark:text-white">Statistiques rapides</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Places disponibles</span>
                        <span class="font-semibold text-slate-800 dark:text-white">{{ $tontine->max_members - $activeMembers->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Taux de remplissage</span>
                        <span class="font-semibold text-slate-800 dark:text-white">{{ round(($activeMembers->count() / $tontine->max_members) * 100) }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-purple-600 rounded-full transition-all duration-500" style="width: {{ ($activeMembers->count() / $tontine->max_members) * 100 }}%"></div>
                    </div>
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4 mt-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Total parts</span>
                            <span class="font-semibold text-slate-800 dark:text-white">{{ $tontine->total_parts }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Cagnotte/tour</span>
                            <span class="font-semibold text-violet-600 dark:text-violet-400">{{ $tontine->formatted_pot_amount }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-slate-400">Cotisation de base</span>
                            <span class="font-semibold text-slate-800 dark:text-white">{{ $tontine->formatted_amount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Ajouter un membre --}}
    @if($userMember && $userMember->canManage())
    <x-modal id="add-member" maxWidth="md" title="Ajouter un membre">
        <div x-data="{
            tab: 'search',
            query: '',
            results: [],
            selectedUser: null,
            loading: false,
            searched: false,
            async search() {
                if (this.query.length < 2) {
                    this.results = [];
                    this.searched = false;
                    return;
                }
                this.loading = true;
                this.searched = true;
                try {
                    const response = await fetch('{{ route('tontines.members.search', $tontine) }}?q=' + encodeURIComponent(this.query), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.results = await response.json();
                } catch (e) {
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            },
            selectUser(user) { this.selectedUser = user; },
            clearSelection() { this.selectedUser = null; }
        }">
            {{-- Tabs --}}
            <div class="flex bg-slate-100 dark:bg-slate-700/50 rounded-xl p-1 mb-6">
                <button @click="tab = 'search'" :class="tab === 'search' ? 'bg-white dark:bg-slate-800 shadow-sm text-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:hover:text-white'" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Rechercher
                    </span>
                </button>
                <button @click="tab = 'create'" :class="tab === 'create' ? 'bg-white dark:bg-slate-800 shadow-sm text-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:hover:text-white'" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Nouveau membre
                    </span>
                </button>
            </div>

            {{-- Search Tab --}}
            <div x-show="tab === 'search'">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Rechercher un utilisateur existant</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="query" @input.debounce.300ms="search()" placeholder="Nom ou numéro de téléphone..." aria-label="Rechercher un membre" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-12 pr-4 py-3 text-slate-800 dark:text-white text-sm placeholder-slate-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-200 min-h-[44px]" />
                    </div>
                </div>

                {{-- Selected User --}}
                <div x-show="selectedUser" x-cloak class="mb-4">
                    <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-500/10 dark:to-teal-500/10 border border-emerald-200 dark:border-emerald-500/20">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-emerald-500/30">
                                <span x-text="selectedUser ? selectedUser.name.substring(0, 2).toUpperCase() : ''"></span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-white" x-text="selectedUser?.name"></p>
                                <p class="text-sm text-slate-500 dark:text-slate-400" x-text="selectedUser?.phone"></p>
                            </div>
                        </div>
                        <button @click="clearSelection()" class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Search Results --}}
                <div x-show="!selectedUser && searched" x-cloak class="mb-4">
                    <div x-show="loading" class="text-center py-8">
                        <div class="w-8 h-8 mx-auto border-3 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-3">Recherche en cours...</p>
                    </div>
                    <div x-show="!loading && results.length > 0" class="space-y-2 max-h-60 overflow-y-auto rounded-xl">
                        <template x-for="user in results" :key="user.id">
                            <button @click="selectUser(user)" class="flex items-center gap-3 w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-blue-50 dark:hover:bg-blue-500/10 border border-transparent hover:border-blue-200 dark:hover:border-blue-500/30 transition-all duration-200 text-left group">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold shadow-md">
                                    <span x-text="user.name.substring(0, 2).toUpperCase()"></span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-slate-800 dark:text-white" x-text="user.name"></p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400" x-text="user.phone"></p>
                                </div>
                                <svg class="w-5 h-5 text-slate-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </button>
                        </template>
                    </div>
                    <div x-show="!loading && results.length === 0 && query.length >= 2" class="text-center py-8">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Aucun utilisateur trouvé</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Essayez avec un autre nom ou numéro</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('tontines.members.add', $tontine) }}">
                    @csrf
                    <input type="hidden" name="user_id" x-ref="userIdInput" x-effect="$refs.userIdInput.value = selectedUser ? selectedUser.id : ''">
                    <div x-show="selectedUser" x-cloak class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nombre de parts</label>
                        <input type="number" name="parts" value="1" min="1" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-white text-sm focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-200" />
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">1 part = {{ format_amount($tontine->contribution_amount) }}/tour</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="$dispatch('close-modal-add-member')" class="px-5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">Annuler</button>
                        <button type="submit" x-bind:disabled="!selectedUser" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white hover:from-blue-600 hover:to-purple-700 shadow-lg shadow-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none transition-all duration-200">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Ajouter
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Create Tab --}}
            <div x-show="tab === 'create'" x-cloak>
                <form method="POST" action="{{ route('tontines.members.createAndAdd', $tontine) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nom complet</label>
                        <input type="text" name="name" placeholder="Ex: Kouassi Yao" required class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-white text-sm placeholder-slate-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-200" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Numéro de téléphone</label>
                        <input type="tel" name="phone" placeholder="+2250701020304" required class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-white text-sm placeholder-slate-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-200" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nombre de parts</label>
                        <input type="number" name="parts" value="1" min="1" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-white text-sm focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-200" />
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">1 part = {{ format_amount($tontine->contribution_amount) }}/tour</p>
                    </div>
                    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20">
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-amber-800 dark:text-amber-300">Information</p>
                                <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Un compte sera créé automatiquement. Le mot de passe par défaut sera les <strong>4 derniers chiffres</strong> du numéro de téléphone.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="$dispatch('close-modal-add-member')" class="px-5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">Annuler</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white hover:from-emerald-600 hover:to-teal-700 shadow-lg shadow-emerald-500/30 transition-all duration-200">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Créer et ajouter
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-modal>
    @endif

    {{-- Modals Modifier les parts --}}
    @if($userMember && $userMember->isAdmin())
        @foreach($activeMembers as $member)
        @if($member->user_id !== auth()->id())
        <x-modal id="edit-parts-{{ $member->id }}" maxWidth="sm" title="Modifier les parts">
            <form method="POST" action="{{ route('tontines.members.updateParts', [$tontine, $member]) }}">
                @csrf
                @method('PUT')
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                        <x-avatar :user="$member->user" size="md" />
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-white">{{ $member->user->name }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Actuellement {{ $member->parts }} part(s)</p>
                        </div>
                    </div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nombre de parts</label>
                    <input type="number" name="parts" value="{{ $member->parts }}" min="1" required class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-white text-sm focus:border-violet-500 dark:focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 outline-none transition-all duration-200" />
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">1 part = {{ format_amount($tontine->contribution_amount) }}/tour. Le membre sera bénéficiaire autant de fois que son nombre de parts.</p>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="$dispatch('close-modal-edit-parts-{{ $member->id }}')" class="px-5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">Annuler</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gradient-to-r from-violet-500 to-purple-600 text-white hover:from-violet-600 hover:to-purple-700 shadow-lg shadow-violet-500/30 transition-all duration-200">Enregistrer</button>
                </div>
            </form>
        </x-modal>
        @endif
        @endforeach
    @endif

    {{-- Modal SMS Invitation --}}
    @if($userMember && $userMember->canManage())
    <x-modal id="sms-invite-members" maxWidth="lg" title="Inviter par SMS">
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
                <x-button type="button" variant="ghost" @click="$dispatch('close-modal-sms-invite-members')">Annuler</x-button>
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Envoyer les invitations
                </x-button>
            </div>
        </form>
    </x-modal>
    @endif

    {{-- Modal Import CSV --}}
    @if($userMember && $userMember->canManage())
    <x-modal id="import-csv" maxWidth="md" title="Importer des membres par CSV">
        <form method="POST" action="{{ route('tontines.members.import', $tontine) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Format du fichier CSV</p>
                        <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">Le fichier doit contenir les colonnes : <strong>nom</strong>, <strong>telephone</strong>, <strong>email</strong> (optionnel).</p>
                        <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">Les numéros seront automatiquement normalisés au format +225.</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Fichier CSV</label>
                <input type="file" name="csv_file" accept=".csv,.txt" required class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-500/20 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-blue-500/30 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all duration-200" />
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Formats acceptés : .csv, .txt - Taille max : 2 Mo</p>
                @error('csv_file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <a href="{{ route('tontines.members.importTemplate', $tontine) }}" class="inline-flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Télécharger le modèle CSV
                </a>
            </div>

            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-amber-800 dark:text-amber-300">Bon à savoir</p>
                        <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Les utilisateurs existants seront retrouvés par leur numéro de téléphone. Les nouveaux comptes recevront un mot de passe temporaire par SMS. Les doublons seront ignorés.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                <button type="button" @click="$dispatch('close-modal-import-csv')" class="px-5 py-2.5 text-sm font-medium rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">Annuler</button>
                <button type="submit" style="background-color: #7c3aed; color: white;" class="px-5 py-2.5 text-sm font-medium rounded-xl hover:opacity-90 shadow-lg transition-all duration-200">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Importer
                    </span>
                </button>
            </div>
        </form>
    </x-modal>
    @endif

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 px-6 py-3 bg-slate-800 dark:bg-white text-white dark:text-slate-800 rounded-xl shadow-2xl z-50 flex items-center gap-3 animate-fade-in';
                toast.innerHTML = '<svg class="w-5 h-5 text-emerald-400 dark:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="font-medium">Code copié !</span>';
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.classList.add('animate-fade-out');
                    setTimeout(() => toast.remove(), 300);
                }, 2000);
            });
        }
    </script>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(1rem); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fade-out {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(1rem); }
        }
        .animate-fade-in { animation: fade-in 0.3s ease-out; }
        .animate-fade-out { animation: fade-out 0.3s ease-out; }
    </style>
</x-layouts.app>

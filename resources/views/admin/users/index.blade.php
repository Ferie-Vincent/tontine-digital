<x-layouts.app title="Gestion des utilisateurs">
    <x-slot:header>
        Gestion des utilisateurs
    </x-slot:header>

    {{-- Stats Cards Section --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
        {{-- Total Users Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total utilisateurs</p>
                    <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $users->total() }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Comptes enregistres</p>
                </div>
                <div class="w-14 h-14 rounded-xl primary-bg/10 flex items-center justify-center">
                    <svg class="w-7 h-7 primary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Users Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Utilisateurs actifs</p>
                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ App\Models\User::where('status', 'active')->count() }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Comptes operationnels</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Suspended Users Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Utilisateurs suspendus</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ App\Models\User::where('status', 'suspended')->count() }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Comptes desactives</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-red-500/10 flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div x-data="{
        selectedUsers: [],
        selectAll: false,
        toggleAll() {
            if (this.selectAll) {
                this.selectedUsers = [...document.querySelectorAll('input[name=user_checkbox]')].map(el => el.value);
            } else {
                this.selectedUsers = [];
            }
        },
        toggleUser(id) {
            const idx = this.selectedUsers.indexOf(id);
            if (idx > -1) {
                this.selectedUsers.splice(idx, 1);
            } else {
                this.selectedUsers.push(id);
            }
            this.selectAll = this.selectedUsers.length === document.querySelectorAll('input[name=user_checkbox]').length;
        }
    }">

    {{-- Bulk Actions Bar --}}
    <div x-show="selectedUsers.length > 0" x-cloak class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-4 flex items-center justify-between">
        <span class="text-sm text-blue-700 dark:text-blue-300" x-text="selectedUsers.length + ' utilisateur(s) selectionne(s)'"></span>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.users.bulk') }}" class="inline">
                @csrf
                <template x-for="id in selectedUsers" :key="id"><input type="hidden" name="user_ids[]" :value="id"></template>
                <button type="submit" name="action" value="activate" class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-100 rounded-lg hover:bg-emerald-200 dark:text-emerald-400 dark:bg-emerald-900/30">Activer</button>
                <button type="submit" name="action" value="suspend" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 dark:text-red-400 dark:bg-red-900/30 ml-1">Suspendre</button>
                <button type="submit" name="action" value="export" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 dark:text-blue-400 dark:bg-blue-900/30 ml-1">Exporter CSV</button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        {{-- Search & Filter Section --}}
        <div class="p-5 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
            <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
                <form method="GET" class="flex-1 w-full lg:w-auto">
                    <div class="flex flex-col sm:flex-row gap-3">
                        {{-- Search Input --}}
                        <div class="relative flex-1 min-w-0 sm:min-w-[280px]">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Rechercher par nom, telephone..."
                                class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl pl-11 pr-4 py-3 text-slate-800 dark:text-white text-sm placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all duration-200"
                            >
                        </div>

                        {{-- Status Filter --}}
                        <div class="relative min-w-[160px]">
                            <select
                                name="status"
                                class="w-full appearance-none bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 pr-10 text-slate-800 dark:text-white text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all duration-200 cursor-pointer"
                            >
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendus</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Filter Button --}}
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-medium rounded-xl border border-slate-200 dark:border-slate-600 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filtrer
                        </button>
                    </div>
                </form>

                {{-- Create User Button --}}
                <button
                    @click="$dispatch('open-modal-create-user')"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 primary-bg hover:opacity-90 text-white text-sm font-medium rounded-xl shadow-sm hover:shadow-md transition-all duration-200 whitespace-nowrap"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouvel utilisateur
                </button>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-slate-50 dark:bg-slate-900/50">
                        <th class="px-4 py-4 w-10">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500 dark:bg-slate-700">
                        </th>
                        <th class="px-6 py-4">Utilisateur</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4 text-center">Tontines</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Inscription</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @forelse($users as $user)
                    <tr class="group hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors duration-150">
                        {{-- Checkbox --}}
                        <td class="px-4 py-4">
                            <input type="checkbox" name="user_checkbox" value="{{ $user->id }}" :checked="selectedUsers.includes('{{ $user->id }}')" @change="toggleUser('{{ $user->id }}')" class="rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500 dark:bg-slate-700">
                        </td>
                        {{-- User Info --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <x-avatar :user="$user" size="md" class="ring-2 ring-white dark:ring-slate-700 shadow-sm" />
                                    @if($user->status === 'active')
                                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full"></span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-slate-800 dark:text-white font-semibold group-hover:primary-text transition-colors">{{ $user->name }}</p>
                                    @if($user->is_admin)
                                    <span class="inline-flex items-center gap-1.5 mt-1 px-2 py-0.5 bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-400 text-xs font-medium rounded-full">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Administrateur
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Contact Info --}}
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <p class="text-slate-700 dark:text-slate-200 font-medium">{{ $user->phone }}</p>
                                @if($user->email)
                                <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $user->email }}
                                </p>
                                @endif
                            </div>
                        </td>

                        {{-- Tontines Count --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2.5rem] h-10 px-3 rounded-xl primary-bg/10 primary-text text-sm font-bold">
                                {{ $user->tontine_members_count }}
                            </span>
                        </td>

                        {{-- Status Badge --}}
                        <td class="px-6 py-4">
                            @if($user->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-semibold rounded-full">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                Actif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 text-xs font-semibold rounded-full">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                Suspendu
                            </span>
                            @endif
                        </td>

                        {{-- Registration Date --}}
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <p class="text-slate-700 dark:text-slate-200 font-medium">{{ $user->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                        </td>

                        {{-- Action Buttons --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                {{-- View Profile Button --}}
                                <a
                                    href="{{ route('admin.users.show', $user) }}"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition-all duration-200"
                                    title="Voir le profil"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                @if(!$user->is_admin)
                                    @if($user->status === 'active')
                                    {{-- Suspend Button --}}
                                    <div x-data="{ showSuspendConfirm: false }">
                                        <button
                                            type="button"
                                            @click="showSuspendConfirm = true"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all duration-200"
                                            title="Suspendre"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        </button>
                                        <div x-show="showSuspendConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                            <div class="fixed inset-0 bg-black/50" @click="showSuspendConfirm = false"></div>
                                            <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95">
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Suspendre cet utilisateur ?</h3>
                                                </div>
                                                <p class="text-slate-600 dark:text-slate-400 mb-6">L'utilisateur ne pourra plus se connecter ni accéder à la plateforme.</p>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="showSuspendConfirm = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                                        Annuler
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                                            Suspendre
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    {{-- Activate Button --}}
                                    <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-all duration-200"
                                            title="Reactiver"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 font-medium mb-1">Aucun utilisateur trouve</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Modifiez vos criteres de recherche</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/30">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Affichage de <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $users->firstItem() }}</span>
                    a <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $users->lastItem() }}</span>
                    sur <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $users->total() }}</span> resultats
                </p>
                <div class="flex items-center gap-2">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>

    </div>{{-- End x-data wrapper --}}

    {{-- Create User Modal --}}
    <x-modal id="create-user" maxWidth="md" title="Nouvel utilisateur">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf

            {{-- Modal Header Icon --}}
            <div class="text-center pb-4">
                <div class="w-20 h-20 rounded-2xl primary-bg/10 flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <svg class="w-10 h-10 primary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-1">Creer un nouveau compte</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Remplissez les informations de l'utilisateur</p>
            </div>

            {{-- Form Fields --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nom complet</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ex: Kouassi Yao"
                            required
                            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl pl-11 pr-4 py-3 text-slate-800 dark:text-white text-sm placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all duration-200"
                        >
                    </div>
                    @error('name')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Numéro de téléphone</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <input
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="+2250701020304"
                            required
                            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl pl-11 pr-4 py-3 text-slate-800 dark:text-white text-sm placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all duration-200"
                        >
                    </div>
                    @error('phone')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email (optionnel)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="email@exemple.com"
                            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl pl-11 pr-4 py-3 text-slate-800 dark:text-white text-sm placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all duration-200"
                        >
                    </div>
                    @error('email')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Password Info Alert --}}
            <div class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-300">Mot de passe par défaut</p>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">Les <strong>4 derniers chiffres</strong> du numéro de téléphone seront utilisés comme mot de passe initial.</p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                <button
                    type="button"
                    @click="$dispatch('close-modal-create-user')"
                    class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all duration-200"
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 primary-bg hover:opacity-90 text-white text-sm font-medium rounded-xl shadow-sm hover:shadow-md transition-all duration-200"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Creer l'utilisateur
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Auto-open modal on validation errors --}}
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new Event('open-modal-create-user'));
        });
    </script>
    @endif
</x-layouts.app>

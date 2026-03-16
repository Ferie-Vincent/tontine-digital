<x-layouts.app title="Paramètres">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center shadow-lg shadow-slate-500/25">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Paramètres</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Gérez votre compte et vos préférences</p>
            </div>
        </div>
    </x-slot:header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="max-w-5xl mx-auto mb-4">
            <x-alert type="success">{{ session('success') }}</x-alert>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-5xl mx-auto mb-4">
            <x-alert type="error">{{ session('error') }}</x-alert>
        </div>
    @endif

    <div class="max-w-5xl mx-auto" x-data="{ activeTab: '{{ $errors->any() ? ($errors->has('current_password') || $errors->has('password') ? 'password' : ($errors->has('name') || $errors->has('phone') || $errors->has('email') || $errors->has('avatar') ? 'profile' : 'profile')) : 'profile' }}' }">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar navigation (desktop) / Horizontal tabs (mobile) --}}
            <nav class="lg:w-56 flex-shrink-0">
                <div class="flex lg:flex-col gap-1 overflow-x-auto lg:overflow-visible bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-2">
                    <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700'" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap w-full text-left transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil
                    </button>
                    <button @click="activeTab = 'password'" :class="activeTab === 'password' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700'" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap w-full text-left transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Mot de passe
                    </button>
                    <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700'" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap w-full text-left transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Notifications
                    </button>
                    <button @click="activeTab = 'appearance'" :class="activeTab === 'appearance' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700'" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap w-full text-left transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        Apparence
                    </button>
                    <button @click="activeTab = 'sessions'" :class="activeTab === 'sessions' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700'" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap w-full text-left transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Appareils
                    </button>
                    @if(auth()->user()->is_admin)
                    <div class="hidden lg:block h-px bg-slate-200 dark:bg-slate-700 my-1"></div>
                    <button @click="activeTab = 'admin'" :class="activeTab === 'admin' ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700'" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap w-full text-left transition-colors">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Administration
                    </button>
                    @endif
                </div>
            </nav>

            {{-- Content area --}}
            <div class="flex-1 min-w-0">

                {{-- ======================= PROFIL ======================= --}}
                <div x-show="activeTab === 'profile'">
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Informations du profil</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Mettez à jour vos informations personnelles</p>
                        </div>
                        <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data" class="p-5">
                            @csrf
                            @method('PUT')

                            {{-- Avatar --}}
                            <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-200 dark:border-slate-700" x-data="{ preview: null }">
                                <div class="relative flex-shrink-0">
                                    <template x-if="preview">
                                        <img :src="preview" class="w-16 h-16 rounded-full object-cover ring-4 ring-primary-500/20" />
                                    </template>
                                    <template x-if="!preview">
                                        <x-avatar :src="auth()->user()->avatar_url" :name="auth()->user()->name" size="xl" />
                                    </template>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Photo de profil</p>
                                    <div class="flex items-center gap-2">
                                        <label class="cursor-pointer px-3 py-1.5 text-sm font-medium rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                Changer
                                            </span>
                                            <input type="file" name="avatar" accept="image/*" class="hidden"
                                                @change="const file = $event.target.files[0]; if(file) { preview = URL.createObjectURL(file) }" />
                                        </label>
                                        @if(auth()->user()->avatar)
                                        <button type="button" onclick="document.getElementById('remove-avatar-form').submit()"
                                            class="px-3 py-1.5 text-sm font-medium rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                            Supprimer
                                        </button>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">JPG, PNG ou GIF. Max 2 Mo.</p>
                                    @error('avatar')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nom complet</label>
                                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                        class="w-full bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl px-4 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all"
                                        required />
                                    @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Numéro de téléphone</label>
                                    <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                                        class="w-full bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl px-4 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all"
                                        required />
                                    @error('phone')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Email (optionnel)</label>
                                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                        class="w-full bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl px-4 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all"
                                        placeholder="exemple@email.com" />
                                    @error('email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Account info inline --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Membre depuis</p>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white mt-0.5">{{ auth()->user()->created_at->translatedFormat('d M Y') }}</p>
                                </div>
                                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Statut</p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Actif</p>
                                    </div>
                                </div>
                                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tontines</p>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white mt-0.5">{{ auth()->user()->tontines()->count() }}</p>
                                </div>
                                <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider">Connexion</p>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white mt-0.5">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Maintenant' }}</p>
                                </div>
                            </div>

                            <div class="flex justify-end mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                <button type="submit" class="px-5 py-2 text-sm font-medium rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 text-white hover:from-primary-600 hover:to-primary-700 shadow-lg shadow-primary-500/30 transition-all duration-300">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Enregistrer
                                    </span>
                                </button>
                            </div>
                        </form>
                        <form id="remove-avatar-form" method="POST" action="{{ route('settings.avatar.remove') }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                {{-- ======================= MOT DE PASSE ======================= --}}
                <div x-show="activeTab === 'password'" x-cloak>
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Mot de passe</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Sécurisez votre compte avec un mot de passe fort</p>
                        </div>
                        <form method="POST" action="{{ route('settings.password') }}" class="p-5">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Mot de passe actuel</label>
                                    <input type="password" name="current_password"
                                        class="w-full bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl px-4 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all"
                                        required />
                                    @error('current_password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nouveau mot de passe</label>
                                        <input type="password" name="password"
                                            class="w-full bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl px-4 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all"
                                            required />
                                        @error('password')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Confirmer le mot de passe</label>
                                        <input type="password" name="password_confirmation"
                                            class="w-full bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl px-4 py-2.5 text-slate-800 dark:text-white placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all"
                                            required />
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                <button type="submit" class="px-5 py-2 text-sm font-medium rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white hover:from-amber-600 hover:to-orange-600 shadow-lg shadow-amber-500/30 transition-all duration-300">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        Changer le mot de passe
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ======================= NOTIFICATIONS ======================= --}}
                <div x-show="activeTab === 'notifications'" x-cloak>
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Notifications</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Gérez vos préférences de notifications</p>
                        </div>
                        <form method="POST" action="{{ route('settings.notifications') }}" class="p-5">
                            @csrf
                            @method('PUT')
                            @php
                                $notifPrefs = auth()->user()->notification_preferences ?? [];
                            @endphp

                            {{-- Notification toggles --}}
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800 dark:text-white">Contributions</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Rappels de paiement et confirmations</p>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" name="notify_contributions" value="1" {{ ($notifPrefs['contributions'] ?? true) ? 'checked' : '' }} class="sr-only peer" />
                                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer peer-checked:bg-emerald-500 transition-colors"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm peer-checked:translate-x-5 transition-transform"></div>
                                    </div>
                                </label>

                                <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800 dark:text-white">Tours</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Début de tour et attribution du bénéficiaire</p>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" name="notify_tours" value="1" {{ ($notifPrefs['tours'] ?? true) ? 'checked' : '' }} class="sr-only peer" />
                                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer peer-checked:bg-blue-500 transition-colors"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm peer-checked:translate-x-5 transition-transform"></div>
                                    </div>
                                </label>

                                <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800 dark:text-white">Membres</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Nouveaux membres et demandes d'adhésion</p>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" name="notify_members" value="1" {{ ($notifPrefs['members'] ?? true) ? 'checked' : '' }} class="sr-only peer" />
                                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer peer-checked:bg-purple-500 transition-colors"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-sm peer-checked:translate-x-5 transition-transform"></div>
                                    </div>
                                </label>
                            </div>

                            {{-- Email digest --}}
                            <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Fréquence des emails</label>
                                        <select name="notification_digest" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white">
                                            <option value="instant" {{ auth()->user()->notification_digest === 'instant' ? 'selected' : '' }}>Instantané (chaque notification)</option>
                                            <option value="daily" {{ auth()->user()->notification_digest === 'daily' ? 'selected' : '' }}>Résumé quotidien</option>
                                            <option value="weekly" {{ auth()->user()->notification_digest === 'weekly' ? 'selected' : '' }}>Résumé hebdomadaire</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Push notifications --}}
                            <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700"
                                 x-data="{
                                     supported: false,
                                     subscribed: false,
                                     loading: true,
                                     async init() {
                                         this.supported = window.isPushSupported();
                                         if (this.supported) {
                                             this.subscribed = await window.isPushSubscribed();
                                         }
                                         this.loading = false;
                                     },
                                     async toggle() {
                                         this.loading = true;
                                         if (this.subscribed) {
                                             const ok = await window.unsubscribeFromPush();
                                             if (ok) this.subscribed = false;
                                         } else {
                                             const ok = await window.subscribeToPush();
                                             if (ok) this.subscribed = true;
                                         }
                                         this.loading = false;
                                     }
                                 }">
                                <template x-if="!supported">
                                    <div class="flex items-center gap-2 p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        <p class="text-sm">Les notifications push ne sont pas supportées par votre navigateur.</p>
                                    </div>
                                </template>
                                <template x-if="supported">
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" :class="subscribed ? 'bg-emerald-100 dark:bg-emerald-500/20' : 'bg-slate-200 dark:bg-slate-600'">
                                                <svg class="w-4 h-4" :class="subscribed ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-slate-800 dark:text-white" x-text="subscribed ? 'Push activées' : 'Push désactivées'"></p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400" x-text="subscribed ? 'Vous recevez des notifications push' : 'Activez pour recevoir des alertes en temps réel'"></p>
                                            </div>
                                        </div>
                                        <button type="button" @click="toggle()" :disabled="loading" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none disabled:opacity-50" :class="subscribed ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'">
                                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="subscribed ? 'translate-x-5' : 'translate-x-0'"></span>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <div class="flex justify-end mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                <button type="submit" class="px-5 py-2 text-sm font-medium rounded-xl bg-gradient-to-r from-violet-500 to-purple-600 text-white hover:from-violet-600 hover:to-purple-700 shadow-lg shadow-violet-500/30 transition-all duration-300">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Enregistrer les préférences
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ======================= APPARENCE ======================= --}}
                <div x-show="activeTab === 'appearance'" x-cloak>
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Apparence</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Personnalisez l'apparence de l'application</p>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <button onclick="setTheme('light')" class="group p-3 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-primary-500 dark:hover:border-primary-500 transition-all duration-300 text-left" id="theme-light">
                                    <div class="w-full h-16 rounded-lg bg-gradient-to-br from-slate-100 to-white border border-slate-200 mb-2 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-white">Clair</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Thème lumineux</p>
                                </button>

                                <button onclick="setTheme('dark')" class="group p-3 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-primary-500 dark:hover:border-primary-500 transition-all duration-300 text-left" id="theme-dark">
                                    <div class="w-full h-16 rounded-lg bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 mb-2 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-white">Sombre</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Thème foncé</p>
                                </button>

                                <button onclick="setTheme('system')" class="group p-3 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-primary-500 dark:hover:border-primary-500 transition-all duration-300 text-left" id="theme-system">
                                    <div class="w-full h-16 rounded-lg bg-gradient-to-r from-slate-100 via-slate-400 to-slate-800 border border-slate-300 mb-2 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-white">Système</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Suit le système</p>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ======================= APPAREILS / SESSIONS ======================= --}}
                <div x-show="activeTab === 'sessions'" x-cloak>
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Appareils connectés</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Gérez les sessions actives de votre compte</p>
                        </div>
                        <div class="p-5">
                            <div class="space-y-2">
                                @forelse($sessions ?? [] as $session)
                                <div class="flex items-center justify-between p-3 rounded-xl {{ $session->session_id === session()->getId() ? 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-700/50' }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full {{ $session->session_id === session()->getId() ? 'bg-emerald-200 dark:bg-emerald-800' : 'bg-slate-200 dark:bg-slate-600' }} flex items-center justify-center">
                                            @if($session->isMobile())
                                            <svg class="w-4 h-4 {{ $session->session_id === session()->getId() ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            @else
                                            <svg class="w-4 h-4 {{ $session->session_id === session()->getId() ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $session->device_name ?? 'Appareil inconnu' }}
                                                @if($session->session_id === session()->getId())
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 ml-1">cet appareil</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $session->ip_address ?? 'IP inconnue' }}
                                                &middot;
                                                {{ $session->last_activity ? $session->last_activity->diffForHumans() : 'Activité inconnue' }}
                                            </p>
                                        </div>
                                    </div>
                                    @if($session->session_id !== session()->getId())
                                    <form method="POST" action="{{ route('settings.sessions.destroy', $session) }}" onsubmit="return confirm('Voulez-vous vraiment déconnecter cet appareil ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:text-white bg-red-50 hover:bg-red-600 dark:text-red-400 dark:bg-red-500/10 dark:hover:bg-red-600 dark:hover:text-white rounded-lg transition-all duration-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Déconnecter
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                @empty
                                <div class="text-center py-6">
                                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Aucune session active trouvée.</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Les sessions seront enregistrées lors de vos prochaines connexions.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ======================= ADMIN ======================= --}}
                @if(auth()->user()->is_admin)
                <div x-show="activeTab === 'admin'" x-cloak>
                    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        {{-- Plateforme --}}
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-indigo-500/5 to-purple-500/5">
                                <h3 class="font-semibold text-slate-800 dark:text-white">Plateforme</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Identité et informations générales</p>
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <label for="platform_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nom de la plateforme</label>
                                    <input type="text" name="platform_name" id="platform_name"
                                        value="{{ \App\Models\SiteSettings::get('platform_name', 'DIGI-TONTINE CI') }}"
                                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="support_phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Téléphone du support</label>
                                        <input type="text" name="support_phone" id="support_phone"
                                            value="{{ \App\Models\SiteSettings::get('support_phone', '') }}"
                                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                            placeholder="+2250700000000">
                                    </div>
                                    <div>
                                        <label for="support_email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email du support</label>
                                        <input type="email" name="support_email" id="support_email"
                                            value="{{ \App\Models\SiteSettings::get('support_email', '') }}"
                                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                            placeholder="support@digitontine.ci">
                                    </div>
                                </div>
                                {{-- Maintenance mode --}}
                                <div class="flex items-center justify-between p-3 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20"
                                     x-data="{ enabled: {{ \App\Models\SiteSettings::getBoolean('maintenance_mode') ? 'true' : 'false' }} }">
                                    <div class="flex-1 pr-4">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <p class="text-sm font-medium text-red-800 dark:text-red-300">Mode maintenance</p>
                                        </div>
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">Seuls les administrateurs pourront se connecter.</p>
                                    </div>
                                    <input type="hidden" name="maintenance_mode" :value="enabled ? '1' : '0'">
                                    <button type="button" @click="enabled = !enabled"
                                        :class="enabled ? 'bg-red-500' : 'bg-slate-300 dark:bg-slate-600'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                                        <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Gestion des tontines --}}
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-500/5 to-cyan-500/5">
                                <h3 class="font-semibold text-slate-800 dark:text-white">Gestion des tontines</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Permissions et limites</p>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30"
                                     x-data="{ enabled: {{ \App\Models\SiteSettings::getBoolean('allow_user_tontine_creation') ? 'true' : 'false' }} }">
                                    <div class="flex-1 pr-4">
                                        <p class="text-sm font-medium text-slate-800 dark:text-white">Création par les utilisateurs</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Permet à tous les utilisateurs de créer des tontines.</p>
                                    </div>
                                    <input type="hidden" name="allow_user_tontine_creation" :value="enabled ? '1' : '0'">
                                    <button type="button" @click="enabled = !enabled"
                                        :class="enabled ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-600'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                                        <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <div>
                                        <label for="max_tontines_per_user" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Max tontines / user</label>
                                        <input type="number" name="max_tontines_per_user" id="max_tontines_per_user" min="1" max="50"
                                            value="{{ \App\Models\SiteSettings::get('max_tontines_per_user', 5) }}"
                                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label for="default_max_members" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Membres max</label>
                                        <input type="number" name="default_max_members" id="default_max_members" min="2" max="100"
                                            value="{{ \App\Models\SiteSettings::get('default_max_members', 20) }}"
                                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label for="min_contribution_amount" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Cotis. min (FCFA)</label>
                                        <input type="number" name="min_contribution_amount" id="min_contribution_amount" min="100" step="500" inputmode="numeric"
                                            value="{{ \App\Models\SiteSettings::get('min_contribution_amount', 1000) }}"
                                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label for="max_contribution_amount" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Cotis. max (FCFA)</label>
                                        <input type="number" name="max_contribution_amount" id="max_contribution_amount" min="1000" step="1000" inputmode="numeric"
                                            value="{{ \App\Models\SiteSettings::get('max_contribution_amount', 1000000) }}"
                                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sécurité --}}
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-amber-500/5 to-orange-500/5">
                                <h3 class="font-semibold text-slate-800 dark:text-white">Sécurité & Inscriptions</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Contrôle d'accès</p>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30"
                                     x-data="{ enabled: {{ \App\Models\SiteSettings::getBoolean('allow_registration', true) ? 'true' : 'false' }} }">
                                    <div class="flex-1 pr-4">
                                        <p class="text-sm font-medium text-slate-800 dark:text-white">Inscriptions publiques</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Permet aux nouveaux utilisateurs de s'inscrire.</p>
                                    </div>
                                    <input type="hidden" name="allow_registration" :value="enabled ? '1' : '0'">
                                    <button type="button" @click="enabled = !enabled"
                                        :class="enabled ? 'bg-amber-500' : 'bg-slate-300 dark:bg-slate-600'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                                        <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                                    </button>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30"
                                     x-data="{ enabled: {{ \App\Models\SiteSettings::getBoolean('require_phone_verification') ? 'true' : 'false' }} }">
                                    <div class="flex-1 pr-4">
                                        <p class="text-sm font-medium text-slate-800 dark:text-white">Vérification téléphone obligatoire</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Exige la vérification par SMS avant utilisation.</p>
                                    </div>
                                    <input type="hidden" name="require_phone_verification" :value="enabled ? '1' : '0'">
                                    <button type="button" @click="enabled = !enabled"
                                        :class="enabled ? 'bg-amber-500' : 'bg-slate-300 dark:bg-slate-600'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                                        <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                                    </button>
                                </div>
                                <div>
                                    <label for="max_login_attempts" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tentatives de connexion avant blocage</label>
                                    <div class="flex items-center gap-3">
                                        <input type="number" name="max_login_attempts" id="max_login_attempts" min="3" max="20"
                                            value="{{ \App\Models\SiteSettings::get('max_login_attempts', 5) }}"
                                            class="w-28 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors">
                                        <span class="text-sm text-slate-500 dark:text-slate-400">tentatives</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Notifications globales --}}
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-emerald-500/5 to-green-500/5">
                                <h3 class="font-semibold text-slate-800 dark:text-white">Notifications globales</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Canaux et rappels</p>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30"
                                     x-data="{ enabled: {{ \App\Models\SiteSettings::getBoolean('enable_email_notifications', true) ? 'true' : 'false' }} }">
                                    <div class="flex items-center gap-2 flex-1 pr-4">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <p class="text-sm font-medium text-slate-800 dark:text-white">Notifications email</p>
                                    </div>
                                    <input type="hidden" name="enable_email_notifications" :value="enabled ? '1' : '0'">
                                    <button type="button" @click="enabled = !enabled"
                                        :class="enabled ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                                        <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                                    </button>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/30"
                                     x-data="{ enabled: {{ \App\Models\SiteSettings::getBoolean('enable_push_notifications', true) ? 'true' : 'false' }} }">
                                    <div class="flex items-center gap-2 flex-1 pr-4">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <p class="text-sm font-medium text-slate-800 dark:text-white">Notifications push</p>
                                    </div>
                                    <input type="hidden" name="enable_push_notifications" :value="enabled ? '1' : '0'">
                                    <button type="button" @click="enabled = !enabled"
                                        :class="enabled ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800">
                                        <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                                    </button>
                                </div>
                                <div>
                                    <label for="reminder_default_days" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jours de rappel par défaut</label>
                                    <input type="text" name="reminder_default_days" id="reminder_default_days"
                                        value="{{ \App\Models\SiteSettings::get('reminder_default_days', '3,1,0') }}"
                                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                        placeholder="3,1,0">
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Ex: <strong>3,1,0</strong> = rappel à J-3, J-1 et le jour J</p>
                                </div>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-slate-400 dark:text-slate-500">Les modifications prennent effet immédiatement.</p>
                            <button type="submit" class="px-5 py-2 text-sm font-medium rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 shadow-lg shadow-indigo-500/30 transition-all duration-300">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Enregistrer
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
                @endif

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function setTheme(theme) {
            if (theme === 'system') {
                localStorage.removeItem('theme');
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } else if (theme === 'dark') {
                localStorage.setItem('theme', 'dark');
                document.documentElement.classList.add('dark');
            } else {
                localStorage.setItem('theme', 'light');
                document.documentElement.classList.remove('dark');
            }
            updateThemeButtons();
        }

        function updateThemeButtons() {
            const theme = localStorage.getItem('theme');
            document.getElementById('theme-light').classList.remove('border-primary-500', 'ring-2', 'ring-primary-500/20');
            document.getElementById('theme-dark').classList.remove('border-primary-500', 'ring-2', 'ring-primary-500/20');
            document.getElementById('theme-system').classList.remove('border-primary-500', 'ring-2', 'ring-primary-500/20');

            if (theme === 'light') {
                document.getElementById('theme-light').classList.add('border-primary-500', 'ring-2', 'ring-primary-500/20');
            } else if (theme === 'dark') {
                document.getElementById('theme-dark').classList.add('border-primary-500', 'ring-2', 'ring-primary-500/20');
            } else {
                document.getElementById('theme-system').classList.add('border-primary-500', 'ring-2', 'ring-primary-500/20');
            }
        }

        document.addEventListener('DOMContentLoaded', updateThemeButtons);
    </script>
    @endpush
</x-layouts.app>

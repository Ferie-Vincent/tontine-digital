<x-layouts.app title="Paramètres de la plateforme">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/25">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Administration</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Configuration globale de la plateforme</p>
            </div>
        </div>
    </x-slot:header>

    <div class="max-w-6xl mx-auto" x-data="{ activeTab: 'general' }">

        @if(session('success'))
            <div class="mb-4">
                <x-alert type="success" :message="session('success')" />
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4">
                <x-alert type="error" :message="session('error')" />
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar Navigation --}}
            <div class="lg:w-56 shrink-0">
                {{-- Mobile: horizontal scroll tabs --}}
                <div class="lg:hidden flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                    <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'" class="whitespace-nowrap px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                        Général
                    </button>
                    <button @click="activeTab = 'tontines'" :class="activeTab === 'tontines' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'" class="whitespace-nowrap px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                        Tontines
                    </button>
                    <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'" class="whitespace-nowrap px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                        Sécurité
                    </button>
                    <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'" class="whitespace-nowrap px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                        Notifications
                    </button>
                    <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'" class="whitespace-nowrap px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                        Paiements
                    </button>
                    <button @click="activeTab = 'sms'" :class="activeTab === 'sms' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'" class="whitespace-nowrap px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                        SMS
                    </button>
                    <button @click="activeTab = 'whatsapp'" :class="activeTab === 'whatsapp' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'" class="whitespace-nowrap px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                        WhatsApp
                    </button>
                </div>

                {{-- Desktop: vertical sidebar --}}
                <nav class="hidden lg:block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-2 sticky top-24 space-y-1">
                    <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors">
                        <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Général
                    </button>
                    <button @click="activeTab = 'tontines'" :class="activeTab === 'tontines' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors">
                        <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Tontines
                    </button>
                    <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors">
                        <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Sécurité
                    </button>
                    <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors">
                        <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Notifications
                    </button>
                    <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors">
                        <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Paiements
                    </button>

                    <div class="border-t border-slate-200 dark:border-slate-700 my-2 !mt-3"></div>
                    <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Messagerie</p>

                    <button @click="activeTab = 'sms'" :class="activeTab === 'sms' ? 'bg-teal-50 dark:bg-teal-500/10 text-teal-700 dark:text-teal-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors">
                        <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        SMS
                    </button>
                    <button @click="activeTab = 'whatsapp'" :class="activeTab === 'whatsapp' ? 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors">
                        <svg class="w-[18px] h-[18px] shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </button>
                </nav>

                {{-- Stats mini card (desktop only) --}}
                <div class="hidden lg:block mt-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3">Aperçu</h4>
                    @php
                        $totalUsers = \App\Models\User::count();
                        $activeUsers = \App\Models\User::where('status', 'active')->count();
                        $totalTontines = \App\Models\Tontine::count();
                        $activeTontines = \App\Models\Tontine::where('status', 'active')->count();
                    @endphp
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Utilisateurs</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $totalUsers }}</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $totalUsers > 0 ? round($activeUsers / $totalUsers * 100) : 0 }}%"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Tontines</span>
                            <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $totalTontines }}</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $totalTontines > 0 ? round($activeTontines / $totalTontines * 100) : 0 }}%"></div>
                        </div>
                        <p class="text-[10px] text-slate-400">{{ $activeUsers }} actifs / {{ $activeTontines }} tontines actives</p>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB: Général --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                                <h3 class="font-semibold text-slate-800 dark:text-white">Plateforme</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Identité et informations générales de la plateforme</p>
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nom de la plateforme</label>
                                    <input type="text" name="platform_name" value="{{ \App\Models\SiteSettings::get('platform_name', 'DIGI-TONTINE CI') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    <p class="text-xs text-slate-400 mt-1">Affiché dans l'interface et les notifications</p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Téléphone du support</label>
                                        <input type="text" name="support_phone" value="{{ \App\Models\SiteSettings::get('support_phone', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email du support</label>
                                        <input type="email" name="support_email" value="{{ \App\Models\SiteSettings::get('support_email', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 dark:bg-red-500/5 border border-red-200 dark:border-red-500/20">
                                    <div>
                                        <p class="text-sm font-medium text-red-700 dark:text-red-400">Mode maintenance</p>
                                        <p class="text-xs text-red-500 dark:text-red-400/70 mt-0.5">Seuls les administrateurs pourront se connecter</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="maintenance_mode" value="1" {{ \App\Models\SiteSettings::get('maintenance_mode', '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-red-300 dark:peer-focus:ring-red-800 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-red-600"></div>
                                    </label>
                                </div>
                            </div>

                            {{-- Save button --}}
                            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg shadow-sm transition-colors" style="background-color: #4f46e5; color: white;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB: Tontines --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div x-show="activeTab === 'tontines'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                                <h3 class="font-semibold text-slate-800 dark:text-white">Gestion des tontines</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Permissions et limites pour les tontines</p>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                                    <div>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Création par les utilisateurs</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Permet à tous les utilisateurs de créer leur propre tontine</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="allow_user_tontine_creation" value="1" {{ \App\Models\SiteSettings::get('allow_user_tontine_creation', '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Max tontines/user</label>
                                        <input type="number" name="max_tontines_per_user" value="{{ \App\Models\SiteSettings::get('max_tontines_per_user', '5') }}" min="1" max="50" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-colors" inputmode="numeric">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Membres max</label>
                                        <input type="number" name="default_max_members" value="{{ \App\Models\SiteSettings::get('default_max_members', '20') }}" min="2" max="100" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-colors" inputmode="numeric">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Cotisation min</label>
                                        <input type="number" name="min_contribution_amount" value="{{ \App\Models\SiteSettings::get('min_contribution_amount', '1000') }}" min="100" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-colors" inputmode="numeric">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Cotisation max</label>
                                        <input type="number" name="max_contribution_amount" value="{{ \App\Models\SiteSettings::get('max_contribution_amount', '1000000') }}" min="1000" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-colors" inputmode="numeric">
                                    </div>
                                </div>
                                <p class="text-xs text-slate-400">Les cotisations sont en FCFA. Les limites s'appliquent lors de la création d'une tontine.</p>
                            </div>

                            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg shadow-sm transition-colors" style="background-color: #4f46e5; color: white;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB: Sécurité --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div x-show="activeTab === 'security'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                                <h3 class="font-semibold text-slate-800 dark:text-white">Sécurité & Inscriptions</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Contrôle d'accès et politique de sécurité</p>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                                    <div>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Inscriptions publiques</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Permet aux nouveaux utilisateurs de s'inscrire librement</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="allow_registration" value="1" {{ \App\Models\SiteSettings::get('allow_registration', '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                                    <div>
                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Vérification du téléphone</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Exige la vérification SMS avant d'utiliser la plateforme</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="require_phone_verification" value="1" {{ \App\Models\SiteSettings::get('require_phone_verification', '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tentatives de connexion avant blocage</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="max_login_attempts" value="{{ \App\Models\SiteSettings::get('max_login_attempts', '5') }}" min="3" max="20" class="w-24 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-colors" inputmode="numeric">
                                        <span class="text-sm text-slate-500 dark:text-slate-400">tentatives (blocage 15 min)</span>
                                    </div>
                                </div>
                            </div>

                            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg shadow-sm transition-colors" style="background-color: #4f46e5; color: white;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB: Notifications --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div x-show="activeTab === 'notifications'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                                <h3 class="font-semibold text-slate-800 dark:text-white">Notifications</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Canaux et paramètres de notification globaux</p>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Notifications par email</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Rappels, confirmations, alertes</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="enable_email_notifications" value="1" {{ \App\Models\SiteSettings::get('enable_email_notifications', '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/10 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Notifications push</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Navigateur et mobile</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="enable_push_notifications" value="1" {{ \App\Models\SiteSettings::get('enable_push_notifications', '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Jours de rappel par défaut</label>
                                    <input type="text" name="default_reminder_days" value="{{ \App\Models\SiteSettings::get('default_reminder_days', '3,1,0') }}" class="w-full sm:w-64 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 transition-colors">
                                    <p class="text-xs text-slate-400 mt-1">Jours avant l'échéance (virgules). Ex: <code class="text-indigo-500">3,1,0</code> = J-3, J-1, Jour J</p>
                                </div>
                            </div>

                            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg shadow-sm transition-colors" style="background-color: #4f46e5; color: white;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB: Paiements --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div x-show="activeTab === 'payments'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                                <h3 class="font-semibold text-slate-800 dark:text-white">Passerelles de paiement</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Clés API des opérateurs mobile money</p>
                            </div>
                            <div class="p-5 space-y-3" x-data="{ expanded: null }">
                                @php
                                    $gateways = [
                                        ['key' => 'orange_money', 'name' => 'Orange Money', 'short' => 'OM', 'color' => 'orange', 'bg' => 'bg-orange-500'],
                                        ['key' => 'mtn_momo', 'name' => 'MTN MoMo', 'short' => 'MTN', 'color' => 'yellow', 'bg' => 'bg-yellow-500'],
                                        ['key' => 'moov_money', 'name' => 'Moov Money', 'short' => 'MV', 'color' => 'blue', 'bg' => 'bg-blue-500'],
                                        ['key' => 'wave', 'name' => 'Wave', 'short' => 'W', 'color' => 'cyan', 'bg' => 'bg-cyan-500'],
                                    ];
                                @endphp

                                @foreach($gateways as $gw)
                                <div class="rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                                    <div class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors" @click="expanded = expanded === '{{ $gw['key'] }}' ? null : '{{ $gw['key'] }}'">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-lg {{ $gw['bg'] }} text-white text-xs font-bold flex items-center justify-center">{{ $gw['short'] }}</span>
                                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $gw['name'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                                                <input type="checkbox" name="{{ $gw['key'] }}_enabled" value="1" {{ \App\Models\SiteSettings::get($gw['key'] . '_enabled', '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                            </label>
                                            <svg class="w-4 h-4 text-slate-400 transition-transform" :class="expanded === '{{ $gw['key'] }}' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </div>
                                    </div>
                                    <div x-show="expanded === '{{ $gw['key'] }}'" x-collapse class="px-4 pb-4 pt-1 border-t border-slate-100 dark:border-slate-700">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">API Key</label>
                                                <input type="password" name="{{ $gw['key'] }}_api_key" value="{{ \App\Models\SiteSettings::get($gw['key'] . '_api_key', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-colors">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">API Secret</label>
                                                <input type="password" name="{{ $gw['key'] }}_api_secret" value="{{ \App\Models\SiteSettings::get($gw['key'] . '_api_secret', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-colors">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <p class="text-xs text-slate-400 pt-1">Les clés API sont stockées en base. Le fallback manuel reste disponible si l'API ne peut confirmer automatiquement.</p>
                            </div>

                            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex justify-end">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg shadow-sm transition-colors" style="background-color: #4f46e5; color: white;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB: SMS --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div x-show="activeTab === 'sms'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden" x-data="{ smsProvider: '{{ \App\Models\SiteSettings::get('sms_provider', 'disabled') }}' }">
                            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-teal-500/5 to-cyan-500/5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-teal-500/10 flex items-center justify-center">
                                        <svg class="w-[18px] h-[18px] text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-slate-800 dark:text-white">Service SMS</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Rappels, alertes et codes de vérification par SMS</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Fournisseur</label>
                                    <select name="sms_provider" x-model="smsProvider" class="w-full sm:w-72 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
                                        <option value="disabled">Désactivé</option>
                                        <option value="twilio">Twilio (International)</option>
                                        <option value="infobip">Infobip (International)</option>
                                        <option value="orange_sms">Orange SMS (Côte d'Ivoire)</option>
                                        <option value="letexto">Letexto (Côte d'Ivoire)</option>
                                    </select>
                                </div>

                                {{-- Twilio --}}
                                <div x-show="smsProvider === 'twilio'" x-collapse class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/30 space-y-3">
                                    <h4 class="text-sm font-medium text-slate-800 dark:text-white">Configuration Twilio</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Account SID</label>
                                            <input type="text" name="sms_twilio_sid" value="{{ \App\Models\SiteSettings::get('sms_twilio_sid', '') }}" placeholder="ACxxxxxxxxxxxxxxx" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Auth Token</label>
                                            <input type="password" name="sms_twilio_token" value="{{ \App\Models\SiteSettings::get('sms_twilio_token', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Numéro expéditeur</label>
                                        <input type="text" name="sms_twilio_from" value="{{ \App\Models\SiteSettings::get('sms_twilio_from', '') }}" placeholder="+1234567890" class="w-full sm:w-64 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                </div>

                                {{-- Infobip --}}
                                <div x-show="smsProvider === 'infobip'" x-collapse class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/30 space-y-3">
                                    <h4 class="text-sm font-medium text-slate-800 dark:text-white">Configuration Infobip</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">API Key</label>
                                            <input type="password" name="sms_infobip_api_key" value="{{ \App\Models\SiteSettings::get('sms_infobip_api_key', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Nom expéditeur</label>
                                            <input type="text" name="sms_infobip_sender" value="{{ \App\Models\SiteSettings::get('sms_infobip_sender', 'TONTINE') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Base URL</label>
                                        <input type="text" name="sms_infobip_base_url" value="{{ \App\Models\SiteSettings::get('sms_infobip_base_url', 'https://api.infobip.com') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                </div>

                                {{-- Orange SMS --}}
                                <div x-show="smsProvider === 'orange_sms'" x-collapse class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/30 space-y-3">
                                    <h4 class="text-sm font-medium text-slate-800 dark:text-white">Configuration Orange SMS</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">API Key</label>
                                            <input type="password" name="sms_orange_api_key" value="{{ \App\Models\SiteSettings::get('sms_orange_api_key', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">API Secret</label>
                                            <input type="password" name="sms_orange_api_secret" value="{{ \App\Models\SiteSettings::get('sms_orange_api_secret', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Sender Address</label>
                                            <input type="text" name="sms_orange_sender_address" value="{{ \App\Models\SiteSettings::get('sms_orange_sender_address', '') }}" placeholder="tel:+2250000" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Base URL</label>
                                            <input type="text" name="sms_orange_base_url" value="{{ \App\Models\SiteSettings::get('sms_orange_base_url', 'https://api.orange.com/smsmessaging/v1') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                        </div>
                                    </div>
                                </div>

                                {{-- Letexto --}}
                                <div x-show="smsProvider === 'letexto'" x-collapse class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/30 space-y-3">
                                    <h4 class="text-sm font-medium text-slate-800 dark:text-white">Configuration Letexto</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">API Key</label>
                                            <input type="password" name="sms_letexto_api_key" value="{{ \App\Models\SiteSettings::get('sms_letexto_api_key', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Nom expéditeur</label>
                                            <input type="text" name="sms_letexto_sender" value="{{ \App\Models\SiteSettings::get('sms_letexto_sender', 'TONTINE') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Base URL</label>
                                        <input type="text" name="sms_letexto_base_url" value="{{ \App\Models\SiteSettings::get('sms_letexto_base_url', 'https://api.letexto.com/v1') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-teal-500">
                                    </div>
                                </div>
                            </div>

                            {{-- Test SMS (outside main form) --}}
                            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3 flex-wrap">
                                <div x-show="smsProvider !== 'disabled'" class="flex items-center gap-2 flex-1 min-w-0">
                                    <input type="tel" form="sms-test-form" name="test_phone" placeholder="Numéro de test" class="w-44 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm" inputmode="tel">
                                    <button type="submit" form="sms-test-form" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition-colors" style="background-color: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        Tester
                                    </button>
                                </div>
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg shadow-sm transition-colors" style="background-color: #4f46e5; color: white;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB: WhatsApp --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div x-show="activeTab === 'whatsapp'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden" x-data="{ whatsappProvider: '{{ \App\Models\SiteSettings::get('whatsapp_provider', 'disabled') }}' }">
                            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-green-500/5 to-emerald-500/5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-green-100 dark:bg-green-500/10 flex items-center justify-center">
                                        <svg class="w-[18px] h-[18px] text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-slate-800 dark:text-white">Service WhatsApp</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Notifications directes sur le téléphone des membres</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Fournisseur</label>
                                    <select name="whatsapp_provider" x-model="whatsappProvider" class="w-full sm:w-72 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                        <option value="disabled">Désactivé</option>
                                        <option value="twilio">Twilio WhatsApp</option>
                                        <option value="meta">Meta (WhatsApp Cloud API)</option>
                                    </select>
                                </div>

                                {{-- Twilio WhatsApp --}}
                                <div x-show="whatsappProvider === 'twilio'" x-collapse class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/30 space-y-3">
                                    <h4 class="text-sm font-medium text-slate-800 dark:text-white">Configuration Twilio WhatsApp</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Account SID</label>
                                            <input type="text" name="whatsapp_twilio_sid" value="{{ \App\Models\SiteSettings::get('whatsapp_twilio_sid', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-green-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Auth Token</label>
                                            <input type="password" name="whatsapp_twilio_token" value="{{ \App\Models\SiteSettings::get('whatsapp_twilio_token', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-green-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Numéro WhatsApp</label>
                                        <input type="text" name="whatsapp_twilio_from" value="{{ \App\Models\SiteSettings::get('whatsapp_twilio_from', '') }}" placeholder="+1234567890" class="w-full sm:w-64 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-green-500">
                                    </div>
                                </div>

                                {{-- Meta WhatsApp --}}
                                <div x-show="whatsappProvider === 'meta'" x-collapse class="p-4 rounded-xl bg-slate-50 dark:bg-slate-700/30 space-y-3">
                                    <h4 class="text-sm font-medium text-slate-800 dark:text-white">Configuration Meta Cloud API</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Access Token</label>
                                            <input type="password" name="whatsapp_meta_access_token" value="{{ \App\Models\SiteSettings::get('whatsapp_meta_access_token', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-green-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Phone Number ID</label>
                                            <input type="text" name="whatsapp_meta_phone_number_id" value="{{ \App\Models\SiteSettings::get('whatsapp_meta_phone_number_id', '') }}" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-green-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Version API</label>
                                        <input type="text" name="whatsapp_meta_api_version" value="{{ \App\Models\SiteSettings::get('whatsapp_meta_api_version', 'v18.0') }}" placeholder="v18.0" class="w-full sm:w-40 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 px-3 py-2 text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-green-500">
                                    </div>
                                </div>
                            </div>

                            {{-- Test WhatsApp --}}
                            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3 flex-wrap">
                                <div x-show="whatsappProvider !== 'disabled'" class="flex items-center gap-2 flex-1 min-w-0">
                                    <input type="tel" form="whatsapp-test-form" name="test_phone" placeholder="Numéro de test" class="w-44 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white px-3 py-2 text-sm" inputmode="tel">
                                    <button type="submit" form="whatsapp-test-form" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition-colors" style="background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        Tester
                                    </button>
                                </div>
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg shadow-sm transition-colors" style="background-color: #4f46e5; color: white;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </div>

                </form>

                {{-- Hidden test forms (outside main form) --}}
                <form id="sms-test-form" method="POST" action="{{ route('admin.test.sms') }}" class="hidden">@csrf</form>
                <form id="whatsapp-test-form" method="POST" action="{{ route('admin.test.whatsapp') }}" class="hidden">@csrf</form>
            </div>
        </div>
    </div>
</x-layouts.app>

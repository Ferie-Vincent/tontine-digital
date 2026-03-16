@props(['title' => 'DIGI-TONTINE CI'])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#3C50E0" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1C2434" media="(prefers-color-scheme: dark)">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>{{ $title }}</title>
    <script>
        (function(){var s=localStorage.getItem('theme');if(s==='dark'||(!s&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark')}})();
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Sidebar styles - TailAdmin inspired */
        .app-sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 18rem;
            background-color: #1C2434;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 300ms;
        }
        @media (min-width: 1024px) {
            .app-sidebar {
                transform: translateX(0);
            }
            .app-main {
                margin-left: 18rem;
            }
        }
        .sidebar-open {
            transform: translateX(0) !important;
        }
        .primary-btn {
            background-color: #3C50E0;
            color: white;
        }
        .primary-btn:hover {
            background-color: #1C3FB7;
        }
        .primary-color {
            color: #3C50E0;
        }
        .primary-bg-color {
            background-color: #3C50E0;
        }
        .primary-bg-light-color {
            background-color: rgba(60, 80, 224, 0.1);
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="app-sidebar" :class="sidebarOpen && 'sidebar-open'">
            <div class="flex flex-col h-full">
                {{-- Logo --}}
                <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10">
                    <div class="w-10 h-10 rounded-lg primary-bg-color flex items-center justify-center">
                        <span class="text-white font-bold text-lg">DT</span>
                    </div>
                    <div>
                        <span class="text-white font-bold text-lg">DIGI-TONTINE</span>
                        <span class="block text-xs text-slate-400">Cote d'Ivoire</span>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 px-4 py-6 overflow-y-auto" aria-label="Menu principal">
                    <p class="px-4 mb-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Menu principal</p>

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Tableau de bord
                    </a>

                    <a href="{{ route('tontines.index') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('tontines.*') && !request()->routeIs('tontines.join') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Mes Tontines
                    </a>

                    <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('notifications.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a2 2 0 00-2 2v.29C7.12 5.14 5 7.82 5 11v3.59l-1.71 1.7A1 1 0 004 18h16a1 1 0 00.71-1.71L19 14.59V11c0-3.18-2.12-5.86-5-6.71V4a2 2 0 00-2-2z"/><path d="M12 23a3 3 0 003-3H9a3 3 0 003 3z"/></svg>
                        <span class="flex-1">Notifications</span>
                        @php $unreadNotifCount = \App\Models\Notification::forUser(auth()->id())->unread()->count(); @endphp
                        @if($unreadNotifCount > 0)
                        <span class="bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shrink-0">{{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}</span>
                        @endif
                    </a>

                    <p class="px-4 mt-8 mb-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Actions</p>

                    <a href="{{ route('tontines.join') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('tontines.join') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Rejoindre une tontine
                    </a>

                    <a href="{{ route('requests.index') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('requests.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="flex-1">Mes requêtes</span>
                        @php $pendingRequestCount = \App\Models\UserRequest::forUser(auth()->id())->pending()->count(); @endphp
                        @if($pendingRequestCount > 0)
                        <span class="bg-amber-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shrink-0">{{ $pendingRequestCount > 9 ? '9+' : $pendingRequestCount }}</span>
                        @endif
                    </a>

                    <p class="px-4 mt-8 mb-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Compte</p>

                    <a href="{{ route('financial-history') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('financial-history') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Historique financier
                    </a>

                    <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('settings*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Paramètres
                    </a>

                    @if(auth()->user()->is_admin)
                    <p class="px-4 mt-8 mb-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Administration</p>

                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Vue d'ensemble
                    </a>

                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Utilisateurs
                    </a>

                    <a href="{{ route('admin.activity') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.activity') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Historique
                    </a>

                    <a href="{{ route('admin.requests.index') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.requests.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        <span class="flex-1">Requêtes</span>
                        @php $adminPendingCount = \App\Models\UserRequest::pending()->count(); @endphp
                        @if($adminPendingCount > 0)
                        <span class="bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shrink-0">{{ $adminPendingCount > 9 ? '9+' : $adminPendingCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.guide') }}" class="flex items-center gap-3 px-4 py-3 mb-1 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.guide') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Mode d'emploi
                    </a>

                    @endif
                </nav>

                {{-- User info --}}
                <div class="px-4 py-4 pb-24 lg:pb-4 border-t border-white/10">
                    <div class="flex items-center gap-3">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full object-cover" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ auth()->user()->phone }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-white transition min-h-[44px] min-w-[44px] inline-flex items-center justify-center" aria-label="Déconnexion" title="Déconnexion">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Overlay for mobile --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition.opacity x-cloak></div>

        {{-- Main content --}}
        <div class="app-main flex-1">
            {{-- Impersonation Banner --}}
            @if(session('impersonating_from'))
            <div class="bg-amber-500 text-white text-center py-2 px-4 text-sm font-medium">
                <span>Vous êtes connecté en tant que {{ auth()->user()->name }}.</span>
                <form method="POST" action="{{ route('impersonate.stop') }}" class="inline">
                    @csrf
                    <button type="submit" class="underline font-bold ml-2 hover:text-amber-100">Revenir à mon compte</button>
                </form>
            </div>
            @endif

            {{-- Header --}}
            <header class="sticky top-0 z-30 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                    <div class="flex items-center gap-4">
                        {{-- Hamburger visible uniquement sur tablette/entre les breakpoints, cache sur mobile (bottom nav) et desktop (sidebar) --}}
                        <button @click="sidebarOpen = !sidebarOpen" class="hidden p-2.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition" aria-label="Ouvrir le menu" title="Menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div class="text-lg font-semibold text-slate-800 dark:text-white">
                            {{ $header ?? '' }}
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Theme toggle --}}
                        <button onclick="toggleTheme()" class="p-2.5 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition" aria-label="Changer de thème" title="Changer de thème">
                            <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        </button>
                        @livewire('message.message-bell')
                        @livewire('notification.notification-bell')
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            <div class="px-4 sm:px-6">
                @if(session('success'))
                    <div class="mt-4">
                        <x-alert type="success" dismissible>{{ session('success') }}</x-alert>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mt-4">
                        <x-alert type="error" dismissible>{{ session('error') }}</x-alert>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mt-4">
                        <x-alert type="error" dismissible>
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-alert>
                    </div>
                @endif
            </div>

            {{-- Page Content --}}
            <main class="px-4 pt-4 pb-40 sm:px-6 sm:pt-6 lg:pb-6">
                {{ $slot }}
            </main>
        </div>
    </div>
    {{-- Mobile Bottom Navigation Bar --}}
    <nav class="fixed bottom-0 inset-x-0 z-50 lg:hidden flex flex-col items-center" style="padding-bottom:env(safe-area-inset-bottom,0px)" aria-label="Navigation mobile">
        {{-- Avatar sureleve (Accueil) --}}
        <a href="{{ route('dashboard') }}" class="relative z-10" aria-label="Accueil" style="margin-bottom:-16px">
            <div class="rounded-full bg-white dark:bg-slate-700 shadow-[0_4px_24px_rgba(60,80,224,0.4)]" style="width:60px;height:60px;padding:3px">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full rounded-full object-cover" />
            </div>
        </a>
        {{-- Barre pleine largeur --}}
        <div class="w-full bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex items-center h-[56px] px-1">
            {{-- 1. Requêtes --}}
            <a href="{{ route('requests.index') }}" class="flex-1 flex flex-col items-center justify-center gap-0.5 min-h-[44px] {{ request()->routeIs('requests.*') ? 'text-[#3C50E0]' : 'text-slate-400' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-[10px] font-normal leading-none">Requêtes</span>
            </a>

            {{-- 2. Mes Tontines --}}
            <a href="{{ route('tontines.index') }}" class="flex-1 flex flex-col items-center justify-center gap-0.5 min-h-[44px] {{ request()->routeIs('tontines.*') && !request()->routeIs('tontines.join') ? 'text-[#3C50E0]' : 'text-slate-400' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-[10px] font-normal leading-none">Tontines</span>
            </a>

            {{-- 3. Espace central pour l'avatar (Accueil) --}}
            <div class="flex flex-col items-center pt-3" style="width:68px;flex-shrink:0">
                <span class="text-[10px] font-normal leading-none {{ request()->routeIs('dashboard') ? 'text-[#3C50E0]' : 'text-slate-400' }}">Accueil</span>
            </div>

            {{-- 4. Historique financier --}}
            <a href="{{ route('financial-history') }}" class="flex-1 flex flex-col items-center justify-center gap-0.5 min-h-[44px] {{ request()->routeIs('financial-history') ? 'text-[#3C50E0]' : 'text-slate-400' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-[10px] font-normal leading-none whitespace-nowrap">Historique</span>
            </a>

            {{-- 5. Menu --}}
            <button @click="sidebarOpen = true" class="flex-1 flex flex-col items-center justify-center gap-0.5 min-h-[44px] text-slate-400" aria-label="Ouvrir le menu">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <span class="text-[10px] font-normal leading-none">Menu</span>
            </button>
        </div>
    </nav>

    @livewireScripts
    @stack('scripts')
</body>
</html>

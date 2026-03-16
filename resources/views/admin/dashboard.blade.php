<x-layouts.app title="Administration">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Tableau de bord Admin</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Vue d'ensemble de la plateforme</p>
            </div>
        </div>
    </x-slot:header>

    {{-- ======== KPI ROW ======== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        {{-- Total Users --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-20 h-20 rounded-bl-full" style="background-color: rgba(59,130,246,0.08);"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 shadow-lg" style="background-color: #3B82F6;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Utilisateurs</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($stats['users_count']) }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $stats['active_users'] }} actifs</p>
            </div>
        </div>

        {{-- New Users This Month --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-20 h-20 rounded-bl-full" style="background-color: rgba(139,92,246,0.08);"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 shadow-lg" style="background-color: #8B5CF6;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Nouveaux ce mois</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($stats['new_users_month']) }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">inscriptions</p>
            </div>
        </div>

        {{-- Active Tontines --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-20 h-20 rounded-bl-full" style="background-color: rgba(16,185,129,0.08);"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 shadow-lg" style="background-color: #10B981;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tontines actives</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($stats['active_tontines']) }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">sur {{ $stats['tontines_count'] }} total</p>
            </div>
        </div>

        {{-- Total Collected --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-20 h-20 rounded-bl-full" style="background-color: rgba(245,158,11,0.08);"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 shadow-lg" style="background-color: #F59E0B;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total collecte</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ format_amount($stats['total_collected']) }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">confirme</p>
            </div>
        </div>

        {{-- Pending Amount --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-20 h-20 rounded-bl-full" style="background-color: rgba(99,102,241,0.08);"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 shadow-lg" style="background-color: #6366F1;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">En attente</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ format_amount($stats['total_pending']) }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">a confirmer</p>
            </div>
        </div>

        {{-- Late Contributions --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-20 h-20 rounded-bl-full" style="background-color: rgba(239,68,68,0.08);"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 shadow-lg" style="background-color: #EF4444;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">En retard</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($stats['late_count']) }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">contributions</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        {{-- Main Content Area --}}
        <div class="xl:col-span-3 space-y-6">

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Line Chart: Inscriptions (6 derniers mois) --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Inscriptions</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">6 derniers mois</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(139,92,246,0.1);">
                            <svg class="w-5 h-5" style="color: #8B5CF6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="relative" style="height: 280px;">
                        <canvas id="registrationChart"></canvas>
                    </div>
                </div>

                {{-- Bar Chart: Activite mensuelle (contributions) --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Activite mensuelle</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Contributions des 6 derniers mois</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="relative" style="height: 280px;">
                        <canvas id="adminMonthlyChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Second Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Horizontal Bar Chart: Top 5 tontines --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Top 5 tontines</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Par montant collecte</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(245,158,11,0.1);">
                            <svg class="w-5 h-5" style="color: #F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                    </div>
                    @if(count($topTontineNames) > 0)
                    <div class="relative" style="height: 280px;">
                        <canvas id="topTontinesChart"></canvas>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center text-slate-500 dark:text-slate-400 text-sm py-12">
                        <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p>Aucune contribution confirmee</p>
                    </div>
                    @endif
                </div>

                {{-- Doughnut: Repartition des tontines par statut --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Repartition des tontines</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Par statut</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: rgba(99,102,241,0.1);">
                            <svg class="w-5 h-5" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                        </div>
                    </div>
                    @if(count($doughnutValues) > 0)
                    <div class="relative flex items-center justify-center" style="height: 200px;">
                        <canvas id="adminDoughnutChart"></canvas>
                    </div>
                    {{-- Status stat cards --}}
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($tontinesByStatus as $status => $count)
                            @php $cfg = $statusConfig[$status] ?? ['label' => ucfirst($status), 'color' => '#94A3B8']; @endphp
                            <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $cfg['color'] }};"></span>
                                <span class="text-xs text-slate-600 dark:text-slate-300">{{ $cfg['label'] }}</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-white ml-auto">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center text-slate-500 dark:text-slate-400 text-sm py-8">
                        <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        </svg>
                        <p>Aucune tontine</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Recent Activity Table --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-indigo-500/5 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(99,102,241,0.1);">
                            <svg class="w-4 h-4" style="color: #6366F1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-white">Activite recente</h3>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-700/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($recentActivity as $log)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($log->user)
                                        <img src="{{ $log->user->avatar_url }}" alt="" class="w-7 h-7 rounded-full object-cover" />
                                        <span class="font-medium text-slate-800 dark:text-white">{{ $log->user->name }}</span>
                                        @else
                                        <span class="text-slate-400 dark:text-slate-500 italic">Systeme</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-slate-600 dark:text-slate-300">{{ $log->action_label }}</td>
                                <td class="px-6 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Aucune activite recente</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Tables Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent Users --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-blue-500/5 to-transparent">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold text-slate-800 dark:text-white">Derniers inscrits</h3>
                            </div>
                            <a href="{{ route('admin.users') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1">
                                Voir tous
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($recentUsers as $user)
                        <div class="flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover shadow-sm" />
                                <div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->phone }} &bull; {{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->status === 'active' ? 'bg-emerald-500' : 'bg-red-500' }} mr-1.5"></span>
                                {{ $user->status === 'active' ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        @empty
                        <div class="p-8 text-center">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Aucun utilisateur recent</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Tontines --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-emerald-500/5 to-transparent">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold text-slate-800 dark:text-white">Tontines recentes</h3>
                            </div>
                            <a href="{{ route('tontines.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 flex items-center gap-1">
                                Voir toutes
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($recentTontines as $tontine)
                        <div class="flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-sm font-semibold shadow-sm">
                                    {{ strtoupper(substr($tontine->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $tontine->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $tontine->creator->name }} &bull; {{ $tontine->active_members_count }} membres</p>
                                </div>
                            </div>
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
                        </div>
                        @empty
                        <div class="p-8 text-center">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Aucune tontine recente</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions Sidebar --}}
        <div class="xl:col-span-1 space-y-6">
            {{-- Quick Actions Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-violet-500/5 to-transparent">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-white">Actions rapides</h3>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 group-hover:bg-blue-500/20 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800 dark:text-white">Gerer les utilisateurs</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $stats['users_count'] }} utilisateurs</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a href="{{ route('tontines.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 group-hover:bg-emerald-500/20 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800 dark:text-white">Creer une tontine</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Nouveau groupe</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a href="{{ route('tontines.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-amber-500/10 group-hover:bg-amber-500/20 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800 dark:text-white">Voir les tontines</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $stats['tontines_count'] }} tontines</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            {{-- System Status Card --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-white">Etat du systeme</h3>
                    </div>
                </div>
                <div class="p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-sm text-slate-600 dark:text-slate-400">Serveur</span>
                        </div>
                        <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">En ligne</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-sm text-slate-600 dark:text-slate-400">Base de donnees</span>
                        </div>
                        <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Connectee</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-sm text-slate-600 dark:text-slate-400">API Paiements</span>
                        </div>
                        <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Active</span>
                    </div>
                </div>
            </div>

            {{-- Monthly Contributions Summary --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(245,158,11,0.1);">
                            <svg class="w-4 h-4" style="color: #F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-slate-800 dark:text-white">Ce mois-ci</h3>
                    </div>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Contributions confirmees</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white">{{ format_amount($stats['monthly_contributions']) }}</p>
                    </div>
                    <div class="h-px bg-slate-100 dark:bg-slate-700"></div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Nouveaux utilisateurs</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white">{{ $stats['new_users_month'] }}</p>
                    </div>
                    <div class="h-px bg-slate-100 dark:bg-slate-700"></div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Retards en cours</p>
                        <p class="text-lg font-bold" style="color: #EF4444;">{{ $stats['late_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94A3B8' : '#64748B';
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(100, 116, 139, 0.1)';
            const tooltipBg = isDark ? '#1E293B' : '#fff';
            const tooltipTitle = isDark ? '#fff' : '#1E293B';
            const tooltipBody = isDark ? '#94A3B8' : '#64748B';
            const tooltipBorder = isDark ? '#334155' : '#E2E8F0';

            function formatMoney(value) {
                return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
            }

            const tooltipStyle = {
                backgroundColor: tooltipBg,
                titleColor: tooltipTitle,
                bodyColor: tooltipBody,
                borderColor: tooltipBorder,
                borderWidth: 1,
                cornerRadius: 8,
                padding: 12,
            };

            // ===== Line Chart: Inscriptions 6 derniers mois =====
            const regCtx = document.getElementById('registrationChart');
            if (regCtx) {
                new Chart(regCtx, {
                    type: 'line',
                    data: {
                        labels: @json($registrationLabels),
                        datasets: [{
                            label: 'Inscriptions',
                            data: @json($registrationValues),
                            borderColor: '#8B5CF6',
                            backgroundColor: function(context) {
                                const chart = context.chart;
                                const {ctx, chartArea} = chart;
                                if (!chartArea) return 'rgba(139,92,246,0.1)';
                                const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                gradient.addColorStop(0, 'rgba(139,92,246,0.25)');
                                gradient.addColorStop(1, 'rgba(139,92,246,0.02)');
                                return gradient;
                            },
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#8B5CF6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                ...tooltipStyle,
                                callbacks: {
                                    label: function(ctx) {
                                        return ctx.parsed.y + ' inscription' + (ctx.parsed.y > 1 ? 's' : '');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { weight: '500' } },
                                grid: { display: false },
                                border: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: textColor,
                                    stepSize: 1,
                                    precision: 0,
                                },
                                grid: { color: gridColor, drawBorder: false },
                                border: { display: false }
                            }
                        }
                    }
                });
            }

            // ===== Bar Chart: Activite mensuelle (contributions) =====
            const monthlyCtx = document.getElementById('adminMonthlyChart');
            if (monthlyCtx) {
                new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Confirmees',
                                data: @json($chartConfirmed),
                                backgroundColor: function(context) {
                                    const chart = context.chart;
                                    const {ctx, chartArea} = chart;
                                    if (!chartArea) return '#10B981';
                                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                    gradient.addColorStop(0, '#059669');
                                    gradient.addColorStop(1, '#10B981');
                                    return gradient;
                                },
                                borderRadius: 6,
                                maxBarThickness: 32,
                            },
                            {
                                label: 'En attente',
                                data: @json($chartDeclared),
                                backgroundColor: function(context) {
                                    const chart = context.chart;
                                    const {ctx, chartArea} = chart;
                                    if (!chartArea) return '#F59E0B';
                                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                    gradient.addColorStop(0, '#D97706');
                                    gradient.addColorStop(1, '#F59E0B');
                                    return gradient;
                                },
                                borderRadius: 6,
                                maxBarThickness: 32,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    color: textColor,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 16,
                                    font: { weight: '500', size: 12 }
                                }
                            },
                            tooltip: {
                                ...tooltipStyle,
                                callbacks: {
                                    label: function(ctx) {
                                        return ctx.dataset.label + ' : ' + formatMoney(ctx.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { weight: '500' } },
                                grid: { display: false },
                                border: { display: false }
                            },
                            y: {
                                ticks: {
                                    color: textColor,
                                    callback: function(v) {
                                        return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v);
                                    }
                                },
                                grid: { color: gridColor, drawBorder: false },
                                border: { display: false }
                            }
                        }
                    }
                });
            }

            // ===== Horizontal Bar Chart: Top 5 tontines =====
            const topCtx = document.getElementById('topTontinesChart');
            if (topCtx) {
                const barColors = ['#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EF4444'];
                new Chart(topCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($topTontineNames),
                        datasets: [{
                            label: 'Montant collecte',
                            data: @json($topTontineAmounts),
                            backgroundColor: barColors.slice(0, @json(count($topTontineNames))),
                            borderRadius: 6,
                            maxBarThickness: 28,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                ...tooltipStyle,
                                callbacks: {
                                    label: function(ctx) {
                                        return formatMoney(ctx.parsed.x);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: textColor,
                                    callback: function(v) {
                                        return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v);
                                    }
                                },
                                grid: { color: gridColor, drawBorder: false },
                                border: { display: false }
                            },
                            y: {
                                ticks: {
                                    color: textColor,
                                    font: { weight: '500', size: 12 },
                                    callback: function(value, index) {
                                        const label = this.getLabelForValue(value);
                                        return label.length > 20 ? label.substring(0, 20) + '...' : label;
                                    }
                                },
                                grid: { display: false },
                                border: { display: false }
                            }
                        }
                    }
                });
            }

            // ===== Doughnut Chart: Repartition tontines =====
            const doughnutCtx = document.getElementById('adminDoughnutChart');
            if (doughnutCtx) {
                const labels = @json($doughnutLabels);
                const values = @json($doughnutValues);
                const colors = @json($doughnutColors);
                const total = values.reduce((a, b) => a + b, 0);

                new Chart(doughnutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderWidth: 0,
                            hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                ...tooltipStyle,
                            }
                        }
                    },
                    plugins: [{
                        id: 'centerText',
                        beforeDraw: function(chart) {
                            const { ctx: c } = chart;
                            c.save();
                            c.font = 'bold 24px Inter, system-ui, sans-serif';
                            c.fillStyle = isDark ? '#F1F5F9' : '#1E293B';
                            c.textAlign = 'center';
                            c.textBaseline = 'middle';
                            const chartArea = chart.chartArea;
                            const centerX = (chartArea.left + chartArea.right) / 2;
                            const centerY = (chartArea.top + chartArea.bottom) / 2;
                            c.fillText(total, centerX, centerY - 8);
                            c.font = '500 11px Inter, system-ui, sans-serif';
                            c.fillStyle = textColor;
                            c.fillText('tontine' + (total > 1 ? 's' : ''), centerX, centerY + 14);
                            c.restore();
                        }
                    }]
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>

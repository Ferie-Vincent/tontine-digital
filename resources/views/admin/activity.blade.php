<x-layouts.app title="Historique d'activite - Admin">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <div class="p-2 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Historique d'activite global</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Toutes les actions sur la plateforme</p>
            </div>
        </div>
    </x-slot:header>

    <div class="max-w-5xl mx-auto space-y-6">
        {{-- Filters --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
            <form method="GET" action="{{ route('admin.activity') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <select name="tontine_id" onchange="this.form.submit()" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les tontines</option>
                        @foreach($tontines as $id => $name)
                            <option value="{{ $id }}" {{ request('tontine_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <select name="action" onchange="this.form.submit()" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">Toutes les actions</option>
                        @foreach($actionTypes as $action => $count)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                {{ \App\Models\ActivityLog::make(['action' => $action])->action_label }} ({{ $count }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('tontine_id') || (request('action') && request('action') !== 'all'))
                    <a href="{{ route('admin.activity') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Effacer
                    </a>
                @endif
            </form>
        </div>

        {{-- Activity List --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            @if($activities->isEmpty())
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">Aucune activité</h3>
                    <p class="text-slate-500 dark:text-slate-400">Aucune activité enregistrée pour les filtres sélectionnés.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($activities as $activity)
                        <div class="flex gap-3 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            {{-- Icon --}}
                            @php
                                $iconColor = match(true) {
                                    str_contains($activity->action, 'confirm') => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/20',
                                    str_contains($activity->action, 'reject') || str_contains($activity->action, 'exclud') || str_contains($activity->action, 'fail') => 'text-red-500 bg-red-50 dark:bg-red-900/20',
                                    str_contains($activity->action, 'late') || str_contains($activity->action, 'penalty') => 'text-amber-500 bg-amber-50 dark:bg-amber-900/20',
                                    str_contains($activity->action, 'auto_') => 'text-purple-500 bg-purple-50 dark:bg-purple-900/20',
                                    str_contains($activity->action, 'started') || str_contains($activity->action, 'completed') => 'text-indigo-500 bg-indigo-50 dark:bg-indigo-900/20',
                                    default => 'text-blue-500 bg-blue-50 dark:bg-blue-900/20',
                                };

                                $icon = match(true) {
                                    str_contains($activity->action, 'confirm') => 'M5 13l4 4L19 7',
                                    str_contains($activity->action, 'reject') || str_contains($activity->action, 'exclud') => 'M6 18L18 6M6 6l12 12',
                                    str_contains($activity->action, 'fail') => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z',
                                    str_contains($activity->action, 'late') || str_contains($activity->action, 'penalty') => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                    str_contains($activity->action, 'auto_') => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                                    str_contains($activity->action, 'contributed') => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                                    str_contains($activity->action, 'started') || str_contains($activity->action, 'completed') => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                    str_contains($activity->action, 'joined') || str_contains($activity->action, 'left') => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                                    str_contains($activity->action, 'updated') || str_contains($activity->action, 'created') => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                    str_contains($activity->action, 'reminder') => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                                    default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                };
                            @endphp
                            <div class="shrink-0 w-8 h-8 rounded-lg {{ $iconColor }} flex items-center justify-center mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-800 dark:text-white">
                                    <span class="font-medium">{{ $activity->user?->name ?? 'Systeme' }}</span>
                                    <span class="text-slate-500 dark:text-slate-400">{{ $activity->action_label }}</span>
                                </p>
                                @if($activity->tontine)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $activity->tontine->name }}
                                        </span>
                                    </p>
                                @endif
                                @if($activity->properties)
                                    <div class="mt-1 text-xs text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-700/50 rounded-lg px-3 py-1.5 inline-block max-w-full overflow-hidden">
                                        @foreach($activity->properties as $key => $value)
                                            <span class="font-medium text-slate-500 dark:text-slate-400">{{ $key }}:</span>
                                            {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}
                                            @if(!$loop->last) <span class="text-slate-300 dark:text-slate-600 mx-1">|</span> @endif
                                        @endforeach
                                    </div>
                                @endif
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                    <time datetime="{{ $activity->created_at->toISOString() }}" title="{{ $activity->created_at->format('d/m/Y H:i:s') }}">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </time>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($activities->hasPages())
                    <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                        {{ $activities->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>

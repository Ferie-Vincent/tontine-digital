<x-layouts.app title="Notifications">
    <x-slot:header>Notifications</x-slot:header>

    <div class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Toutes les notifications</h2>
            @if($notifications->where('read_at', null)->count() > 0)
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <x-button type="submit" variant="secondary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tout marquer lu
                </x-button>
            </form>
            @endif
        </div>

        {{-- Notifications List --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            @forelse($notifications as $notification)
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/50 last:border-b-0 {{ $notification->read_at ? '' : 'bg-primary-50/30 dark:bg-primary-500/5' }}">
                <div class="flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="shrink-0 mt-0.5">
                        @php
                            $iconClass = match(true) {
                                str_contains($notification->type, 'payment') => 'text-emerald-500 bg-emerald-100 dark:bg-emerald-500/20',
                                str_contains($notification->type, 'member') => 'text-blue-500 bg-blue-100 dark:bg-blue-500/20',
                                str_contains($notification->type, 'tour') => 'text-amber-500 bg-amber-100 dark:bg-amber-500/20',
                                str_contains($notification->type, 'excluded') => 'text-red-500 bg-red-100 dark:bg-red-500/20',
                                str_contains($notification->type, 'rejected') => 'text-red-500 bg-red-100 dark:bg-red-500/20',
                                default => 'text-slate-500 bg-slate-100 dark:bg-slate-700',
                            };
                        @endphp
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $iconClass }}">
                            @if(str_contains($notification->type, 'payment'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @elseif(str_contains($notification->type, 'member') || str_contains($notification->type, 'excluded'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            @elseif(str_contains($notification->type, 'tour'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @endif
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $notification->title }}</p>
                            @if(!$notification->read_at)
                            <span class="w-2 h-2 rounded-full bg-primary-500 animate-pulse"></span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ $notification->content }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Action --}}
                    @if($notification->data && isset($notification->data['tontine_id']))
                    <div class="shrink-0">
                        @if(isset($notification->data['tour_id']))
                        <a href="{{ route('tontines.tours.show', [$notification->data['tontine_id'], $notification->data['tour_id']]) }}" class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition">
                            Voir &rarr;
                        </a>
                        @else
                        <a href="{{ route('tontines.show', $notification->data['tontine_id']) }}" class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition">
                            Voir &rarr;
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">Aucune notification</h3>
                <p class="text-slate-500 dark:text-slate-400">Vous êtes à jour ! Aucune notification pour le moment.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</x-layouts.app>

<div class="relative" wire:poll.5s="loadNotifications" x-data="{ open: false }" @click.outside="open = false">
    {{-- Bell Button --}}
    <button @click="open = !open" class="relative p-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition" title="Notifications">
        <svg class="w-6 h-6 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2a2 2 0 00-2 2v.29C7.12 5.14 5 7.82 5 11v3.59l-1.71 1.7A1 1 0 004 18h16a1 1 0 00.71-1.71L19 14.59V11c0-3.18-2.12-5.86-5-6.71V4a2 2 0 00-2-2z"/>
            <path d="M12 23a3 3 0 003-3H9a3 3 0 003 3z"/>
        </svg>
        @if($unreadCount > 0)
        <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold w-[18px] h-[18px] rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-800">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Backdrop overlay (mobile only) --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         x-cloak
         class="sm:hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-40">
    </div>

    {{-- Dropdown - bottom sheet on mobile, dropdown on desktop --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
         x-cloak
         class="fixed inset-x-0 bottom-0 sm:absolute sm:inset-auto sm:right-0 sm:bottom-auto sm:mt-2 w-auto sm:w-80 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-t-2xl sm:rounded-xl shadow-xl z-50 overflow-hidden max-h-[85vh] sm:max-h-none">

        {{-- Drag handle (mobile) --}}
        <div class="sm:hidden flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-white">Notifications</h3>
            @if($unreadCount > 0)
            <button wire:click="markAllAsRead" class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition">
                Tout marquer lu
            </button>
            @endif
        </div>

        {{-- Notifications List --}}
        <div class="max-h-[60vh] sm:max-h-80 overflow-y-auto overscroll-contain">
            @forelse($notifications as $notification)
            <div wire:click="goToNotification('{{ $notification->id }}')"
                 class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer border-b border-slate-100 dark:border-slate-700/50 last:border-b-0 transition-colors {{ $notification->read_at ? '' : 'bg-primary-50/50 dark:bg-primary-500/10' }}">
                <div class="flex items-start gap-3">
                    @if(!$notification->read_at)
                    <span class="w-2 h-2 rounded-full bg-primary-500 mt-1.5 shrink-0 animate-pulse"></span>
                    @else
                    <span class="w-2 h-2 mt-1.5 shrink-0"></span>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $notification->title }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">{{ $notification->content }}</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-4 py-12 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400">Aucune notification</p>
            </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($notifications->count() > 0)
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 safe-area-bottom">
            <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition flex items-center justify-center gap-1">
                Voir toutes les notifications
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        @endif
    </div>
</div>

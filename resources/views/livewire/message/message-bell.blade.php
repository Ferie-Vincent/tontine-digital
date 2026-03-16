<div class="relative" wire:poll.5s="loadUnread" x-data="{ open: false }" @click.outside="open = false">
    {{-- Message Button --}}
    <button @click="open = !open" class="relative p-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition" title="Messages">
        <svg class="w-6 h-6 text-slate-500 dark:text-slate-400 transition" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
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
            <h3 class="text-sm font-semibold text-slate-800 dark:text-white">Messages</h3>
            @if($unreadCount > 0)
            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $unreadCount }} non lu{{ $unreadCount > 1 ? 's' : '' }}</span>
            @endif
        </div>

        {{-- Messages List --}}
        <div class="max-h-[60vh] sm:max-h-80 overflow-y-auto overscroll-contain">
            @forelse($tontinesWithUnread as $item)
            <div wire:click="goToTontineMessages({{ $item['tontine_id'] }})"
                 class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer border-b border-slate-100 dark:border-slate-700/50 last:border-b-0 transition-colors bg-blue-50/50 dark:bg-blue-500/10">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-violet-600 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $item['tontine_name'] }}</p>
                            <span class="bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shrink-0">{{ $item['unread_count'] > 9 ? '9+' : $item['unread_count'] }}</span>
                        </div>
                        @if($item['sender_name'])
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                            <span class="font-medium">{{ $item['sender_name'] }}</span> : {{ \Illuminate\Support\Str::limit($item['last_message'], 40) }}
                        </p>
                        @endif
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">{{ $item['last_message_at'] }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-4 py-12 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-400 dark:text-slate-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400">Aucun nouveau message</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

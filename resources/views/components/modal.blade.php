@props(['id', 'maxWidth' => 'lg', 'title' => ''])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    default => 'sm:max-w-lg',
};
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal-{{ $id }}.window="open = true"
    x-on:close-modal-{{ $id }}.window="open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[60] overflow-y-auto"
    role="dialog"
    aria-modal="true"
    @if($title) aria-label="{{ $title }}" @endif
>
    <div class="flex items-end sm:items-center justify-center min-h-screen p-0 sm:p-4">
        {{-- Backdrop --}}
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50"
            @click="open = false"
        ></div>

        {{-- Modal Content --}}
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
            class="relative bg-white dark:bg-slate-800 rounded-t-2xl sm:rounded-lg shadow-xl w-full {{ $maxWidthClass }} transform transition-all max-h-[90vh] overflow-y-auto"
        >
            @if($title)
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white">{{ $title }}</h3>
                <button @click="open = false" class="p-2 -m-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded-lg transition min-h-[44px] min-w-[44px] inline-flex items-center justify-center" aria-label="Fermer la fenêtre" title="Fermer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @endif
            <div class="px-6 pt-5 pb-6 sm:pb-5">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

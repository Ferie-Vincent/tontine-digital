@props(['title' => 'Aucune donnee', 'description' => null, 'icon' => null, 'action' => null, 'actionLabel' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 px-4']) }}>
    @if($icon)
    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4 text-slate-400 dark:text-slate-500">
        {!! $icon !!}
    </div>
    @else
    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
    </div>
    @endif
    <h3 class="text-base font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ $title }}</h3>
    @if($description)
    <p class="text-sm text-slate-500 dark:text-slate-400 text-center max-w-sm mb-4">{{ $description }}</p>
    @endif
    @if($action)
    <x-button :href="$action" variant="primary" size="sm">{{ $actionLabel }}</x-button>
    @endif
</div>

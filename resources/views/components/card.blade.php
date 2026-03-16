@props(['title' => null, 'subtitle' => null, 'padding' => true, 'footer' => null, 'hover' => false])

@php
$cardClasses = 'bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm';
if ($hover) {
    $cardClasses .= ' hover:shadow-md transition-shadow duration-200';
}
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    @if($title)
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
        <h3 class="text-base font-semibold text-slate-800 dark:text-white">{{ $title }}</h3>
        @if($subtitle)
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>
    @endif
    <div @class(['px-6 py-5' => $padding, 'p-0' => !$padding])>
        {{ $slot }}
    </div>
    @if($footer)
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 rounded-b-lg">
        {{ $footer }}
    </div>
    @endif
</div>

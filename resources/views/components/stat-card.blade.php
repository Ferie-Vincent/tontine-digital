@props(['title', 'value' => null, 'subtitle' => null, 'icon' => null, 'trend' => null, 'trendUp' => true, 'color' => 'primary'])

@php
$iconBgClasses = match($color) {
    'primary' => 'primary-bg-light primary-text',
    'secondary' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400',
    'accent' => 'bg-amber-500/10 text-amber-600',
    'success' => 'bg-emerald-500/10 text-emerald-600',
    'warning' => 'bg-amber-500/10 text-amber-600',
    'danger' => 'bg-red-500/10 text-red-600',
    default => 'primary-bg-light primary-text',
};
@endphp

<div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $title }}</p>
            @if($value)
            <p class="text-2xl font-bold text-slate-800 dark:text-white mt-2">{{ $value }}</p>
            @else
            <div class="mt-2">{{ $slot }}</div>
            @endif
            @if($subtitle)
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $subtitle }}</p>
            @endif
            @if($trend)
            <div class="flex items-center gap-1.5 mt-3">
                @if($trendUp)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    {{ $trend }}
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    {{ $trend }}
                </span>
                @endif
            </div>
            @endif
        </div>
        @if($icon)
        <div class="w-12 h-12 rounded-full {{ $iconBgClasses }} flex items-center justify-center">
            {!! $icon !!}
        </div>
        @endif
    </div>
</div>

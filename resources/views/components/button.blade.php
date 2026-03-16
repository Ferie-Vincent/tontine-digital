@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button', 'href' => null, 'disabled' => false])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = match($variant) {
    'primary' => 'btn-primary focus:ring-blue-500/50',
    'secondary' => 'bg-slate-600 hover:bg-slate-700 text-white focus:ring-slate-500/50',
    'success' => 'bg-emerald-500 hover:bg-emerald-600 text-white focus:ring-emerald-500/50',
    'danger' => 'bg-red-500 hover:bg-red-600 text-white focus:ring-red-500/50',
    'warning' => 'bg-amber-500 hover:bg-amber-600 text-white focus:ring-amber-500/50',
    'outline' => 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:ring-slate-500/50',
    'ghost' => 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 focus:ring-slate-500/50',
    default => 'btn-primary focus:ring-blue-500/50',
};

$sizeClasses = match($size) {
    'xs' => 'px-2.5 py-1.5 text-xs min-h-[44px] min-w-[44px]',
    'sm' => 'px-3 py-2 text-sm min-h-[44px] min-w-[44px]',
    'md' => 'px-4 py-2.5 text-sm min-h-[44px] min-w-[44px]',
    'lg' => 'px-5 py-3 text-base min-h-[44px] min-w-[44px]',
    default => 'px-4 py-2.5 text-sm min-h-[44px] min-w-[44px]',
};
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</a>
@else
<button type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</button>
@endif

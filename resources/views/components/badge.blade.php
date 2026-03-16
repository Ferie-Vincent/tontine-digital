@props(['color' => 'gray', 'size' => 'sm', 'dot' => false])

@php
$colorClasses = match($color) {
    'gray' => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
    'green' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    'yellow' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400',
    'red' => 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400',
    'blue' => 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400',
    'purple' => 'bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400',
    'orange' => 'bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400',
    'amber' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400',
    'cyan' => 'bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400',
    default => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
};

$dotColorClasses = match($color) {
    'gray' => 'bg-slate-400',
    'green' => 'bg-emerald-500',
    'yellow' => 'bg-amber-500',
    'red' => 'bg-red-500',
    'blue' => 'bg-blue-500',
    'purple' => 'bg-violet-500',
    'orange' => 'bg-orange-500',
    'amber' => 'bg-amber-500',
    'cyan' => 'bg-cyan-500',
    default => 'bg-slate-400',
};

$sizeClasses = match($size) {
    'xs' => 'px-2 py-0.5 text-[10px]',
    'sm' => 'px-2.5 py-1 text-xs',
    'md' => 'px-3 py-1.5 text-sm',
    default => 'px-2.5 py-1 text-xs',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-medium rounded {$colorClasses} {$sizeClasses}"]) }}>
    @if($dot)
    <span class="w-1.5 h-1.5 rounded-full {{ $dotColorClasses }}"></span>
    @endif
    {{ $slot }}
</span>

@props(['href', 'icon' => null, 'active' => false, 'badge' => null])

@php
$baseClasses = 'flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors';
$activeClasses = $active
    ? 'primary-bg/10 text-white'
    : 'text-gray-400 hover:text-white hover:bg-white/5';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "{$baseClasses} {$activeClasses}"]) }}>
    @if($icon)
    <span class="w-5 h-5 flex items-center justify-center flex-shrink-0">{!! $icon !!}</span>
    @endif
    <span class="flex-1 truncate">{{ $slot }}</span>
    @if($badge)
    <span class="flex-shrink-0 bg-red-500 text-white text-[10px] font-bold rounded-full px-2 py-0.5 min-w-[20px] text-center">{{ $badge }}</span>
    @endif
</a>

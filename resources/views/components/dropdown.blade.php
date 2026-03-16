@props(['align' => 'right', 'width' => '48'])

@php
$alignClasses = match($align) {
    'left' => 'left-0 origin-top-left',
    'right' => 'right-0 origin-top-right',
    'center' => 'left-1/2 -translate-x-1/2 origin-top',
    default => 'right-0 origin-top-right',
};
$widthClass = "w-{$width}";
@endphp

<div x-data="{ open: false }" @click.outside="open = false" class="relative" {{ $attributes }}>
    <div @click="open = !open" class="cursor-pointer">
        {{ $trigger }}
    </div>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $widthClass }} {{ $alignClasses }} rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-lg py-1"
        x-cloak
    >
        {{ $slot }}
    </div>
</div>

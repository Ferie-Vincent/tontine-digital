@props(['label' => null, 'error' => null, 'hint' => null, 'prefix' => null, 'suffix' => null, 'name' => null])

@php
    $inputName = $name ?? $attributes->get('name');
    $hasError = $error || ($inputName && $errors->has($inputName));
    $errorMessage = $error ?? ($inputName ? $errors->first($inputName) : null);
    $oldValue = $inputName ? old($inputName, $attributes->get('value')) : $attributes->get('value');
    $inputType = $attributes->get('type', 'text');

    $inputClasses = 'w-full rounded-lg border bg-white dark:bg-slate-800 px-4 py-2.5 text-slate-800 dark:text-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors';
    if ($prefix) $inputClasses .= ' pl-10';
    if ($suffix) $inputClasses .= ' pr-10';
    $inputClasses .= $hasError ? ' border-red-500' : ' border-slate-300 dark:border-slate-600';

    $extraAttributes = ['class' => $inputClasses];
    if ($inputType === 'number' && !$attributes->has('inputmode')) {
        $extraAttributes['inputmode'] = 'numeric';
    }
@endphp

<div>
    @if($label)
    <label @if($inputName) for="{{ $inputName }}" @endif class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $label }}</label>
    @endif
    <div class="relative">
        @if($prefix)
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
            {!! $prefix !!}
        </span>
        @endif
        <input
            @if($inputName) name="{{ $inputName }}" id="{{ $inputName }}" @endif
            @if($oldValue !== null) value="{{ $oldValue }}" @endif
            {{ $attributes->except(['name', 'value'])->merge($extraAttributes) }}
        >
        @if($suffix)
        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
            {!! $suffix !!}
        </span>
        @endif
    </div>
    @if($errorMessage)
    <p class="mt-1.5 text-sm text-red-500 flex items-center gap-1">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        {{ $errorMessage }}
    </p>
    @elseif($hint)
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>

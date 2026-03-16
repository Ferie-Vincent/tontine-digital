@props(['label' => null, 'error' => null, 'options' => [], 'placeholder' => null, 'name' => null, 'hint' => null])

@php
    $inputName = $name ?? $attributes->get('name');
    $hasError = $error || ($inputName && $errors->has($inputName));
    $errorMessage = $error ?? ($inputName ? $errors->first($inputName) : null);
    $oldValue = $inputName ? old($inputName, $attributes->get('value')) : $attributes->get('value');

    $selectClasses = 'w-full rounded-lg border bg-white dark:bg-slate-800 px-4 py-2.5 pr-10 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:primary-border transition-colors appearance-none';
    $selectClasses .= $hasError ? ' border-red-500' : ' border-slate-300 dark:border-slate-600';
@endphp

<div>
    @if($label)
    <label @if($inputName) for="{{ $inputName }}" @endif class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $label }}</label>
    @endif
    <div class="relative">
        <select
            @if($inputName) name="{{ $inputName }}" id="{{ $inputName }}" @endif
            {{ $attributes->except(['name', 'value'])->merge(['class' => $selectClasses]) }}
        >
            @if($placeholder)
            <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $value => $optionLabel)
            <option value="{{ $value }}" @selected($oldValue == $value)>{{ $optionLabel }}</option>
            @endforeach
            {{ $slot }}
        </select>
        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
    </div>
    @if($errorMessage)
    <p class="mt-1.5 text-sm text-red-500">{{ $errorMessage }}</p>
    @elseif($hint)
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>

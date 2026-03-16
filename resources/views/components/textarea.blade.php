@props(['label' => null, 'error' => null, 'hint' => null, 'value' => null, 'name' => null])

@php
    $inputName = $name ?? $attributes->get('name');
    $hasError = $error || ($inputName && $errors->has($inputName));
    $errorMessage = $error ?? ($inputName ? $errors->first($inputName) : null);

    // Ensure textValue is always a string
    $oldValue = $inputName ? old($inputName) : null;
    $textValue = $value ?? (is_string($oldValue) ? $oldValue : null);

    $textareaClasses = 'w-full rounded-lg border bg-white dark:bg-slate-800 px-4 py-2.5 text-slate-800 dark:text-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors resize-y';
    $textareaClasses .= $hasError ? ' border-red-500' : ' border-slate-300 dark:border-slate-600';
@endphp

<div>
    @if($label)
    <label @if($inputName) for="{{ $inputName }}" @endif class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $label }}</label>
    @endif
    <textarea
        @if($inputName) name="{{ $inputName }}" id="{{ $inputName }}" @endif
        {{ $attributes->except(['name', 'value'])->merge(['class' => $textareaClasses, 'rows' => '4']) }}
    >{{ $textValue ?? $slot }}</textarea>
    @if($errorMessage)
    <p class="mt-1.5 text-sm text-red-500">{{ $errorMessage }}</p>
    @elseif($hint)
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>

@props(['label' => null, 'error' => null, 'accept' => 'image/*', 'hint' => null, 'preview' => null, 'name' => null])

@php
    $inputName = $name ?? $attributes->get('name');
    $hasError = $error || ($inputName && $errors->has($inputName));
    $errorMessage = $error ?? ($inputName ? $errors->first($inputName) : null);
@endphp

<div x-data="{ fileName: '', previewUrl: '{{ $preview }}' }">
    @if($label)
    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $label }}</label>
    @endif
    <div class="relative">
        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-colors bg-slate-50 dark:bg-slate-800 {{ $hasError ? 'border-red-500' : 'border-slate-300 dark:border-slate-600 hover:primary-border' }}">
            <template x-if="previewUrl">
                <img :src="previewUrl" class="h-full w-full object-cover rounded-lg" />
            </template>
            <template x-if="!previewUrl">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-8 h-8 mb-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm text-slate-500 dark:text-slate-400" x-text="fileName || 'Cliquer pour telecharger'"></p>
                </div>
            </template>
            <input type="file" class="hidden" accept="{{ $accept }}" @if($inputName) name="{{ $inputName }}" id="{{ $inputName }}" @endif {{ $attributes->except(['name']) }} @change="fileName = $event.target.files[0]?.name; if($event.target.files[0]) { previewUrl = URL.createObjectURL($event.target.files[0]) }">
        </label>
    </div>
    @if($errorMessage)
    <p class="mt-1.5 text-sm text-red-500">{{ $errorMessage }}</p>
    @elseif($hint)
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>

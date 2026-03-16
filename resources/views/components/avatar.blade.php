@props(['src' => null, 'name' => '', 'size' => 'md', 'status' => null, 'user' => null])

@php
$sizeClasses = match($size) {
    'xs' => 'w-6 h-6 text-[10px]',
    'sm' => 'w-8 h-8 text-xs',
    'md' => 'w-10 h-10 text-sm',
    'lg' => 'w-12 h-12 text-base',
    'xl' => 'w-16 h-16 text-lg',
    '2xl' => 'w-20 h-20 text-xl',
    default => 'w-10 h-10 text-sm',
};

$statusSizeClasses = match($size) {
    'xs' => 'w-1.5 h-1.5',
    'sm' => 'w-2 h-2',
    'md' => 'w-2.5 h-2.5',
    'lg' => 'w-3 h-3',
    'xl' => 'w-3.5 h-3.5',
    '2xl' => 'w-4 h-4',
    default => 'w-2.5 h-2.5',
};

$statusColorClasses = match($status) {
    'online' => 'bg-emerald-500',
    'offline' => 'bg-slate-400',
    'busy' => 'bg-red-500',
    'away' => 'bg-amber-500',
    default => 'bg-slate-400',
};

// Auto-resolve from user model
if ($user) {
    $name = $name ?: $user->name;
    $src = $src ?: $user->avatar_url;
}

$initials = collect(explode(' ', $name))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');

// Colors based on name hash
$colors = [
    'bg-blue-500',
    'bg-violet-500',
    'bg-emerald-500',
    'bg-amber-500',
    'bg-rose-500',
    'bg-cyan-500',
    'bg-indigo-500',
    'bg-pink-500',
];
$colorIndex = abs(crc32($name)) % count($colors);
$bgColor = $colors[$colorIndex];
@endphp

<div class="relative inline-flex">
    @if($src)
    <img src="{{ $src }}" alt="{{ $name }}" {{ $attributes->merge(['class' => "rounded-full object-cover {$sizeClasses}"]) }}>
    @else
    <div {{ $attributes->merge(['class' => "rounded-full {$bgColor} flex items-center justify-center text-white font-semibold {$sizeClasses}"]) }}>
        {{ strtoupper($initials) }}
    </div>
    @endif

    @if($status)
    <span class="absolute bottom-0 right-0 {{ $statusSizeClasses }} {{ $statusColorClasses }} rounded-full ring-2 ring-white dark:ring-slate-800"></span>
    @endif
</div>

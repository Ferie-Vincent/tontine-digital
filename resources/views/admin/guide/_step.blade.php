@props(['num', 'title', 'desc'])

<div class="flex gap-4 mb-4">
    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#3C50E0] text-white flex items-center justify-center text-sm font-bold">{{ $num }}</div>
    <div class="flex-1 pt-0.5">
        <h4 class="font-semibold text-slate-800 dark:text-white mb-1">{{ $title }}</h4>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ $desc }}</p>
    </div>
</div>

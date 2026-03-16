@props(['caption' => 'Capture d\'ecran'])

<div class="my-6 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 overflow-hidden">
    <div class="bg-slate-200 dark:bg-slate-700 px-4 py-2 flex items-center gap-2">
        <div class="flex gap-1.5">
            <div class="w-3 h-3 rounded-full bg-red-400"></div>
            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
            <div class="w-3 h-3 rounded-full bg-green-400"></div>
        </div>
        <span class="text-xs text-slate-500 dark:text-slate-400 ml-2">{{ $caption }}</span>
    </div>
    <div class="p-8 flex items-center justify-center min-h-[200px]">
        <div class="text-center">
            <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm text-slate-400 dark:text-slate-500">Capture d'ecran : {{ $caption }}</p>
            <p class="text-xs text-slate-300 dark:text-slate-600 mt-1">Ajoutez une image ici</p>
        </div>
    </div>
</div>

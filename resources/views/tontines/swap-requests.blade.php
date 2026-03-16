<x-layouts.app :title="'Demandes d\'échange - ' . $tontine->name">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Demandes d'échange de position</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $tontine->name }}</p>
            </div>
            <a href="{{ route('tontines.show', $tontine) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
        </div>
    </x-slot:header>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="max-w-3xl mx-auto">
        @if($swaps->isEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">Aucune demande d'échange</h3>
                <p class="text-slate-500 dark:text-slate-400">Vous n'avez aucune demande d'échange de position en attente.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($swaps as $swap)
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-violet-600 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold text-slate-800 dark:text-white">
                                        {{ $swap->requester->name }} souhaite échanger de position avec vous
                                    </h3>
                                    <div class="mt-3 flex flex-wrap gap-4">
                                        <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                                            <span class="text-xs text-slate-500 dark:text-slate-400">Sa position :</span>
                                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">#{{ $swap->requester_position }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        </div>
                                        <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                                            <span class="text-xs text-slate-500 dark:text-slate-400">Votre position :</span>
                                            <span class="text-sm font-bold text-violet-600 dark:text-violet-400">#{{ $swap->target_position }}</span>
                                        </div>
                                    </div>
                                    @if($swap->reason)
                                        <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                            <p class="text-xs font-medium text-amber-700 dark:text-amber-400 mb-1">Motif :</p>
                                            <p class="text-sm text-amber-800 dark:text-amber-300">{{ $swap->reason }}</p>
                                        </div>
                                    @endif
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">Demandé {{ $swap->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                                <form method="POST" action="{{ route('tontines.swap.respond', [$tontine, $swap]) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Refuser
                                    </button>
                                </form>
                                <div x-data="{ showSwapConfirm: false }">
                                    <button type="button" @click="showSwapConfirm = true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-lg shadow-emerald-500/20 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Accepter l'échange
                                    </button>
                                    <div x-show="showSwapConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                        <div class="fixed inset-0 bg-black/50" @click="showSwapConfirm = false"></div>
                                        <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full p-6"
                                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                </div>
                                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Confirmer l'échange de position ?</h3>
                                            </div>
                                            <p class="text-slate-600 dark:text-slate-400 mb-6">Les positions des deux membres seront échangées. Cette action modifiera l'ordre de passage des tours.</p>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="showSwapConfirm = false" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">Annuler</button>
                                                <form method="POST" action="{{ route('tontines.swap.respond', [$tontine, $swap]) }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="action" value="accept">
                                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">Accepter</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>

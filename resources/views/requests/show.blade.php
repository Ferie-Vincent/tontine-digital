<x-layouts.app :title="'Requête #' . $userRequest->id">
    <x-slot:header>Requête #{{ $userRequest->id }}</x-slot:header>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
        <div class="mb-6">
            <a href="{{ route('requests.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour aux requêtes
            </a>
        </div>

        {{-- Request Details --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-badge :color="$userRequest->type->color()">{{ $userRequest->type->label() }}</x-badge>
                        <x-badge :color="$userRequest->status->color()" dot>{{ $userRequest->status->label() }}</x-badge>
                    </div>
                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ $userRequest->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-4">{{ $userRequest->subject }}</h2>
                <div class="prose prose-sm dark:prose-invert max-w-none text-slate-600 dark:text-slate-300">
                    {!! nl2br(e($userRequest->description)) !!}
                </div>

                @if($userRequest->tontine)
                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        <span class="font-medium">Tontine :</span>
                        <a href="{{ route('tontines.show', $userRequest->tontine) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $userRequest->tontine->name }}</a>
                    </p>
                </div>
                @endif

                <div class="mt-4 flex items-center gap-4 text-xs text-slate-400 dark:text-slate-500">
                    <span>Priorite : <span class="font-medium {{ $userRequest->priority === 'high' ? 'text-red-500' : ($userRequest->priority === 'normal' ? 'text-slate-500' : 'text-slate-400') }}">{{ $userRequest->priority === 'high' ? 'Haute' : ($userRequest->priority === 'normal' ? 'Normale' : 'Faible') }}</span></span>
                    <span>Soumise {{ $userRequest->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- Admin Response --}}
        @if($userRequest->admin_response)
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-emerald-50 dark:bg-emerald-500/5">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Reponse de l'administrateur</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="prose prose-sm dark:prose-invert max-w-none text-slate-600 dark:text-slate-300">
                    {!! nl2br(e($userRequest->admin_response)) !!}
                </div>
                <div class="mt-4 flex items-center gap-4 text-xs text-slate-400 dark:text-slate-500">
                    @if($userRequest->responder)
                    <span>Par {{ $userRequest->responder->name }}</span>
                    @endif
                    @if($userRequest->responded_at)
                    <span>{{ $userRequest->responded_at->format('d/m/Y H:i') }}</span>
                    @endif
                </div>
            </div>
        </div>
        @else
        <div class="bg-amber-50 dark:bg-amber-500/5 rounded-xl border border-amber-200 dark:border-amber-500/20 p-6 text-center">
            <svg class="w-10 h-10 mx-auto mb-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-amber-700 dark:text-amber-300 font-medium">En attente de reponse de l'administrateur</p>
            <p class="text-xs text-amber-500 dark:text-amber-400 mt-1">Vous serez notifie des que l'administrateur aura repondu.</p>
        </div>
        @endif
    </div>
</x-layouts.app>

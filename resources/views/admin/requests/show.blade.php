<x-layouts.app :title="'Requête #' . $userRequest->id">
    <x-slot:header>Requête #{{ $userRequest->id }}</x-slot:header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
        <div class="mb-6">
            <a href="{{ route('admin.requests.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour aux requêtes
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Request Details --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">{{ $userRequest->subject }}</h2>
                            <x-badge :color="$userRequest->status->color()" dot>{{ $userRequest->status->label() }}</x-badge>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="prose prose-sm dark:prose-invert max-w-none text-slate-600 dark:text-slate-300">
                            {!! nl2br(e($userRequest->description)) !!}
                        </div>
                    </div>
                </div>

                {{-- Existing Response --}}
                @if($userRequest->admin_response)
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-emerald-50 dark:bg-emerald-500/5">
                        <h3 class="font-semibold text-slate-800 dark:text-white">Reponse precedente</h3>
                    </div>
                    <div class="p-6">
                        <div class="prose prose-sm dark:prose-invert max-w-none text-slate-600 dark:text-slate-300">
                            {!! nl2br(e($userRequest->admin_response)) !!}
                        </div>
                        <div class="mt-3 text-xs text-slate-400">
                            @if($userRequest->responder) Par {{ $userRequest->responder->name }} @endif
                            @if($userRequest->responded_at) - {{ $userRequest->responded_at->format('d/m/Y H:i') }} @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Response Form --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="font-semibold text-slate-800 dark:text-white">{{ $userRequest->admin_response ? 'Modifier la reponse' : 'Repondre' }}</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.requests.respond', $userRequest) }}" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')

                        <x-textarea name="admin_response" label="Votre reponse" rows="5" :value="old('admin_response', $userRequest->admin_response)" :error="$errors->first('admin_response')" required placeholder="Redigez votre reponse..." />

                        <x-select name="status" label="Changer le statut" :error="$errors->first('status')" required>
                            <option value="in_progress" {{ old('status', $userRequest->status->value) === 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="resolved" {{ old('status', $userRequest->status->value) === 'resolved' ? 'selected' : '' }}>Resolu</option>
                            <option value="rejected" {{ old('status', $userRequest->status->value) === 'rejected' ? 'selected' : '' }}>Rejete</option>
                        </x-select>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                            <x-button type="submit" variant="primary">Envoyer la reponse</x-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sidebar Info --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4">Informations</h3>
                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">Utilisateur</dt>
                            <dd class="font-medium text-slate-800 dark:text-white mt-0.5">{{ $userRequest->user->name }}</dd>
                            <dd class="text-xs text-slate-400">{{ $userRequest->user->phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">Type</dt>
                            <dd class="mt-0.5"><x-badge :color="$userRequest->type->color()" size="xs">{{ $userRequest->type->label() }}</x-badge></dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">Priorite</dt>
                            <dd class="font-medium mt-0.5 {{ $userRequest->priority === 'high' ? 'text-red-500' : ($userRequest->priority === 'normal' ? 'text-slate-800 dark:text-white' : 'text-slate-400') }}">
                                {{ $userRequest->priority === 'high' ? 'Haute' : ($userRequest->priority === 'normal' ? 'Normale' : 'Faible') }}
                            </dd>
                        </div>
                        @if($userRequest->tontine)
                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">Tontine</dt>
                            <dd class="mt-0.5">
                                <a href="{{ route('tontines.show', $userRequest->tontine) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ $userRequest->tontine->name }}</a>
                            </dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-slate-500 dark:text-slate-400">Date de soumission</dt>
                            <dd class="font-medium text-slate-800 dark:text-white mt-0.5">{{ $userRequest->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

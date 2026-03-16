<x-layouts.app :title="'Nouvelle requête'">
    <x-slot:header>Nouvelle requête</x-slot:header>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
        <div class="mb-6">
            <a href="{{ route('requests.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour aux requêtes
            </a>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Soumettre une requête</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Décrivez votre demande et nous vous répondrons dans les meilleurs délais.</p>
            </div>

            <form method="POST" action="{{ route('requests.store') }}" class="p-6 space-y-5">
                @csrf

                <x-select name="type" label="Type de requête" :error="$errors->createRequest->first('type')" required>
                    <option value="">-- Sélectionnez --</option>
                    @foreach(\App\Enums\RequestType::cases() as $type)
                    <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                    @endforeach
                </x-select>

                <x-input name="subject" label="Sujet" placeholder="Résumé de votre demande" :value="old('subject')" :error="$errors->createRequest->first('subject')" required />

                <x-textarea name="description" label="Description" placeholder="Décrivez votre demande en détail..." :value="old('description')" rows="5" :error="$errors->createRequest->first('description')" required />

                <x-select name="tontine_id" label="Tontine concernée (optionnel)" :error="$errors->createRequest->first('tontine_id')">
                    <option value="">-- Aucune tontine --</option>
                    @foreach($tontines as $tontine)
                    <option value="{{ $tontine->id }}" {{ old('tontine_id') == $tontine->id ? 'selected' : '' }}>{{ $tontine->name }}</option>
                    @endforeach
                </x-select>

                <x-select name="priority" label="Priorite" :error="$errors->createRequest->first('priority')">
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Faible</option>
                    <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normale</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                </x-select>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <x-button type="submit" variant="primary">Envoyer la requête</x-button>
                    <x-button :href="route('requests.index')" variant="ghost">Annuler</x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

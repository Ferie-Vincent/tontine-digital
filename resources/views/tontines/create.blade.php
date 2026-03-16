<x-layouts.app title="Créer une tontine">
    <x-slot:header>
        <div class="flex items-center gap-3">
            <a href="{{ route('tontines.index') }}" class="text-text-muted hover:text-text transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-bold text-text">Créer une tontine</h2>
        </div>
    </x-slot:header>

    <div class="max-w-2xl mx-auto">
        <x-card>
            <form method="POST" action="{{ route('tontines.store') }}" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                <x-input name="name" label="Nom de la tontine" placeholder="Ex: Tontine des collègues" required />
                <x-textarea name="description" label="Description" placeholder="Décrivez votre tontine..." rows="3" />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input name="contribution_amount" label="Montant de cotisation (FCFA)" type="number" min="1000" step="500" placeholder="50000" required />
                    <x-select name="frequency" label="Fréquence" required>
                        <option value="">Choisir...</option>
                        <option value="weekly" {{ old('frequency') === 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="biweekly" {{ old('frequency') === 'biweekly' ? 'selected' : '' }}>Bimensuelle</option>
                        <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                    </x-select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input name="target_amount_per_tour" label="Cagnotte visée par tour (FCFA)" type="number" min="1000" step="500" placeholder="500000" hint="Montant indicatif que chaque beneficiaire devrait recevoir." />
                    <x-input name="target_amount_total" label="Objectif global (FCFA)" type="number" min="1000" step="500" placeholder="5000000" hint="Montant total vise sur toute la duree." />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input name="max_members" label="Nombre max de membres" type="number" min="2" max="100" placeholder="10" required />
                    <x-input name="start_date" label="Date de début" type="date" required />
                </div>
                <x-input name="end_date" label="Date de fin (optionnel)" type="date" hint="Laissez vide si la tontine n'a pas de date de fin définie." />
                <x-textarea name="rules" label="Règlement (optionnel)" placeholder="Règles de la tontine..." rows="4" />
                <div class="flex justify-end gap-3">
                    <a href="{{ route('tontines.index') }}"><x-button variant="ghost">Annuler</x-button></a>
                    <x-button type="submit" variant="primary" ::disabled="submitting">
                        <span x-show="!submitting">Créer la tontine</span>
                        <span x-show="submitting" x-cloak class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Création en cours...
                        </span>
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>

<div>
    @if(!$showForm)
    <x-button wire:click="$set('showForm', true)" variant="accent" size="sm" class="w-full">
        Déclarer ma contribution
    </x-button>
    @else
    <div class="mt-4 mb-3 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800">
        <p class="text-sm text-blue-800 dark:text-blue-200">
            <span class="font-semibold">Montant attendu :</span>
            {{ format_amount($expectedAmount) }}
            @if($memberParts > 1)
                <span class="text-blue-600 dark:text-blue-400">({{ $memberParts }} parts &times; {{ format_amount($contribution->tontine->contribution_amount) }})</span>
            @endif
        </p>
        @if($contribution->amount != $expectedAmount)
            <p class="text-sm text-amber-700 dark:text-amber-400 mt-1 font-medium">
                Le montant de cette contribution ({{ format_amount($contribution->amount) }}) diffère du montant attendu.
                @php $deviation = abs($contribution->amount - $expectedAmount) / $expectedAmount * 100; @endphp
                @if($deviation > 10)
                    Cette déclaration nécessitera une vérification par un gestionnaire.
                @endif
            </p>
        @endif
    </div>

    <form wire:submit="declare" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-text-secondary mb-1.5">Méthode de paiement</label>
            <select wire:model="payment_method" class="w-full bg-input border border-border rounded-lg px-4 py-2.5 text-text text-sm focus:border-[#2E86AB] focus:ring-1 focus:ring-[#2E86AB]">
                @foreach(\App\Enums\PaymentMethod::cases() as $method)
                    <option value="{{ $method->value }}">{{ $method->label() }}</option>
                @endforeach
            </select>
            @error('payment_method') <p class="mt-1 text-sm text-[#E74C3C]">{{ $message }}</p> @enderror
        </div>

        <x-input
            label="Référence de transaction"
            wire:model="transaction_reference"
            placeholder="Ex: CI240101XXXX"
            :error="$errors->first('transaction_reference')"
        />

        <x-input
            label="Numéro émetteur"
            wire:model="sender_phone"
            placeholder="07 XX XX XX XX"
            :error="$errors->first('sender_phone')"
        />

        <x-input
            label="Date de la transaction"
            wire:model="transaction_date"
            type="datetime-local"
            :error="$errors->first('transaction_date')"
        />

        <div>
            <label class="block text-sm font-medium text-text-secondary mb-1.5">Capture d'écran (optionnel)</label>
            <input type="file" wire:model="screenshot" accept="image/*" class="w-full text-sm text-text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-[#2E86AB]/20 file:text-[#2E86AB] hover:file:bg-[#2E86AB]/30">
            @error('screenshot') <p class="mt-1 text-sm text-[#E74C3C]">{{ $message }}</p> @enderror
        </div>

        <x-textarea
            label="Notes (optionnel)"
            wire:model="notes"
            placeholder="Informations complémentaires..."
            rows="2"
        />

        <div class="flex gap-2">
            <x-button type="submit" variant="accent" size="sm" class="flex-1">
                <span wire:loading.remove wire:target="declare">Déclarer</span>
                <span wire:loading wire:target="declare">Envoi...</span>
            </x-button>
            <x-button wire:click="$set('showForm', false)" variant="ghost" size="sm">Annuler</x-button>
        </div>
    </form>
    @endif
</div>

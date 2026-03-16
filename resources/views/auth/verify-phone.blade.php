<x-layouts.guest title="Vérification du téléphone - DIGI-TONTINE CI">
    <h2 class="text-xl font-bold text-text mb-2">Vérifiez votre numéro</h2>
    <p class="text-sm text-text-muted mb-6">
        Un code de vérification à 6 chiffres sera envoyé au <strong>{{ auth()->user()->formatted_phone }}</strong>
    </p>

    @if(session('status'))
        <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    @if(session('error'))
        <x-alert type="error" class="mb-4">{{ session('error') }}</x-alert>
    @endif

    {{-- Bouton envoyer le code --}}
    <form method="POST" action="{{ route('phone.verify.send') }}" class="mb-6" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        <x-button type="submit" variant="secondary" class="w-full" size="lg" ::disabled="submitting">
            <span x-show="!submitting">Envoyer le code de vérification</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Envoi en cours...
            </span>
        </x-button>
    </form>

    {{-- Formulaire de vérification --}}
    <form method="POST" action="{{ route('phone.verify.submit') }}" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        <x-input
            label="Code de vérification"
            type="text"
            name="code"
            placeholder="000000"
            maxlength="6"
            :error="$errors->first('code')"
            required
            autofocus
        />

        <x-button type="submit" variant="primary" class="w-full" size="lg" ::disabled="submitting">
            <span x-show="!submitting">Vérifier</span>
            <span x-show="submitting" x-cloak class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Vérification...
            </span>
        </x-button>
    </form>

    <div class="mt-6 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-[#2E86AB] hover:text-[#2E86AB]/80">Se déconnecter</button>
        </form>
    </div>
</x-layouts.guest>

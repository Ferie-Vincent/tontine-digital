<x-layouts.guest title="Vérification du code - DIGI-TONTINE CI">
    <h2 class="text-xl font-bold text-text mb-2">Vérification du code</h2>
    <p class="text-sm text-text-muted mb-6">Entrez le code reçu par SMS</p>

    @if(session('status'))
        <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.reset.sms.verify') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="phone" value="{{ $phone }}">

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

        <x-button type="submit" variant="primary" class="w-full" size="lg">
            Vérifier le code
        </x-button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('password.request') }}" class="text-sm text-[#2E86AB] hover:text-[#2E86AB]/80">Retour</a>
    </div>
</x-layouts.guest>

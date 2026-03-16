<x-layouts.guest title="Nouveau mot de passe - DIGI-TONTINE CI">
    <h2 class="text-xl font-bold text-text mb-2">Nouveau mot de passe</h2>
    <p class="text-sm text-text-muted mb-6">Choisissez votre nouveau mot de passe</p>

    @if($errors->any())
        <x-alert type="error" class="mb-4">{{ $errors->first() }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.reset.submit') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="via" value="{{ $via }}">
        @if($via === 'email')
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
        @endif

        <x-input
            label="Nouveau mot de passe"
            type="password"
            name="password"
            :error="$errors->first('password')"
            required
        />

        <x-input
            label="Confirmer le mot de passe"
            type="password"
            name="password_confirmation"
            required
        />

        <x-button type="submit" variant="primary" class="w-full" size="lg">
            Réinitialiser le mot de passe
        </x-button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-[#2E86AB] hover:text-[#2E86AB]/80">Retour à la connexion</a>
    </div>
</x-layouts.guest>

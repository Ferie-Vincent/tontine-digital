<x-layouts.guest title="Connexion - DIGI-TONTINE CI">
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">Connexion</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Connectez-vous a votre compte DIGI-TONTINE</p>
    </div>

    @livewire("auth.login-form")

    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700 text-center">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Pas encore de compte ?
            <a href="{{ route("register") }}" class="primary-text hover:underline font-semibold">Creer un compte</a>
        </p>
    </div>
</x-layouts.guest>

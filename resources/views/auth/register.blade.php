<x-layouts.guest title="Inscription - DIGI-TONTINE CI">
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">Creer un compte</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Rejoignez la communaute DIGI-TONTINE</p>
    </div>

    @livewire("auth.register-form")

    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700 text-center">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Deja inscrit ?
            <a href="{{ route("login") }}" class="primary-text hover:underline font-semibold">Se connecter</a>
        </p>
    </div>
</x-layouts.guest>

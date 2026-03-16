<x-layouts.guest title="Mot de passe oublié - DIGI-TONTINE CI">
    <h2 class="text-xl font-bold text-text mb-2">Mot de passe oublié</h2>
    <p class="text-sm text-text-muted mb-6">Choisissez votre méthode de récupération</p>

    @if(session('status'))
        <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    @if($errors->any())
        <x-alert type="error" class="mb-4">{{ $errors->first() }}</x-alert>
    @endif

    <div x-data="{ tab: 'sms' }" class="space-y-5">
        {{-- Tab switcher --}}
        <div class="flex rounded-lg bg-slate-100 dark:bg-slate-800 p-1">
            <button type="button" @click="tab = 'sms'"
                :class="tab === 'sms' ? 'bg-white dark:bg-slate-700 shadow text-[#2E86AB] font-semibold' : 'text-slate-500'"
                class="flex-1 py-2 px-3 text-sm rounded-md transition-all duration-200">
                Par SMS
            </button>
            <button type="button" @click="tab = 'email'"
                :class="tab === 'email' ? 'bg-white dark:bg-slate-700 shadow text-[#2E86AB] font-semibold' : 'text-slate-500'"
                class="flex-1 py-2 px-3 text-sm rounded-md transition-all duration-200">
                Par Email
            </button>
        </div>

        {{-- SMS form --}}
        <form x-show="tab === 'sms'" method="POST" action="{{ route('password.reset.sms') }}" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            <x-input
                label="Numéro de téléphone"
                type="tel"
                name="phone"
                :value="old('phone')"
                placeholder="07 XX XX XX XX"
                :error="$errors->first('phone')"
                required
            />
            <x-button type="submit" variant="primary" class="w-full" size="lg" ::disabled="submitting">
                <span x-show="!submitting">Recevoir un code par SMS</span>
                <span x-show="submitting" x-cloak class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Envoi en cours...
                </span>
            </x-button>
        </form>

        {{-- Email form --}}
        <form x-show="tab === 'email'" method="POST" action="{{ route('password.email') }}" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            <x-input
                label="Email"
                type="email"
                name="email"
                :value="old('email')"
                placeholder="votre@email.com"
                :error="$errors->first('email')"
                required
            />
            <x-button type="submit" variant="primary" class="w-full" size="lg" ::disabled="submitting">
                <span x-show="!submitting">Envoyer le lien par email</span>
                <span x-show="submitting" x-cloak class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Envoi en cours...
                </span>
            </x-button>
        </form>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-[#2E86AB] hover:text-[#2E86AB]/80">Retour à la connexion</a>
    </div>
</x-layouts.guest>

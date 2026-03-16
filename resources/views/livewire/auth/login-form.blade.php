<form wire:submit="login" class="space-y-5">
    <x-input
        label="Numéro de téléphone"
        type="tel"
        wire:model="phone"
        placeholder="07 XX XX XX XX"
        :error="$errors->first('phone')"
        required
    />

    <x-input
        label="Mot de passe"
        type="password"
        wire:model="password"
        placeholder="Votre mot de passe"
        :error="$errors->first('password')"
        required
    />

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 primary-text focus:ring-[#3C50E0]">
            <span class="text-sm text-slate-600 dark:text-slate-400">Se souvenir de moi</span>
        </label>
        <a href="{{ route('password.request') }}" class="text-sm primary-text hover:underline">Mot de passe oublie ?</a>
    </div>

    <x-button type="submit" variant="primary" class="w-full" size="lg">
        <span wire:loading.remove>Se connecter</span>
        <span wire:loading>
            <x-loading size="sm" />
        </span>
    </x-button>
</form>

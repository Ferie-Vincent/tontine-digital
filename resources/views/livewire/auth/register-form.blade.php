<form wire:submit="register" class="space-y-5">
    <x-input
        label="Nom complet"
        type="text"
        wire:model="name"
        placeholder="Votre nom et prenoms"
        :error="$errors->first('name')"
        required
    />

    <x-input
        label="Numéro de téléphone"
        type="tel"
        wire:model="phone"
        placeholder="07 XX XX XX XX"
        hint="Format: 07/05/01 XX XX XX XX"
        :error="$errors->first('phone')"
        required
    />

    <x-input
        label="Email (optionnel)"
        type="email"
        wire:model="email"
        placeholder="votre@email.com"
        :error="$errors->first('email')"
    />

    <x-input
        label="Mot de passe"
        type="password"
        wire:model="password"
        placeholder="Minimum 8 caracteres"
        :error="$errors->first('password')"
        required
    />

    <x-input
        label="Confirmer le mot de passe"
        type="password"
        wire:model="password_confirmation"
        placeholder="Confirmez votre mot de passe"
        required
    />

    <x-button type="submit" variant="primary" class="w-full" size="lg">
        <span wire:loading.remove>Creer mon compte</span>
        <span wire:loading>
            <x-loading size="sm" />
        </span>
    </x-button>
</form>

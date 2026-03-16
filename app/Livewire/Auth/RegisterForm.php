<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class RegisterForm extends Component
{
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    protected $messages = [
        'name.required' => 'Le nom est obligatoire.',
        'phone.required' => 'Le numéro de téléphone est obligatoire.',
        'email.unique' => 'Cet email est déjà utilisé.',
        'password.required' => 'Le mot de passe est obligatoire.',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        'password.confirmed' => 'Les mots de passe ne correspondent pas.',
    ];

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-]/', '', $phone);
        if (str_starts_with($phone, '00225')) {
            $phone = '+225' . substr($phone, 5);
        }
        if (str_starts_with($phone, '225') && !str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        if (str_starts_with($phone, '0')) {
            $phone = '+225' . substr($phone, 1);
        }
        if (!str_starts_with($phone, '+')) {
            $phone = '+225' . $phone;
        }
        return $phone;
    }

    public function register()
    {
        $this->validate();

        $phone = $this->normalizePhone($this->phone);

        if (User::where('phone', $phone)->exists()) {
            $this->addError('phone', 'Ce numéro de téléphone est déjà enregistré.');
            return;
        }

        if (!preg_match('/^\+225\d{10}$/', $phone)) {
            $this->addError('phone', 'Format invalide. Utilisez un numéro ivoirien (ex: 07 XX XX XX XX).');
            return;
        }

        $user = User::create([
            'name' => $this->name,
            'phone' => $phone,
            'email' => $this->email ?: null,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);
        session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register-form');
    }
}

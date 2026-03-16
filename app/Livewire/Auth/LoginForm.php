<?php

namespace App\Livewire\Auth;

use App\Models\UserSession;
use App\Services\LoginProtectionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginForm extends Component
{
    public string $phone = '';
    public string $password = '';
    public bool $remember = false;

    protected $rules = [
        'phone' => 'required|string',
        'password' => 'required|string',
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

    public function login()
    {
        $this->validate();

        $phone = $this->normalizePhone($this->phone);
        $protectionService = app(LoginProtectionService::class);

        // Verifier si le compte est verrouille
        $lockStatus = $protectionService->checkLock($phone);
        if ($lockStatus['locked']) {
            throw ValidationException::withMessages([
                'phone' => $lockStatus['message'],
            ]);
        }

        if (Auth::attempt(['phone' => $phone, 'password' => $this->password], $this->remember)) {
            $protectionService->clearOnSuccess($phone);

            if (auth()->user()->status !== 'active') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'phone' => 'Votre compte a été suspendu. Contactez l\'administrateur.',
                ]);
            }

            session()->regenerate();

            // Enregistrer la session de l'appareil
            UserSession::create([
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_name' => UserSession::parseDeviceName(request()->userAgent()),
                'last_activity' => now(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        // Enregistrer la tentative echouee
        $protectionService->recordFailure($phone);

        throw ValidationException::withMessages([
            'phone' => 'Identifiants incorrects. Vérifiez votre numéro et mot de passe.',
        ]);
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;

class PhoneVerificationController extends Controller
{
    public function show()
    {
        if (auth()->user()->isPhoneVerified()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-phone');
    }

    public function sendCode(OtpService $otpService)
    {
        $user = auth()->user();

        if ($user->isPhoneVerified()) {
            return redirect()->route('dashboard');
        }

        $result = $otpService->send($user->phone, 'registration');

        $message = 'Code de vérification envoyé au ' . $user->formatted_phone . '.';
        if (app()->environment('local') && isset($result['code'])) {
            $message .= ' (Dev: ' . $result['code'] . ')';
        }

        return back()->with('status', $message);
    }

    public function verify(Request $request, OtpService $otpService)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'Le code est obligatoire.',
            'code.size' => 'Le code doit contenir 6 chiffres.',
        ]);

        $user = auth()->user();

        if ($user->isPhoneVerified()) {
            return redirect()->route('dashboard');
        }

        if (!$otpService->verify($user->phone, $request->code, 'registration')) {
            return back()->withErrors(['code' => 'Code invalide ou expiré. Veuillez réessayer.']);
        }

        $user->update(['phone_verified_at' => now()]);

        return redirect()->route('dashboard')->with('success', 'Numéro de téléphone vérifié avec succès !');
    }
}

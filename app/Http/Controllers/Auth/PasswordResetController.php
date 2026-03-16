<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset link by email.
     */
    public function sendByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'Format d\'email invalide.',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            // Send email with reset link
            $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($user->email));

            Mail::send('emails.password-reset', ['resetUrl' => $resetUrl, 'user' => $user], function ($message) use ($user) {
                $message->to($user->email)->subject('Réinitialisation de votre mot de passe - DIGI-TONTINE CI');
            });
        }

        return back()->with('status', 'Si un compte existe avec cet email, vous recevrez un lien de réinitialisation.');
    }

    /**
     * Send OTP by SMS.
     */
    public function sendBySms(Request $request, OtpService $otpService)
    {
        $request->validate([
            'phone' => 'required|string',
        ], [
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
        ]);

        $phone = $this->normalizePhone($request->phone);
        $user = User::where('phone', $phone)->first();

        if ($user) {
            $result = $otpService->send($phone, 'reset');

            $devCode = null;
            if (app()->environment('local') && isset($result['code'])) {
                $devCode = $result['code'];
            }
        }

        $message = 'Si un compte existe avec ce numéro, vous recevrez un code par SMS.';
        if (isset($devCode)) {
            $message .= ' (Dev: ' . $devCode . ')';
        }

        return redirect()->route('password.reset.sms.verify.form', ['phone' => $request->phone])
            ->with('status', $message);
    }

    /**
     * Show SMS OTP verification form.
     */
    public function showSmsVerifyForm(Request $request)
    {
        return view('auth.reset-password-sms-verify', ['phone' => $request->query('phone', '')]);
    }

    /**
     * Verify SMS OTP and show new password form.
     */
    public function verifySmsCode(Request $request, OtpService $otpService)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        $phone = $this->normalizePhone($request->phone);

        if (!$otpService->verify($phone, $request->code, 'reset')) {
            return back()->withErrors(['code' => 'Code invalide ou expiré.'])->withInput();
        }

        // Generate a temporary token to allow password reset
        $resetToken = Str::random(64);
        session(['sms_reset_token' => $resetToken, 'sms_reset_phone' => $phone]);

        return redirect()->route('password.reset.new', ['via' => 'sms']);
    }

    /**
     * Show reset form (from email link or after SMS verification).
     */
    public function showResetForm(Request $request)
    {
        $via = $request->query('via', 'email');

        if ($via === 'sms') {
            if (!session('sms_reset_token')) {
                return redirect()->route('password.request')->withErrors(['error' => 'Session expirée. Veuillez recommencer.']);
            }
            return view('auth.reset-password', ['via' => 'sms', 'token' => null, 'email' => null]);
        }

        return view('auth.reset-password', [
            'via' => 'email',
            'token' => $request->query('token'),
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Process password reset.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $via = $request->input('via', 'email');

        if ($via === 'sms') {
            $phone = session('sms_reset_phone');
            if (!$phone || !session('sms_reset_token')) {
                return redirect()->route('password.request')->withErrors(['error' => 'Session expirée.']);
            }

            $user = User::where('phone', $phone)->first();
            if (!$user) {
                return redirect()->route('password.request')->withErrors(['error' => 'Utilisateur introuvable.']);
            }

            $user->update(['password' => Hash::make($request->password)]);
            session()->forget(['sms_reset_token', 'sms_reset_phone']);

        } else {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
            ]);

            $record = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$record || !Hash::check($request->token, $record->token)) {
                return back()->withErrors(['email' => 'Lien de réinitialisation invalide ou expiré.']);
            }

            if (now()->diffInMinutes($record->created_at) > 60) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return back()->withErrors(['email' => 'Le lien a expiré. Veuillez en demander un nouveau.']);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return back()->withErrors(['email' => 'Utilisateur introuvable.']);
            }

            $user->update(['password' => Hash::make($request->password)]);
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        }

        return redirect()->route('login')->with('success', 'Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }

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
}

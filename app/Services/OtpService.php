<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\SiteSettings;

class OtpService
{
    public function __construct(private SmsService $smsService) {}

    /**
     * Generate and send OTP code.
     * Types: registration, login, reset
     */
    public function send(string $phone, string $type = 'registration'): array
    {
        // Invalidate previous OTP codes for this phone/type
        OtpCode::where('phone', $phone)
            ->where('type', $type)
            ->whereNull('verified_at')
            ->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'type' => $type,
            'expires_at' => now()->addMinutes(10),
        ]);

        $appName = SiteSettings::get('platform_name', 'DIGI-TONTINE CI');
        $message = "{$appName} - Votre code de vérification est : {$code}. Il expire dans 10 minutes.";

        $result = $this->smsService->send($phone, $message);

        return [
            'success' => true,
            'sms_sent' => $result['success'] ?? false,
            'code' => app()->environment('local') ? $code : null, // Only in dev
        ];
    }

    /**
     * Verify an OTP code.
     */
    public function verify(string $phone, string $code, string $type = 'registration'): bool
    {
        $otp = OtpCode::valid($phone, $code)
            ->where('type', $type)
            ->first();

        if (!$otp) {
            return false;
        }

        $otp->update(['verified_at' => now()]);

        return true;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    /**
     * Whitelist of allowed setting keys that can be updated via admin panel.
     */
    private const ALLOWED_KEYS = [
        // Tontines
        'allow_user_tontine_creation',
        'max_tontines_per_user',
        'min_contribution_amount',
        'max_contribution_amount',
        'default_max_members',
        // Security
        'allow_registration',
        'require_phone_verification',
        'max_login_attempts',
        // Notifications
        'enable_email_notifications',
        'enable_push_notifications',
        'reminder_default_days',
        // Platform
        'platform_name',
        'support_phone',
        'support_email',
        'maintenance_mode',
        'archive_after_days',
        // SMS
        'sms_provider',
        'sms_twilio_sid',
        'sms_twilio_token',
        'sms_twilio_from',
        'sms_infobip_api_key',
        'sms_infobip_base_url',
        'sms_infobip_sender',
        'sms_orange_api_key',
        'sms_orange_api_secret',
        'sms_orange_sender_address',
        'sms_orange_base_url',
        'sms_letexto_api_key',
        'sms_letexto_sender',
        'sms_letexto_base_url',
        // WhatsApp
        'whatsapp_provider',
        'whatsapp_twilio_sid',
        'whatsapp_twilio_token',
        'whatsapp_twilio_from',
        'whatsapp_meta_access_token',
        'whatsapp_meta_phone_number_id',
        'whatsapp_meta_api_version',
        // Payment gateways
        'pg_orange_enabled',
        'pg_orange_api_key',
        'pg_orange_api_secret',
        'pg_orange_merchant_id',
        'pg_orange_base_url',
        'pg_mtn_enabled',
        'pg_mtn_api_key',
        'pg_mtn_api_user',
        'pg_mtn_subscription_key',
        'pg_mtn_base_url',
        'pg_mtn_environment',
        'pg_moov_enabled',
        'pg_moov_api_key',
        'pg_moov_merchant_code',
        'pg_moov_base_url',
        'pg_wave_enabled',
        'pg_wave_api_key',
        'pg_wave_base_url',
    ];

    public function index()
    {
        return view('admin.settings');
    }

    public function update(Request $request)
    {
        $settingsToUpdate = $request->except('_token', '_method');

        // Get all boolean settings to handle unchecked checkboxes
        $booleanSettings = SiteSettings::where('type', 'boolean')
            ->whereIn('key', self::ALLOWED_KEYS)
            ->pluck('key')
            ->toArray();

        foreach ($booleanSettings as $key) {
            SiteSettings::set($key, isset($settingsToUpdate[$key]) ? '1' : '0');
            unset($settingsToUpdate[$key]);
        }

        foreach ($settingsToUpdate as $key => $value) {
            if (in_array($key, self::ALLOWED_KEYS) && SiteSettings::where('key', $key)->exists()) {
                SiteSettings::set($key, $value);
            }
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Paramètres mis à jour avec succès.');
    }
}

<?php

use App\Models\SiteSettings;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            // ── Payment Gateway: Orange Money ──
            ['key' => 'pg_orange_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'payment_gateway', 'label' => 'Orange Money activé'],
            ['key' => 'pg_orange_api_key', 'value' => '', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'Orange Money - API Key'],
            ['key' => 'pg_orange_api_secret', 'value' => '', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'Orange Money - API Secret'],
            ['key' => 'pg_orange_merchant_id', 'value' => '', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'Orange Money - Merchant ID'],
            ['key' => 'pg_orange_base_url', 'value' => 'https://api.orange.com/orange-money-webpay/dev/v1', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'Orange Money - Base URL'],

            // ── Payment Gateway: MTN MoMo ──
            ['key' => 'pg_mtn_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'payment_gateway', 'label' => 'MTN MoMo activé'],
            ['key' => 'pg_mtn_api_key', 'value' => '', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'MTN MoMo - API Key'],
            ['key' => 'pg_mtn_api_user', 'value' => '', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'MTN MoMo - API User'],
            ['key' => 'pg_mtn_subscription_key', 'value' => '', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'MTN MoMo - Subscription Key'],
            ['key' => 'pg_mtn_base_url', 'value' => 'https://sandbox.momodeveloper.mtn.com/collection/v1_0', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'MTN MoMo - Base URL'],
            ['key' => 'pg_mtn_environment', 'value' => 'sandbox', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'MTN MoMo - Environnement'],

            // ── Payment Gateway: Moov Money ──
            ['key' => 'pg_moov_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'payment_gateway', 'label' => 'Moov Money activé'],
            ['key' => 'pg_moov_api_key', 'value' => '', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'Moov Money - API Key'],
            ['key' => 'pg_moov_merchant_code', 'value' => '', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'Moov Money - Merchant Code'],
            ['key' => 'pg_moov_base_url', 'value' => 'https://api.moov-africa.com/payment/v1', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'Moov Money - Base URL'],

            // ── Payment Gateway: Wave ──
            ['key' => 'pg_wave_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'payment_gateway', 'label' => 'Wave activé'],
            ['key' => 'pg_wave_api_key', 'value' => '', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'Wave - API Key'],
            ['key' => 'pg_wave_base_url', 'value' => 'https://api.wave.com/v1', 'type' => 'string', 'group' => 'payment_gateway', 'label' => 'Wave - Base URL'],

            // ── SMS Provider ──
            ['key' => 'sms_provider', 'value' => 'disabled', 'type' => 'string', 'group' => 'sms', 'label' => 'Fournisseur SMS'],
            ['key' => 'sms_twilio_sid', 'value' => '', 'type' => 'string', 'group' => 'sms', 'label' => 'Twilio - Account SID'],
            ['key' => 'sms_twilio_token', 'value' => '', 'type' => 'string', 'group' => 'sms', 'label' => 'Twilio - Auth Token'],
            ['key' => 'sms_twilio_from', 'value' => '', 'type' => 'string', 'group' => 'sms', 'label' => 'Twilio - Numéro expéditeur'],
            ['key' => 'sms_infobip_api_key', 'value' => '', 'type' => 'string', 'group' => 'sms', 'label' => 'Infobip - API Key'],
            ['key' => 'sms_infobip_base_url', 'value' => 'https://api.infobip.com', 'type' => 'string', 'group' => 'sms', 'label' => 'Infobip - Base URL'],
            ['key' => 'sms_infobip_sender', 'value' => 'TONTINE', 'type' => 'string', 'group' => 'sms', 'label' => 'Infobip - Nom expéditeur'],
            ['key' => 'sms_orange_api_key', 'value' => '', 'type' => 'string', 'group' => 'sms', 'label' => 'Orange SMS - API Key'],
            ['key' => 'sms_orange_api_secret', 'value' => '', 'type' => 'string', 'group' => 'sms', 'label' => 'Orange SMS - API Secret'],
            ['key' => 'sms_orange_sender_address', 'value' => '', 'type' => 'string', 'group' => 'sms', 'label' => 'Orange SMS - Sender Address'],
            ['key' => 'sms_orange_base_url', 'value' => 'https://api.orange.com/smsmessaging/v1', 'type' => 'string', 'group' => 'sms', 'label' => 'Orange SMS - Base URL'],
            ['key' => 'sms_letexto_api_key', 'value' => '', 'type' => 'string', 'group' => 'sms', 'label' => 'Letexto - API Key'],
            ['key' => 'sms_letexto_sender', 'value' => 'TONTINE', 'type' => 'string', 'group' => 'sms', 'label' => 'Letexto - Nom expéditeur'],
            ['key' => 'sms_letexto_base_url', 'value' => 'https://api.letexto.com/v1', 'type' => 'string', 'group' => 'sms', 'label' => 'Letexto - Base URL'],
        ];

        foreach ($settings as $setting) {
            SiteSettings::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    public function down(): void
    {
        SiteSettings::whereIn('group', ['payment_gateway', 'sms'])->delete();
    }
};

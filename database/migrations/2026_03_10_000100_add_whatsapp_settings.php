<?php

use App\Models\SiteSettings;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            // ── WhatsApp Provider ──
            ['key' => 'whatsapp_provider', 'value' => 'disabled', 'type' => 'string', 'group' => 'whatsapp', 'label' => 'Fournisseur WhatsApp'],
            ['key' => 'whatsapp_twilio_sid', 'value' => '', 'type' => 'string', 'group' => 'whatsapp', 'label' => 'Twilio - Account SID'],
            ['key' => 'whatsapp_twilio_token', 'value' => '', 'type' => 'string', 'group' => 'whatsapp', 'label' => 'Twilio - Auth Token'],
            ['key' => 'whatsapp_twilio_from', 'value' => '', 'type' => 'string', 'group' => 'whatsapp', 'label' => 'Twilio - Numéro WhatsApp'],
            ['key' => 'whatsapp_meta_access_token', 'value' => '', 'type' => 'string', 'group' => 'whatsapp', 'label' => 'Meta - Access Token'],
            ['key' => 'whatsapp_meta_phone_number_id', 'value' => '', 'type' => 'string', 'group' => 'whatsapp', 'label' => 'Meta - Phone Number ID'],
            ['key' => 'whatsapp_meta_api_version', 'value' => 'v18.0', 'type' => 'string', 'group' => 'whatsapp', 'label' => 'Meta - Version API'],
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
        SiteSettings::where('group', 'whatsapp')->delete();
    }
};

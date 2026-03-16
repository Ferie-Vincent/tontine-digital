<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $settings = [
            // Tontines
            [
                'key' => 'max_tontines_per_user',
                'value' => '5',
                'type' => 'integer',
                'group' => 'tontines',
                'label' => 'Nombre max de tontines par utilisateur',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'min_contribution_amount',
                'value' => '1000',
                'type' => 'integer',
                'group' => 'tontines',
                'label' => 'Montant minimum de cotisation (FCFA)',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'max_contribution_amount',
                'value' => '1000000',
                'type' => 'integer',
                'group' => 'tontines',
                'label' => 'Montant maximum de cotisation (FCFA)',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'default_max_members',
                'value' => '20',
                'type' => 'integer',
                'group' => 'tontines',
                'label' => 'Nombre max de membres par defaut',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Inscriptions
            [
                'key' => 'allow_registration',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'label' => 'Autoriser les inscriptions publiques',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'require_phone_verification',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'security',
                'label' => 'Exiger la verification du telephone',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Tentatives de connexion avant blocage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Notifications
            [
                'key' => 'enable_email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Activer les notifications par email',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'enable_push_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Activer les notifications push',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'reminder_default_days',
                'value' => '3,1,0',
                'type' => 'string',
                'group' => 'notifications',
                'label' => 'Jours de rappel par defaut (separes par des virgules)',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Plateforme
            [
                'key' => 'platform_name',
                'value' => 'DIGI-TONTINE CI',
                'type' => 'string',
                'group' => 'platform',
                'label' => 'Nom de la plateforme',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'support_phone',
                'value' => '+2250700000000',
                'type' => 'string',
                'group' => 'platform',
                'label' => 'Telephone du support',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'support_email',
                'value' => 'support@digitontine.ci',
                'type' => 'string',
                'group' => 'platform',
                'label' => 'Email du support',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'platform',
                'label' => 'Mode maintenance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    public function down(): void
    {
        DB::table('site_settings')->whereIn('key', [
            'max_tontines_per_user', 'min_contribution_amount', 'max_contribution_amount',
            'default_max_members', 'allow_registration', 'require_phone_verification',
            'max_login_attempts', 'enable_email_notifications', 'enable_push_notifications',
            'reminder_default_days', 'platform_name', 'support_phone', 'support_email',
            'maintenance_mode',
        ])->delete();
    }
};

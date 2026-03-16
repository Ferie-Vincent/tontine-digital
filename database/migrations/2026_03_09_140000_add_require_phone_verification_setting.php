<?php

use App\Models\SiteSettings;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SiteSettings::firstOrCreate(
            ['key' => 'require_phone_verification'],
            [
                'value' => '0',
                'type' => 'boolean',
                'group' => 'security',
                'label' => 'Exiger la vérification du téléphone',
            ]
        );
    }

    public function down(): void
    {
        SiteSettings::where('key', 'require_phone_verification')->delete();
    }
};

<?php

use App\Models\SiteSettings;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SiteSettings::firstOrCreate(
            ['key' => 'archive_after_days'],
            [
                'value' => '30',
                'type' => 'integer',
                'group' => 'tontine',
                'label' => 'Archiver les tontines terminées après (jours)',
            ]
        );
    }

    public function down(): void
    {
        SiteSettings::where('key', 'archive_after_days')->delete();
    }
};

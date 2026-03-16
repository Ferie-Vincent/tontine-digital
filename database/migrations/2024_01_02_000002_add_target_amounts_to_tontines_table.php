<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tontines', function (Blueprint $table) {
            $table->decimal('target_amount_per_tour', 12, 0)->nullable()->after('contribution_amount');
            $table->decimal('target_amount_total', 12, 0)->nullable()->after('target_amount_per_tour');
        });
    }

    public function down(): void
    {
        Schema::table('tontines', function (Blueprint $table) {
            $table->dropColumn(['target_amount_per_tour', 'target_amount_total']);
        });
    }
};

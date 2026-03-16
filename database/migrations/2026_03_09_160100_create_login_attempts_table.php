<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->index();
            $table->string('ip_address', 45);
            $table->timestamp('attempted_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('locked_until')->nullable()->after('phone_verified_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('locked_until');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tontine_message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tontine_id')->constrained()->onDelete('cascade');
            $table->timestamp('last_read_at');
            $table->timestamps();

            $table->unique(['user_id', 'tontine_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tontine_message_reads');
    }
};

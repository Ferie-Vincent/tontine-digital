<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tontine_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['text', 'system', 'payment_submission'])->default('text');
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tontine_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tontine_messages');
    }
};

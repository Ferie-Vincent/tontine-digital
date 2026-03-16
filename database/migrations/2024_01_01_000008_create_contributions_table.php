<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tontine_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 0);
            $table->enum('status', ['pending', 'declared', 'confirmed', 'rejected', 'late'])->default('pending');
            $table->timestamp('declared_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tour_id', 'user_id']);
            $table->index(['tontine_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};

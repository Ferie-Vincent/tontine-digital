<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tontine_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['admin', 'treasurer', 'member'])->default('member');
            $table->unsignedInteger('position')->nullable();
            $table->enum('status', ['pending', 'active', 'excluded', 'left'])->default('pending');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['tontine_id', 'user_id']);
            $table->index(['tontine_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tontine_members');
    }
};

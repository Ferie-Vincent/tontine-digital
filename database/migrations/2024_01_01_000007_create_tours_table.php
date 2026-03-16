<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tontine_id')->constrained()->onDelete('cascade');
            $table->foreignId('beneficiary_id')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('tour_number');
            $table->date('due_date');
            $table->date('collection_date')->nullable();
            $table->decimal('expected_amount', 12, 0);
            $table->decimal('collected_amount', 12, 0)->default(0);
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'failed'])->default('upcoming');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tontine_id', 'tour_number']);
            $table->index(['tontine_id', 'status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};

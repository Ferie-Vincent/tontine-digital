<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_id')->constrained()->onDelete('cascade');
            $table->string('transaction_reference')->nullable();
            $table->enum('payment_method', ['orange_money', 'mtn_momo', 'wave', 'cash', 'bank_transfer', 'other'])->default('orange_money');
            $table->string('sender_phone')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
    }
};

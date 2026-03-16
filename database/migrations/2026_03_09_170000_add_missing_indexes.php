<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payment Proofs - contribution_id already indexed via FK constraint
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->index('transaction_reference');
            $table->index(['sender_phone', 'transaction_date']);
        });

        // Contributions - existing: unique(tour_id, user_id), index(tontine_id, user_id, status)
        Schema::table('contributions', function (Blueprint $table) {
            $table->index(['tour_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        // Tours - existing: unique(tontine_id, tour_number), index(tontine_id, status, due_date)
        Schema::table('tours', function (Blueprint $table) {
            $table->index(['beneficiary_id', 'status']);
            $table->index(['status', 'due_date']);
        });

        // Tontine Members - existing: unique(tontine_id, user_id), index(tontine_id, position)
        Schema::table('tontine_members', function (Blueprint $table) {
            $table->index(['tontine_id', 'status']);
            $table->index(['user_id', 'status']);
        });

        // Notifications - existing: index(user_id, read_at)
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
        });

        // Activity Logs - existing: index(tontine_id, created_at), index(user_id, action)
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->dropIndex(['transaction_reference']);
            $table->dropIndex(['sender_phone', 'transaction_date']);
        });

        Schema::table('contributions', function (Blueprint $table) {
            $table->dropIndex(['tour_id', 'status']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->dropIndex(['beneficiary_id', 'status']);
            $table->dropIndex(['status', 'due_date']);
        });

        Schema::table('tontine_members', function (Blueprint $table) {
            $table->dropIndex(['tontine_id', 'status']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['action', 'created_at']);
        });
    }
};

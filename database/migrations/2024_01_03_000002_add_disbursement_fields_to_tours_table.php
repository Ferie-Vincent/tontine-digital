<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->timestamp('disbursed_at')->nullable()->after('notes');
            $table->foreignId('disbursed_by')->nullable()->after('disbursed_at')->constrained('users')->nullOnDelete();
            $table->string('disbursement_method')->nullable()->after('disbursed_by');
            $table->string('disbursement_reference', 100)->nullable()->after('disbursement_method');
            $table->text('disbursement_notes')->nullable()->after('disbursement_reference');
            $table->timestamp('beneficiary_confirmed_at')->nullable()->after('disbursement_notes');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropForeign(['disbursed_by']);
            $table->dropColumn([
                'disbursed_at',
                'disbursed_by',
                'disbursement_method',
                'disbursement_reference',
                'disbursement_notes',
                'beneficiary_confirmed_at',
            ]);
        });
    }
};

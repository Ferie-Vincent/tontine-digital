<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tontine_messages MODIFY COLUMN type ENUM('text','system','payment_submission','image') DEFAULT 'text'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tontine_messages MODIFY COLUMN type ENUM('text','system','payment_submission') DEFAULT 'text'");
    }
};

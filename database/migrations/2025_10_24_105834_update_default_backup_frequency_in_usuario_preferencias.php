<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE usuario_preferencias MODIFY COLUMN backup_frequency ENUM('daily', 'weekly', 'monthly') NOT NULL DEFAULT 'daily'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Importar a classe DB

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Apenas tenta modificar a coluna para ENUM se NÃO estiver a usar 'sqlite'
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE usuario_preferencias MODIFY COLUMN backup_frequency ENUM('daily', 'weekly', 'monthly') NOT NULL DEFAULT 'daily'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não é necessário reverter esta alteração
    }
};
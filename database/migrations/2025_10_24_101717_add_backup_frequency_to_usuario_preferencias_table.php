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
        // Adiciona a coluna como uma string simples, que funciona em todos os bancos
        Schema::table('usuario_preferencias', function (Blueprint $table) {
            $table->string('backup_frequency', 50)->default('daily')->after('notif_app');
        });

        // Apenas tenta modificar a coluna para ENUM se NÃO estiver a usar 'sqlite'
        if (DB::connection()->getDriverName() !== 'sqlite') {
            // Esta linha (a 22 original) é a que falha no SQLite
            DB::statement("ALTER TABLE usuario_preferencias MODIFY COLUMN backup_frequency ENUM('daily', 'weekly') NOT NULL DEFAULT 'daily'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario_preferencias', function (Blueprint $table) {
            $table->dropColumn('backup_frequency');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; 

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('usuario_preferencias', function (Blueprint $table) {
            $table->enum('backup_frequency', ['disabled', 'daily', 'weekly'])
                  ->default('daily') 
                  ->after('tamanho_fonte');
        });

        DB::table('usuario_preferencias')
            ->where('backup_frequency', 'disabled')
            ->update(['backup_frequency' => 'daily']);
        DB::statement("ALTER TABLE usuario_preferencias MODIFY COLUMN backup_frequency ENUM('daily', 'weekly') NOT NULL DEFAULT 'daily'");

    }

    public function down(): void
    {
        Schema::table('usuario_preferencias', function (Blueprint $table) {
            $table->dropColumn('backup_frequency');
        });
    }
};
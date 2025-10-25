<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('componentes_curriculares', function (Blueprint $table) {
            $table->foreignId('id_escola')->nullable()->after('id_usuario_criador') 
                  ->constrained('escolas', 'id_escola')
                  ->nullOnDelete(); // Se a escola for deletada, a disciplina vira global
        });
    }

    public function down(): void
    {
        Schema::table('componentes_curriculares', function (Blueprint $table) {
            $table->dropForeign(['id_escola']);
            $table->dropColumn('id_escola');
        });
    }
};
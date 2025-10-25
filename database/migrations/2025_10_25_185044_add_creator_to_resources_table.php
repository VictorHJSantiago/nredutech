<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recursos_didaticos', function (Blueprint $table) {
            $table->foreignId('id_usuario_criador')->nullable()->after('id_escola')
                  ->constrained('usuarios', 'id_usuario')
                  ->nullOnDelete(); 
        });
    }

    public function down(): void
    {
        Schema::table('recursos_didaticos', function (Blueprint $table) {
            $table->dropForeign(['id_usuario_criador']);
            $table->dropColumn('id_usuario_criador');
        });
    }
};
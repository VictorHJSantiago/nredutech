<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escolas', function (Blueprint $table) {
            $table->id('id_escola');
            $table->string('nome', 255);
            $table->string('endereco', 255)->nullable();
            $table->foreignId('id_municipio')->constrained('municipios', 'id_municipio');
            $table->foreignId('id_diretor_responsavel')->nullable()->constrained('usuarios', 'id_usuario');
            $table->enum('nivel_ensino', ['colegio_estadual', 'escola_tecnica', 'escola_municipal']);
            $table->enum('tipo', ['urbana', 'rural']); 
            $table->timestamps(); 
        });
        
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignId('id_escola')->nullable()->constrained('escolas', 'id_escola');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_escola']);
            $table->dropColumn('id_escola');
        });

        Schema::dropIfExists('escolas');
    }
};
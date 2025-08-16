<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oferta_componentes', function (Blueprint $table) {
            $table->id('id_oferta');
            $table->foreignId('id_turma')->constrained('turmas', 'id_turma');
            $table->foreignId('id_professor')->constrained('usuarios', 'id_usuario');
            $table->foreignId('id_componente')->constrained('componentes_curriculares', 'id_componente');
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oferta_componentes');
    }
};
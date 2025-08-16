<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('componentes_curriculares', function (Blueprint $table) {
            $table->id('id_componente');
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->string('carga_horaria');
            $table->enum('status', ['pendente', 'aprovado', 'reprovado'])->default('pendente');
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('componentes_curriculares');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id('id_agendamento');
            $table->dateTime('data_hora_inicio');
            $table->dateTime('data_hora_fim');
            $table->enum('status', ['agendado', 'livre'])->default('livre');
            $table->foreignId('id_recurso')->constrained('recursos_didaticos', 'id_recurso');
            $table->foreignId('id_oferta')->constrained('oferta_componentes', 'id_oferta');
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
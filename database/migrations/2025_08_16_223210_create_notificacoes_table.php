<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id('id_notificacao');
            $table->string('titulo', 255);
            $table->text('mensagem');
            $table->dateTime('data_envio');
            $table->enum('status_mensagem', ['enviada', 'lida']);
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario');
            $table->foreignId('id_agendamento')->nullable()->constrained('agendamentos', 'id_agendamento');
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
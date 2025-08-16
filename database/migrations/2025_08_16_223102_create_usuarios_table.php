<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nome_completo', 255);
            $table->string('username', 80)->unique();
            $table->string('email', 255)->unique();
            $table->date('data_nascimento')->nullable();
            $table->string('cpf', 14)->unique()->nullable();
            $table->string('rg', 20)->unique()->nullable();
            $table->string('rco_siape', 50)->unique()->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('formacao', 255)->nullable();
            $table->string('area_formacao', 255)->nullable();
            $table->dateTime('data_registro');
            $table->enum('status_aprovacao', ['ativo', 'pendente', 'bloqueado'])->default('pendente');
            $table->enum('tipo_usuario', ['administrador', 'diretor', 'professor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
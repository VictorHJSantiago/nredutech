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
            $table->string('nome_completo');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('tipo_usuario');
            $table->string('status_aprovacao')->default('pendente');
            $table->date('data_nascimento')->nullable();
            $table->string('cpf')->unique()->nullable();
            $table->string('rg')->unique()->nullable();
            $table->string('rco_siape')->nullable();
            $table->string('telefone')->nullable();
            $table->string('formacao')->nullable();
            $table->string('area_formacao')->nullable();
            $table->dateTime('data_registro')->nullable();            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};

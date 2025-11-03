<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nome_completo');
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->date('data_nascimento')->nullable();
            $table->text('cpf')->unique()->nullable();
            $table->text('rg')->unique()->nullable();
            $table->text('rco_siape')->unique()->nullable();
            $table->text('telefone')->nullable();
            $table->string('formacao')->nullable();
            $table->string('area_formacao')->nullable();
            $table->timestamp('data_registro')->useCurrent();
            $table->string('status_aprovacao')->default('pendente');
            $table->string('tipo_usuario');
            
            $table->unsignedBigInteger('id_escola')->nullable();
            // LINHA DA CHAVE ESTRANGEIRA REMOVIDA DAQUI
            
            $table->string('password');
            $table->timestamps(); // <-- LINHA ADICIONADA
            $table->softDeletes();
            $table->rememberToken();
        });
        
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
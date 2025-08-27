<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios_apagados', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario'); 
            $table->string('nome_completo', 255);
            $table->string('username', 80);
            $table->string('email', 255);
            $table->date('data_nascimento')->nullable();
            $table->string('cpf', 14)->nullable();
            $table->string('rg', 20)->nullable();
            $table->string('rco_siape', 50)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('formacao', 255)->nullable();
            $table->string('area_formacao', 255)->nullable();
            $table->dateTime('data_registro');
            $table->string('status_aprovacao'); 
            $table->string('tipo_usuario');     
            
            $table->timestamps(); 

            $table->timestamp('data_exclusao')->useCurrent();

            $table->primary('id_usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_apagados');
    }
};
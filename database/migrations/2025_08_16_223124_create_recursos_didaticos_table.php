<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recursos_didaticos', function (Blueprint $table) {
            $table->id('id_recurso');
            $table->string('nome', 255);
            $table->string('marca', 100)->nullable();
            $table->string('numero_serie', 100)->unique()->nullable();
            $table->integer('quantidade')->default(1);
            $table->text('observacoes')->nullable();
            $table->date('data_ultima_limpeza')->nullable();
            $table->enum('status', ['funcionando', 'em_manutencao', 'quebrado', 'descartado']);
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recursos_didaticos');
    }
};
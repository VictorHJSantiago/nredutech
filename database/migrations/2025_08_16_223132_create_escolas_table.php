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
        Schema::create('escolas', function (Blueprint $table) {
            $table->id('id_escola');
            $table->string('nome');
            $table->string('nivel_ensino'); 
            $table->string('tipo'); 
            
            $table->unsignedBigInteger('id_municipio');
            $table->foreign('id_municipio')->references('id_municipio')->on('municipios')->onDelete('cascade');
            
            $table->unsignedBigInteger('id_diretor_1')->nullable();
            $table->unsignedBigInteger('id_diretor_2')->nullable();
            // LINHAS DAS CHAVES ESTRANGEIRAS REMOVIDAS DAQUI

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escolas');
    }
};
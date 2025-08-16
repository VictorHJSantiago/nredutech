<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turmas', function (Blueprint $table) {
            $table->id('id_turma');
            $table->string('serie', 50);
            $table->enum('turno', ['manha', 'tarde', 'noite']);
            $table->integer('ano_letivo');
            $table->enum('nivel_escolaridade', ['fundamental_1', 'fundamental_2', 'medio']);
            $table->foreignId('id_escola')->constrained('escolas', 'id_escola');
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turmas');
    }
};
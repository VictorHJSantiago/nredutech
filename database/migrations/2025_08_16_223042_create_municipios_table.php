<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipios', function (Blueprint $table) {
            $table->id('id_municipio');
            $table->string('nome', 255);
            $table->enum('tipo', ['urbano', 'rural']);
            $table->timestamps(); // Opcional, boa prática no Laravel
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
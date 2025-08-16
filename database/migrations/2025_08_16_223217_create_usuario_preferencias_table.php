<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_preferencias', function (Blueprint $table) {
            $table->foreignId('id_usuario')->primary()->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            
            $table->boolean('notif_email')->default(true);
            $table->boolean('notif_popup')->default(true);
            $table->enum('tema', ['claro', 'escuro'])->default('claro');
            $table->enum('tamanho_fonte', ['padrao', 'medio', 'grande'])->default('padrao');
            $table->timestamps(); 
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('usuario_preferencias');
    }
};
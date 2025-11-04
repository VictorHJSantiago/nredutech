<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreign('id_escola')
                  ->references('id_escola')
                  ->on('escolas')
                  ->onDelete('set null');
        });

        Schema::table('escolas', function (Blueprint $table) {
            $table->foreign('id_diretor_1')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('set null');
                  
            $table->foreign('id_diretor_2')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_escola']);
        });

        Schema::table('escolas', function (Blueprint $table) {
            $table->dropForeign(['id_diretor_1']);
            $table->dropForeign(['id_diretor_2']);
        });
    }
};
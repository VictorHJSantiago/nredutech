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
        Schema::table('usuario_preferencias', function (Blueprint $table) {
            // CORREÇÃO: Altere 'notif_app' para 'notif_popup'
            $table->string('backup_frequency', 50)->default('daily')->after('notif_popup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario_preferencias', function (Blueprint $table) {
            $table->dropColumn('backup_frequency');
        });
    }
};
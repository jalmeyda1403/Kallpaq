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
        Schema::table('encuesta_satisfaccion_detalles', function (Blueprint $table) {
            // Change column names to use the 'esd_' prefix
            $table->renameColumn('categoria', 'esd_categoria');
            $table->renameColumn('puntaje', 'esd_puntaje');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encuesta_satisfaccion_detalles', function (Blueprint $table) {
            // Change column names back
            $table->renameColumn('esd_categoria', 'categoria');
            $table->renameColumn('esd_puntaje', 'puntaje');
        });
    }
};

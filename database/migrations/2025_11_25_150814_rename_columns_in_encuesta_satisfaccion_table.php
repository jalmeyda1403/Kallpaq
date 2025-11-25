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
        Schema::table('encuestas_satisfaccion', function (Blueprint $table) {
            // Change column names to use the 'es_' prefix first
            $table->renameColumn('periodo', 'es_periodo');
            $table->renameColumn('numero_periodo', 'es_numero_periodo');
            $table->renameColumn('anio', 'es_anio');
            $table->renameColumn('nps_score', 'es_nps_score');
            $table->renameColumn('cantidad_encuestas', 'es_cantidad');
            $table->renameColumn('informe_path', 'es_informe_path');
        });

        Schema::table('encuestas_satisfaccion', function (Blueprint $table) {
            // Add the new score column after the renamed es_nps_score
            $table->double('es_score', 8, 2)->nullable()->after('es_nps_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encuestas_satisfaccion', function (Blueprint $table) {
            // Change column names back (opposite order of up())
            $table->renameColumn('es_periodo', 'periodo');
            $table->renameColumn('es_numero_periodo', 'numero_periodo');
            $table->renameColumn('es_anio', 'anio');
            $table->renameColumn('es_nps_score', 'nps_score');
            $table->renameColumn('es_cantidad', 'cantidad_encuestas');
            $table->renameColumn('es_informe_path', 'informe_path');
        });

        Schema::table('encuestas_satisfaccion', function (Blueprint $table) {
            // Remove the es_score column
            $table->dropColumn('es_score');
        });
    }
};

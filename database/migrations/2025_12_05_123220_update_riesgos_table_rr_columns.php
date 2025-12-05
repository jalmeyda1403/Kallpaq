<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('riesgos', function (Blueprint $table) {
            $table->renameColumn('probabilidad_rr', 'riesgo_probabilidad_rr');
            $table->renameColumn('impacto_rr', 'riesgo_impacto_rr');
            $table->renameColumn('evaluacion_rr', 'riesgo_valor_rr');
            $table->renameColumn('estado_riesgo_rr', 'riesgo_estado_rr');
            $table->renameColumn('fecha_valoracion_rr', 'riesgo_fecha_valoracion_rr');
            $table->string('riesgo_nivel_rr')->nullable()->after('riesgo_valor_rr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riesgos', function (Blueprint $table) {
            $table->dropColumn('riesgo_nivel_rr');
            $table->renameColumn('riesgo_probabilidad_rr', 'probabilidad_rr');
            $table->renameColumn('riesgo_impacto_rr', 'impacto_rr');
            $table->renameColumn('riesgo_valor_rr', 'evaluacion_rr');
            $table->renameColumn('riesgo_estado_rr', 'estado_riesgo_rr');
            $table->renameColumn('riesgo_fecha_valoracion_rr', 'fecha_valoracion_rr');
        });
    }
};

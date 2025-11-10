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
        Schema::table('hallazgos', function (Blueprint $table) {
            if (!Schema::hasColumn('hallazgos', 'hallazgo_cod')) { $table->string('hallazgo_cod')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'informe_id')) { $table->unsignedBigInteger('informe_id')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'especialista_id')) { $table->unsignedBigInteger('especialista_id')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'auditor_id')) { $table->unsignedBigInteger('auditor_id')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'user_asigna_id')) { $table->unsignedBigInteger('user_asigna_id')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_resumen')) { $table->text('hallazgo_resumen')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_descripcion')) { $table->text('hallazgo_descripcion')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_criterio')) { $table->text('hallazgo_criterio')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_clasificacion')) { $table->string('hallazgo_clasificacion')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_origen')) { $table->string('hallazgo_origen')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_tipo_cierre')) { $table->string('hallazgo_tipo_cierre')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_evidencia')) { $table->text('hallazgo_evidencia')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_avance')) { $table->integer('hallazgo_avance')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_estado')) { $table->string('hallazgo_estado')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_fecha_identificacion')) { $table->date('hallazgo_fecha_identificacion')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_fecha_aprobacion')) { $table->date('hallazgo_fecha_aprobacion')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_fecha_asignacion')) { $table->date('hallazgo_fecha_asignacion')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_fecha_conclusion')) { $table->date('hallazgo_fecha_conclusion')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_fecha_cierre')) { $table->date('hallazgo_fecha_cierre')->nullable(); }
            if (!Schema::hasColumn('hallazgos', 'hallazgo_sig')) { $table->json('hallazgo_sig')->nullable(); }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hallazgos', function (Blueprint $table) {
            $columns = [
                'hallazgo_cod', 'informe_id', 'especialista_id', 'auditor_id',
                'user_asigna_id', 'hallazgo_resumen', 'hallazgo_descripcion',
                'hallazgo_criterio', 'hallazgo_clasificacion', 'hallazgo_origen',
                'hallazgo_tipo_cierre', 'hallazgo_evidencia', 'hallazgo_avance',
                'hallazgo_estado', 'hallazgo_fecha_identificacion', 'hallazgo_fecha_aprobacion',
                'hallazgo_fecha_asignacion', 'hallazgo_fecha_conclusion', 'hallazgo_fecha_cierre',
                'hallazgo_sig'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('hallazgos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
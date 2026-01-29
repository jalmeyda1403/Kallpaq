<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Renombrar columnas solo si existen con nombre antiguo
        Schema::table('acciones', function (Blueprint $table) {
            if (Schema::hasColumn('acciones', 'hallazgo_id') && !Schema::hasColumn('acciones', 'accion_hallazgo_id')) {
                try {
                    $table->dropForeign(['hallazgo_id']);
                } catch (\Exception $e) {
                }
                $table->renameColumn('hallazgo_id', 'accion_hallazgo_id');
            }
            if (Schema::hasColumn('acciones', 'obligacion_id') && !Schema::hasColumn('acciones', 'accion_obligacion_id')) {
                try {
                    $table->dropForeign(['obligacion_id']);
                } catch (\Exception $e) {
                }
                $table->renameColumn('obligacion_id', 'accion_obligacion_id');
            }
            if (Schema::hasColumn('acciones', 'hallazgo_proceso_id') && !Schema::hasColumn('acciones', 'accion_hallazgo_proceso_id')) {
                try {
                    $table->dropForeign(['hallazgo_proceso_id']);
                } catch (\Exception $e) {
                }
                $table->renameColumn('hallazgo_proceso_id', 'accion_hallazgo_proceso_id');
            }
            if (Schema::hasColumn('acciones', 'es_control_permanente') && !Schema::hasColumn('acciones', 'accion_es_control_permanente')) {
                $table->renameColumn('es_control_permanente', 'accion_es_control_permanente');
            }
        });

        // 2. Agregar nueva columna si no existe
        if (!Schema::hasColumn('acciones', 'accion_riesgo_id')) {
            Schema::table('acciones', function (Blueprint $table) {
                $table->unsignedBigInteger('accion_riesgo_id')->nullable()->after('id');
            });
        }

        // 3. Recrear llaves foráneas faltantes
        Schema::table('acciones', function (Blueprint $table) {
            try {
                $table->foreign('accion_hallazgo_id')->references('id')->on('hallazgos')->onDelete('cascade');
            } catch (\Exception $e) {
            }
            try {
                $table->foreign('accion_obligacion_id')->references('id')->on('obligaciones')->onDelete('cascade');
            } catch (\Exception $e) {
            }
            try {
                $table->foreign('accion_hallazgo_proceso_id')->references('id')->on('hallazgo_procesos')->onDelete('cascade');
            } catch (\Exception $e) {
            }
            try {
                $table->foreign('accion_riesgo_id')->references('id')->on('riesgos')->onDelete('cascade');
            } catch (\Exception $e) {
            }
        });

        // 4. Migrar datos faltantes
        if (Schema::hasTable('riesgo_acciones')) {
            $riesgoAcciones = DB::table('riesgo_acciones')->get();
            foreach ($riesgoAcciones as $ra) {
                // Verificar si ya existe en 'acciones' (por descripción y riesgo_id)
                $existe = DB::table('acciones')
                    ->where('accion_riesgo_id', $ra->riesgo_id)
                    ->where('accion_descripcion', $ra->ra_descripcion ?? $ra->descripcion)
                    ->exists();

                if (!$existe) {
                    $accionId = DB::table('acciones')->insertGetId([
                        'accion_riesgo_id' => $ra->riesgo_id ?? null,
                        'accion_descripcion' => $ra->ra_descripcion ?? $ra->descripcion ?? '',
                        'accion_fecha_inicio' => $ra->ra_fecha_inicio ?? $ra->fecha_prog_inicio ?? null,
                        'accion_fecha_fin_planificada' => $ra->ra_fecha_fin_planificada ?? $ra->fecha_prog_fin ?? null,
                        'accion_fecha_fin_reprogramada' => $ra->ra_fecha_fin_reprogramada ?? null,
                        'accion_fecha_fin_real' => $ra->ra_fecha_fin_real ?? $ra->fecha_implementacion ?? null,
                        'accion_justificacion' => $ra->ra_justificacion ?? null,
                        'accion_responsable' => $ra->ra_responsable ?? $ra->responsable ?? null,
                        'accion_responsable_correo' => $ra->ra_responsable_correo ?? null,
                        'accion_estado' => strtolower($ra->ra_estado ?? $ra->estado ?? 'pendiente'),
                        'accion_ciclo' => $ra->ra_ciclo ?? 0,
                        'accion_cod' => 'RA-' . ($ra->id),
                        'created_at' => $ra->created_at ?? now(),
                        'updated_at' => $ra->updated_at ?? now(),
                    ]);

                    // Migrar reprogramaciones
                    if (Schema::hasTable('riesgo_acciones_reprogramaciones')) {
                        $rar = DB::table('riesgo_acciones_reprogramaciones')
                            ->where('riesgo_accion_id', $ra->id)
                            ->get();

                        foreach ($rar as $r) {
                            DB::table('accion_reprogramaciones')->insert([
                                'accion_id' => $accionId,
                                'ar_fecha_anterior' => $r->rar_fecha_anterior,
                                'ar_fecha_nueva' => $r->rar_fecha_nueva,
                                'ar_justificacion' => $r->rar_justificacion,
                                'ar_usuario_id' => $r->rar_aprobado_por,
                                'ar_estado' => strtolower($r->rar_estado),
                                'created_at' => $r->created_at ?? now(),
                                'updated_at' => $r->updated_at ?? now(),
                            ]);
                        }
                    }

                    // Migrar avances/evidencias
                    if (!empty($ra->ra_evidencia) || !empty($ra->ra_comentario)) {
                        DB::table('accion_avances')->insert([
                            'accion_id' => $accionId,
                            'accion_avance_porcentaje' => ($ra->ra_estado === 'implementada' || ($ra->estado ?? null) === 'Implementado') ? 100 : 0,
                            'accion_avance_comentario' => $ra->ra_comentario ?? $ra->comentario ?? 'Migración inicial',
                            'accion_avance_evidencia' => $ra->ra_evidencia ? json_encode([['path' => $ra->ra_evidencia, 'name' => basename($ra->ra_evidencia)]]) : null,
                            'accion_avance_estado' => strtolower($ra->ra_estado ?? $ra->estado ?? 'pendiente'),
                            'user_id' => null,
                            'created_at' => $ra->updated_at ?? now(),
                            'updated_at' => $ra->updated_at ?? now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acciones', function (Blueprint $table) {
            try {
                $table->dropForeign(['accion_hallazgo_id']);
                $table->dropForeign(['accion_obligacion_id']);
                $table->dropForeign(['accion_hallazgo_proceso_id']);
                $table->dropForeign(['accion_riesgo_id']);
            } catch (\Exception $e) {
            }

            if (Schema::hasColumn('acciones', 'accion_riesgo_id')) {
                $table->dropColumn('accion_riesgo_id');
            }

            if (Schema::hasColumn('acciones', 'accion_hallazgo_id'))
                $table->renameColumn('accion_hallazgo_id', 'hallazgo_id');
            if (Schema::hasColumn('acciones', 'accion_obligacion_id'))
                $table->renameColumn('accion_obligacion_id', 'obligacion_id');
            if (Schema::hasColumn('acciones', 'accion_hallazgo_proceso_id'))
                $table->renameColumn('accion_hallazgo_proceso_id', 'hallazgo_proceso_id');
            if (Schema::hasColumn('acciones', 'accion_es_control_permanente'))
                $table->renameColumn('accion_es_control_permanente', 'es_control_permanente');

            try {
                $table->foreign('hallazgo_id')->references('id')->on('hallazgos')->onDelete('cascade');
            } catch (\Exception $e) {
            }
            try {
                $table->foreign('obligacion_id')->references('id')->on('obligaciones')->onDelete('cascade');
            } catch (\Exception $e) {
            }
            try {
                $table->foreign('hallazgo_proceso_id')->references('id')->on('hallazgo_procesos')->onDelete('cascade');
            } catch (\Exception $e) {
            }
        });
    }
};

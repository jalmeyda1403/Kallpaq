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
        // 0. Update structure to allow null user_id (system actions)
        if (Schema::hasTable('accion_avances')) {
            Schema::table('accion_avances', function (Blueprint $table) {
                // Check if user_id exists before modifying? It should exist from creation.
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }

        // 1. Migrate legacy data
        $legacyAcciones = DB::table('acciones')
            ->whereNotNull('accion_comentario')
            ->orWhereNotNull('accion_ruta_evidencia')
            ->get();

        foreach ($legacyAcciones as $accion) {
            if ($accion->accion_comentario || $accion->accion_ruta_evidencia) {
                // If evidence is "[]" (empty json array), treat as null
                $evidencia = $accion->accion_ruta_evidencia;
                if ($evidencia === '[]' || $evidencia === 'null')
                    $evidencia = null;

                if ($accion->accion_comentario || $evidencia) {
                    DB::table('accion_avances')->insert([
                        'accion_id' => $accion->id,
                        'accion_avance_porcentaje' => 0, // Default for legacy
                        'accion_avance_comentario' => $accion->accion_comentario ?? 'Migración de datos históricos',
                        'accion_avance_evidencia' => $evidencia,
                        'accion_avance_estado' => $accion->accion_estado ?? 'programada',
                        'user_id' => null,
                        'created_at' => $accion->updated_at ?? now(),
                        'updated_at' => $accion->updated_at ?? now(),
                    ]);
                }
            }
        }

        // 2. Drop columns
        Schema::table('acciones', function (Blueprint $table) {
            // Check if columns exist before dropping to avoid errors if re-running partials
            if (Schema::hasColumn('acciones', 'accion_comentario')) {
                $table->dropColumn('accion_comentario');
            }
            if (Schema::hasColumn('acciones', 'accion_ruta_evidencia')) {
                $table->dropColumn('accion_ruta_evidencia');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acciones', function (Blueprint $table) {
            $table->text('accion_comentario')->nullable();
            $table->text('accion_ruta_evidencia')->nullable();
        });

        // Revert nullable user_id? Maybe not strictly necessary for rollback dev convenience.
        // But strictly:
        // Schema::table('accion_avances', function (Blueprint $table) {
        //     $table->unsignedBigInteger('user_id')->change(); 
        // });
    }
};

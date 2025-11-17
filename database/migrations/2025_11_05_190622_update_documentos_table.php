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
        Schema::table('documentos', function (Blueprint $table) {
            if (Schema::hasColumn('documentos', 'nombre') && !Schema::hasColumn('documentos', 'nombre_documento')) {
                $table->renameColumn('nombre', 'nombre_documento');
            }
            if (Schema::hasColumn('documentos', 'fuente') && !Schema::hasColumn('documentos', 'fuente_documento')) {
                $table->renameColumn('fuente', 'fuente_documento');
            }
            if (Schema::hasColumn('documentos', 'estado') && !Schema::hasColumn('documentos', 'estado_documento')) {
                $table->renameColumn('estado', 'estado_documento');
            }
            if (Schema::hasColumn('documentos', 'vigencia_at') && !Schema::hasColumn('documentos', 'fecha_vigencia_documento')) {
                $table->renameColumn('vigencia_at', 'fecha_vigencia_documento');
            }

            if (!Schema::hasColumn('documentos', 'area_compliance_id')) {
                $table->foreignId('area_compliance_id')->nullable()->constrained();
            }
            if (!Schema::hasColumn('documentos', 'subarea_compliance_id')) {
                $table->foreignId('subarea_compliance_id')->nullable()->constrained();
            }
            if (!Schema::hasColumn('documentos', 'documento_padre_id')) {
                $table->foreignId('documento_padre_id')->nullable()->constrained('documentos');
            }
            if (!Schema::hasColumn('documentos', 'resumen_documento')) {
                $table->text('resumen_documento')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'palabras_clave_documento')) {
                $table->text('palabras_clave_documento')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'observaciones_documento')) {
                $table->text('observaciones_documento')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'archivo_path_documento')) {
                $table->string('archivo_path_documento')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'usa_versiones_documento')) {
                $table->boolean('usa_versiones_documento')->default(false);
            }
            if (!Schema::hasColumn('documentos', 'fecha_aprobacion_documento')) {
                $table->timestamp('fecha_aprobacion_documento')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'fecha_derogacion_documento')) {
                $table->timestamp('fecha_derogacion_documento')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'frecuencia_revision_documento')) {
                $table->integer('frecuencia_revision_documento')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'instrumento_aprueba_documento')) {
                $table->string('instrumento_aprueba_documento')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'instrumento_deroga_documento')) {
                $table->string('instrumento_deroga_documento')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'origen_documento')) {
                $table->string('origen_documento')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'enlace_valido')) {
                $table->boolean('enlace_valido')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'user_created')) {
                $table->foreignId('user_created')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('documentos', 'user_pubilshed')) {
                $table->foreignId('user_pubilshed')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('documentos', 'user_deleted')) {
                $table->foreignId('user_deleted')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('documentos', 'user_updated')) {
                $table->foreignId('user_updated')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('documentos', 'pubished_at')) {
                $table->timestamp('pubished_at')->nullable();
            }
            if (!Schema::hasColumn('documentos', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            if (Schema::hasColumn('documentos', 'nombre_documento') && !Schema::hasColumn('documentos', 'nombre')) {
                $table->renameColumn('nombre_documento', 'nombre');
            }
            if (Schema::hasColumn('documentos', 'fuente_documento') && !Schema::hasColumn('documentos', 'fuente')) {
                $table->renameColumn('fuente_documento', 'fuente');
            }
            if (Schema::hasColumn('documentos', 'estado_documento') && !Schema::hasColumn('documentos', 'estado')) {
                $table->renameColumn('estado_documento', 'estado');
            }
            if (Schema::hasColumn('documentos', 'fecha_vigencia_documento') && !Schema::hasColumn('documentos', 'vigencia_at')) {
                $table->renameColumn('fecha_vigencia_documento', 'vigencia_at');
            }

            $table->dropForeign(['area_compliance_id']);
            $table->dropForeign(['subarea_compliance_id']);
            $table->dropForeign(['documento_padre_id']);
            $table->dropForeign(['user_created']);
            $table->dropForeign(['user_pubilshed']);
            $table->dropForeign(['user_deleted']);
            $table->dropForeign(['user_updated']);

            $table->dropColumn([
                'area_compliance_id',
                'subarea_compliance_id',
                'documento_padre_id',
                'resumen_documento',
                'palabras_clave_documento',
                'observaciones_documento',
                'archivo_path_documento',
                'usa_versiones_documento',
                'fecha_aprobacion_documento',
                'fecha_derogacion_documento',
                'frecuencia_revision_documento',
                'instrumento_aprueba_documento',
                'instrumento_deroga_documento',
                'origen_documento',
                'enlace_valido',
                'user_created',
                'user_pubilshed',
                'user_deleted',
                'user_updated',
                'pubished_at',
                'deleted_at',
            ]);
        });
    }
};
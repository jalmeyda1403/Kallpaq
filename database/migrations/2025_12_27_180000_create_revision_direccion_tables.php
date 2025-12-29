<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Módulo de Revisión por la Dirección (ISO 9001 §9.3)
     */
    public function up(): void
    {
        // Tabla principal de revisiones por la dirección
        Schema::create('revisiones_direccion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('titulo');
            $table->date('fecha_programada');
            $table->date('fecha_reunion')->nullable();
            $table->string('periodo', 20); // 'Q1-2025', 'S1-2025', 'A-2025'
            $table->year('anio');
            $table->text('participantes')->nullable();
            $table->text('agenda')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['programada', 'en_preparacion', 'realizada', 'cancelada'])->default('programada');
            $table->string('acta_path')->nullable(); // Ruta del archivo de acta
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Entradas de la revisión (inputs del SGC)
        Schema::create('revision_entradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revision_id')->constrained('revisiones_direccion')->cascadeOnDelete();
            $table->enum('tipo_entrada', [
                'estado_acciones_anteriores',
                'cambios_contexto_externo',
                'cambios_contexto_interno',
                'retroalimentacion_partes_interesadas',
                'desempeno_procesos',
                'conformidad_productos_servicios',
                'no_conformidades_acciones_correctivas',
                'resultados_auditorias',
                'desempeno_proveedores',
                'adecuacion_recursos',
                'eficacia_acciones_riesgos',
                'oportunidades_mejora',
                'satisfaccion_cliente',
                'cumplimiento_objetivos',
                'otros'
            ]);
            $table->string('titulo');
            $table->text('descripcion');
            $table->text('datos_soporte')->nullable(); // JSON con referencias a módulos
            $table->text('conclusion')->nullable();
            $table->enum('estado', ['pendiente', 'revisado', 'aprobado'])->default('pendiente');
            $table->timestamps();
        });

        // Salidas/Decisiones de la revisión
        Schema::create('revision_salidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revision_id')->constrained('revisiones_direccion')->cascadeOnDelete();
            $table->enum('tipo_salida', [
                'decision_mejora',
                'necesidad_cambio_sgc',
                'necesidad_recursos',
                'actualizacion_riesgos',
                'actualizacion_objetivos',
                'otros'
            ]);
            $table->text('descripcion');
            $table->text('justificacion')->nullable();
            $table->timestamps();
        });

        // Compromisos derivados de la revisión
        Schema::create('revision_compromisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revision_id')->constrained('revisiones_direccion')->cascadeOnDelete();
            $table->foreignId('salida_id')->nullable()->constrained('revision_salidas')->nullOnDelete();
            $table->string('codigo', 20)->nullable();
            $table->text('descripcion');
            $table->foreignId('responsable_id')->constrained('users');
            $table->date('fecha_limite');
            $table->date('fecha_cierre')->nullable();
            $table->enum('estado', ['pendiente', 'en_proceso', 'completado', 'vencido', 'cancelado'])->default('pendiente');
            $table->text('evidencia_path')->nullable(); // JSON array de archivos
            $table->text('observaciones')->nullable();
            $table->integer('avance')->default(0); // Porcentaje 0-100
            $table->timestamps();
        });

        // Historial de seguimiento de compromisos
        Schema::create('revision_compromiso_seguimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compromiso_id')->constrained('revision_compromisos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('comentario');
            $table->integer('avance_anterior')->nullable();
            $table->integer('avance_nuevo');
            $table->string('estado_anterior')->nullable();
            $table->string('estado_nuevo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revision_compromiso_seguimientos');
        Schema::dropIfExists('revision_compromisos');
        Schema::dropIfExists('revision_salidas');
        Schema::dropIfExists('revision_entradas');
        Schema::dropIfExists('revisiones_direccion');
    }
};

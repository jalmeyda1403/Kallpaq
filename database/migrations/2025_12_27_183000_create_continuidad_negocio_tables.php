<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Módulo de Gestión de Continuidad de Negocio (ISO 22301)
     */
    public function up(): void
    {
        // Activos críticos de la organización
        Schema::create('activos_criticos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', [
                'personal',
                'tecnologia',
                'informacion',
                'infraestructura',
                'proveedor',
                'proceso',
                'otro'
            ]);
            $table->foreignId('proceso_id')->nullable()->constrained('procesos')->nullOnDelete();
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('nivel_criticidad', ['bajo', 'medio', 'alto', 'critico'])->default('medio');
            $table->integer('rto')->nullable()->comment('Recovery Time Objective en horas');
            $table->integer('rpo')->nullable()->comment('Recovery Point Objective en horas');
            $table->integer('mtpd')->nullable()->comment('Maximum Tolerable Period of Disruption en horas');
            $table->text('dependencias')->nullable(); // JSON de activos de los que depende
            $table->text('ubicacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Escenarios de riesgo/interrupción
        Schema::create('escenarios_continuidad', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre');
            $table->text('descripcion');
            $table->enum('categoria', [
                'desastre_natural',
                'falla_tecnologica',
                'ciberataque',
                'pandemia',
                'incidente_seguridad',
                'falla_proveedor',
                'falla_infraestructura',
                'otro'
            ]);
            $table->enum('probabilidad', ['muy_baja', 'baja', 'media', 'alta', 'muy_alta'])->default('media');
            $table->enum('impacto', ['insignificante', 'menor', 'moderado', 'mayor', 'catastrofico'])->default('moderado');
            $table->integer('nivel_riesgo')->nullable(); // Calculado
            $table->text('activos_afectados')->nullable(); // JSON array de IDs
            $table->text('procesos_afectados')->nullable(); // JSON array de IDs
            $table->text('controles_existentes')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Planes de continuidad
        Schema::create('planes_continuidad', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre');
            $table->text('objetivo');
            $table->enum('tipo_plan', [
                'bcp',  // Business Continuity Plan
                'drp',  // Disaster Recovery Plan
                'irp',  // Incident Response Plan
                'crmp'  // Crisis Management Plan
            ]);
            $table->foreignId('escenario_id')->nullable()->constrained('escenarios_continuidad')->nullOnDelete();
            $table->foreignId('proceso_id')->nullable()->constrained('procesos')->nullOnDelete();
            $table->foreignId('responsable_id')->constrained('users');
            $table->text('alcance')->nullable();
            $table->text('equipo_respuesta')->nullable(); // JSON con roles y responsables
            $table->text('procedimientos_activacion')->nullable();
            $table->text('procedimientos_recuperacion')->nullable();
            $table->text('recursos_necesarios')->nullable();
            $table->text('comunicacion_crisis')->nullable();
            $table->string('documento_path')->nullable();
            $table->string('version', 10)->default('1.0');
            $table->date('fecha_aprobacion')->nullable();
            $table->date('proxima_revision')->nullable();
            $table->enum('estado', ['borrador', 'en_revision', 'aprobado', 'obsoleto'])->default('borrador');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Estrategias de continuidad por activo
        Schema::create('estrategias_continuidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('planes_continuidad')->cascadeOnDelete();
            $table->foreignId('activo_id')->nullable()->constrained('activos_criticos')->nullOnDelete();
            $table->string('nombre');
            $table->text('descripcion');
            $table->enum('tipo_estrategia', [
                'respaldo',
                'redundancia',
                'alternativa',
                'manual',
                'outsourcing',
                'otro'
            ]);
            $table->text('recursos_requeridos')->nullable();
            $table->decimal('costo_estimado', 12, 2)->nullable();
            $table->integer('tiempo_implementacion')->nullable()->comment('En horas');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->timestamps();
        });

        // Pruebas y ejercicios de continuidad
        Schema::create('pruebas_continuidad', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre');
            $table->foreignId('plan_id')->constrained('planes_continuidad')->cascadeOnDelete();
            $table->enum('tipo_prueba', [
                'documental',       // Revisión de documentación
                'walkthrough',      // Recorrido paso a paso
                'simulacion',       // Simulación de escritorio
                'funcional',        // Prueba de componentes
                'ejercicio_total'   // Ejercicio completo
            ]);
            $table->date('fecha_programada');
            $table->date('fecha_ejecucion')->nullable();
            $table->text('objetivo');
            $table->text('alcance')->nullable();
            $table->text('participantes')->nullable();
            $table->text('escenario_prueba')->nullable();
            $table->enum('estado', ['programada', 'en_ejecucion', 'completada', 'cancelada', 'postergada'])->default('programada');
            $table->text('resultados')->nullable();
            $table->text('hallazgos')->nullable();
            $table->text('lecciones_aprendidas')->nullable();
            $table->text('acciones_mejora')->nullable();
            $table->integer('calificacion')->nullable()->comment('1-5');
            $table->string('informe_path')->nullable();
            $table->foreignId('responsable_id')->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Incidentes de continuidad (registro histórico)
        Schema::create('incidentes_continuidad', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('titulo');
            $table->text('descripcion');
            $table->foreignId('escenario_id')->nullable()->constrained('escenarios_continuidad')->nullOnDelete();
            $table->foreignId('plan_activado_id')->nullable()->constrained('planes_continuidad')->nullOnDelete();
            $table->datetime('fecha_inicio');
            $table->datetime('fecha_fin')->nullable();
            $table->enum('severidad', ['baja', 'media', 'alta', 'critica']);
            $table->text('impacto_real')->nullable();
            $table->text('acciones_tomadas')->nullable();
            $table->text('lecciones_aprendidas')->nullable();
            $table->integer('tiempo_respuesta_minutos')->nullable();
            $table->integer('tiempo_recuperacion_minutos')->nullable();
            $table->boolean('plan_fue_efectivo')->nullable();
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidentes_continuidad');
        Schema::dropIfExists('pruebas_continuidad');
        Schema::dropIfExists('estrategias_continuidad');
        Schema::dropIfExists('planes_continuidad');
        Schema::dropIfExists('escenarios_continuidad');
        Schema::dropIfExists('activos_criticos');
    }
};

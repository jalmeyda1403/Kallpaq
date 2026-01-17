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
        Schema::create('auditoria_ejecuciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ae_id'); // Auditoria Específica
            $table->unsignedBigInteger('proceso_id')->nullable(); // Proceso Auditado (puede ser null para auditoría general)
            $table->unsignedBigInteger('auditor_id')->nullable(); // Auditor Responsable

            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();

            $table->enum('estado', ['Pendiente', 'En Curso', 'Completado', 'Cerrado'])->default('Pendiente');
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Foreign Keys
            // We assume 'auditoria_especifica' and 'procesos' tables exist. 
            // If table names differ, adjustments might be needed (e.g. auditoria_especificas?)
            // Checking AuditoriaEspecifica model: protected $table = 'auditoria_especifica';
            $table->foreign('ae_id')->references('id')->on('auditoria_especifica')->onDelete('cascade');
            $table->foreign('proceso_id')->references('id')->on('procesos')->onDelete('set null');
            $table->foreign('auditor_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('auditoria_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ejecucion_id');

            $table->string('norma_referencia')->nullable(); // e.g., "ISO 9001:2015"
            $table->string('requisito_referencia')->nullable(); // e.g., "4.1"

            $table->text('requisito_contenido')->nullable(); // Texto del requisito
            $table->text('pregunta')->nullable(); // Pregunta generada por IA
            $table->text('criterio_auditoria')->nullable();
            $table->text('evidencia_esperada')->nullable();

            // Resultados
            $table->enum('estado_cumplimiento', ['Sin Evaluar', 'Conforme', 'No Conforme', 'Oportunidad de Mejora', 'No Aplica'])->default('Sin Evaluar');
            $table->text('evidencia_registrada')->nullable(); // URL o texto
            $table->text('hallazgo_detectado')->nullable(); // Sugerencia de hallazgo
            $table->enum('tipo_hallazgo', ['NCM', 'NCMen', 'OM', 'OBS'])->nullable();

            $table->text('comentarios')->nullable();
            $table->boolean('ai_generated')->default(false);

            $table->timestamps();

            $table->foreign('ejecucion_id')->references('id')->on('auditoria_ejecuciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_checklists');
        Schema::dropIfExists('auditoria_ejecuciones');
    }
};

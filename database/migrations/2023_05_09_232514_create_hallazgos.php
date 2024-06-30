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
        Schema::create('hallazgos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('informe_id');
            $table->unsignedBigInteger('proceso_cod');
            $table->text('descripcion');
            $table->string('evidencia');
            $table->string('criterio');
            $table->enum('clasificacion', ['NCM', 'Ncme', 'Obs', 'Odm']);
            $table->enum('origen', ['IN', 'EX', 'SN', 'RI', 'GR', 'RV', 'CL', 'HA', 'ACAL', 'O']);
            $table->enum('estado', ['Abierto', 'En implementación', 'Pendiente', 'Para evaluación','Cerrado']);
            $table->date('fecha_solicitud');
            $table->date('fecha_cierre_acciones');
            $table->date('fecha_planificacion_evaluacion');
            $table->string('evaluacion');
            $table->date('fecha_evaluacion');
            $table->date('fecha_cierre_hallazgo');
            $table->enum('estado_final', ['Sin Efacia', 'Con Eficacia']);         
            $table->timestamps();

           // Relación con la tabla de Procesos.
            $table->foreign('proceso_cod')->references('id')->on('procesos');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallazgos');
    }
};

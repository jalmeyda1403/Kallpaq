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
        Schema::dropIfExists('snc_acciones');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('snc_acciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('snc_id')->constrained('salidas_no_conformes')->onDelete('cascade');
            $table->text('accion_descripcion')->comment('Descripción de la acción correctiva');
            $table->foreignId('accion_responsable_id')->constrained('users')->comment('Responsable de la acción');
            $table->date('accion_fecha_planificada')->comment('Fecha planificada de ejecución');
            $table->date('accion_fecha_real')->nullable()->comment('Fecha real de ejecución');
            $table->enum('accion_estado', ['planificada', 'en ejecución', 'completada', 'cancelada'])->default('planificada');
            $table->string('accion_evidencia')->nullable()->comment('Ruta a archivo de evidencia');
            $table->timestamps();
        });
    }
};

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
        // Create the table instead of altering it
        Schema::create('acciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hallazgo_id')->nullable();
            $table->unsignedBigInteger('proceso_id')->nullable();
            $table->string('accion_cod')->nullable();
            $table->string('tipo_accion')->nullable();
            $table->text('accion_descripcion')->nullable();
            $table->text('accion_comentario')->nullable();
            $table->date('accion_fecha_inicio')->nullable();
            $table->date('accion_fecha_fin_planificada')->nullable();
            $table->date('accion_fecha_fin_reprogramada')->nullable();
            $table->date('accion_fecha_cancelada')->nullable();
            $table->date('accion_fecha_fin_real')->nullable();
            $table->text('accion_justificacion')->nullable();
            $table->text('accion_ruta_evidencia')->nullable();
            $table->string('accion_responsable')->nullable();
            $table->string('accion_responsable_correo')->nullable();
            $table->string('accion_estado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acciones');
    }
};

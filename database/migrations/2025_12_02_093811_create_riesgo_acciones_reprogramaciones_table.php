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
        Schema::create('riesgo_acciones_reprogramaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('riesgo_accion_id');
            $table->date('rar_fecha_anterior');
            $table->date('rar_fecha_nueva');
            $table->text('rar_justificacion');
            $table->string('rar_evidencia')->nullable();
            $table->enum('rar_estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->unsignedBigInteger('rar_aprobado_por')->nullable();
            $table->timestamp('rar_fecha_aprobacion')->nullable();
            $table->text('rar_comentario_aprobacion')->nullable();
            $table->timestamps();

            $table->foreign('riesgo_accion_id')->references('id')->on('riesgo_acciones')->onDelete('cascade');
            $table->foreign('rar_aprobado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riesgo_acciones_reprogramaciones');
    }
};

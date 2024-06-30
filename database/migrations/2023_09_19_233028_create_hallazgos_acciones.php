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
        Schema::create('hallazgos_acciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hallazgo_id');
            $table->text('descripcion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->unsignedBigInteger('responsable_id');
            $table->text('comentario');
            $table->date('fecha_fin_reprogramada')->nullable();
            $table->text('ruta');
            $table->enum('estado', ['Pendiente', 'En implementación', 'Cancelado','Finalizado']);
            $table->boolean('es_correctiva')->default(false); // Nuevo campo para identificar si es una acción correctiva
            $table->timestamps();

            // Relación con la tabla de hallazgos
            $table->foreign('hallazgo_id')->references('id')->on('hallazgos')->onDelete('cascade');        
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallazgos_acciones');
    }
};

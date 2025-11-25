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
        Schema::create('sugerencias', function (Blueprint $table) {
            $table->id();
            $table->string('sugerencia_clasificacion'); // MP, MT, AC, MF, CF, CT
            $table->text('sugerencia_detalle');
            $table->date('sugerencia_fecha_ingreso');
            $table->string('sugerencia_procedencia'); // interno, cliente, externo, etc.
            $table->text('sugerencia_analisis')->nullable();
            $table->string('sugerencia_viabilidad')->nullable(); // viable, no viable
            $table->text('sugerencia_tratamiento')->nullable();
            $table->string('sugerencia_estado')->default('abierta'); // abierta, en progreso, cerrada
            $table->date('sugerencia_fecha_fin_prog')->nullable();
            $table->date('sugerencia_fecha_fin_real')->nullable();
            $table->foreignId('proceso_id')->constrained('procesos')->onDelete('cascade');
            $table->json('sugerencia_evidencias')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sugerencias');
    }
};

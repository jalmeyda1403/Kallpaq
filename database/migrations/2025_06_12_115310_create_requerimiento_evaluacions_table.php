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
        Schema::create('requerimiento_evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requerimiento_id')->index();
            $table->unsignedTinyInteger('num_actividades')->comment('1–4 de acuerdo a la complejidad');
            $table->unsignedTinyInteger('num_areas')->comment('1–4 de acuerdo a la complejidad'); // <-- Corregido
            $table->unsignedTinyInteger('num_requisitos')->comment('1–4 de acuerdo a la complejidad'); // <-- Corregido
            $table->unsignedTinyInteger('nivel_documentacion')->comment('1–4 de acuerdo a la complejidad');
            $table->unsignedTinyInteger('impacto_requerimiento')->comment('1–4 de acuerdo a la complejidad'); // <-- Corregido
            $table->unsignedTinyInteger('complejidad_valor'); // Promedio o puntuación final // <-- Corregido
            $table->enum('complejidad_nivel', ['baja', 'media', 'alta', 'muy alta']);
            $table->date('fecha_evaluacion');
            $table->timestamps();
            $table->foreign('requerimiento_id')->references('id')->on('requerimientos')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requerimiento_evaluaciones');
    }
};

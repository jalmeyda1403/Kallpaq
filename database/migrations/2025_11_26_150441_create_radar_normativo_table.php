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
        Schema::create('radar_normativo', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('numero_norma')->nullable();
            $table->string('organismo_emisor')->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->text('resumen_ia')->nullable();
            $table->longText('texto_completo')->nullable();
            $table->enum('nivel_relevancia', ['Alta', 'Media', 'Baja'])->default('Media');
            $table->enum('estado', ['Pendiente', 'En Análisis', 'Aplicable', 'No Aplicable'])->default('Pendiente');
            $table->text('analisis_humano')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radar_normativo');
    }
};

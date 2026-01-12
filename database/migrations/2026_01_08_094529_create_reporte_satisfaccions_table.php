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
        Schema::create('reporte_satisfaccions', function (Blueprint $table) {
            $table->id();
            $table->integer('anio');
            $table->integer('trimestre');
            $table->date('fecha_generacion');
            $table->foreignId('proceso_id')->constrained('procesos');
            $table->text('resumen_encuestas')->nullable();
            $table->text('resumen_sugerencias')->nullable();
            $table->text('reclamos')->nullable(); // Campo manual solicitado
            $table->text('resumen_snc')->nullable();
            $table->text('oportunidades_mejora')->nullable(); // IA + Humano
            $table->text('conclusiones')->nullable(); // IA + Humano
            $table->string('archivo_path')->nullable();
            $table->enum('estado', ['borrador', 'generado', 'firmado'])->default('borrador');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_satisfaccions');
    }
};

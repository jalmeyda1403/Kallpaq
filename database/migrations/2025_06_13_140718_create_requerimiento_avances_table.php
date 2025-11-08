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
        Schema::create('requerimiento_avances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requerimiento_id')->constrained()->onDelete('cascade');

            // Hitos de avance
            $table->boolean('levantamiento')->default(false);
            $table->text('comentario_levantamiento')->nullable();

            $table->boolean('contexto')->default(false);
            $table->text('comentario_contexto')->nullable();

            $table->boolean('caracterizacion')->default(false);
            $table->text('comentario_caracterizacion')->nullable();

            $table->boolean('formatos')->default(false);
            $table->text('comentario_formatos')->nullable();

            $table->boolean('revision_interna')->default(false);
            $table->text('comentario_revision_interna')->nullable();

            $table->boolean('revision_tecnica')->default(false);
            $table->text('comentario_revision_tecnica')->nullable();

            $table->boolean('firma')->default(false);
            $table->text('comentario_firma')->nullable();

            $table->boolean('publicacion')->default(false);
            $table->text('comentario_publicacion')->nullable();

            // Ruta general a evidencias o documentos relacionados
            $table->string('ruta_evidencias')->nullable();

            $table->unsignedDecimal('avance_registrado', 5, 2)->default(0); // porcentaje 0–100%

            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requerimiento_avances');
    }
};

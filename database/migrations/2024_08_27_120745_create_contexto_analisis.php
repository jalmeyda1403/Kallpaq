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
        Schema::create('contexto_analisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contexto_determinacion_id')->constrained('contexto_determinacion')->onDelete('cascade');
            $table->foreignId('internal_context_id')->constrained('contexto_interno')->onDelete('cascade');
            $table->foreignId('external_context_id')->constrained('contexto_externo')->onDelete('cascade');
            $table->text('analisis');
            $table->enum('nivel', ['Muy Alto','Alto', 'Medio', 'Bajo']);
            $table->unsignedInteger('valoracion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contexto_analisis');
    }
};

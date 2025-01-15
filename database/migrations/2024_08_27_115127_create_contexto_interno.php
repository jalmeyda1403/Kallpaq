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
        Schema::create('contexto_interno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contexto_determinacion_id')->constrained('contexto_determinacion')->onDelete('cascade');
            $table->enum('perspective_type', ['normativa', 'infraestructura', 'tecnologia', 'organizacion', 'personal', 'cultura_organizacional']);
            $table->text('fortalezas');
            $table->text('debilidades');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contexto_interno');
    }
};

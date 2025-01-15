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
        Schema::create('contexto_externo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contexto_determinacion_id')->constrained('contexto_determinacion')->onDelete('cascade');
            $table->enum('perspective_type', ['legal', 'politico', 'institucional', 'tecnologia', 'social', 'economico']);
            $table->text('amenazas');
            $table->text('oportunidades');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contexto_externo');
    }
};

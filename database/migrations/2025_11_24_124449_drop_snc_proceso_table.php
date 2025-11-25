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
        Schema::dropIfExists('snc_proceso');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('snc_proceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('snc_id')->constrained('salidas_no_conformes')->onDelete('cascade');
            $table->foreignId('proceso_id')->constrained('procesos')->onDelete('cascade');
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['snc_id', 'proceso_id']);
        });
    }
};

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
        Schema::create('documento_proceso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained()->onDelete('cascade');
            $table->foreignId('proceso_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Esto asegura que la combinación de documento_id y proceso_id sea única
            $table->unique(['documento_id', 'proceso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_proceso');
    }
};

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
        Schema::create('necesidades_expectativas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parte_interesada_id')->constrained('partes_interesadas')->onDelete('cascade');
            $table->enum('expectativa_tipo', ['necesidad', 'expectativa']);
            $table->text('expectativa_descripcion');
            $table->json('expectativ_sig')->nullable(); // Ej: ["ISO 9001", "ISO 37301"]
            $table->unsignedBigInteger('proceso_id'); // Vinculación con macroproceso
            $table->text('expectativa_observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('necesidades_expectativas');
    }
};

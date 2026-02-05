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
        Schema::create('obligacion_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obligacion_id')->constrained('obligaciones')->onDelete('cascade');
            $table->decimal('oe_puntaje_total', 8, 2);
            $table->enum('oe_nivel_criticidad', ['baja', 'media', 'alta', 'muy_alta']);
            $table->date('oe_fecha_evaluacion');
            $table->json('oe_criterios_json')->nullable(); // Guardar detalle de criterios
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obligacion_evaluacion');
    }
};

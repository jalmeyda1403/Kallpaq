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
        // Eliminamos las tablas obsoletas
        Schema::dropIfExists('riesgo_acciones_reprogramaciones');
        Schema::dropIfExists('riesgo_acciones');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es práctico recrearlas con datos originales aquí, 
        // ya que la migración de unificación ya movió los datos.
    }
};

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
        Schema::create('salida_no_conforme_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salida_no_conforme_id')->constrained('salidas_no_conformes')->onDelete('cascade');
            $table->string('estado');
            $table->text('observacion')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('fecha_movimiento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salida_no_conforme_movimientos');
    }
};

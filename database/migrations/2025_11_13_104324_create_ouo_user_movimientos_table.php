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
        Schema::create('ouo_user_movimientos', function (Blueprint $table) {
           $table->id();
            
            // FK a la tabla principal ouo_user
            $table->unsignedBigInteger('ouo_user_id');
            $table->foreign('ouo_user_id')->references('id')->on('ouo_user')->onDelete('cascade');

            // FK al usuario que realizó el cambio
            $table->unsignedBigInteger('cambiado_por');
            $table->foreign('cambiado_por')->references('id')->on('users')->onDelete('restrict');

            // Registro del tipo de cambio
            $table->enum('tipo_anterior', ['titular', 'suplente', 'facilitador']);
            $table->enum('tipo_nuevo', ['titular', 'suplente', 'facilitador']);
            
            // Motivo del cambio
            $table->text('motivo')->nullable();
            
            // Fecha del registro del movimiento
            $table->dateTime('fecha_cambio')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ouo_user_movimientos');
    }
};

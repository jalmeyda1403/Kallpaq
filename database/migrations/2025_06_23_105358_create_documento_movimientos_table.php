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
        Schema::create('documento_movimientos', function (Blueprint $table) {
            $table->id();
            // Documento relacionado
            $table->foreignId('documento_id')
                ->constrained()
                ->onDelete('cascade');

            // Acción registrada
            $table->enum('accion', [
                'creado',
                'modificado',
                'publicado',
                'eliminado',
                'reactivado',
            ]);

            // Observación adicional (opcional)
            $table->text('observacion')->nullable();

            // Usuario que realizó la acción
            $table->foreignId('usuario_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Fecha/hora exacta del evento
            $table->timestamp('fecha_movimiento')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_movimientos');
    }
};

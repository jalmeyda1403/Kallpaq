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
        Schema::create('sugerencia_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sugerencia_id')->constrained('sugerencias')->onDelete('cascade');
            $table->string('estado');
            $table->text('observacion')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users'); // Nullable for system actions if any, or constrained
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sugerencia_movimientos');
    }
};

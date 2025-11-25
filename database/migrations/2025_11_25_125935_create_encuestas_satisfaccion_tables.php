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
        Schema::create('encuestas_satisfaccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained('procesos')->onDelete('cascade');
            $table->string('periodo'); // 'Trimestre 1', 'Mensual', etc.
            $table->integer('anio');
            $table->decimal('nps_score', 5, 2)->nullable();
            $table->integer('cantidad_encuestas')->nullable();
            $table->string('informe_path')->nullable();
            $table->timestamps();
        });

        Schema::create('encuesta_satisfaccion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')->constrained('encuestas_satisfaccion')->onDelete('cascade');
            $table->string('categoria'); // 'Trato', 'Tiempo', etc.
            $table->decimal('puntaje', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encuesta_satisfaccion_detalles');
        Schema::dropIfExists('encuestas_satisfaccion');
    }
};

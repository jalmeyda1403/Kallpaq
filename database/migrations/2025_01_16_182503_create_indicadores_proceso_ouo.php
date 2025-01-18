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
        Schema::create('indicadores_proceso_ouo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_proceso_ouo');
            $table->unsignedBigInteger('id_indicador');
            $table->float('meta');
            $table->char('year');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->timestamps();
          
            // Foreign keys
            $table->foreign('id_proceso_ouo')->references('id')->on('procesos_ouo')->onDelete('cascade');
            $table->foreign('id_indicador')->references('id')->on('indicadores')->onDelete('cascade');
        });
      }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicadores_proceso_ouo');
    }
};

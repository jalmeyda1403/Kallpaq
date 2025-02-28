<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('obligacion_riesgo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obligacion_id')->constrained('obligaciones')->onDelete('cascade'); // Relación con 'obligaciones'
            $table->foreignId('riesgo_id')->constrained('riesgos')->onDelete('cascade'); // Relación con 'riesgos'
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps(); // Timestamps para seguimiento
        });
    }

    /**
     * Revierte las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('obligacion_riesgo');
    }
};


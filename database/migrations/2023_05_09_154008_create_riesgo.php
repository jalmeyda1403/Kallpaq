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
        Schema::create('riesgos', function (Blueprint $table) {
            $table->id();
            $table->string('riesgo_cod')->unique();
            $table->string('nombre');
            $table->unsignedBigInteger('proceso_cod');
            $table->foreign('proceso_cod')->references('id')->on('procesos');
            $table->text('descripcion');
            $table->enum('tipo_riesgo', ['Riesgo', 'Oportunidad']);
            $table->unsignedBigInteger('factor_cod');
            $table->foreign('factor_cod')->references('id')->on('factores');
            $table->integer('probabilidad');
            $table->integer('impacto');
            $table->integer('valoracion_riesgo');
            $table->enum('tratamiento_riesgo', ['Reducir', 'Aceptar','Trasladar']);
            $table->enum('estado', ['Abierto', 'Cerrado']);
            $table->date('fecha_valoracion_rr')->nullable();
            $table->integer('probabilidad_rr')->nullable();
            $table->integer('impacto_rr')->nullable();
            $table->integer('evaluacion_rr')->nullable();
            $table->enum('estado_riesgo_rr', ['Con Eficacia', 'Sin eficacia']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('riesgos');
    }
};

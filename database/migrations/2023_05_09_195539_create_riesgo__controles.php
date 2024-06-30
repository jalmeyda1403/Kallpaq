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
        Schema::create('riesgo_controles', function (Blueprint $table) {
            $table->id();
            $table->string('control_cod');
            $table->text('descripcion');
            $table->enum('tipo', ['Manual', 'Automatico','Mixto']);
            $table->string('frecuencia');
            $table->string('responsable');
            $table->date('fecha_implementacion')->nullable();;
            $table->date('fecha_evaluacion')->nullable();
            $table->enum('evaluación', ['implementado', 'parcialmente', 'no implementado','inadecuado']);
            $table->date('fecha_revaluacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('riesgo_cod');
            $table->foreign('riesgo_cod')->references('id')->on('riesgos');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riesgo_controles');
    }
};

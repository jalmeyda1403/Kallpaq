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
        Schema::create('riesgo_acciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('riesgo_cod');
            $table->foreign('riesgo_cod')->references('id')->on('riesgos');
            $table->string('nombre');
            $table->text('descripcion');
            $table->date('fecha_prog_inicio')->nullable();
            $table->date('fecha_prog_fin')->nullable();
            $table->date('fecha_implementacion')->nullable();
            $table->string('responsable')->nullable();
            $table->enum('estado', ['En Implementación','Pendiente', 'Implementado','Cancelado']);
            $table->longtext('comentario')->nullable();;
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riesgo_acciones');
    }
};

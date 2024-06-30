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
        Schema::create('indicadores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indicador_id');
            $table->unsignedBigInteger('proceso_cod');
            $table->text('producto');
            $table->text('cliente');
            $table->string('ObjSIG');
            $table->boolean('objSIG_estado')->default(false);
            $table->boolean('estado')->default(true);
            $table->text('nombre');
            $table->text('descripcion');
            $table->string('formula');
            $table->enum('frencuencia', ['mensual', 'trimestral', 'semestral', 'anual']);
            $table->float('meta');
            $table->enum('unidad_medida', ['ratio', 'porcentaje', 'numero', 'indice','otros']);
            $table->enum('tipo_indicador', ['Producto', 'Servicio', 'Resultado', 'Calidad']);
            // Agregar los campos adicionales necesarios
            $table->timestamps();
            $table->foreign('proceso_cod')->references('id')->on('procesos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicadores');
    }
};

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
        Schema::create('indicadores_seguimiento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indicador_id');
            $table->text('mes');
            $table->float('meta');
            $table->float('valor');
            $table->float('var1');
            $table->float('var2');
            $table->float('var3');
            $table->float('var4');
            $table->float('var5');
            $table->float('var6');
            $table->text('accion');
            $table->text('responsable');
            $table->date('plazo');
            $table->date('plazo_repr');
            $table->enum('accion_estado', ['cerrada', 'pendiente', 'implementada', 'cancelada']);
            $table->longtext('evidencias')->nullable();
            $table->timestamps();
            $table->foreign('indicador_id')->references('id')->on('indicadores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicadores__seguimiento');
    }
};

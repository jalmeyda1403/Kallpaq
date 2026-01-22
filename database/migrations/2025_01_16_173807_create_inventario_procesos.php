<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('inventario_procesos', function (Blueprint $table) {
            $table->id('id_inventario');
            $table->unsignedBigInteger('id_proceso');
            $table->unsignedBigInteger('id_ouo_responsable');
            $table->unsignedBigInteger('id_ouo_delegada');
            $table->string('version');
            $table->string('documento_aprobacion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_proceso')->references('id')->on('procesos')->onDelete('cascade');
            $table->foreign('id_ouo_responsable')->references('id')->on('ouos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventario_procesos');
    }
};

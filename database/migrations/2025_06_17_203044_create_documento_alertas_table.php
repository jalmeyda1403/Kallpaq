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
        Schema::create('documento_alertas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documento_id');
            $table->unsignedBigInteger('documento_impactado_id');
            $table->text('comentario');
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');
            $table->timestamps();

            $table->foreign('documento_id')->references('id')->on('documentos');
            $table->foreign('documento_impactado_id')->references('id')->on('documentos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_alertas');
    }
};

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
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sipoc_id'); // Relación con SIPOC
            $table->string('salida'); // Descripción de la salida
            $table->enum('tipo', ['servicio', 'producto', 'regulacion']); // Tipo de salida
            $table->timestamps();
    
            $table->foreign('sipoc_id')->references('id')->on('sipocs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};

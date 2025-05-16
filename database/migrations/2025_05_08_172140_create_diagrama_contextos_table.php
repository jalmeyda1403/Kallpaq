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
        Schema::create('diagrama_contexto', function (Blueprint $table) {
            $table->id();
            $table->string('archivo');  // Ruta del archivo (imagen) del diagrama
            $table->string('version');  // Versión del diagrama
            $table->date('vigencia');   // Fecha de vigencia del diagrama       
            $table->enum('estado', ['activo', 'inactivo'])->default('inactivo'); 
            $table->date('inactive_at');   
            $table->timestamps();      // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagrama_contexto');
    }
};

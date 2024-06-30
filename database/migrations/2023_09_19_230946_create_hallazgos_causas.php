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
        Schema::create('hallazgos_causas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hallazgo_id');
            $table->text('descripcion');
            $table->enum('dimension', [
                'Personal', 
                'Procedimientos Internos', 
                'Equipamiento', 
                'Regulatorio', 
                'Corrupcion',
                'Seguridad de la Información',
                'Comunicación Organizacional',
            ]);
            $table->timestamps();

            // Relación con la tabla de hallazgos
            $table->foreign('hallazgo_id')->references('id')->on('hallazgos')->onDelete('cascade');
        });
        
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hallazgos_causas');
    }
};

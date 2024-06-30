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
        Schema::create('requerimiento_tipo_documento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requerimiento_id')->constrained('requerimientos')->onDelete('cascade');
            $table->foreignId('tipo_documento_id')->constrained('tipos_documentos');
            $table->enum('estado', [
                'crear', 
                'actualizar', 
                'eliminar'
            ]);
            $table->text('nombre_documento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requerimiento_necesidad');
    }
};

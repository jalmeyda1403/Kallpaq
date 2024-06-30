<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requerimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained();
            $table->foreignId('user_id')->constrained('users');
            $table->text('facilitador_id');
            $table->text('descripcion');
            $table->text('justificacion');
            $table->enum('estado', [
                'creado', 
                'aprobado', 
                'asignado', 
                'atendido',
                'desestimado'
            ]);
            $table->enum('prioridad', [
                'baja', 
                'media', 
                'alta', 
                'muy alta'
            ]);
            $table->enum('complejidad', [
                'baja', 
                'media', 
                'alta', 
                'muy alta'
            ]);
            $table->string('ruta_archivo_desistimacion')->nullable();
            $table->string('ruta_archivo_entregable')->nullable();
            $table->timestamp('fecha_limite')->nullable();
            $table->timestamp('fecha_cierre')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requerimientos');
    }
};

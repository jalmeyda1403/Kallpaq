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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->string('auditoria_cod');
            $table->string('objetivo');
            $table->string('criterios_auditoria');
            $table->string('alcance_auditoria');
            $table->enum('tipo_auditoria', ['INT', 'EXT']);
            $table->string('sistema_iso');
            $table->double('costo_programado');
            $table->double('costo_ejecutado');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedBigInteger('programa_auditoria_id');
            $table->foreign('programa_auditoria_id')->references('id')->on('programa_auditoria')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};

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

        Schema::create('obligaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_compliance_id')->constrained('areas_compliance');
            $table->foreignId('subarea_compliance_id')->nullable()->constrained('subareas_compliance');
            $table->string('documento_tecnico_normativo');
            $table->text('obligacion_principal');
            $table->text('obligacion_controles')->nullable(); // Still exists before pending migration
            $table->text('consecuencia_incumplimiento')->nullable();
            $table->text('documento_deroga')->nullable();
            $table->string('estado_obligacion')->default('pendiente');
            $table->foreignId('radar_id')->nullable()->constrained('radar_normativo');
            $table->foreignId('documento_id')->nullable()->constrained('documentos');
            $table->string('tipo_obligacion')->default('Legal');
            $table->integer('frecuencia_revision')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obligacion');
    }
};

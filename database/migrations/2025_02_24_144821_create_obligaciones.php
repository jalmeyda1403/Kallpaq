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
            $table->foreignId('subarea_compliance_id')->nullable()->constrained('subareas_compliance')->onDelete('set null');
            $table->text('obligacion_documento')->nullable();
            $table->text('obligacion_principal');
            $table->text('obligacion_consecuencia')->nullable();
            $table->text('obligacion_documento_deroga')->nullable();
            $table->enum('obligacion_estado', ['pendiente', 'mitigada', 'controlada', 'vencida', 'inactiva', 'suspendida'])->default('pendiente');
            $table->foreignId('radar_id')->nullable()->constrained('radar_normativo')->onDelete('set null');
            $table->foreignId('documento_id')->nullable()->constrained('documentos')->onDelete('set null');
            $table->enum('obligacion_tipo', ['legal', 'contractual', 'voluntaria'])->default('legal');
            $table->integer('obligacion_frecuencia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obligaciones');
    }
};

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
       
    Schema::create('obligaciones', function (Blueprint $table) {
        $table->id();
        $table->foreignId('proceso_id')->constrained('procesos');
        $table->foreignId('area_compliance_id')->constrained('areas_compliance');
        $table->string('documento_tecnico_normativo');
        $table->text('obligacion_principal');
        $table->text('obligacion_controles');
        $table->text('consecuencia_incumplimiento');
        $table->text('documento_deroga');
        $table->enum('estado_obligacion', ['vigente', 'inactivo'])->default('vigente');
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

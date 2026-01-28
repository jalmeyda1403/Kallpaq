<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        try {
            // 1. Drop Old Table (Clean Slate)
            Schema::dropIfExists('riesgo_controles');

            // 2. Create Master Table
            if (!Schema::hasTable('controles')) {
                Schema::create('controles', function (Blueprint $table) {
                    $table->id();
                    $table->string('nombre')->nullable();
                    $table->text('descripcion')->nullable();
                    $table->string('tipo')->nullable();
                    $table->string('frecuencia')->nullable();
                    $table->unsignedBigInteger('responsable_id')->nullable();
                    $table->string('responsable')->nullable();
                    $table->date('fecha_implementacion')->nullable();
                    $table->string('estado')->default('Activo');

                    $table->timestamps();
                });
            }

            // 3. Create Pivot Table
            if (!Schema::hasTable('control_riesgo')) {
                Schema::create('control_riesgo', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('riesgo_id')->constrained('riesgos')->onDelete('cascade');
                    $table->foreignId('control_id')->constrained('controles')->onDelete('cascade');

                    // Instance data
                    $table->string('eficacia')->nullable();
                    $table->date('fecha_ultima_evaluacion')->nullable();
                    $table->date('fecha_revaluacion')->nullable();
                    $table->text('observaciones')->nullable();

                    $table->timestamps();
                });
            }

        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    public function down(): void
    {
        // Warn: This down migration cannot restore lost data.
        Schema::dropIfExists('control_riesgo');
        Schema::dropIfExists('controles');
        // Re-creating empty original table
        Schema::create('riesgo_controles', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion')->nullable();
            // ... truncated restoration
            $table->timestamps();
        });
    }
};

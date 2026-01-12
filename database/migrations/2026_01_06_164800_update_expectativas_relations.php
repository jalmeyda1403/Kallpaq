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
        // 1. Remove proceso_id from necesidades_expectativas
        if (Schema::hasColumn('necesidades_expectativas', 'proceso_id')) {
            // Check if foreign key exists before dropping - difficult in generic migration without name.
            // Ignoring FK drop for now, typically MySQL handles dropColumn by dropping FK if strict mode off, or fails.
            // If it fails, we need the exact FK name. usually necesidades_expectativas_proceso_id_foreign
            try {
                Schema::table('necesidades_expectativas', function (Blueprint $table) {
                     $table->dropForeign(['proceso_id']);
                });
            } catch (\Exception $e) {
                // Ignore if FK doesn't exist
            }
            
            Schema::table('necesidades_expectativas', function (Blueprint $table) {
                $table->dropColumn('proceso_id');
            });
        }

        // 2. Create Pivot Table for Risks
        if (!Schema::hasTable('expectativa_riesgo')) {
            Schema::create('expectativa_riesgo', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('expectativa_id');
                $table->unsignedBigInteger('riesgo_id');
                $table->timestamps();

                $table->foreign('expectativa_id')->references('id')->on('necesidades_expectativas')->onDelete('cascade');
                $table->foreign('riesgo_id')->references('id')->on('riesgo')->onDelete('cascade');
            });
        }

        // 3. Create Pivot Table for Obligations
        if (!Schema::hasTable('expectativa_obligacion')) {
            Schema::create('expectativa_obligacion', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('expectativa_id');
                $table->unsignedBigInteger('obligacion_id');
                $table->timestamps();

                $table->foreign('expectativa_id')->references('id')->on('necesidades_expectativas')->onDelete('cascade');
                // Use 'obligaciones' table name explicitly
                $table->foreign('obligacion_id')->references('id')->on('obligaciones')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expectativa_obligacion');
        Schema::dropIfExists('expectativa_riesgo');

        if (!Schema::hasColumn('necesidades_expectativas', 'proceso_id')) {
            Schema::table('necesidades_expectativas', function (Blueprint $table) {
                $table->unsignedBigInteger('proceso_id')->nullable();
            });
        }
    }
};

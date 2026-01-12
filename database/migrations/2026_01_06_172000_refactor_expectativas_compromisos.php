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
        // Drop previous pivot tables
        Schema::dropIfExists('expectativa_riesgo');
        Schema::dropIfExists('expectativa_obligacion');

        // Create Compromisos table
        if (!Schema::hasTable('expectativa_compromisos')) {
            Schema::create('expectativa_compromisos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('expectativa_id');
                $table->text('ec_descripcion');
                $table->unsignedBigInteger('ec_responsable_id')->nullable();
                $table->date('ec_fecha_limite')->nullable();
                $table->string('ec_estado')->default('pendiente'); // pendiente, en_proceso, completado
                $table->text('ec_avance')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('expectativa_id')->references('id')->on('necesidades_expectativas')->onDelete('cascade');
                $table->foreign('ec_responsable_id')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expectativa_compromisos');
    }
};

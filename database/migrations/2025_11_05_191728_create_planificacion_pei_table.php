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
        if (!Schema::hasTable('planificacion_pei')) {
            Schema::create('planificacion_pei', function (Blueprint $table) {
                $table->id();
                $table->string('planificacion_pei_cod');
                $table->string('planificacion_pei_nombre');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planificacion_pei');
    }
};
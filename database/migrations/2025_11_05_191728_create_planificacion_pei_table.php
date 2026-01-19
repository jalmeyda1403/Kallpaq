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
                $table->string('pp_cod');
                $table->string('pp_nombre');
                $table->string('pp_alcance');
                $table->string('pp_documento_aprueba');
                $table->dateTime('pp_fecha_aprueba')->nullable();
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
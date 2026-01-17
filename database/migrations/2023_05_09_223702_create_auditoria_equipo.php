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
        Schema::create('auditoria_equipo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ae_id');
            $table->unsignedBigInteger('auditor_id');
            $table->string('aeq_rol');
            $table->decimal('aeq_horas_planificadas', 8, 2)->default(0);
            $table->decimal('aeq_horas_ejecutadas', 8, 2)->default(0);
            $table->timestamps();

            $table->foreign('ae_id')->references('id')->on('auditoria_especifica')->onDelete('cascade');
            $table->foreign('auditor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_equipo');
    }
};

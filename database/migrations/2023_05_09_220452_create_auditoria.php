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
        Schema::create('auditoria_especifica', function (Blueprint $table) {
            $table->id(); // Model assumes 'id' or 'ae_id'? Model uses default 'id' usually.
            $table->unsignedBigInteger('pa_id'); // Relation to Programa
            // $table->foreign('pa_id')->references('id')->on('programa_auditoria')->onDelete('cascade'); // Add foreign key if referenced table exists correctly

            $table->string('ae_codigo')->nullable();
            $table->text('ae_objetivo')->nullable();
            $table->text('ae_criterios')->nullable();
            $table->text('ae_alcance')->nullable();
            $table->string('ae_tipo')->nullable(); // INT or EXT
            $table->string('ae_sistema_kallpaq')->nullable(); // sistema_iso
            $table->text('ae_lugar')->nullable();
            $table->text('ae_direccion')->nullable();
            $table->string('ae_estado')->default('Programada');

            $table->date('ae_fecha_inicio')->nullable();
            $table->date('ae_fecha_fin')->nullable();
            $table->dateTime('ae_reunion_apertura')->nullable();
            $table->dateTime('ae_reunion_cierre')->nullable();

            $table->unsignedBigInteger('proceso_id')->nullable(); // Add process link directly here if needed, or keeping separate. Model has process_id.

            $table->text('ae_equipo_auditor')->nullable();
            $table->text('ae_auditado')->nullable();

            $table->timestamps();

            $table->foreign('pa_id')->references('id')->on('programa_auditoria')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_especifica');
    }
};

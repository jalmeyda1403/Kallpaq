<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('auditoria_informes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ae_id')->constrained('auditoria_especifica')->onDelete('cascade');
            $table->string('codigo')->unique();
            $table->enum('estado', ['Borrador', 'En Revisión', 'Aprobado', 'Emitido'])->default('Borrador');
            $table->text('resumen_ejecutivo')->nullable();
            $table->text('alcance_criterios')->nullable();
            $table->json('hallazgos_conformidad')->nullable();
            $table->json('hallazgos_no_conformidad')->nullable();
            $table->text('conclusiones')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->foreignId('elaborado_por')->nullable()->constrained('users');
            $table->foreignId('aprobado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_informes');
    }
};

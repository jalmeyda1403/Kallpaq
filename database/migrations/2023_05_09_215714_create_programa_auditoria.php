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
        Schema::create('programa_auditoria', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->string('periodo');
            $table->decimal('presupuesto', 10, 2);
            $table->date('fecha_aprobacion');
            $table->decimal('avance');
            $table->text('observaciones')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->date('fecha_publicacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programa_auditoria');
    }
};

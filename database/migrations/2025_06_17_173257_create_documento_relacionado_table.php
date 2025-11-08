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
        Schema::create('documento_relacionado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos')->onDelete('cascade'); // Documento actual
            $table->foreignId('relacionado_id')->constrained('documentos')->onDelete('cascade'); // Documento relacionado
            $table->enum('tipo_relacion', ['impacta','modifica','deroga'])->default('impacta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_relacionado');
    }
};

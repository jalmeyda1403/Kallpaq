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
        Schema::create('documento_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos')->onDelete('cascade');
            $table->string('da_codigo', 50); // e.g., P01-F001
            $table->string('da_nombre', 255);
            $table->string('da_archivo_ruta', 500);
            $table->integer('da_version')->default(1);
            $table->enum('da_estado', ['VIGENTE', 'OBSOLETO'])->default('VIGENTE');
            $table->text('da_observacion')->nullable(); // Control de cambios text
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_anexos');
    }
};

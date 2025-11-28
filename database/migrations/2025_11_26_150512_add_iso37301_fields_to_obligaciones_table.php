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
        Schema::table('obligaciones', function (Blueprint $table) {
            $table->foreignId('radar_id')->nullable()->constrained('radar_normativo')->onDelete('set null');
            $table->foreignId('documento_id')->nullable()->constrained('documentos')->onDelete('set null');
            $table->enum('tipo_obligacion', ['Legal', 'Contractual', 'Voluntaria'])->nullable();
            $table->enum('nivel_riesgo_inherente', ['Bajo', 'Medio', 'Alto', 'Muy Alto'])->nullable();
            $table->enum('nivel_riesgo_residual', ['Bajo', 'Medio', 'Alto', 'Muy Alto'])->nullable();
            $table->integer('frecuencia_revision')->nullable()->comment('En días');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obligaciones', function (Blueprint $table) {
            $table->dropForeign(['radar_id']);
            $table->dropForeign(['documento_id']);
            $table->dropColumn([
                'radar_id',
                'documento_id',
                'tipo_obligacion',
                'nivel_riesgo_inherente',
                'nivel_riesgo_residual',
                'frecuencia_revision'
            ]);
        });
    }
};

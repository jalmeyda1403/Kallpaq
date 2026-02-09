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
            $table->enum('cumplimiento', ['pendiente', 'cumplida', 'parcialmente_cumplida', 'no_cumplida'])
                ->default('pendiente')
                ->after('obligacion_frecuencia'); // Adjust position if needed
            $table->date('fecha_cumplimiento')->nullable()->after('cumplimiento');
            $table->text('comentario_cumplimiento')->nullable()->after('fecha_cumplimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obligaciones', function (Blueprint $table) {
            $table->dropColumn(['cumplimiento', 'fecha_cumplimiento', 'comentario_cumplimiento']);
        });
    }
};

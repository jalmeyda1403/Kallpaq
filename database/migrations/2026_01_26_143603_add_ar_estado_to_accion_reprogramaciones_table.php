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
        Schema::table('accion_reprogramaciones', function (Blueprint $table) {
            $table->string('ar_estado')->default('aprobado')->after('ar_justificacion'); // pendiente, aprobado, rechazado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accion_reprogramaciones', function (Blueprint $table) {
            $table->dropColumn('ar_estado');
        });
    }
};

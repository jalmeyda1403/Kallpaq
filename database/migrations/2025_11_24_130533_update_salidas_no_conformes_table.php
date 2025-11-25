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
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            // Eliminar las columnas que no se van a usar
            $table->dropColumn(['snc_producto_servicio', 'snc_tipo']);

            // Cambiar snc_responsable_id a snc_responsable y cambiar a varchar
            $table->dropForeign(['snc_responsable_id']);
            $table->dropColumn('snc_responsable_id');
            $table->string('snc_responsable')->nullable()->after('snc_detectado_por');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            // Restaurar las columnas eliminadas
            $table->string('snc_producto_servicio');
            $table->enum('snc_tipo', ['producto', 'servicio', 'proceso']);

            // Restaurar snc_responsable_id y eliminar snc_responsable
            $table->dropColumn('snc_responsable');
            $table->foreignId('snc_responsable_id')->nullable()->constrained('users')->comment('Usuario responsable del tratamiento')->after('snc_detectado_por');

            // Eliminar snc_origen
            $table->dropColumn('snc_origen');
        });
    }
};

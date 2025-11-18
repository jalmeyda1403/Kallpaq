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
        Schema::table('inventarios', function (Blueprint $table) {
            $table->string('estado_flujo')->nullable(); // 'borrador', 'aprobado', 'cerrado'
            $table->unsignedBigInteger('inventario_cierre')->nullable(); // FK a inventarios
            $table->timestamp('fecha_cierre')->nullable(); // Fecha de cierre del inventario

            // Opcional: Añadir la clave foránea para inventario_cierre
            // $table->foreign('inventario_cierre')->references('id')->on('inventarios')->onDelete('set null');
            // Si se añade la FK, se debe borrar en 'down' también.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropColumn(['estado_flujo', 'inventario_cierre', 'fecha_cierre']);
            // Si se creó la FK, se debe eliminar aquí antes de borrar la columna.
            // $table->dropForeign(['inventario_cierre']);
        });
    }
};
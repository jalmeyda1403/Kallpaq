<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cambiar a VARCHAR temporalmente para permitir cualquier valor
        DB::statement("ALTER TABLE obligaciones MODIFY COLUMN obligacion_estado VARCHAR(50)");

        // 2. Actualizar estados antiguos
        DB::statement("UPDATE obligaciones SET obligacion_estado = 'identificada' WHERE obligacion_estado = 'pendiente'");
        DB::statement("UPDATE obligaciones SET obligacion_estado = 'controlada' WHERE obligacion_estado = 'mitigada'");
        DB::statement("UPDATE obligaciones SET obligacion_estado = 'no_controlada' WHERE obligacion_estado = 'vencida'");

        // Valores por defecto para cualquier otro caso
        DB::statement("UPDATE obligaciones SET obligacion_estado = 'identificada' WHERE obligacion_estado NOT IN ('identificada', 'evaluada', 'en_tratamiento', 'controlada', 'no_controlada', 'suspendida', 'inactiva')");

        // 3. Aplicar el nuevo ENUM
        DB::statement("ALTER TABLE obligaciones MODIFY COLUMN obligacion_estado ENUM('identificada', 'evaluada', 'en_tratamiento', 'controlada', 'no_controlada', 'suspendida', 'inactiva') DEFAULT 'identificada'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a los estados antiguos (aproximación)
        DB::statement("ALTER TABLE obligaciones MODIFY COLUMN obligacion_estado ENUM('pendiente', 'mitigada', 'controlada', 'vencida', 'inactiva', 'suspendida') DEFAULT 'pendiente'");
    }
};

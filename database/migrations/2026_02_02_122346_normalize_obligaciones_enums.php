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
        // 1. Normalizar datos existentes a minúsculas
        DB::table('obligaciones')->update([
            'obligacion_estado' => DB::raw('LOWER(obligacion_estado)'),
            'obligacion_tipo' => DB::raw('LOWER(obligacion_tipo)')
        ]);

        // 2. Corregir estados específicos
        DB::table('obligaciones')->where('obligacion_estado', 'mitigada')->update(['obligacion_estado' => 'controlada']);

        // 3. Modificar el enum usando SQL directo para evitar problemas de compatibilidad de Laravel con MySQL change()
        DB::statement("ALTER TABLE obligaciones MODIFY COLUMN obligacion_estado ENUM('pendiente', 'controlada', 'vencida', 'inactiva', 'suspendida') DEFAULT 'pendiente'");
        DB::statement("ALTER TABLE obligaciones MODIFY COLUMN obligacion_tipo ENUM('legal', 'contractual', 'voluntaria') DEFAULT 'legal'");
    }

    public function down(): void
    {
        // No down needed for normalization
    }
};

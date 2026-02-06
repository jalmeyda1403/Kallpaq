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
        // Use raw SQL to modify enum column because Doctrine DBAL has issues with ENUMs sometimes
        // and we need to add 'en análisis'
        DB::statement("ALTER TABLE salidas_no_conformes MODIFY COLUMN snc_estado ENUM('identificada', 'en análisis', 'en tratamiento', 'tratada', 'cerrada', 'observada') NOT NULL DEFAULT 'identificada'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE salidas_no_conformes MODIFY COLUMN snc_estado ENUM('identificada', 'en tratamiento', 'tratada', 'cerrada', 'observada') NOT NULL DEFAULT 'identificada'");
    }
};

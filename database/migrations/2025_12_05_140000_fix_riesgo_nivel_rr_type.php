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
        // Use raw SQL to avoid Doctrine DBAL enum issues
        DB::statement('ALTER TABLE riesgos MODIFY riesgo_nivel_rr VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to int if needed (though unlikely)
        DB::statement('ALTER TABLE riesgos MODIFY riesgo_nivel_rr INT NULL');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify causa_metodo to allow NULL
        DB::statement('ALTER TABLE hallazgo_causas MODIFY causa_metodo ENUM("ishikawa","cinco_porques") NULL');
        
        // Modify causa_resultado to allow NULL
        DB::statement('ALTER TABLE hallazgo_causas MODIFY causa_resultado TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to NOT NULL
        DB::statement('ALTER TABLE hallazgo_causas MODIFY causa_metodo ENUM("ishikawa","cinco_porques") NOT NULL');
        DB::statement('ALTER TABLE hallazgo_causas MODIFY causa_resultado TEXT NOT NULL');
    }
};

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
        Schema::table('radar_normativo', function (Blueprint $table) {
            if (!Schema::hasColumn('radar_normativo', 'obligacion_principal')) {
                $table->text('obligacion_principal')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('radar_normativo', 'analisis_humano')) {
                $table->text('analisis_humano')->nullable()->after('obligacion_principal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('radar_normativo', function (Blueprint $table) {
            $table->dropColumn(['obligacion_principal', 'analisis_humano']);
        });
    }
};

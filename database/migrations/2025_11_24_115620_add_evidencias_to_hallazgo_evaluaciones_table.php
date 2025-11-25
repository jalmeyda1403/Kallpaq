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
        Schema::table('hallazgo_evaluaciones', function (Blueprint $table) {
            $table->json('evidencias')->nullable()->after('observaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hallazgo_evaluaciones', function (Blueprint $table) {
            $table->dropColumn('evidencias');
        });
    }
};

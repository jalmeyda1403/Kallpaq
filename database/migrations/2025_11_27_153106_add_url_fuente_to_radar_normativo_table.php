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
            $table->string('url_fuente', 500)->nullable()->after('organismo_emisor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('radar_normativo', function (Blueprint $table) {
            $table->dropColumn('url_fuente');
        });
    }
};

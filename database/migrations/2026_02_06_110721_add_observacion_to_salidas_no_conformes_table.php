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
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            $table->text('snc_observacion')->nullable()->after('snc_estado');
            $table->timestamp('snc_fecha_observacion')->nullable()->after('snc_observacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            $table->dropColumn(['snc_observacion', 'snc_fecha_observacion']);
        });
    }
};

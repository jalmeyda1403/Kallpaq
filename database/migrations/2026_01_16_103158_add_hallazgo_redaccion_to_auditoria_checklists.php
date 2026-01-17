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
        Schema::table('auditoria_checklists', function (Blueprint $table) {
            $table->text('hallazgo_redaccion')->nullable()->after('hallazgo_detectado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_checklists', function (Blueprint $table) {
            $table->dropColumn('hallazgo_redaccion');
        });
    }
};

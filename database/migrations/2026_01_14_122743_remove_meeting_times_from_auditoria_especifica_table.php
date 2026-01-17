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
        Schema::table('auditoria_especifica', function (Blueprint $table) {
            $table->dropColumn(['ae_reunion_apertura', 'ae_reunion_cierre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_especifica', function (Blueprint $table) {
            $table->datetime('ae_reunion_apertura')->nullable();
            $table->datetime('ae_reunion_cierre')->nullable();
        });
    }
};

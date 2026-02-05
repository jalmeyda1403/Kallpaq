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
        Schema::table('auditoria_agenda', function (Blueprint $table) {
            $table->text('aea_requisito')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_agenda', function (Blueprint $table) {
            // Revert to nullable string (default 255)
            // Note: Data truncation might occur if reverting from large text
            $table->string('aea_requisito', 255)->nullable()->change();
        });
    }
};

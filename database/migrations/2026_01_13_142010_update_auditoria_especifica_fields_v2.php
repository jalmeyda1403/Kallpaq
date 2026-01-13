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
            if (!Schema::hasColumn('auditoria_especifica', 'ae_sistema')) {
                $table->json('ae_sistema')->nullable(); // JSON for multiple ISOs
            }
            if (!Schema::hasColumn('auditoria_especifica', 'ae_tipo')) {
                $table->string('ae_tipo')->default('Interna'); // Interna / Externa
            }
        });
    }

    public function down(): void
    {
        Schema::table('auditoria_especifica', function (Blueprint $table) {
            $table->dropColumn(['ae_sistema', 'ae_tipo']);
        });
    }
};

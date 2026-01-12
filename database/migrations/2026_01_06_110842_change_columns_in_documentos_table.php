<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->string('frecuencia_revision_documento')->nullable()->change();
            $table->string('fuente_documento')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            // Note: Reverting to integer might cause data loss if non-numeric values were stored.
            $table->integer('frecuencia_revision_documento')->nullable()->change();
            $table->string('fuente_documento')->nullable()->change();
        });
    }
};

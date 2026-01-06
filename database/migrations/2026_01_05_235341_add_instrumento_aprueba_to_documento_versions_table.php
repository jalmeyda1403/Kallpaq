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
        Schema::table('documento_versions', function (Blueprint $table) {
            if (!Schema::hasColumn('documento_versions', 'instrumento_aprueba')) {
                $table->string('instrumento_aprueba')->nullable()->after('fecha_aprobacion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_versions', function (Blueprint $table) {
             if (Schema::hasColumn('documento_versions', 'instrumento_aprueba')) {
                $table->dropColumn('instrumento_aprueba');
            }
        });
    }
};

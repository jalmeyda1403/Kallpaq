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
        Schema::table('revisiones_direccion', function (Blueprint $table) {
            $table->json('sistemas_gestion')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revisiones_direccion', function (Blueprint $table) {
            $table->dropColumn('sistemas_gestion');
        });
    }
};

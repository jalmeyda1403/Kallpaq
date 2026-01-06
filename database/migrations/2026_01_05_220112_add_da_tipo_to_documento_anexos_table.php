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
        Schema::table('documento_anexos', function (Blueprint $table) {
            $table->enum('da_tipo', ['FORMATO', 'MATRIZ', 'INFOGRAFIA', 'LISTADO', 'OTROS'])->default('FORMATO')->after('da_nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_anexos', function (Blueprint $table) {
            $table->dropColumn('da_tipo');
        });
    }
};

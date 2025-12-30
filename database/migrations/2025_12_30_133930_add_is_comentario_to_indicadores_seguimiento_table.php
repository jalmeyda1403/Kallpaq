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
        Schema::table('indicadores_seguimiento', function (Blueprint $table) {
            $table->text('is_comentario')->nullable()->after('is_valor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicadores_seguimiento', function (Blueprint $table) {
            $table->dropColumn('is_comentario');
        });
    }
};

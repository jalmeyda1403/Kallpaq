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
        Schema::table('acciones', function (Blueprint $table) {
            $table->unsignedBigInteger('obligacion_id')->nullable()->after('hallazgo_id');
            $table->foreign('obligacion_id')->references('id')->on('obligaciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acciones', function (Blueprint $table) {
            $table->dropForeign(['obligacion_id']);
            $table->dropColumn('obligacion_id');
        });
    }
};

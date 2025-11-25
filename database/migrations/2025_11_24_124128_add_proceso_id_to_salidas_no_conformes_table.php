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
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            $table->foreignId('proceso_id')->nullable()->constrained('procesos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salidas_no_conformes', function (Blueprint $table) {
            $table->dropForeign(['proceso_id']);
            $table->dropColumn('proceso_id');
        });
    }
};

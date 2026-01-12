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
        Schema::table('expectativa_compromisos', function (Blueprint $table) {
            // Drop wrong FK
            // Note: We used 'expectativa_id' referencing 'necesidades_expectativas'
            // Laravel default name: expectativa_compromisos_expectativa_id_foreign
            $table->dropForeign(['expectativa_id']);

            // Add correct FK to 'expectativas'
            $table->foreign('expectativa_id')->references('id')->on('expectativas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expectativa_compromisos', function (Blueprint $table) {
            $table->dropForeign(['expectativa_id']);
             // Restore old (wrong) one if needed, but rarely needed.
             // We'll just point back to expectations if we revert, or assume needs_expectations if that was intent.
             // Ideally we shouldn't revert to a broken state, but for strict rollback:
             $table->foreign('expectativa_id')->references('id')->on('necesidades_expectativas')->onDelete('cascade');
        });
    }
};

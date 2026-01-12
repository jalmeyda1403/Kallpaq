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
        Schema::table('expectativas', function (Blueprint $table) {
            $table->enum('exp_estado', ['pendiente', 'en_proceso', 'implementado'])->default('pendiente')->after('exp_prioridad');
            $table->text('exp_observaciones')->nullable()->after('exp_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expectativas', function (Blueprint $table) {
            $table->dropColumn(['exp_estado', 'exp_observaciones']);
        });
    }
};

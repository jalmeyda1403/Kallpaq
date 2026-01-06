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
        Schema::table('documento_movimientos', function (Blueprint $table) {
            $table->string('accion', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_movimientos', function (Blueprint $table) {
             // Revert to original enum if needed. Note: data loss possible if strings don't match enum.
             // We will try to map back to enum but usually down() on type change is destructive or hard.
             // For safety in this dev environment, we'll revert to simply string or try enum.
             // $table->enum('accion', ['creado', 'modificado', 'publicado', 'eliminado', 'reactivado'])->change();
        });
    }
};

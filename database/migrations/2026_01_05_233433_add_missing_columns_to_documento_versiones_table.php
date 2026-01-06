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
            if (!Schema::hasColumn('documento_versions', 'control_cambios')) {
                $table->text('control_cambios')->nullable();
            }
            if (!Schema::hasColumn('documento_versions', 'fecha_aprobacion')) {
                $table->date('fecha_aprobacion')->nullable();
            }
            if (!Schema::hasColumn('documento_versions', 'instrumento_aprueba')) {
                $table->string('instrumento_aprueba')->nullable();
            }
            if (!Schema::hasColumn('documento_versions', 'enlace_valido')) {
                $table->boolean('enlace_valido')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_versions', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('documento_versions', 'control_cambios')) $columnsToDrop[] = 'control_cambios';
            if (Schema::hasColumn('documento_versions', 'fecha_aprobacion')) $columnsToDrop[] = 'fecha_aprobacion';
            if (Schema::hasColumn('documento_versions', 'instrumento_aprueba')) $columnsToDrop[] = 'instrumento_aprueba';
            if (Schema::hasColumn('documento_versions', 'enlace_valido')) $columnsToDrop[] = 'enlace_valido';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

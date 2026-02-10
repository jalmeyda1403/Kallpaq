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
        // 1. Add progress column
        Schema::table('auditoria_especifica', function (Blueprint $table) {
            if (!Schema::hasColumn('auditoria_especifica', 'ae_avance')) {
                $table->decimal('ae_avance', 5, 2)->default(0)->after('ae_estado');
            }
        });

        // 2. Drop Foreign Key (if exists) - handled separately to catch error
        try {
            Schema::table('auditoria_especifica', function (Blueprint $table) {
                $table->dropForeign(['proceso_id']); // Default name: auditoria_especifica_proceso_id_foreign
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Ignore "Can't DROP FOREIGN KEY" error (SQLSTATE 42000 / 1091)
        } catch (\Exception $e) {
            // Ignore other errors
        }

        // 3. Drop Columns
        Schema::table('auditoria_especifica', function (Blueprint $table) {
            if (Schema::hasColumn('auditoria_especifica', 'proceso_id')) {
                $table->dropColumn('proceso_id');
            }

            if (Schema::hasColumn('auditoria_especifica', 'ae_equipo_auditor')) {
                $table->dropColumn('ae_equipo_auditor');
            }

            if (Schema::hasColumn('auditoria_especifica', 'ae_auditado')) {
                $table->dropColumn('ae_auditado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditoria_especifica', function (Blueprint $table) {
            if (Schema::hasColumn('auditoria_especifica', 'ae_avance')) {
                $table->dropColumn('ae_avance');
            }

            // Restore columns (nullable for safety)
            if (!Schema::hasColumn('auditoria_especifica', 'proceso_id')) {
                $table->unsignedBigInteger('proceso_id')->nullable();
                $table->foreign('proceso_id')->references('id')->on('procesos')->onDelete('cascade');
            }

            if (!Schema::hasColumn('auditoria_especifica', 'ae_equipo_auditor')) {
                $table->text('ae_equipo_auditor')->nullable();
            }

            if (!Schema::hasColumn('auditoria_especifica', 'ae_auditado')) {
                $table->text('ae_auditado')->nullable();
            }
        });
    }
};

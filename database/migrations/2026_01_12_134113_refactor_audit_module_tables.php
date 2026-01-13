<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Refactor 'programa_auditoria'
        Schema::table('programa_auditoria', function (Blueprint $table) {
            if (!Schema::hasColumn('programa_auditoria', 'pa_estado'))
                $table->string('pa_estado')->default('Borrador');
            if (!Schema::hasColumn('programa_auditoria', 'pa_objetivo_general'))
                $table->text('pa_objetivo_general')->nullable();
            if (!Schema::hasColumn('programa_auditoria', 'pa_alcance'))
                $table->text('pa_alcance')->nullable();
            if (!Schema::hasColumn('programa_auditoria', 'pa_metodos'))
                $table->text('pa_metodos')->nullable();
            if (!Schema::hasColumn('programa_auditoria', 'pa_criterios'))
                $table->text('pa_criterios')->nullable();
            if (!Schema::hasColumn('programa_auditoria', 'pa_riesgos'))
                $table->text('pa_riesgos')->nullable();

            // pa_recursos change skipped due to data type conflict (Text vs Decimal).
            /*
            if (Schema::hasColumn('programa_auditoria', 'pa_recursos')) {
                $table->decimal('pa_recursos', 15, 2)->change();
            }
            */
        });

        // 2. Refactor 'auditoria_especifica'
        Schema::table('auditoria_especifica', function (Blueprint $table) {
            if (!Schema::hasColumn('auditoria_especifica', 'ae_estado'))
                $table->string('ae_estado')->default('Programada');
            if (!Schema::hasColumn('auditoria_especifica', 'ae_lugar'))
                $table->string('ae_lugar')->nullable();
            if (!Schema::hasColumn('auditoria_especifica', 'ae_direccion'))
                $table->string('ae_direccion')->nullable();
            if (!Schema::hasColumn('auditoria_especifica', 'ae_reunion_apertura'))
                $table->dateTime('ae_reunion_apertura')->nullable();
            if (!Schema::hasColumn('auditoria_especifica', 'ae_reunion_cierre'))
                $table->dateTime('ae_reunion_cierre')->nullable();
        });

        // 3. Refactor 'auditoria_equipo'
        if (Schema::hasTable('auditoria_equipo')) {
            Schema::table('auditoria_equipo', function (Blueprint $table) {
                // Renames - only if old exists and new does not
                if (Schema::hasColumn('auditoria_equipo', 'auditoria_id') && !Schema::hasColumn('auditoria_equipo', 'ae_id')) {
                    $table->renameColumn('auditoria_id', 'ae_id');
                }
                if (Schema::hasColumn('auditoria_equipo', 'personal_id') && !Schema::hasColumn('auditoria_equipo', 'auditor_id')) {
                    $table->renameColumn('personal_id', 'auditor_id');
                }
                if (Schema::hasColumn('auditoria_equipo', 'rol') && !Schema::hasColumn('auditoria_equipo', 'aeq_rol')) {
                    $table->renameColumn('rol', 'aeq_rol');
                }
                if (Schema::hasColumn('auditoria_equipo', 'equipo')) {
                    $table->dropColumn('equipo');
                }
            });
            // Add new columns separate from renames to be safe
            Schema::table('auditoria_equipo', function (Blueprint $table) {
                if (!Schema::hasColumn('auditoria_equipo', 'aeq_horas_planificadas')) {
                    $table->decimal('aeq_horas_planificadas', 8, 2)->default(0);
                }
                if (!Schema::hasColumn('auditoria_equipo', 'aeq_horas_ejecutadas')) {
                    $table->decimal('aeq_horas_ejecutadas', 8, 2)->default(0);
                }
            });
        }

        // 4. Create 'auditoria_agenda'
        if (!Schema::hasTable('auditoria_agenda')) {
            Schema::create('auditoria_agenda', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ae_id');
                $table->date('aea_fecha');
                $table->time('aea_hora_inicio');
                $table->time('aea_hora_fin');
                $table->text('aea_actividad'); // Process / Activity
                $table->string('aea_auditado')->nullable(); // Role or Name
                $table->string('aea_auditor')->nullable(); // Role or Name
                $table->string('aea_requisito')->nullable(); // ISO Clause
                $table->string('aea_lugar')->nullable();
                $table->timestamps();

                $table->foreign('ae_id')->references('id')->on('auditoria_especifica')->onDelete('cascade');
            });
        }

        // 5. Create 'auditoria_evaluacion' (360 Evaluation)
        if (!Schema::hasTable('auditoria_evaluacion')) {
            Schema::create('auditoria_evaluacion', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ae_id');
                $table->unsignedBigInteger('evaluador_id'); // User ID
                $table->unsignedBigInteger('evaluado_id'); // User ID (Auditor)

                $table->string('aev_rol_evaluado'); // Auditor Lider, Auditor Interno, etc.
                $table->json('aev_criterios')->nullable(); // JSON storage for dynamic criteria ratings
                $table->decimal('aev_promedio', 5, 2)->nullable();
                $table->text('aev_comentario')->nullable();

                $table->timestamps();

                $table->foreign('ae_id')->references('id')->on('auditoria_especifica')->onDelete('cascade');
                $table->foreign('evaluador_id')->references('id')->on('users');
                $table->foreign('evaluado_id')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Simplified down: just dropping new tables, reversing renames is complex and risky without data mapping
        Schema::dropIfExists('auditoria_evaluacion');
        Schema::dropIfExists('auditoria_agenda');

        // Reverting columns would go here... (Omitted for brevity in this task context)
    }
};

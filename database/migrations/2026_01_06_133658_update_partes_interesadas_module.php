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
        // 1. Actualizar `partes_interesadas`
        if (Schema::hasTable('partes_interesadas')) {
            Schema::table('partes_interesadas', function (Blueprint $table) {
                if (!Schema::hasColumn('partes_interesadas', 'pi_nivel_interes')) {
                    $table->enum('pi_nivel_interes', ['bajo', 'medio', 'alto'])->nullable()->after('pi_nivel_influencia');
                }
            });
        }

        // 2. Recrear `expectativas`
        // Eliminamos las dependientas primero
        Schema::dropIfExists('expectativa_riesgo');
        Schema::dropIfExists('expectativa_compromisos');
        Schema::dropIfExists('expectativas');
        // También la antigua si existe con otro nombre
        Schema::dropIfExists('necesidades_expectativas');

        Schema::create('expectativas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parte_interesada_id')->constrained('partes_interesadas')->onDelete('cascade');
            $table->foreignId('proceso_id')->nullable()->constrained('procesos')->onDelete('set null'); // Proceso Responsable
            
            $table->text('exp_descripcion');
            $table->enum('exp_tipo', ['necesidad', 'expectativa'])->default('expectativa');
            $table->json('exp_normas')->nullable(); // Para multi-norma (ISO 9001, 37001, etc.)
            
            // Valoración
            $table->integer('exp_criticidad')->default(1); // 1-5
            $table->integer('exp_viabilidad')->default(1); // 1-5
            $table->float('exp_prioridad')->default(0); // Calculado
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Crear `expectativa_compromisos`
        Schema::create('expectativa_compromisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expectativa_id')->constrained('expectativas')->onDelete('cascade');
            $table->text('ec_descripcion');
            $table->foreignId('ec_responsable_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('ec_fecha_limite')->nullable();
            $table->enum('ec_estado', ['pendiente', 'en_proceso', 'completado'])->default('pendiente');
            $table->text('ec_avance')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Crear `expectativa_riesgo` (Pivote)
        Schema::create('expectativa_riesgo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expectativa_id')->constrained('expectativas')->onDelete('cascade');
            $table->foreignId('riesgo_id')->constrained('riesgos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expectativa_riesgo');
        Schema::dropIfExists('expectativa_compromisos');
        Schema::dropIfExists('expectativas');

        if (Schema::hasTable('partes_interesadas')) {
            Schema::table('partes_interesadas', function (Blueprint $table) {
                if (Schema::hasColumn('partes_interesadas', 'pi_nivel_interes')) {
                    $table->dropColumn('pi_nivel_interes');
                }
            });
        }
    }
};

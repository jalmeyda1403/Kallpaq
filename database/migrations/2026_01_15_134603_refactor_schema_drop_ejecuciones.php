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
        // 1. Modificar auditoria_agenda
        Schema::table('auditoria_agenda', function (Blueprint $table) {
            $table->unsignedBigInteger('proceso_id')->nullable()->after('ae_id');
            $table->string('estado')->default('Programada')->after('aea_tipo'); // Programada, En Curso, Concluida

            $table->foreign('proceso_id')->references('id')->on('procesos')->onDelete('set null');
        });

        // 2. Modificar auditoria_checklists
        Schema::table('auditoria_checklists', function (Blueprint $table) {
            $table->unsignedBigInteger('agenda_id')->nullable()->after('id');
            // Drop ejecucion_id logic would require careful handling if data exists. 
            // Here assuming we can drop it. Note: 'ejecucion_id' FK constraint should be dropped first.
            // But we will drop the table ejecuciones next, so the constraint might auto-drop or throw error.
            // Best practice: drop foreign key, then column.

            // Check constraint name if possible or assume laravel convention
            // $table->dropForeign(['ejecucion_id']); 
            // $table->dropColumn('ejecucion_id');
        });

        // Populate agenda_id from ejecucion relation before dropping columns (Optional / Best Effort)
        // DB::statement("UPDATE auditoria_checklists c JOIN auditoria_ejecuciones e ON c.ejecucion_id = e.id SET c.agenda_id = e.agenda_id");

        Schema::table('auditoria_checklists', function (Blueprint $table) {
            // Drop trigger/fk might be required.
            // We'll drop the column after linking.
            $table->dropForeign(['ejecucion_id']);
            $table->dropColumn('ejecucion_id');

            $table->foreign('agenda_id')->references('id')->on('auditoria_agenda')->onDelete('cascade');
        });

        // 3. Eliminar auditoria_ejecuciones
        Schema::dropIfExists('auditoria_ejecuciones');
    }

    public function down(): void
    {
        // Reverse operations (simplified)
        Schema::create('auditoria_ejecuciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ae_id');
            $table->timestamps();
        });

        Schema::table('auditoria_checklists', function (Blueprint $table) {
            $table->dropForeign(['agenda_id']);
            $table->dropColumn('agenda_id');
            $table->unsignedBigInteger('ejecucion_id')->nullable();
        });

        Schema::table('auditoria_agenda', function (Blueprint $table) {
            $table->dropForeign(['proceso_id']);
            $table->dropColumn(['proceso_id', 'estado']);
        });
    }
};

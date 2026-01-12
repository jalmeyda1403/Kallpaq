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
        Schema::create('programa_auditoria', function (Blueprint $table) {
            $table->id();
            $table->string('pa_version');
            $table->string('pa_anio'); // Was periodo
            $table->text('pa_recursos')->nullable(); // Was presupuesto (decimal), changed to text/nullable to match Model usage/flexibility or keep decimal if strict. Model has 'pa_recursos'. Let's stick to text for flexibility as "Recursos/Presupuesto"
            $table->date('pa_fecha_aprobacion');
            $table->string('pa_estado')->default('Borrador'); // Added state
            $table->text('pa_objetivo_general')->nullable();
            $table->text('pa_alcance')->nullable();
            $table->text('pa_metodos')->nullable();
            $table->text('pa_criterios')->nullable();
            $table->decimal('avance')->nullable()->default(0); // This one in Model is 'avance' without prefix? Model says 'avance'. Keep it or check Model again. Model Step 785: 'avance'. Okay.
            $table->text('pa_descripcion')->nullable(); // Was observaciones
            $table->string('archivo_pdf')->nullable();
            $table->date('fecha_publicacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programa_auditoria');
    }
};

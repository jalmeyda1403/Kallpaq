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
        Schema::create('salidas_no_conformes', function (Blueprint $table) {
            $table->id();

            $table->text('snc_descripcion');
            $table->decimal('snc_cantidad_afectada', 10, 2);
            $table->string('snc_responsable');
            $table->enum('snc_origen', ['cliente', 'auditoría interna', 'auditoría externa', 'otro']);
            $table->enum('snc_clasificacion', ['crítica', 'mayor', 'menor']);

            // Tratamiento
            $table->enum('snc_tratamiento', ['corrección', 'concesion o aceptación', 'rechazo', 'sustitucion'])->nullable();
            $table->text('snc_descripcion_tratamiento')->nullable();
            $table->decimal('snc_costo_estimado', 10, 2)->nullable();

            // Estado y seguimiento
            $table->enum('snc_estado', ['identificada', 'en tratamiento', 'tratada', 'cerrada', 'observada'])
                ->default('identificada');
            $table->text('snc_observacion')->nullable();

            $table->boolean('snc_requiere_accion_correctiva')->nullable()->default(0); // tinyint(1) default 0

            $table->date('snc_fecha_deteccion');
            $table->date('snc_fecha_fecha_fin_prog')->nullable(); // Matches DB description
            $table->date('snc_fecha_fin_real')->nullable();

            $table->timestamp('snc_fecha_observacion')->nullable();
            $table->date('snc_fecha_cierre')->nullable();
            $table->text('snc_observaciones')->nullable();

            $table->text('snc_archivos')->nullable();
            $table->text('snc_evidencias')->nullable();

            $table->foreignId('proceso_id')->nullable()->constrained('procesos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas_no_conformes');
    }
};

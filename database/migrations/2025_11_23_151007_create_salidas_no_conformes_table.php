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
        Schema::create('salidas_no_conformes', function (Blueprint $table) {
            $table->id();
            
            // Identificación
            $table->string('snc_codigo')->unique()->comment('Código único generado (ej: SNC-2025-001)');
            $table->text('snc_descripcion')->comment('Descripción de la no conformidad');
            $table->string('snc_producto_servicio')->comment('Producto o servicio afectado');
            $table->decimal('snc_cantidad_afectada', 10, 2)->nullable()->comment('Cantidad de unidades afectadas');
            
            // Detección
            $table->date('snc_fecha_deteccion')->comment('Fecha de detección');
            $table->foreignId('snc_detectado_por')->constrained('users')->comment('Usuario que detectó');
            $table->foreignId('snc_responsable_id')->nullable()->constrained('users')->comment('Usuario responsable del tratamiento');
            
            // Clasificación y origen
            $table->enum('snc_tipo', ['producto', 'servicio', 'proceso'])->comment('Tipo de salida no conforme');
            $table->enum('snc_origen', ['producción', 'inspección', 'cliente', 'auditoría interna', 'auditoría externa', 'otro'])->comment('Origen de la detección');
            $table->enum('snc_clasificacion', ['crítica', 'mayor', 'menor'])->comment('Clasificación por severidad');
            
            // Tratamiento
            $table->enum('snc_tratamiento', ['corrección', 'reproceso', 'reclasificación', 'rechazo', 'concesión', 'pendiente'])->default('pendiente')->comment('Tipo de tratamiento aplicado');
            $table->text('snc_descripcion_tratamiento')->nullable()->comment('Descripción detallada del tratamiento');
            $table->date('snc_fecha_tratamiento')->nullable()->comment('Fecha de aplicación del tratamiento');
            $table->decimal('snc_costo_estimado', 10, 2)->nullable()->comment('Costo estimado del tratamiento');
            
            // Estado y seguimiento
            $table->enum('snc_estado', ['registrada', 'en análisis', 'en tratamiento', 'tratada', 'cerrada'])->default('registrada')->comment('Estado actual');
            $table->boolean('snc_requiere_accion_correctiva')->default(false)->comment('Indica si requiere acción correctiva');
            $table->date('snc_fecha_cierre')->nullable()->comment('Fecha de cierre');
            $table->text('snc_observaciones')->nullable()->comment('Observaciones generales');
            
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

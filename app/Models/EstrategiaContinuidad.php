<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstrategiaContinuidad extends Model
{
    use HasFactory;

    protected $table = 'estrategias_continuidad';

    protected $fillable = [
        'plan_id',
        'activo_id',
        'nombre',
        'descripcion',
        'tipo_estrategia',
        'recursos_requeridos',
        'costo_estimado',
        'tiempo_implementacion',
        'prioridad',
    ];

    protected $casts = [
        'costo_estimado' => 'decimal:2',
    ];

    /**
     * Tipos de estrategia
     */
    public static function getTiposEstrategia()
    {
        return [
            'respaldo' => 'Respaldo/Backup',
            'redundancia' => 'Redundancia',
            'alternativa' => 'Sitio/Recurso alternativo',
            'manual' => 'Procedimiento manual',
            'outsourcing' => 'Tercerización',
            'otro' => 'Otro',
        ];
    }

    /**
     * Plan al que pertenece
     */
    public function plan()
    {
        return $this->belongsTo(PlanContinuidad::class, 'plan_id');
    }

    /**
     * Activo protegido
     */
    public function activo()
    {
        return $this->belongsTo(ActivoCritico::class, 'activo_id');
    }

    /**
     * Color de prioridad
     */
    public function getPrioridadColorAttribute()
    {
        return match ($this->prioridad) {
            'baja' => 'success',
            'media' => 'warning',
            'alta' => 'orange',
            'critica' => 'danger',
            default => 'secondary',
        };
    }
}

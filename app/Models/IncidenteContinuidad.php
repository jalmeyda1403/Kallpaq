<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidenteContinuidad extends Model
{
    use HasFactory;

    protected $table = 'incidentes_continuidad';

    protected $fillable = [
        'codigo',
        'titulo',
        'descripcion',
        'escenario_id',
        'plan_activado_id',
        'fecha_inicio',
        'fecha_fin',
        'severidad',
        'impacto_real',
        'acciones_tomadas',
        'lecciones_aprendidas',
        'tiempo_respuesta_minutos',
        'tiempo_recuperacion_minutos',
        'plan_fue_efectivo',
        'responsable_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'plan_fue_efectivo' => 'boolean',
    ];

    /**
     * Genera código automáticamente
     */
    protected static function booted()
    {
        static::creating(function ($incidente) {
            if (empty($incidente->codigo)) {
                $anio = date('Y');
                $count = static::whereYear('created_at', $anio)->count() + 1;
                $incidente->codigo = 'INC-' . $anio . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Escenario que se materializó
     */
    public function escenario()
    {
        return $this->belongsTo(EscenarioContinuidad::class, 'escenario_id');
    }

    /**
     * Plan que se activó
     */
    public function planActivado()
    {
        return $this->belongsTo(PlanContinuidad::class, 'plan_activado_id');
    }

    /**
     * Responsable de la gestión
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Duración del incidente
     */
    public function getDuracionAttribute()
    {
        if (!$this->fecha_fin) return null;
        return $this->fecha_inicio->diffInHours($this->fecha_fin);
    }

    /**
     * Color de severidad
     */
    public function getSeveridadColorAttribute()
    {
        return match ($this->severidad) {
            'baja' => 'success',
            'media' => 'warning',
            'alta' => 'orange',
            'critica' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Estado del incidente
     */
    public function getEstadoAttribute()
    {
        return $this->fecha_fin ? 'cerrado' : 'activo';
    }
}

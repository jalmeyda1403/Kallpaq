<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PruebaContinuidad extends Model
{
    use HasFactory;

    protected $table = 'pruebas_continuidad';

    protected $fillable = [
        'codigo',
        'nombre',
        'plan_id',
        'tipo_prueba',
        'fecha_programada',
        'fecha_ejecucion',
        'objetivo',
        'alcance',
        'participantes',
        'escenario_prueba',
        'estado',
        'resultados',
        'hallazgos',
        'lecciones_aprendidas',
        'acciones_mejora',
        'calificacion',
        'informe_path',
        'responsable_id',
        'created_by',
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'fecha_ejecucion' => 'date',
    ];

    /**
     * Tipos de prueba
     */
    public static function getTiposPrueba()
    {
        return [
            'documental' => 'Revisión documental',
            'walkthrough' => 'Recorrido (Walkthrough)',
            'simulacion' => 'Simulación de escritorio',
            'funcional' => 'Prueba funcional',
            'ejercicio_total' => 'Ejercicio completo',
        ];
    }

    /**
     * Genera código automáticamente
     */
    protected static function booted()
    {
        static::creating(function ($prueba) {
            if (empty($prueba->codigo)) {
                $anio = date('Y');
                $count = static::whereYear('created_at', $anio)->count() + 1;
                $prueba->codigo = 'PRB-' . $anio . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Plan probado
     */
    public function plan()
    {
        return $this->belongsTo(PlanContinuidad::class, 'plan_id');
    }

    /**
     * Responsable de la prueba
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Creador
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Color del estado
     */
    public function getEstadoColorAttribute()
    {
        return match ($this->estado) {
            'programada' => 'info',
            'en_ejecucion' => 'warning',
            'completada' => 'success',
            'cancelada' => 'secondary',
            'postergada' => 'orange',
            default => 'primary',
        };
    }

    /**
     * Color de calificación
     */
    public function getCalificacionColorAttribute()
    {
        if ($this->calificacion >= 4) return 'success';
        if ($this->calificacion >= 3) return 'warning';
        if ($this->calificacion >= 2) return 'orange';
        return 'danger';
    }

    /**
     * Verifica si está vencida
     */
    public function getEstaVencidaAttribute()
    {
        return $this->estado === 'programada' && 
               $this->fecha_programada && 
               $this->fecha_programada->isPast();
    }
}

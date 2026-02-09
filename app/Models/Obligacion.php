<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obligacion extends Model
{
    use HasFactory;

    // Especificamos el nombre de la tabla en caso de que no siga la convención predeterminada
    protected $table = 'obligaciones';

    // Estados de la obligación (ISO 37301)
    const ESTADO_IDENTIFICADA = 'identificada';
    const ESTADO_EVALUADA = 'evaluada';
    const ESTADO_EN_TRATAMIENTO = 'en_tratamiento';
    const ESTADO_CONTROLADA = 'controlada';
    const ESTADO_NO_CONTROLADA = 'no_controlada';
    const ESTADO_SUSPENDIDA = 'suspendida';
    const ESTADO_INACTIVA = 'inactiva';

    const CUMPLIMIENTO_PENDIENTE = 'pendiente';
    const CUMPLIMIENTO_CUMPLIDA = 'cumplida';
    const CUMPLIMIENTO_PARCIAL = 'parcialmente_cumplida';
    const CUMPLIMIENTO_NO_CUMPLIDA = 'no_cumplida';

    protected $fillable = [
        'area_compliance_id',
        'subarea_compliance_id',
        'obligacion_documento',
        'obligacion_principal',
        'obligacion_consecuencia',
        'obligacion_documento_deroga',
        'obligacion_estado',
        'radar_id',
        'documento_id',
        'obligacion_tipo',
        'obligacion_frecuencia',
        'obligacion_fecha_identificacion',
        'cumplimiento',
        'fecha_cumplimiento',
        'comentario_cumplimiento'
    ];

    protected $casts = [
        'fecha_cumplimiento' => 'date',
        'obligacion_fecha_identificacion' => 'date'
    ];

    /**
     * Actualiza el cumplimiento y ajusta el estado automáticamente.
     */
    public function actualizarCumplimiento($cumplimiento, $comentario = null, $fecha = null)
    {
        $this->cumplimiento = $cumplimiento;
        $this->comentario_cumplimiento = $comentario;
        $this->fecha_cumplimiento = $fecha ?? now();

        // Lógica automática de estados
        if ($cumplimiento === self::CUMPLIMIENTO_CUMPLIDA) {
            $this->obligacion_estado = self::ESTADO_CONTROLADA;
        } elseif ($cumplimiento === self::CUMPLIMIENTO_NO_CUMPLIDA) {
            $this->obligacion_estado = self::ESTADO_NO_CONTROLADA;
        } elseif ($cumplimiento === self::CUMPLIMIENTO_PARCIAL) {
            // Opcional: Mantener en tratamiento o definir otro estado
            if ($this->obligacion_estado !== self::ESTADO_EN_TRATAMIENTO) {
                $this->obligacion_estado = self::ESTADO_EN_TRATAMIENTO;
            }
        }

        $this->save();
    }

    /**
     * Relación con la tabla 'areas_compliance'.
     * Una obligación pertenece a un área de compliance.
     */
    public function area_compliance()
    {
        return $this->belongsTo(AreaCompliance::class, 'area_compliance_id');
    }

    public function subarea_compliance()
    {
        return $this->belongsTo(SubAreaCompliance::class, 'subarea_compliance_id');
    }

    /**
     * Relación con la tabla 'procesos' (Muchos a Muchos).
     */
    public function procesos()
    {
        return $this->belongsToMany(Proceso::class, 'obligacion_proceso', 'obligacion_id', 'proceso_id');
    }

    /**
     * Relación con los riesgos a través de la tabla intermedia 'obligacion_riesgo'.
     * Una obligación tiene muchos riesgos.
     */
    public function riesgos()
    {
        return $this->belongsToMany(Riesgo::class, 'obligacion_riesgo');
    }

    /**
     * Relación con las evaluaciones de criticidad.
     */
    public function evaluaciones()
    {
        return $this->hasMany(ObligacionEvaluacion::class)->orderBy('oe_fecha_evaluacion', 'desc');
    }

    /**
     * Obtener la evaluación más reciente.
     */
    public function evaluacionActual()
    {
        return $this->hasOne(ObligacionEvaluacion::class)->latestOfMany('oe_fecha_evaluacion');
    }


    /**
     * Relación con los controles directos.
     */
    public function controles()
    {
        return $this->belongsToMany(Control::class, 'obligacion_control');
    }


    public function asociarRiesgo(Riesgo $riesgo)
    {
        $this->riesgos()->attach($riesgo->id);
    }

    /**
     * Cambiar el estado de la obligación, por ejemplo, cuando se cumpla o se dé de baja.
     * 
     * @param string $estado
     * @return void
     */
    public function cambiarEstado($estado)
    {
        $this->update(['obligacion_estado' => $estado]);
    }
    public function cantidadRiesgos()
    {
        return $this->riesgos()->count();  // Devuelve la cantidad de riesgos asociados
    }

    public function getEstadoClassAttribute()
    {
        return match ($this->obligacion_estado) {
            'pendiente' => 'bg-warning text-dark',
            'controlada' => 'bg-success',
            'inactiva' => 'bg-secondary',
            'suspendida' => 'bg-danger',
            default => 'bg-light text-dark',
        };
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function radar()
    {
        return $this->belongsTo(RadarNormativo::class, 'radar_id');
    }

    public function acciones()
    {
        return $this->hasMany(Accion::class, 'accion_obligacion_id');
    }
}
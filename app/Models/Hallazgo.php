<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Support\Carbon|null $hallazgo_fecha_identificacion
 * @property \Illuminate\Support\Carbon|null $hallazgo_fecha_aprobacion
 * @property \Illuminate\Support\Carbon|null $hallazgo_fecha_desestimacion
 * @property \Illuminate\Support\Carbon|null $hallazgo_fecha_conclusion
 * @property \Illuminate\Support\Carbon|null $hallazgo_fecha_evaluacion
 * @property \Illuminate\Support\Carbon|null $hallazgo_fecha_cierre
 */
class Hallazgo extends Model
{
    use HasFactory;

    protected $table = 'hallazgos';

    protected $fillable = [
        'hallazgo_cod',
        'informe_id',
        'especialista_id',
        'auditor_id',
        'emisor',
        'facilitador_id',
        'hallazgo_resumen',
        'hallazgo_sig',
        'hallazgo_descripcion',
        'hallazgo_criterio',
        'hallazgo_evidencia',
        'hallazgo_clasificacion',
        'hallazgo_origen',
        'hallazgo_origen_ot',
        'hallazgo_avance',
        'hallazgo_tipo_cierre',
        'hallazgo_estado',
        'hallazgo_fecha_identificacion',
        'hallazgo_fecha_aprobacion',
        'hallazgo_fecha_desestimacion',
        'hallazgo_fecha_conclusion',
        'hallazgo_fecha_evaluacion',
        'hallazgo_fecha_cierre',
        'hallazgo_ciclo',
        'ruta_plan_accion',
    ];

    protected $casts = [
        'hallazgo_sig' => 'array',
        'hallazgo_fecha_identificacion' => 'date',
        'hallazgo_fecha_aprobacion' => 'date',
        'hallazgo_fecha_desestimacion' => 'date',
        'hallazgo_fecha_conclusion' => 'date',
        'hallazgo_fecha_evaluacion' => 'date',
        'hallazgo_fecha_cierre' => 'date',
    ];

    public function procesos()
    {

        return $this->belongsToMany(Proceso::class, 'hallazgo_proceso', 'hallazgo_id', 'proceso_id');

    }

    public function hallazgoProcesos()
    {
        return $this->hasMany(HallazgoProceso::class);
    }

    public function evaluaciones()
    {
        return $this->hasMany(HallazgoEvaluacion::class);
    }

    // Última evaluación registrada
    public function ultimaEvaluacion()
    {
        return $this->hasOne(HallazgoEvaluacion::class)->latestOfMany();
    }

    /* 🔧 Métodos auxiliares */

    // Obtener resultado actual de eficacia
    public function getResultadoActualAttribute()
    {
        return optional($this->ultimaEvaluacion)->resultado ?? $this->hallazgo_tipo_cierre;
    }

    // Saber si el hallazgo requiere reevaluación
    public function requiereReevaluacion(): bool
    {
        return $this->hallazgo_estado === 'reevaluacion';
    }

    public function especialista()
    {
        return $this->belongsTo(User::class, 'especialista_id');
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function movimientos()
    {
        return $this->hasMany(HallazgoMovimientos::class);
    }

    public function causa()
    {
        return $this->hasOne(Causa::class, 'hallazgo_id', 'id');
    }

    public function acciones()
    {
        return $this->hasMany(Accion::class, 'accion_hallazgo_id');
    }

    public function scopeFilterBySig($query, $sig)
    {
        if ($sig) {
            return $query->where('sig', $sig);
        }

        return $query;
    }

    public function scopeFilterByInformeId($query, $informe_id)
    {
        if ($informe_id) {
            return $query->where('informe_id', $informe_id);
        }

        return $query;
    }

    public function scopeFilterByYear($query, $year)
    {
        if ($year) {
            return $query->whereYear('fecha_solicitud', $year);
        }

        return $query;
    }

    public function scopeFilterByClasificacion($query, $clasificacion)
    {
        if (is_array($clasificacion)) {
            return $query->whereIn('hallazgo_clasificacion', $clasificacion);
        }

        return $query->where('hallazgo_clasificacion', $clasificacion);
    }

    /**
     * Verifica y actualiza el estado del hallazgo según las reglas del negocio.
     */
    public function verificarYActualizarEstado()
    {
        $estadoActual = $this->hallazgo_estado;

        // 1. Cerrado: última evaluación 'con eficacia'
        $ultimaEval = $this->ultimaEvaluacion;
        if ($ultimaEval && $ultimaEval->he_resultado === 'con eficacia') {
            $this->hallazgo_estado = 'cerrado';
        }
        // 2. Concluido: todas las acciones 'implementada'
        else {
            $acciones = $this->acciones;
            if ($acciones->count() > 0) {
                $todasImplementadas = $acciones->every(function ($accion) {
                    return $accion->accion_estado === 'implementada';
                });

                if ($todasImplementadas) {
                    $this->hallazgo_estado = 'concluido';
                } else if ($estadoActual === 'aprobado') {
                    // 3. En Proceso: aprobado y fecha inicio de la acción con menor fecha alcanzada
                    $fechaMinima = $acciones->min('accion_fecha_inicio');
                    if ($fechaMinima && \Carbon\Carbon::parse($fechaMinima)->isPast()) {
                        $this->hallazgo_estado = 'en proceso';
                    }
                }
            }
        }

        if ($this->hallazgo_estado !== $estadoActual) {
            $this->save();
        }
    }
}

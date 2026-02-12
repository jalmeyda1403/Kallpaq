<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Support\Carbon|null $accion_fecha_fin_reprogramada
 * @property \Illuminate\Support\Carbon|null $accion_fecha_fin_real
 */
class Accion extends Model
{

    use HasFactory;

    protected $table = 'acciones';
    protected $fillable = [
        'accion_hallazgo_id',
        'accion_riesgo_id',
        'accion_obligacion_id', // ISO 37301 Link
        'accion_hallazgo_proceso_id',
        'accion_cod',
        'accion_tipo',
        'accion_descripcion',
        'accion_fecha_inicio',
        'accion_fecha_fin_planificada',
        'accion_fecha_fin_reprogramada',
        'accion_fecha_cancelada',
        'accion_fecha_fin_real',
        'accion_justificacion',
        'accion_responsable',
        'accion_responsable_correo',
        'accion_estado',
        'accion_ciclo', // Added accion_ciclo
        'accion_es_control_permanente',
    ];

    protected $casts = [
        'accion_fecha_inicio' => 'date',
        'accion_fecha_fin_planificada' => 'date',
        'accion_fecha_fin_reprogramada' => 'date',
        'accion_fecha_cancelada' => 'date',
        'accion_fecha_fin_real' => 'date',
    ];

    public function hallazgoProceso()
    {
        return $this->belongsTo(HallazgoProceso::class, 'accion_hallazgo_proceso_id');
    }
    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class, 'accion_hallazgo_id');
    }

    public function riesgo()
    {
        return $this->belongsTo(Riesgo::class, 'accion_riesgo_id');
    }

    public function obligacion()
    {
        return $this->belongsTo(Obligacion::class, 'accion_obligacion_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'accion_responsable');
    }

    public function reprogramaciones()
    {
        return $this->hasMany(AccionReprogramacion::class, 'accion_id');
    }

    public function avances()
    {
        return $this->hasMany(AccionAvance::class, 'accion_id');
    }

    public function movimientos()
    {
        return $this->hasMany(AccionMovimientos::class, 'accion_id');
    }

}

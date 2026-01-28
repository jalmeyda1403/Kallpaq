<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accion extends Model
{
    use HasFactory;

    protected $table = 'acciones';
    protected $fillable = [
        'hallazgo_id',
        'obligacion_id', // ISO 37301 Link
        'hallazgo_proceso_id',
        'accion_cod',
        'accion_tipo',
        'accion_descripcion',
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
        'es_control_permanente',
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
        return $this->belongsTo(HallazgoProceso::class);
    }
    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class);
    }

    public function obligacion()
    {
        return $this->belongsTo(Obligacion::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'accion_responsable');
    }

    public function reprogramaciones()
    {
        return $this->hasMany(AccionReprogramacion::class);
    }

    public function avances()
    {
        return $this->hasMany(AccionAvance::class);
    }

    public function movimientos()
    {
        return $this->hasMany(AccionMovimientos::class, 'accion_id');
    }

}

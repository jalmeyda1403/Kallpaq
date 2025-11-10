<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hallazgo extends Model
{
    protected $table = 'hallazgos';
    protected $fillable = [
        'hallazgo_cod',
        'informe_id',
        'especialista_id',
        'auditor_id',
        'user_asigna_id',
        'hallazgo_resumen',
        'hallazgo_descripcion',
        'hallazgo_criterio',
        'hallazgo_clasificacion',
        'hallazgo_origen',
        'hallazgo_hallazgo_tipo_cierre',
        'hallazgo_evidencia',
        'hallazgo_avance',
        'hallazgo_estado',
        'hallazgo_fecha_identificacion',
        'hallazgo_fecha_aprobacion',
        'hallazgo_fecha_asignacion',
        'hallazgo_fecha_conclusion',
        'hallazgo_fecha_cierre',
        'hallazgo_sig',
    ];

    protected $casts = [
        'hallazgo_sig' => 'array',
        'hallazgo_fecha_identificacion' => 'date',
        'hallazgo_fecha_aprobacion' => 'date',
        'hallazgo_fecha_asignacion' => 'date',
        'hallazgo_fecha_conclusion' => 'date',
        'hallazgo_fecha_cierre' => 'date', // Si no lo tenías, es buena práctica para fechas
    ];

    public function procesos()
    {
       
         return $this->belongsToMany(Proceso::class, 'hallazgo_proceso', 'hallazgo_id', 'proceso_id');
       
    }

    public function especialista()
    {
        return $this->belongsTo(User::class, 'especialista_id');
    }
    public function movimientos()
    {
        return $this->hasMany(HallazgoMovimientos::class);
    }

    public function usuario_asigna()
    {
        return $this->belongsTo(User::class, 'user_asigna_id');
    }

    public function causa()
    {
        return $this->hasOne(Causa::class, 'hallazgo_id', 'id');
    }

    public function acciones()
    {
        return $this->hasMany(Accion::class);
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
            return $query->whereIn('clasificacion', $clasificacion);
        }
        return $query->where('clasificacion', $clasificacion);
    }
}

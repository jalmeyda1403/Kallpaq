<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hallazgo extends Model
{
    protected $fillable = [
        'informe_id',
        'smp_cod',
        'proceso_id',
        'resumen',
        'descripcion',
        'evidencia',
        'criterio',
        'clasificacion',
        'origen',
        'estado',
        'sig',
        'auditor',
        'auditor_tipo',
        'fecha_solicitud',
        'fecha_aprobacion',
        'fecha_cierre_acciones',
        'fecha_planificacion_evaluacion',
        'evaluacion',
        'fecha_evaluacion',
        'fecha_cierre_hallazgo',
        'estado_final',
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id', 'id');
    }

    public function especialistas()
    {
        return $this->belongsToMany(Especialista::class)
            ->withPivot('motivo_asignacion', 'fecha_asignacion');

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

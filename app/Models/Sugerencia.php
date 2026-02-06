<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sugerencia extends Model
{
    use HasFactory;

    protected $table = 'sugerencias';

    protected $fillable = [
        'sugerencia_clasificacion',
        'sugerencia_detalle',
        'sugerencia_fecha_ingreso',
        'sugerencia_procedencia',
        'sugerencia_analisis',
        'sugerencia_viabilidad',
        'sugerencia_tratamiento',
        'sugerencia_estado',
        'sugerencia_fecha_fin_prog',
        'sugerencia_fecha_fin_real',
        'proceso_id',
        'sugerencia_evidencias',
        'sugerencia_observacion',
        'sugerencia_fecha_observacion',
        'sugerencia_fecha_cierre',
    ];

    protected $casts = [
        'sugerencia_fecha_ingreso' => 'date',
        'sugerencia_fecha_fin_prog' => 'date',
        'sugerencia_fecha_fin_real' => 'date',
        'sugerencia_fecha_observacion' => 'date',
        'sugerencia_fecha_cierre' => 'date',
        'sugerencia_evidencias' => 'array',
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function movimientos()
    {
        return $this->hasMany(SugerenciaMovimiento::class)->orderBy('fecha_movimiento', 'desc');
    }

    // Scopes
    public function scopeFilterByEstado($query, $estado)
    {
        if ($estado) {
            return $query->where('sugerencia_estado', $estado);
        }
        return $query;
    }

    public function scopeFilterByProceso($query, $procesoId)
    {
        if ($procesoId) {
            return $query->where('proceso_id', $procesoId);
        }
        return $query;
    }

    public function scopeFilterByFecha($query, $from, $to)
    {
        if ($from) {
            $query->where('sugerencia_fecha_ingreso', '>=', $from);
        }
        if ($to) {
            $query->where('sugerencia_fecha_ingreso', '<=', $to);
        }
        return $query;
    }

    public function scopeFilterByClasificacion($query, $clasificacion)
    {
        if ($clasificacion) {
            return $query->where('sugerencia_clasificacion', $clasificacion);
        }
        return $query;
    }

    public function scopeFilterByProcesoNombre($query, $procesoNombre)
    {
        if ($procesoNombre) {
            return $query->whereHas('proceso', function ($subquery) use ($procesoNombre) {
                $subquery->where('proceso_nombre', 'LIKE', '%' . $procesoNombre . '%')
                    ->orWhere('nombre', 'LIKE', '%' . $procesoNombre . '%')
                    ->orWhere('descripcion', 'LIKE', '%' . $procesoNombre . '%')
                    ->orWhere('proceso_nombre_corto', 'LIKE', '%' . $procesoNombre . '%');
            });
        }
        return $query;
    }
}

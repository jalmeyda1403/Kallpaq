<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaNoConforme extends Model
{
    use HasFactory;

    protected $table = 'salidas_no_conformes';

    protected $fillable = [
        'snc_descripcion',
        'snc_cantidad_afectada',
        'snc_fecha_deteccion',
        'snc_responsable',
        'snc_origen',
        'snc_clasificacion',
        'snc_tratamiento',
        'snc_descripcion_tratamiento',
        'snc_fecha_tratamiento',
        'snc_costo_estimado',
        'snc_estado',
        'snc_requiere_accion_correctiva',
        'snc_fecha_cierre',
        'snc_observaciones',
        'proceso_id',
        'snc_evidencia',
    ];

    protected $casts = [
        'snc_fecha_deteccion' => 'date',
        'snc_fecha_tratamiento' => 'date',
        'snc_fecha_cierre' => 'date',
        'snc_cantidad_afectada' => 'decimal:2',
        'snc_costo_estimado' => 'decimal:2',
        'snc_requiere_accion_correctiva' => 'boolean',
    ];

    // Accessors
    public function getResponsableNombreAttribute()
    {
        return $this->snc_responsable;
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }


    // Scopes para filtrado
    public function scopeFilterByEstado($query, $estado)
    {
        if ($estado) {
            return $query->where('snc_estado', $estado);
        }
        return $query;
    }

    public function scopeFilterByTipo($query, $tipo)
    {
        if ($tipo) {
            return $query->where('snc_tipo', $tipo);
        }
        return $query;
    }

    public function scopeFilterByClasificacion($query, $clasificacion)
    {
        if ($clasificacion) {
            return $query->where('snc_clasificacion', $clasificacion);
        }
        return $query;
    }

    public function scopeFilterByDescripcion($query, $descripcion)
    {
        if ($descripcion) {
            return $query->where(function ($q) use ($descripcion) {
                $q->where('snc_codigo', 'LIKE', "%{$descripcion}%")
                    ->orWhere('snc_descripcion', 'LIKE', "%{$descripcion}%")
                    ->orWhere('snc_producto_servicio', 'LIKE', "%{$descripcion}%");
            });
        }
        return $query;
    }
}

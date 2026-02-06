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
        'snc_responsable',
        'snc_origen',
        'snc_clasificacion',
        'snc_tratamiento',
        'snc_descripcion_tratamiento',
        'snc_costo_estimado',
        'snc_estado',
        'snc_observacion',
        'snc_requiere_accion_correctiva',
        'snc_fecha_deteccion',
        'snc_fecha_fecha_fin_prog',
        'snc_fecha_fin_real',
        'snc_fecha_observacion',
        'snc_fecha_cierre',
        'snc_observaciones',
        'snc_archivos',
        'snc_evidencias',
        'proceso_id',
    ];

    protected $casts = [
        'snc_cantidad_afectada' => 'decimal:2',
        'snc_costo_estimado' => 'decimal:2',
        'snc_requiere_accion_correctiva' => 'boolean',
        'snc_fecha_deteccion' => 'date',
        'snc_fecha_fecha_fin_prog' => 'date',
        'snc_fecha_fin_real' => 'date',
        'snc_fecha_cierre' => 'date',
        'snc_fecha_observacion' => 'datetime',
        'snc_archivos' => 'array',
        'snc_evidencias' => 'array',
    ];

    public function movimientos()
    {
        return $this->hasMany(SalidaNoConformeMovimiento::class)->orderBy('created_at', 'desc');
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    // Accessors
    public function getResponsableNombreAttribute()
    {
        return $this->snc_responsable;
    }

    // Scopes (kept for backward compatibility with filtering logic)
    public function scopeFilterByEstado($query, $estado)
    {
        if ($estado) {
            return $query->where('snc_estado', $estado);
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
                // snc_codigo and snc_producto_servicio removed as they are not in schema
                $q->where('snc_descripcion', 'LIKE', "%{$descripcion}%")
                    ->orWhere('snc_responsable', 'LIKE', "%{$descripcion}%");
            });
        }
        return $query;
    }
}

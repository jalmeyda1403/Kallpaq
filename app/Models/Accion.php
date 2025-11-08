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
        'proceso_id',
        'accion_cod',
        'tipo_accion',
        'accion_descripcion',
        'accion_comentario',
        'accion_fecha_inicio',
        'accion_fecha_fin_planificada',
        'accion_fecha_fin_reprogramada',
        'accion_fecha_cancelada',
        'accion_fecha_fin_real',
        'accion_justificacion',
        'accion_ruta_evidencia',
        'accion_responsable',
        'accion_responsable_correo',
        'accion_estado',
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }
    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class);
    }

}

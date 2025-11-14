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
        'hallazgo_proceso_id',
        'accion_cod',
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
        'accion_ciclo', // Added accion_ciclo
    ];

    public function hallazgoProceso()
    {
        return $this->belongsTo(HallazgoProceso::class);
    }
    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'accion_responsable');
    }

}

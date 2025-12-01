<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiesgoAccion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'riesgo_acciones';

    protected $fillable = [
        'riesgo_id',
        'ra_descripcion',
        'ra_comentario',
        'ra_fecha_inicio',
        'ra_fecha_fin_planificada',
        'ra_fecha_fin_reprogramada',
        'ra_fecha_fin_cancelada',
        'ra_fecha_fin_real',
        'ra_justificacion',
        'ra_evidencia',
        'ra_responsable',
        'ra_responsable_correo',
        'ra_estado',
        'ra_ciclo',
    ];

    public function riesgo()
    {
        return $this->belongsTo(Riesgo::class, 'riesgo_id');
    }
}

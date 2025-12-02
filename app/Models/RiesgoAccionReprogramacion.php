<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiesgoAccionReprogramacion extends Model
{
    use HasFactory;

    protected $table = 'riesgo_acciones_reprogramaciones';

    protected $fillable = [
        'riesgo_accion_id',
        'rar_fecha_anterior',
        'rar_fecha_nueva',
        'rar_justificacion',
        'rar_evidencia',
        'rar_estado',
        'rar_aprobado_por',
        'rar_fecha_aprobacion',
        'rar_comentario_aprobacion',
    ];

    protected $casts = [
        'rar_fecha_anterior' => 'date',
        'rar_fecha_nueva' => 'date',
        'rar_fecha_aprobacion' => 'datetime',
    ];

    public function riesgoAccion()
    {
        return $this->belongsTo(RiesgoAccion::class, 'riesgo_accion_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'rar_aprobado_por');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SncAccion extends Model
{
    use HasFactory;

    protected $table = 'snc_acciones';

    protected $fillable = [
        'snc_id',
        'accion_descripcion',
        'accion_responsable_id',
        'accion_fecha_planificada',
        'accion_fecha_real',
        'accion_estado',
        'accion_evidencia',
    ];

    protected $casts = [
        'accion_fecha_planificada' => 'date',
        'accion_fecha_real' => 'date',
    ];

    // Relaciones
    public function salidaNoConforme()
    {
        return $this->belongsTo(SalidaNoConforme::class, 'snc_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'accion_responsable_id');
    }
}

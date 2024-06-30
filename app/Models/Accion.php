<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accion extends Model
{
    use HasFactory;

    protected $table = 'hallazgos_acciones';
    protected $fillable = [
        'hallazgo_id',
        'accion',
        'accion_cod',
        'fecha_inicio',
        'fecha_fin',
        'responsable_id',
        'responsable_correo',
        'comentario',
        'fecha_fin_reprogramada',
        'fecha_fin_real',
        'ruta_evidencia',
        'estado',
        'es_correctiva',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class);
    }

}

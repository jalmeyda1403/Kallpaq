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
        'riesgo_cod',
        'nombre',
        'descripcion',
        'fecha_prog_inicio',
        'fecha_prog_fin',
        'fecha_implementacion',
        'responsable',
        'estado',
        'comentario',
    ];

    public function riesgo()
    {
        return $this->belongsTo(Riesgo::class, 'riesgo_cod');
    }
}

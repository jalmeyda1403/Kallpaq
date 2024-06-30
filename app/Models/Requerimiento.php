<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requerimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'proceso_id',
        'user_id',
        'facilitador_id',
        'justificacion',
        'descripcion',
        'estado',
        'prioridad',
        'complejidad',
        'ruta_archivo_desistimacion',
        'ruta_archivo_entregable',
        'fecha_limite'    
    ];

   
    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id',);
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function facilitador()
    {
        return $this->belongsTo(User::class, 'facilitador_id');
    }

    public function movimientos()
    {
        return $this->hasMany(RequerimientoMovimiento::class);
    }

    public function necesidad()
    {
        return $this->hasMany(RequerimientNecesidad::class);
    }
}
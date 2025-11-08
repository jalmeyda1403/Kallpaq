<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requerimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'proceso_id',
        'user_asigna_id',
        'facilitador_id',
        'especialista_id',
        'justificacion',
        'asunto',
        'descripcion',
        'estado',
        'prioridad',
        'complejidad',
        'comentario',
        'ruta_archivo_desistimacion',
        'ruta_archivo_requerimiento',
        'fecha_limite',
        'fecha_asignacion',
        'fecha_inicio',
        'fecha_fin',
    ];
    protected $casts = [
        'fecha_limite' => 'datetime',
        'fecha_asignacion' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id', );
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'facilitador_id');
    }

    public function especialista()
    {
        return $this->belongsTo(User::class, 'especialista_id');
    }
    public function usuario_asigna()
    {
        return $this->belongsTo(User::class, 'user_asigna_id');
    }

    public function movimientos()
    {
        return $this->hasMany(RequerimientoMovimiento::class);
    }

   
    public function avance()
    {
        return $this->hasOne(RequerimientoAvance::class);
    }

    public function evaluacion()
    {
        return $this->hasOne(RequerimientoEvaluacion::class);
    }

    public function getRutaArchivoDesistimacionAttribute($value)
    {
        if ($value) {
            return '/storage/' . $value;
        }
        return null;
    }
}
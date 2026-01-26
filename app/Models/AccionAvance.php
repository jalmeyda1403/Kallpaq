<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccionAvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'accion_id',
        'accion_avance_porcentaje',
        'accion_avance_comentario',
        'accion_avance_estado',
        'accion_avance_evidencia',
        'user_id'
    ];

    public function accion()
    {
        return $this->belongsTo(Accion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

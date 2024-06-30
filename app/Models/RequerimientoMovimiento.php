<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoMovimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'requerimiento_id',
        'estado',
        'comentario',
        'usuario_id',
    ];

    // Relación con el requerimiento
    public function requerimiento()
    {
        return $this->belongsTo(Requerimiento::class);
    }

    // Relación con el usuario
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}

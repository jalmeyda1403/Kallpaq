<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccionMovimientos extends Model
{
    use HasFactory;

    protected $table = 'accion_movimientos';

    protected $fillable = [
        'accion_id',
        'estado',
        'comentario',
        'user_id',
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
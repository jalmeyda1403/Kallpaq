<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SugerenciaMovimiento extends Model
{
    use HasFactory;

    protected $table = 'sugerencia_movimientos';

    protected $fillable = [
        'sugerencia_id',
        'estado',
        'observacion',
        'user_id',
        'fecha_movimiento'
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
    ];

    public function sugerencia()
    {
        return $this->belongsTo(Sugerencia::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

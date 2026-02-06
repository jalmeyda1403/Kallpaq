<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaNoConformeMovimiento extends Model
{
    use HasFactory;

    protected $table = 'salida_no_conforme_movimientos';

    protected $fillable = [
        'salida_no_conforme_id',
        'estado',
        'observacion',
        'user_id',
        'fecha_movimiento'
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
    ];

    public function salidaNoConforme()
    {
        return $this->belongsTo(SalidaNoConforme::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

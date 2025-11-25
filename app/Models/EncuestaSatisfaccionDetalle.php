<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncuestaSatisfaccionDetalle extends Model
{
    use HasFactory;

    protected $table = 'encuesta_satisfaccion_detalles';

    protected $fillable = [
        'encuesta_id',
        'esd_categoria',
        'esd_puntaje',
    ];

    public function encuesta()
    {
        return $this->belongsTo(EncuestaSatisfaccion::class, 'encuesta_id');
    }
}

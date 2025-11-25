<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncuestaSatisfaccion extends Model
{
    use HasFactory;

    protected $table = 'encuestas_satisfaccion';

    protected $fillable = [
        'proceso_id',
        'es_periodo',
        'es_numero_periodo',
        'es_anio',
        'es_nps_score',
        'es_cantidad',
        'es_score',
        'es_informe_path',
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function detalles()
    {
        return $this->hasMany(EncuestaSatisfaccionDetalle::class, 'encuesta_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadarNormativo extends Model
{
    use HasFactory;

    protected $table = 'radar_normativo';

    protected $fillable = [
        'titulo',
        'numero_norma',
        'organismo_emisor',
        'fecha_publicacion',
        'resumen_ia',
        'texto_completo',
        'nivel_relevancia',
        'estado',
        'obligacion_principal',
        'analisis_humano',
        'url_fuente' // Nuevo campo
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
    ];

    public function obligacion()
    {
        return $this->hasOne(Obligacion::class, 'radar_id');
    }
}

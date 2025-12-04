<?php

namespace App\Models;
use App\Models\Indicador;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicadorHistorico extends Model
{
    protected $table = 'indicadores_historico';
    protected $fillable = [
        'indicador_id',
        'ih_año',
        'ih_meta',
        'ih_valor',
        'ih_evidencia'
    ];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }
}

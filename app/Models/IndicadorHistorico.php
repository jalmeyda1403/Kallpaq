<?php

namespace App\Models;
use App\Indicador;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicadorHistorico extends Model
{
    protected $table = 'indicadores_historico';
    protected $fillable = [
        'indicador_id', 'año', 'meta', 'valor',
    ];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class);
    }
}

<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicadorSeguimiento extends Model
{
    protected $table = 'indicadores_seguimiento';
    protected $fillable = [
        'indicador_id',
        'is_fecha',
        'is_meta',
        'is_valor',
        'is_estado',
        'is_var1',
        'is_var2',
        'is_var3',
        'is_var4',
        'is_var5',
        'is_var6',
        'is_evidencias',
        'is_numero_periodo',
        'is_periodo',
        'is_comentario'
    ];

    protected $casts = [
        'is_evidencias' => 'array',
    ];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'indicador_id');
    }
}

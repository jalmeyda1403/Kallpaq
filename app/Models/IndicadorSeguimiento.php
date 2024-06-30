<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicadorSeguimiento extends Model
{
    protected $table = 'indicadores_seguimiento';
    protected $fillable = [
        'indicador_id', 'fecha', 'meta', 'valor', 'estado',
        'var1','var2','var3','var4','var5','var6', 'evidencias'
    ];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class);
    }
}

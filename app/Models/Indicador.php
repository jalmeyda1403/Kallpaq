<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    protected $table = 'indicadores';
    protected $fillable = [
        'producto','proceso_id', 'cliente', 'planificacion_sig_id', ' planificacion_sig_estado', 'estado', 
        'nombre', 'descripcion', 'formula', 'frecuencia', 'meta', 'parametro_medida', 'tipo_agregacion', 'sentido',
        'var1','var2','var3','var4','var5','var6'
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
    public function objetivo()
    {
        return $this->belongsTo(PlanificacionSIG::class, 'planificacion_sig_id');
    }
}

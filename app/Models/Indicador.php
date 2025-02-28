<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    protected $table = 'indicadores';
    protected $fillable = [
        'proceso_id', 'planificacion_pei_id', 'planificacion_sig_id', ' planificacion_sig_estado', 'estado', 
        'nombre', 'descripcion', 'fuente', 'tipo_indicador','sgc','sgas','sgcm','sgsi','sgce','frecuencia', 'formula','meta', 'parametro_medida', 'tipo_agregacion', 'sentido',
        'var1','var2','var3','var4','var5','var6'
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
    public function objetivoSIG()
    {
        return $this->belongsTo(PlanificacionSIG::class, 'planificacion_sig_id');
    }
    public function objetivoPEI()
    {
        return $this->belongsTo(PlanificacionPEI::class, 'planificacion_pei_id');
    }
}

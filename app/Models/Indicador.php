<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    protected $table = 'indicadores';
    protected $fillable = [
        'proceso_id',
        'planificacion_pei_id',
        'planificacion_sig_id',
        'indicador_nombre',
        'indicador_descripcion',
        'indicador_fuente',
        'indicador_tipo_indicador',
        'indicador_sig',
        'indicador_estado',
        'indicador_formula',
        'indicador_frecuencia',
        'indicador_meta',
        'indicador_tipo_agregacion',
        'indicador_parametro_medida',
        'indicador_sentido',
        'indicador_var1',
        'indicador_var2',
        'indicador_var3',
        'indicador_var4',
        'indicador_var5',
        'indicador_var6'
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

    public function seguimientos()
    {
        return $this->hasMany(IndicadorSeguimiento::class, 'indicador_id');
    }

    public function historicos()
    {
        return $this->hasMany(IndicadorHistorico::class, 'indicador_id');
    }
}

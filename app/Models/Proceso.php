<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Proceso extends Model
{
    protected $table = 'procesos';

    protected $fillable = [
        'id',
        'cod_proceso',
        'proceso_sigla',
        'proceso_nombre',
        'proceso_objetivo',
        'proceso_tipo',
        'cod_proceso_padre',
        'proceso_nivel',
        'proceso_estado',
        'planificacion_pei_id',
        'inactivate_at',
    ];

    public function procesoPadre()
    {
        return $this->belongsTo(Proceso::class, 'cod_proceso_padre');
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'proceso_id', 'id');
    }
    public function subprocesos()
    {
        return $this->hasMany(Proceso::class, 'cod_proceso_padre');
    }
    public function hallazgos()
    {
        return $this->hasMany(Hallazgo::class, 'proceso_id', 'id');
    }

    public function ouos()
    {
        return $this->belongsToMany(Ouo::class, 'procesos_ouo', 'id_proceso', 'id_ouo')
        ->withPivot('responsable', 'delegada');
    }
    public function obligaciones()
    {
        return $this->hasMany(Obligacion::class, 'proceso_id', 'id');
    }

    public function riesgos()
    {
        return $this->hasMany(Riesgo::class, 'proceso_id', 'id');
    }
    public function planificacion_pei()
    {
        return $this->belongsTo(PlanificacionPEI::class, 'planificacion_pei_id');
    }
    public function sipoc()
    {
        return $this->hasMany(Sipoc::class);
    }
    public function salidas()
    {
        return $this->hasMany(Salida::class);  // Relación de un subproceso a muchas salidas
    }
    public function documentos()
    {
        return $this->hasMany(Documento::class); // Relación con los documentos
    }

    public function ouo_responsable()
    {
        return $this->ouos()->wherePivot('responsable', 1)->pluck('ouo_nombre')->first();
    }
     

}
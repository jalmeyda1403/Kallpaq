<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Proceso extends Model
{   protected $table = 'procesos';

    protected $fillable = [
        'id',
        'cod_proceso',
        'proceso_nombre',
        'proceso_sigla',
        'proceso_tipo',
        'cod_proceso_padre',
        'proceso_nivel',
        'proeso_estado',
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
        return $this->belongsToMany(OUO::class, 'procesos_ouo', 'id_proceso', 'id_ouo');
    }
    public function obligaciones()
    {
        return $this->hasMany(Obligacion::class, 'proceso_id', 'id');
    } 
  
}
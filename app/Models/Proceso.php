<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Proceso extends Model
{
    protected $fillable = [
        'cod_proceso',
        'nombre',
        'sigla',
        'tipo_proceso',
        'cod_proceso_padre',
        'estado',
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

}
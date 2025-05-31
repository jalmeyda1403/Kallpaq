<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    protected $fillable = ['proceso_id', 'salida', 'tipo' ];

    public function requisitos()
    {
        return $this->hasMany(Requisito::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

   
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContextoDeterminacion extends Model
{
    protected $table = 'contexto_determinacion';

    protected $fillable = [
        'proceso_id', 'year', 'version'
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function contextoInterno()
    {
        return $this->hasMany(ContextoInterno::class);
    }

    public function contextoExterno()
    {
        return $this->hasMany(ContextoExterno::class);
    }

    public function contextoAnalisis()
    {
        return $this->hasMany(ContextoAnalisis::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContextoInterno extends Model
{
    protected $table = 'contexto_interno';

    protected $fillable = [
        'contexto_determinacion_id', 'perspective_type', 'fortalezas', 'debilidades'
    ];

    public function contextoDeterminacion()
    {
        return $this->belongsTo(ContextoDeterminacion::class);
    }

    public function contextoAnalisis()
    {
        return $this->hasMany(ContextoAnalisis::class, 'internal_context_id');
    }
}
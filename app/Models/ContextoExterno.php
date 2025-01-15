<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContextoExterno extends Model
{
    protected $table = 'contexto_externo';

    protected $fillable = [
        'contexto_determinacion_id', 'perspective_type', 'amenazas', 'oportunidades'
    ];

    public function contextoDeterminacion()
    {
        return $this->belongsTo(ContextoDeterminacion::class);
    }

    public function contextoAnalisis()
    {
        return $this->hasMany(ContextoAnalisis::class, 'external_context_id');
    }
}
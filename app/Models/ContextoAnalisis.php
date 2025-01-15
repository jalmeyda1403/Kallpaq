<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContextoAnalisis extends Model
{
    protected $table = 'contexto_analisis';

    protected $fillable = [
        'contexto_determinacion_id', 'internal_context_id', 'external_context_id', 'analisis', 'nivel', 'valoracion'
    ];

    public function contextoDeterminacion()
    {
        return $this->belongsTo(ContextoDeterminacion::class);
    }

    public function contextoInterno()
    {
        return $this->belongsTo(ContextoInterno::class, 'internal_context_id');
    }

    public function contextoExterno()
    {
        return $this->belongsTo(ContextoExterno::class, 'external_context_id');
    }
}
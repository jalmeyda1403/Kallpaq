<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoAvance extends Model
{
    protected $table = 'requerimiento_avances';

    protected $fillable = [
        'requerimiento_id',
        'levantamiento',
        'comentario_levantamiento',
        'contexto',
        'comentario_contexto',
        'caracterizacion',
        'comentario_caracterizacion',
        'formatos',
        'comentario_formatos',
        'revision_interna',
        'comentario_revision_interna',
        'revision_tecnica',
        'comentario_revision_tecnica',
        'firma',
        'comentario_firma',
        'publicacion',
        'comentario_publicacion',
        'ruta_evidencias',
        'avance_registrado',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    
    ];

    public function requerimiento()
    {
        return $this->belongsTo(Requerimiento::class);
    }

    public function calcularAvance(): float
    {
        $avance = 0;
        $avance += $this->contexto ? 15 : 0;
        $avance += $this->levantamiento ? 20 : 0;       
        $avance += $this->caracterizacion ? 25 : 0;
        $avance += $this->formatos ? 20 : 0;
        $avance += $this->revision_interna ? 10 : 0;
        $avance += $this->revision_tecnica ? 5 : 0;
        $avance += $this->firma ? 5 : 0;
        $avance += $this->publicacion ? 5 : 0;
        // Publicación no suma
        return $avance;
    }
}

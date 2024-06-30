<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoNecesidad extends Model
{
    use HasFactory;
    protected $table = 'requerimiento_tipo_documento';
    protected $fillable = [
        'requerimiento_id',
        'tipo_documento_id',
        'estado',
        'nombre_documento',
    ];

    // Relación con el requerimiento
    public function requerimiento()
    {
        return $this->belongsTo(Requerimiento::class);
    }

    // Relación con el tipo de documento
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }
}

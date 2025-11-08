<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoAlerta extends Model
{
    use HasFactory;
    protected $fillable = ['documento_id', 'documento_impactado_id', 'comentario', 'estado'];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function documentoImpactado()
    {
        return $this->belongsTo(Documento::class, 'documento_impactado_id');
    }
}

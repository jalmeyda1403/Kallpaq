<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;

class DocumentoRelacionado extends Pivot
{
   

    protected $table = 'documento_relacionado';
    public $incrementing = true;
    protected $fillable = [
        'documento_id',
        'relacionado_id',
        'tipo_relacion',
    ];

    // Documento principal
   
}
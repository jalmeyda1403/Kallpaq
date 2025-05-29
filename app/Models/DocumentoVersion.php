<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoVersion extends Model
{
    protected $fillable = ['documento_id', 'version', 'archivo_path', 'control_cambios', 'enlace_valido', 'fecha_aprobacion',
    'fecha_publicacion'];
    protected $dates = ['fecha_aprobacion', 'fecha_publicacion'];
    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }
}

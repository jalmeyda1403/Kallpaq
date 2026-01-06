<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoAnexo extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'documento_anexos';

    protected $fillable = [
        'documento_id',
        'da_codigo',
        'da_nombre',
        'da_tipo',
        'da_archivo_ruta',
        'da_version',
        'da_estado',
        'da_observacion',
        'da_fecha_publicacion',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }
}

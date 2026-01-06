<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoVersion extends Model
{
    use SoftDeletes;
    protected $table = 'documento_versions';

    protected $fillable = [
        'documento_id',
        'version',
        'archivo_path',
        'control_cambios',
        'enlace_valido',
        'instrumento_aprueba',
        'fecha_aprobacion',
        'fecha_publicacion',
    ];
    protected $dates = [
        'fecha_aprobacion',
        'fecha_publicacion'
    ];
    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }
     public function dependencias()
    {
        return $this->belongsToMany(Documento::class, 'documento_dependencias', 'padre_version_id', 'hijo_id')
            ->using(DocumentoDependencia::class);
    }
}

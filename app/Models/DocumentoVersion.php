<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoVersion extends Model
{
    protected $fillable = [
        'documento_id',
        'dv_version',
        'dv_archivo_path',
        'dv_control_cambios',
        'dv_enlace_valido',
        'dv_instrumento_aprueba',
        'dv_fecha_aprobacion',
        'dv_fecha_vigencia'
    ];
    protected $dates = [
        'dv_fecha_aprobacion',
        'dv_fecha_vigencia'
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

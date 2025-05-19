<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;
    protected $fillable = ['cod_documento', 'tipo_documento_id', 'proceso_id', 'nombre', 'fuente', 'estado', 'vigencia_at'];

    public function tipo_documento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }
    public function versiones()
    {
        return $this->hasMany(DocumentoVersion::class);
    }

    public function ultimaVersion()
    {
        return $this->hasOne(DocumentoVersion::class)->latestOfMany('version');
    }
}

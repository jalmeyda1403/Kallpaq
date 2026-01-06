<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Documento extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable =
        [
            'cod_documento',
            'tipo_documento_id',
            'proceso_id',
            'area_compliance_id',
            'subarea_compliance_id',
            'documento_padre_id',
            'nombre_documento',
            'resumen_documento',
            'fuente_documento',
            'estado_documento',
            'palabras_clave_documento',
            'observaciones_documento',
            'archivo_path_documento',
            'usa_versiones_documento',
            'fecha_aprobacion_documento',
            'fecha_derogacion_documento',
            'fecha_vigencia_documento',
            'frecuencia_revision_documento',
            'instrumento_aprueba_documento',// ← OPCIONAL         
            'instrumento_deroga_documento',
            'origen_documento',
            'enlace_valido',
            'user_created',
            'user_pubilshed',
            'user_deleted',
            'user_updated',
            'created_at',
            'updated_at',
            'deleted_at',
            'pubished_at',
            'estado_documento',
        ];

    protected $casts = [
        'fecha_aprobacion_documento' => 'datetime',
        'fecha_derogacion_documento' => 'datetime',
        'fecha_vigencia_documento' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function tipo_documento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    public function procesos()
    {
        return $this->belongsToMany(Proceso::class, 'documento_proceso', 'documento_id', 'proceso_id');
    }

    public function obligaciones()
    {
        return $this->hasMany(Obligacion::class);
    }

    public function area_compliance()
    {
        return $this->belongsTo(AreaCompliance::class, 'area_compliance_id');
    }
    public function subarea_compliance()
    {
        return $this->belongsTo(SubareaCompliance::class);
    }
    public function versiones()
    {
        return $this->hasMany(DocumentoVersion::class);
    }
    public function anexos()
    {
        return $this->hasMany(DocumentoAnexo::class, 'documento_id')->where('da_estado', 'VIGENTE');
    }

    public function ultimaVersion()
    {
        return $this->hasOne(DocumentoVersion::class)->latestOfMany();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }


    public function hijosDirectos()
    {
        return $this->belongsToMany(Documento::class, 'documento_dependencias', 'padre_documento_id', 'hijo_id')
            ->whereNull('documento_dependencias.padre_version_id')
            ->using(DocumentoDependencia::class);
    }

    public function padreDirecto()
    {
        return $this->belongsToMany(Documento::class, 'documento_dependencias', 'hijo_id', 'padre_documento_id')
            ->whereNull('documento_dependencias.padre_version_id')
            ->using(DocumentoDependencia::class);
    }
    public function relacionesSalientes()
    {
        return $this->belongsToMany(Documento::class, 'documento_relacionado', 'documento_id', 'relacionado_id')
            ->withPivot('tipo_relacion') // Carga el tipo de relación y la fecha
            ->using(DocumentoRelacionado::class); // Usa tu modelo pivote personalizado
    }

    /**
     * RELACIÓN BASE: Define los documentos que afectan a ESTE documento (entrantes).
     * Ej: Este documento es derogado, modificado o impactado por otros.
     */
    public function relacionesEntrantes()
    {
        return $this->belongsToMany(Documento::class, 'documento_relacionado', 'relacionado_id', 'documento_id')
            ->withPivot('tipo_relacion')
            ->using(DocumentoRelacionado::class);
    }

    public function proximaRevision()
    {
        return $this->frecuencia_revision &&
            $this->ultimaVersion &&
            $this->ultimaVersion->created_at->diffInDays(now()) >= ($this->frecuencia_revision * 365);
    }

    public function scopeBuscarPorTexto($query, $texto)
    {
        return $query->where(function ($q) use ($texto) {
            $q->where('nombre_documento', 'LIKE', "%$texto%")
                ->orWhere('cod_documento', 'LIKE', "%$texto%")
                ->orWhere('palabras_clave_documento', 'LIKE', "%$texto%")
                ->orWhereHas('tags', fn($t) => $t->where('nombre', 'LIKE', "%$texto%"))
                ->orWhereHas('procesos', fn($p) => $p->where('proceso_nombre', 'LIKE', "%$texto%"));
        });
    }
}

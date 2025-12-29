<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivoCritico extends Model
{
    use HasFactory;

    protected $table = 'activos_criticos';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo',
        'proceso_id',
        'responsable_id',
        'nivel_criticidad',
        'rto',
        'rpo',
        'mtpd',
        'dependencias',
        'ubicacion',
        'activo',
    ];

    protected $casts = [
        'dependencias' => 'array',
        'activo' => 'boolean',
    ];

    /**
     * Tipos de activo
     */
    public static function getTipos()
    {
        return [
            'personal' => 'Personal clave',
            'tecnologia' => 'Tecnología/Sistemas',
            'informacion' => 'Información/Datos',
            'infraestructura' => 'Infraestructura física',
            'proveedor' => 'Proveedor crítico',
            'proceso' => 'Proceso crítico',
            'otro' => 'Otro',
        ];
    }

    /**
     * Niveles de criticidad
     */
    public static function getNivelesCriticidad()
    {
        return [
            'bajo' => ['label' => 'Bajo', 'color' => 'success'],
            'medio' => ['label' => 'Medio', 'color' => 'warning'],
            'alto' => ['label' => 'Alto', 'color' => 'orange'],
            'critico' => ['label' => 'Crítico', 'color' => 'danger'],
        ];
    }

    /**
     * Genera código automáticamente
     */
    protected static function booted()
    {
        static::creating(function ($activo) {
            if (empty($activo->codigo)) {
                $count = static::count() + 1;
                $activo->codigo = 'AC-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Proceso al que pertenece
     */
    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    /**
     * Responsable del activo
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Estrategias de continuidad para este activo
     */
    public function estrategias()
    {
        return $this->hasMany(EstrategiaContinuidad::class, 'activo_id');
    }

    /**
     * Obtiene el color del nivel de criticidad
     */
    public function getCriticidadColorAttribute()
    {
        $niveles = self::getNivelesCriticidad();
        return $niveles[$this->nivel_criticidad]['color'] ?? 'secondary';
    }

    /**
     * Formatea RTO para mostrar
     */
    public function getRtoFormateadoAttribute()
    {
        if (!$this->rto) return 'No definido';
        if ($this->rto < 24) return $this->rto . ' horas';
        return round($this->rto / 24, 1) . ' días';
    }
}

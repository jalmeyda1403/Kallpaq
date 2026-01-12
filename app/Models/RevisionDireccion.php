<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RevisionDireccion extends Model
{
    use HasFactory;

    protected $table = 'revisiones_direccion';

    protected $fillable = [
        'codigo',
        'titulo',
        'fecha_programada',
        'fecha_reunion',
        'periodo',
        'anio',
        'participantes',
        'agenda',
        'observaciones',
        'estado',
        'acta_path',
        'responsable_id',
        'created_by',
        'sistemas_gestion',
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'fecha_reunion' => 'date',
        'sistemas_gestion' => 'array',
    ];

    protected $appends = [
        'avance_general',
        'estado_color',
    ];

    /**
     * Genera el código automáticamente
     */
    protected static function booted()
    {
        static::creating(function ($revision) {
            if (empty($revision->codigo)) {
                $anio = $revision->anio ?? date('Y');
                $count = static::where('anio', $anio)->count() + 1;
                $revision->codigo = 'RD-' . $anio . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Usuario responsable de la revisión
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Usuario que creó la revisión
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Entradas de la revisión
     */
    public function entradas()
    {
        return $this->hasMany(RevisionEntrada::class, 'revision_id');
    }

    /**
     * Salidas/Decisiones de la revisión
     */
    public function salidas()
    {
        return $this->hasMany(RevisionSalida::class, 'revision_id');
    }

    /**
     * Compromisos de la revisión
     */
    public function compromisos()
    {
        return $this->hasMany(RevisionCompromiso::class, 'revision_id');
    }

    /**
     * Compromisos pendientes
     */
    public function compromisosPendientes()
    {
        return $this->compromisos()->whereIn('estado', ['pendiente', 'en_proceso', 'vencido']);
    }

    /**
     * Calcula el avance general de los compromisos
     */
    public function getAvanceGeneralAttribute()
    {
        $compromisos = $this->compromisos;
        if ($compromisos->count() === 0)
            return 0;
        return round($compromisos->avg('avance'));
    }

    /**
     * Verifica si la revisión está vencida
     */
    public function getEstaVencidaAttribute()
    {
        if (!$this->fecha_programada)
            return false;

        $fecha = $this->fecha_programada instanceof Carbon
            ? $this->fecha_programada
            : Carbon::parse($this->fecha_programada);

        return $this->estado === 'programada' && $fecha->isPast();
    }

    /**
     * Obtiene el color para el estado
     */
    public function getEstadoColorAttribute()
    {
        return match ($this->estado) {
            'programada', 'aprobada', 'en_preparacion' => 'info',
            'realizada' => 'success',
            'cancelada' => 'secondary',
            default => 'primary',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @property Carbon|null $fecha_limite
 * @property Carbon|null $fecha_cierre
 */
class RevisionCompromiso extends Model
{
    use HasFactory;

    protected $table = 'revision_compromisos';

    protected $fillable = [
        'revision_id',
        'salida_id',
        'codigo',
        'descripcion',
        'responsable_id',
        'fecha_limite',
        'fecha_cierre',
        'estado',
        'evidencia_path',
        'recursos_necesarios',
        'observaciones',
        'avance',
        'sistemas_gestion',
    ];

    protected $casts = [
        'fecha_limite' => 'date',
        'fecha_cierre' => 'date',
        'evidencia_path' => 'array',
        'sistemas_gestion' => 'array',
    ];

    /**
     * Genera código automáticamente
     */
    protected static function booted()
    {
        static::creating(function ($compromiso) {
            if (empty($compromiso->codigo)) {
                $revision = $compromiso->revision;
                $count = $revision->compromisos()->count() + 1;
                $compromiso->codigo = $revision->codigo . '-C' . str_pad($count, 2, '0', STR_PAD_LEFT);
            }
        });

        // Actualizar estado a vencido si pasa la fecha límite
        static::saving(function ($compromiso) {
            if ($compromiso->estado === 'pendiente' || $compromiso->estado === 'en_proceso') {
                if ($compromiso->fecha_limite && $compromiso->fecha_limite->isPast()) {
                    $compromiso->estado = 'vencido';
                }
            }
        });
    }

    /**
     * Revisión a la que pertenece
     */
    public function revision()
    {
        return $this->belongsTo(RevisionDireccion::class, 'revision_id');
    }

    /**
     * Salida de la que deriva (opcional)
     */
    public function salida()
    {
        return $this->belongsTo(RevisionSalida::class, 'salida_id');
    }

    /**
     * Usuario responsable del compromiso
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Seguimientos del compromiso
     */
    public function seguimientos()
    {
        return $this->hasMany(RevisionCompromisoSeguimiento::class, 'compromiso_id')->orderBy('created_at', 'desc');
    }

    /**
     * Días restantes para el vencimiento
     */
    public function getDiasRestantesAttribute()
    {
        if (!$this->fecha_limite)
            return null;
        if ($this->estado === 'completado')
            return null;

        return Carbon::now()->startOfDay()->diffInDays($this->fecha_limite, false);
    }

    /**
     * Color según estado y días restantes
     */
    public function getEstadoColorAttribute()
    {
        return match ($this->estado) {
            'completado' => 'success',
            'cancelado' => 'secondary',
            'vencido' => 'danger',
            'en_proceso' => $this->dias_restantes <= 5 ? 'warning' : 'info',
            'pendiente' => $this->dias_restantes <= 3 ? 'warning' : 'primary',
            default => 'secondary',
        };
    }

    /**
     * Etiqueta del estado
     */
    public function getEstadoLabelAttribute()
    {
        return match ($this->estado) {
            'pendiente' => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'completado' => 'Completado',
            'vencido' => 'Vencido',
            'cancelado' => 'Cancelado',
            default => $this->estado,
        };
    }

    /**
     * Registra un seguimiento
     */
    public function registrarSeguimiento($userId, $comentario, $nuevoAvance = null, $nuevoEstado = null)
    {
        $seguimiento = new RevisionCompromisoSeguimiento([
            'user_id' => $userId,
            'comentario' => $comentario,
            'avance_anterior' => $this->avance,
            'avance_nuevo' => $nuevoAvance ?? $this->avance,
            'estado_anterior' => $this->estado,
            'estado_nuevo' => $nuevoEstado ?? $this->estado,
        ]);

        $this->seguimientos()->save($seguimiento);

        if ($nuevoAvance !== null) {
            $this->avance = $nuevoAvance;
        }
        if ($nuevoEstado !== null) {
            $this->estado = $nuevoEstado;
        }
        if ($nuevoEstado === 'completado') {
            $this->fecha_cierre = Carbon::now();
        }

        $this->save();

        return $seguimiento;
    }
}

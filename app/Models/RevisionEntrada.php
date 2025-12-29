<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevisionEntrada extends Model
{
    use HasFactory;

    protected $table = 'revision_entradas';

    protected $fillable = [
        'revision_id',
        'tipo_entrada',
        'titulo',
        'descripcion',
        'datos_soporte',
        'conclusion',
        'estado',
    ];

    protected $casts = [
        'datos_soporte' => 'array',
    ];

    /**
     * Tipos de entrada según ISO 9001 §9.3.2
     */
    public static function getTiposEntrada()
    {
        return [
            'estado_acciones_anteriores' => 'Estado de las acciones de revisiones anteriores',
            'cambios_contexto_externo' => 'Cambios en cuestiones externas',
            'cambios_contexto_interno' => 'Cambios en cuestiones internas',
            'retroalimentacion_partes_interesadas' => 'Retroalimentación de partes interesadas',
            'desempeno_procesos' => 'Desempeño de los procesos',
            'conformidad_productos_servicios' => 'Conformidad de productos y servicios',
            'no_conformidades_acciones_correctivas' => 'No conformidades y acciones correctivas',
            'resultados_auditorias' => 'Resultados de auditorías',
            'desempeno_proveedores' => 'Desempeño de proveedores externos',
            'adecuacion_recursos' => 'Adecuación de recursos',
            'eficacia_acciones_riesgos' => 'Eficacia de acciones para abordar riesgos',
            'oportunidades_mejora' => 'Oportunidades de mejora',
            'satisfaccion_cliente' => 'Satisfacción del cliente',
            'cumplimiento_objetivos' => 'Cumplimiento de objetivos de calidad',
            'otros' => 'Otros',
        ];
    }

    /**
     * Revisión a la que pertenece
     */
    public function revision()
    {
        return $this->belongsTo(RevisionDireccion::class, 'revision_id');
    }

    /**
     * Obtiene la etiqueta del tipo de entrada
     */
    public function getTipoEntradaLabelAttribute()
    {
        return self::getTiposEntrada()[$this->tipo_entrada] ?? $this->tipo_entrada;
    }

    /**
     * Obtiene el ícono según el tipo
     */
    public function getIconoAttribute()
    {
        return match ($this->tipo_entrada) {
            'estado_acciones_anteriores' => 'fas fa-history',
            'cambios_contexto_externo' => 'fas fa-globe',
            'cambios_contexto_interno' => 'fas fa-building',
            'retroalimentacion_partes_interesadas' => 'fas fa-comments',
            'desempeno_procesos' => 'fas fa-chart-line',
            'conformidad_productos_servicios' => 'fas fa-check-circle',
            'no_conformidades_acciones_correctivas' => 'fas fa-exclamation-triangle',
            'resultados_auditorias' => 'fas fa-clipboard-check',
            'desempeno_proveedores' => 'fas fa-truck',
            'adecuacion_recursos' => 'fas fa-users-cog',
            'eficacia_acciones_riesgos' => 'fas fa-shield-alt',
            'oportunidades_mejora' => 'fas fa-lightbulb',
            'satisfaccion_cliente' => 'fas fa-smile',
            'cumplimiento_objetivos' => 'fas fa-bullseye',
            default => 'fas fa-file-alt',
        };
    }
}

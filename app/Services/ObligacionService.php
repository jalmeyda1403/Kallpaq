<?php

namespace App\Services;

use App\Models\Obligacion;
use App\Models\ObligacionEvaluacion;
use Exception;

class ObligacionService
{
    /**
     * Valida si es posible cambiar al estado deseado según las reglas de negocio (ISO 37301).
     *
     * @param Obligacion $obligacion
     * @param string $nuevoEstado
     * @return bool
     * @throws Exception
     */
    public function validarTransicion(Obligacion $obligacion, string $nuevoEstado): bool
    {
        // Solo validamos transiciones "hacia adelante" o críticas
        if ($nuevoEstado === Obligacion::ESTADO_CONTROLADA) {
            return $this->validarParaControlada($obligacion);
        }

        if ($nuevoEstado === Obligacion::ESTADO_EN_TRATAMIENTO) {
            // Regla simple: debe tener al menos un riesgo o un control identificado
            if ($obligacion->riesgos()->count() === 0 && $obligacion->controles()->count() === 0) {
                throw new Exception("Para pasar a 'En Tratamiento', debe asociar al menos un riesgo o un control.");
            }
        }

        return true;
    }

    /**
     * Reglas estrictas para el estado CONTROLADA
     */
    private function validarParaControlada(Obligacion $obligacion): bool
    {
        $evaluacion = $obligacion->evaluacionActual;

        if (!$evaluacion) {
            throw new Exception("La obligación debe ser evaluada antes de ser marcada como controlada.");
        }

        $criticidad = $evaluacion->oe_nivel_criticidad; // baja, media, alta, muy_alta

        $tieneControles = $obligacion->controles()->count() > 0;
        $tieneRiesgos = $obligacion->riesgos()->count() > 0;

        // Verificar acciones (asociadas a riesgos)
        // Se asume que si hay riesgos, deben tener planes de acción
        $riesgosConAcciones = $obligacion->riesgos()->withCount('acciones')->get();
        $tieneAcciones = $riesgosConAcciones->sum('acciones_count') > 0;

        // Regla: Baja
        if ($criticidad === 'baja') {
            if (!$tieneControles) {
                throw new Exception("Criticidad BAJA: Requiere al menos un control activo.");
            }
        }

        // Regla: Media
        elseif ($criticidad === 'media') {
            if (!$tieneRiesgos) {
                throw new Exception("Criticidad MEDIA: Requiere identificar y evaluar riesgos.");
            }
            if (!$tieneControles) {
                throw new Exception("Criticidad MEDIA: Requiere al menos un control activo.");
            }
        }

        // Regla: Alta / Muy Alta
        elseif (in_array($criticidad, ['alta', 'muy_alta'])) {
            if (!$tieneRiesgos) {
                throw new Exception("Criticidad ALTA/MUY ALTA: Requiere identificar riesgos.");
            }
            if (!$tieneAcciones) {
                throw new Exception("Criticidad ALTA/MUY ALTA: Requiere planes de acción (acciones) definidos.");
            }
            if (!$tieneControles) {
                throw new Exception("Criticidad ALTA/MUY ALTA: Requiere controles implementados.");
            }
        }

        return true;
    }

    /**
     * Calcula la criticidad basada en el puntaje total.
     * Esta lógica puede ajustarse según la matriz definida por el usuario.
     */
    public function calcularCriticidad(float $puntaje): string
    {
        // Ejemplo de escala (ajustar según rúbrica real)
        // Supongamos puntaje maximo 25 (5 criterios * 5 puntos)
        if ($puntaje <= 9)
            return 'baja';
        if ($puntaje <= 14)
            return 'media';
        if ($puntaje <= 19)
            return 'alta';
        return 'muy_alta';
    }
}

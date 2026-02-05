<?php

namespace App\Http\Controllers;

use App\Models\Obligacion;
use App\Models\Accion;
use App\Models\Proceso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardObligacionesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $esAdmin = $user->hasRole('admin') || $user->hasRole('super-admin');
        $userOuos = $user->ouos->pluck('id')->toArray();

        $mostrarTodos = filter_var($request->get('mostrarTodos', false), FILTER_VALIDATE_BOOLEAN);

        // Consulta base de obligaciones (Histórico completo)
        $query = Obligacion::with(['procesos.ouos', 'acciones', 'area_compliance', 'subarea_compliance']);

        // Determinar si debe filtrar por OUO
        $debeFiltrarPorOuo = !$esAdmin || ($esAdmin && !$mostrarTodos);

        if ($debeFiltrarPorOuo && !empty($userOuos)) {
            $query->whereHas('procesos', function ($q) use ($userOuos) {
                $q->whereHas('ouos', function ($sq) use ($userOuos) {
                    $sq->whereIn('ouos.id', $userOuos);
                });
            });
        }

        $obligaciones = $query->get();
        $stats = $this->getStats($obligaciones);

        // Identificación por Año y Estado (Priorizando obligacion_fecha_identificacion)
        $identificacionPorAno = $this->getIdentificacionPorAno($obligaciones);

        // Acciones Vencidas vinculadas a estas obligaciones
        $accionesVencidas = $this->getAccionesVencidas($obligaciones);

        // Resumen por Proceso
        $resumenProcesos = $this->getResumenProcesos($obligaciones);

        return response()->json([
            'stats' => $stats,
            'obligaciones' => $obligaciones,
            'accionesVencidas' => $accionesVencidas,
            'resumenProcesos' => $resumenProcesos,
            'identificacionPorAno' => $identificacionPorAno,
            'esAdmin' => $esAdmin,
        ]);
    }

    /**
     * Obtiene la distribución de obligaciones por año.
     */
    private function getIdentificacionPorAno($obligaciones)
    {
        return $obligaciones->groupBy(function ($o) {
            $fecha = $o->obligacion_fecha_identificacion ?? $o->created_at;
            return Carbon::parse($fecha)->year;
        })->map(function ($items, $year) {
            return [
                'year' => $year,
                'pendiente' => $items->where('obligacion_estado', 'pendiente')->count(),
                'controlada' => $items->where('obligacion_estado', 'controlada')->count(),
                'vencida' => $items->where('obligacion_estado', 'vencida')->count(),
                'inactiva' => $items->where('obligacion_estado', 'inactiva')->count(),
                'suspendida' => $items->where('obligacion_estado', 'suspendida')->count(),
            ];
        })->values()->sortBy('year')->values();
    }

    /**
     * Obtiene las acciones vencidas vinculadas a las obligaciones proporcionadas.
     */
    private function getAccionesVencidas($obligaciones)
    {
        $obligacionIds = $obligaciones->pluck('id')->toArray();
        if (empty($obligacionIds)) {
            return [];
        }

        return Accion::whereIn('accion_obligacion_id', $obligacionIds)
            ->whereNotIn('accion_estado', ['implementada', 'finalizada', 'desestimada'])
            ->where(function ($q) {
                $q->where('accion_fecha_fin_planificada', '<', Carbon::today())
                    ->orWhere('accion_fecha_fin_reprogramada', '<', Carbon::today());
            })
            ->with('obligacion')
            ->get();
    }

    /**
     * Agrupa el resumen de obligaciones por proceso.
     */
    private function getResumenProcesos($obligaciones)
    {
        $procesosMap = [];

        foreach ($obligaciones as $obligacion) {
            foreach ($obligacion->procesos as $proceso) {
                if (!isset($procesosMap[$proceso->id])) {
                    $procesosMap[$proceso->id] = [
                        'id' => $proceso->id,
                        'nombre' => $proceso->proceso_nombre,
                        'total' => 0,
                        'pendientes' => 0,
                    ];
                }
                $procesosMap[$proceso->id]['total']++;
                if ($obligacion->obligacion_estado === 'pendiente') {
                    $procesosMap[$proceso->id]['pendientes']++;
                }
            }
        }

        return array_values($procesosMap);
    }

    private function getStats($obligaciones)
    {
        return [
            'total' => $obligaciones->count(),
            'controladas' => $obligaciones->where('obligacion_estado', 'controlada')->count(),
            'pendientes' => $obligaciones->where('obligacion_estado', 'pendiente')->count(),
            'vencidas' => $obligaciones->filter(function ($o) {
                // Si tuviera fecha de revision vencida, se contaria aqui. 
                // Segun Obligacion.php tiene obligacion_frecuencia pero no fecha_proxima_revision explicita en fillable.
                // Por ahora usamos un placeholder o lógica similar a riesgos si aplica.
                return false;
            })->count(),
            'distribucionEstado' => [
                'Pendiente' => $obligaciones->where('obligacion_estado', 'pendiente')->count(),
                'Controlada' => $obligaciones->where('obligacion_estado', 'controlada')->count(),
                'Vencida' => $obligaciones->where('obligacion_estado', 'vencida')->count(),
                'Inactiva' => $obligaciones->where('obligacion_estado', 'inactiva')->count(),
                'Suspendida' => $obligaciones->where('obligacion_estado', 'suspendida')->count(),
            ]
        ];
    }
}

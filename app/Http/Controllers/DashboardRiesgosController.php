<?php

namespace App\Http\Controllers;

use App\Models\Riesgo;
use App\Models\Accion;
use App\Models\Proceso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardRiesgosController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $esAdmin = $user->hasRole('admin') || $user->hasRole('super-admin');
        $userOuos = $user->ouos->pluck('id')->toArray();

        $mostrarTodos = filter_var($request->get('mostrarTodos', false), FILTER_VALIDATE_BOOLEAN);

        $riesgos = $this->getRiesgos($esAdmin, $mostrarTodos, $userOuos);
        $stats = $this->getStats($riesgos);

        $identificacionPorAno = $this->getIdentificacionPorAno($riesgos);
        $accionesVencidas = $this->getAccionesVencidas($riesgos);
        $resumenProcesos = $this->getResumenProcesos($riesgos);

        return response()->json([
            'stats' => $stats,
            'riesgos' => $riesgos,
            'accionesVencidas' => $accionesVencidas,
            'resumenProcesos' => $resumenProcesos,
            'identificacionPorAno' => $identificacionPorAno,
            'esAdmin' => $esAdmin,
        ]);
    }

    private function getRiesgos($esAdmin, $mostrarTodos, $userOuos)
    {
        // Consulta base de riesgos (Histórico completo)
        $riesgosQuery = Riesgo::with(['proceso.ouos', 'acciones', 'especialista']);

        // Determinar si debe filtrar por OUO
        $debeFiltrarPorOuo = !$esAdmin || ($esAdmin && !$mostrarTodos);

        if ($debeFiltrarPorOuo && !empty($userOuos)) {
            $riesgosQuery->whereHas('proceso', function ($query) use ($userOuos) {
                $query->whereHas('ouos', function ($q) use ($userOuos) {
                    $q->whereIn('ouos.id', $userOuos);
                });
            });
        }

        return $riesgosQuery->get();
    }

    private function getStats($riesgos)
    {
        return [
            'total' => $riesgos->count(),
            'criticos' => $riesgos->whereIn('riesgo_nivel', ['Alto', 'Muy Alto'])->count(),
            'enTratamiento' => $riesgos->whereIn('riesgo_estado', ['en_tratamiento', 'proyecto'])->count(),
            'vencidos' => $riesgos->filter(function (Riesgo $r) {
                return $r->riesgo_fecha_valoracion_rr && Carbon::parse($r->riesgo_fecha_valoracion_rr)->isPast();
            })->count(),
            'distribucionNivel' => [
                'Bajo' => $riesgos->where('riesgo_nivel', 'Bajo')->count(),
                'Medio' => $riesgos->where('riesgo_nivel', 'Medio')->count(),
                'Alto' => $riesgos->where('riesgo_nivel', 'Alto')->count(),
                'Muy Alto' => $riesgos->where('riesgo_nivel', 'Muy Alto')->count(),
            ],
            'distribucionEstado' => $riesgos->groupBy('riesgo_estado')->map->count(),
        ];
    }

    private function getIdentificacionPorAno($riesgos)
    {
        return $riesgos->groupBy(function (Riesgo $r) {
            return $r->created_at->year;
        })->map(function ($items, $year) {
            return [
                'year' => $year,
                'proyecto' => $items->where('riesgo_estado', 'proyecto')->count(),
                'enTratamiento' => $items->where('riesgo_estado', 'en_tratamiento')->count(),
                'pendiente' => $items->where('riesgo_estado', 'pendiente')->count(),
                'controlado' => $items->where('riesgo_estado', 'controlado')->count(),
            ];
        })->values()->sortBy('year')->values();
    }

    private function getAccionesVencidas($riesgos)
    {
        $riesgoIds = $riesgos->pluck('id')->toArray();
        if (empty($riesgoIds)) {
            return [];
        }

        return Accion::whereIn('accion_riesgo_id', $riesgoIds)
            ->whereNotIn('accion_estado', ['implementada', 'finalizada', 'desestimada'])
            ->where(function ($q) {
                $q->where('accion_fecha_fin_planificada', '<', Carbon::today())
                    ->orWhere('accion_fecha_fin_reprogramada', '<', Carbon::today());
            })
            ->with('riesgo')
            ->get();
    }

    private function getResumenProcesos($riesgos)
    {
        $procesosIds = $riesgos->pluck('proceso_id')->unique()->toArray();
        $procesos = [];
        if (!empty($procesosIds)) {
            $procesos = Proceso::whereIn('id', $procesosIds)->get();
        }

        $resumenProcesos = [];
        foreach ($procesos as $proceso) {
            $riesgosProceso = $riesgos->where('proceso_id', $proceso->id);
            $resumenProcesos[] = [
                'id' => $proceso->id,
                'nombre' => $proceso->proceso_nombre,
                'total' => $riesgosProceso->count(),
                'criticos' => $riesgosProceso->whereIn('riesgo_nivel', ['Alto', 'Muy Alto'])->count(),
                'promedioRiesgo' => round($riesgosProceso->avg('riesgo_valor') ?? 0, 2),
                'nivelMaximo' => $riesgosProceso->sortByDesc('riesgo_valor')->first()->riesgo_nivel ?? 'N/A'
            ];
        }

        return $resumenProcesos;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Accion;
use App\Models\Hallazgo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardMejoraController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Determinar si el usuario es administrador
        $esAdmin = $user->hasRole('admin') || $user->hasRole('super-admin');

        // Obtener OUOs del usuario (relación many-to-many)
        $userOuos = $user->ouos->pluck('id')->toArray();

        $mostrarTodos = $request->get('mostrarTodos', false);
        $mostrarTodos = filter_var($mostrarTodos, FILTER_VALIDATE_BOOLEAN);
        $year = $request->get('year');

        // Determinar si se debe filtrar por OUO
        $debeFiltrarPorOuo = $this->shouldFilterByOuo($esAdmin, $mostrarTodos);

        // Obtener hallazgos filtrados
        $hallazgos = $this->getHallazgos($year, $debeFiltrarPorOuo, $userOuos);

        // Obtener acciones filtradas
        $acciones = $this->getAcciones($hallazgos, $debeFiltrarPorOuo);

        // Calcular estadísticas
        $stats = $this->calculateStats($hallazgos);

        // Obtener acciones vencidas y próximas a vencer
        $accionesVencidas = $this->getAccionesVencidas($acciones);
        $accionesProximasVencer = $this->getAccionesProximasVencer($acciones);

        // Agrupar hallazgos por proceso
        $hallazgosPorProceso = $this->getHallazgosPorProceso($hallazgos, $debeFiltrarPorOuo, $userOuos);

        return response()->json([
            'stats' => $stats,
            'hallazgos' => $hallazgos,
            'acciones' => $acciones,
            'accionesVencidas' => $accionesVencidas,
            'accionesProximasVencer' => $accionesProximasVencer,
            'hallazgosPorProceso' => $hallazgosPorProceso,
            'esAdmin' => $esAdmin,
            'userOuos' => $userOuos,
        ]);
    }

    private function shouldFilterByOuo($esAdmin, $mostrarTodos)
    {
        if (!$esAdmin) {
            return true;
        }
        return $esAdmin && !$mostrarTodos;
    }

    private function getHallazgos($year, $debeFiltrarPorOuo, $userOuos)
    {
        $hallazgosQuery = Hallazgo::with(['procesos.ouos', 'acciones']);

        if ($year) {
            if (is_array($year)) {
                $hallazgosQuery->whereIn(\Illuminate\Support\Facades\DB::raw('YEAR(hallazgo_fecha_identificacion)'), $year);
            } else {
                $hallazgosQuery->whereYear('hallazgo_fecha_identificacion', $year);
            }
        }

        if ($debeFiltrarPorOuo && !empty($userOuos)) {
            $hallazgosQuery->whereHas('procesos', function ($query) use ($userOuos) {
                $query->whereHas('ouos', function ($q) use ($userOuos) {
                    $q->whereIn('ouos.id', $userOuos)
                        ->where('procesos_ouo.propietario', 1);
                });
            });
        }

        return $hallazgosQuery->get();
    }

    private function getAcciones($hallazgos, $debeFiltrarPorOuo)
    {
        $acciones = Accion::with(['hallazgo'])->get();

        if ($debeFiltrarPorOuo) {
            $accionIds = $hallazgos->pluck('id')->toArray();
            return $acciones->filter(function (Accion $accion) use ($accionIds) {
                return in_array($accion->accion_hallazgo_id, $accionIds);
            })->values();
        }

        return $acciones;
    }

    private function calculateStats($hallazgos)
    {
        return [
            'hallazgosAbiertos' => $hallazgos->whereIn('hallazgo_estado', ['creado', 'modificado'])->count(),
            'hallazgosCerrados' => $hallazgos->whereIn('hallazgo_estado', ['cerrado', 'evaluado'])->count(),
            'hallazgosEnProceso' => $hallazgos->whereIn('hallazgo_estado', ['en proceso', 'aprobado'])->count(),
            'hallazgosVencidos' => $hallazgos->filter(function (Hallazgo $hallazgo) {
                return $hallazgo->acciones->contains(function (Accion $accion) {
                    return $accion->accion_fecha_fin_planificada && Carbon::parse($accion->accion_fecha_fin_planificada) < Carbon::today();
                });
            })->count(),
        ];
    }

    private function getAccionesVencidas($acciones)
    {
        return $acciones->filter(function (Accion $accion) {
            return $accion->accion_fecha_fin_planificada && Carbon::parse($accion->accion_fecha_fin_planificada) < Carbon::today();
        })->values();
    }

    private function getAccionesProximasVencer($acciones)
    {
        $hastaFecha = Carbon::today()->addDays(7);
        return $acciones->filter(function (Accion $accion) use ($hastaFecha) {
            return $accion->accion_fecha_fin_planificada &&
                Carbon::parse($accion->accion_fecha_fin_planificada) > Carbon::today() &&
                Carbon::parse($accion->accion_fecha_fin_planificada) <= $hastaFecha;
        })->values();
    }

    private function getHallazgosPorProceso($hallazgos, $debeFiltrarPorOuo, $userOuos)
    {
        $procesos = $hallazgos->pluck('procesos')->flatten()->unique('id');

        if ($debeFiltrarPorOuo && !empty($userOuos)) {
            $procesos = $procesos->filter(function ($proceso) use ($userOuos) {
                return $proceso->ouos->contains(function ($ouo) use ($userOuos) {
                    return in_array($ouo->id, $userOuos) && $ouo->pivot->propietario == 1;
                });
            });

            $procesosPropios = \App\Models\Proceso::whereHas('ouos', function ($query) use ($userOuos) {
                $query->whereIn('ouos.id', $userOuos)
                    ->where('procesos_ouo.propietario', 1);
            })->get();

            $procesos = $procesos->merge($procesosPropios)->unique('id');
        }

        $hallazgosPorProceso = [];

        foreach ($procesos as $proceso) {
            $procesoHallazgos = $hallazgos->filter(function (Hallazgo $hallazgo) use ($proceso) {
                return $hallazgo->procesos->contains($proceso);
            });

            $hallazgosPorProceso[] = [
                'id' => $proceso->id,
                'nombre' => $proceso->proceso_nombre,
                'abiertos' => $procesoHallazgos->whereIn('hallazgo_estado', ['creado', 'modificado'])->count(),
                'enProceso' => $procesoHallazgos->whereIn('hallazgo_estado', ['en proceso', 'aprobado'])->count(),
                'cerrados' => $procesoHallazgos->whereIn('hallazgo_estado', ['cerrado', 'evaluado'])->count(),
                'total' => $procesoHallazgos->count(),
            ];
        }

        return $hallazgosPorProceso;
    }
}

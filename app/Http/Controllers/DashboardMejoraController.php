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

        // Consulta base de hallazgos
        $hallazgosQuery = Hallazgo::with(['procesos.ouos', 'acciones']);

        // Aplicar filtro de año si existe
        if ($year) {
            if (is_array($year)) {
                $hallazgosQuery->whereIn(\Illuminate\Support\Facades\DB::raw('YEAR(hallazgo_fecha_identificacion)'), $year);
            } else {
                $hallazgosQuery->whereYear('hallazgo_fecha_identificacion', $year);
            }
        }

        // Aplicar filtros según el rol del usuario
        // Los usuarios NO administradores SIEMPRE se filtran por OUO
        // Los usuarios administradores pueden elegir ver todos (cuando mostrarTodos=true) o solo los de su OUO (por defecto)

        $debeFiltrarPorOuo = false;

        if (!$esAdmin) {
            // Usuarios no administradores SIEMPRE se filtran por OUO
            $debeFiltrarPorOuo = true;
        } elseif ($esAdmin && !$mostrarTodos) {
            // Administradores se filtran por OUO cuando NO han marcado la opción de mostrar todos
            $debeFiltrarPorOuo = true;
        }
        // Si es admin y marcó mostrarTodos, entonces NO se filtra

        if ($debeFiltrarPorOuo && !empty($userOuos)) {
            // Filtrar hallazgos por procesos donde la OUO del usuario es PROPIETARIA (propietario = 1)
            $hallazgosQuery->whereHas('procesos', function ($query) use ($userOuos) {
                $query->whereHas('ouos', function ($q) use ($userOuos) {
                    $q->whereIn('ouos.id', $userOuos)
                        ->where('procesos_ouo.propietario', 1);
                });
            });
        }

        $hallazgos = $hallazgosQuery->get();
        $acciones = Accion::with(['hallazgo'])->get();

        // Filtrar también las acciones según el contexto de OUO
        if ($debeFiltrarPorOuo) {
            // Filtrar acciones para que solo se incluyan las de hallazgos que pertenecen a los procesos del usuario
            $accionIds = $hallazgos->pluck('id')->toArray();
            $acciones = $acciones->filter(function ($accion) use ($accionIds) {
                return in_array($accion->accion_hallazgo_id, $accionIds);
            })->values();
        }

        // Calcular estadísticas
        $stats = [
            'hallazgosAbiertos' => $hallazgos->whereIn('hallazgo_estado', ['creado', 'modificado'])->count(),
            'hallazgosCerrados' => $hallazgos->whereIn('hallazgo_estado', ['cerrado', 'evaluado'])->count(),
            'hallazgosEnProceso' => $hallazgos->whereIn('hallazgo_estado', ['en proceso', 'aprobado'])->count(),
            'hallazgosVencidos' => $hallazgos->filter(function ($hallazgo) {
                return $hallazgo->acciones->contains(function ($accion) {
                    return $accion->accion_fecha_fin_planificada && Carbon::parse($accion->accion_fecha_fin_planificada) < Carbon::today();
                });
            })->count(),
        ];

        // Obtener acciones vencidas
        $accionesVencidas = $acciones->filter(function ($accion) {
            return $accion->accion_fecha_fin_planificada && Carbon::parse($accion->accion_fecha_fin_planificada) < Carbon::today();
        })->values();

        // Obtener acciones próximas a vencer (en los próximos 7 días)
        $hastaFecha = Carbon::today()->addDays(7);
        $accionesProximasVencer = $acciones->filter(function ($accion) use ($hastaFecha) {
            return $accion->accion_fecha_fin_planificada &&
                Carbon::parse($accion->accion_fecha_fin_planificada) > Carbon::today() &&
                Carbon::parse($accion->accion_fecha_fin_planificada) <= $hastaFecha;
        })->values();

        // Agrupar hallazgos por proceso
        $procesos = $hallazgos->pluck('procesos')->flatten()->unique('id');

        // Si se está filtrando por OUO:
        // 1. Filtrar los procesos obtenidos de los hallazgos para dejar SOLO los que pertenecen a la OUO del usuario.
        //    (Esto evita que aparezcan procesos ajenos que comparten un hallazgo con un proceso propio).
        // 2. Agregar los procesos donde la OUO del usuario es propietaria (para mostrar ceros si es necesario).
        if ($debeFiltrarPorOuo && !empty($userOuos)) {
            // 1. Filtrar procesos para dejar SOLO aquellos donde la OUO del usuario es PROPIETARIA (propietario = 1).
            //    El usuario solicitó explícitamente ver solo los procesos de su propiedad.
            $procesos = $procesos->filter(function ($proceso) use ($userOuos) {
                // Asegurarse de cargar la relación si no está cargada (aunque sea lazy loading)
                return $proceso->ouos->contains(function ($ouo) use ($userOuos) {
                    return in_array($ouo->id, $userOuos) && $ouo->pivot->propietario == 1;
                });
            });

            // 2. Agregar procesos propios (propietario = 1) que quizás no tenían hallazgos
            $procesosPropios = \App\Models\Proceso::whereHas('ouos', function ($query) use ($userOuos) {
                $query->whereIn('ouos.id', $userOuos)
                    ->where('procesos_ouo.propietario', 1);
            })->get();

            $procesos = $procesos->merge($procesosPropios)->unique('id');
        }

        $hallazgosPorProceso = [];

        foreach ($procesos as $proceso) {
            $procesoHallazgos = $hallazgos->filter(function ($hallazgo) use ($proceso) {
                return $hallazgo->procesos->contains($proceso);
            });

            $procesoData = [
                'id' => $proceso->id,
                'nombre' => $proceso->proceso_nombre,
                'abiertos' => $procesoHallazgos->whereIn('hallazgo_estado', ['creado', 'modificado'])->count(),
                'enProceso' => $procesoHallazgos->whereIn('hallazgo_estado', ['en proceso', 'aprobado'])->count(),
                'cerrados' => $procesoHallazgos->whereIn('hallazgo_estado', ['cerrado', 'evaluado'])->count(),
                'total' => $procesoHallazgos->count(),
            ];

            $hallazgosPorProceso[] = $procesoData;
        }

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
}

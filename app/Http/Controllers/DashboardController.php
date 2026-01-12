<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Requerimiento;
use App\Models\Especialista;
use Carbon\Carbon;

use App\Models\User;

class DashboardController extends Controller
{
    public function getResumenEspecialistas(Request $request)
    {
        $anioRaw = $request->input('anio', now()->year);
        $anios = is_array($anioRaw) ? $anioRaw : [$anioRaw];

        $especialistas = Especialista::with('user')->where('estado', 1)->get();

        $requerimientos = Requerimiento::with('avance')
            ->whereNotNull('especialista_id')
            ->whereNot('estado', 'desestimado')
            ->whereIn(DB::raw('YEAR(fecha_asignacion)'), $anios)
            ->get();

        $especialistasData = $especialistas->map(function ($especialista) use ($requerimientos) {
            // Filtrar requerimientos para este especialista (usando user_id del especialista)
            $delUsuario = $requerimientos->filter(fn($req) => $req->especialista_id === $especialista->user_id);

            $totalAsignados = $delUsuario->count();
            $totalVencidos = $delUsuario->filter(function ($req) {
                return empty($req->fecha_fin) && !empty($req->fecha_limite) && Carbon::parse($req->fecha_limite)->isPast();
            })->count();
            $totalFinalizados = $delUsuario->where('estado', 'atendido')->count();
            $efectividad = $totalAsignados > 0 ? round(($totalFinalizados / $totalAsignados) * 100) : 0;

            $asignados = $delUsuario->where('estado', 'asignado');
            $totalAvanceAsignados = $asignados->sum(fn($req) => $req->avance->avance_registrado ?? 0);

            if ($asignados->count() > 0) {
                $promedioAvance = round($totalAvanceAsignados / $asignados->count());
            } else {
                $atendidos = $delUsuario->where('estado', 'atendido')->count();
                $desestimados = $delUsuario->where('estado', 'desestimado')->count();
                if ($atendidos > 0 || $desestimados > 0) {
                    $promedioAvance = 100;
                } else {
                    $promedioAvance = 0;
                }
            }

            $totalDesestimados = $delUsuario->where('estado', 'desestimado')->count();

            return (
                [
                    'id' => $especialista->id, // ID del especialista (de la tabla especialistas)
                    'nombre' => $especialista->nombres, // Usar el accessor del modelo Especialista
                    'sigla' => $especialista->user->sigla ?? '', // Asumiendo que sigla está en el modelo User
                    'foto_url' => $especialista->user->foto_url ?? asset('images/user-default.png'),
                    'total_asignados' => $totalAsignados,
                    'total_vencidos' => $totalVencidos,
                    'total_finalizados' => $totalFinalizados,
                    'total_desestimados' => $totalDesestimados,
                    'efectividad' => $efectividad,
                    'promedioAvance' => $promedioAvance,
                ]
            );
        });

        Log::info('Especialistas Data antes de retornar:', $especialistasData->toArray());

        $aniosDisponibles = range(2025, now()->year);

        return response()->json([
            'especialistas' => $especialistasData,
            'aniosDisponibles' => $aniosDisponibles,
            'debug_total_especialistas_raw' => $especialistas->count(),
            'debug_especialistas_data_count' => $especialistasData->count(),
        ]);
    }

    public function getResumenGeneral(Request $request)
    {
        $yearRaw = $request->input('year', now()->year);
        $years = is_array($yearRaw) ? $yearRaw : [$yearRaw];

        $requerimientos = Requerimiento::whereIn(DB::raw('YEAR(created_at)'), $years)->get();

        $creados = $requerimientos->where('estado', 'creado')->count();
        $finalizados = $requerimientos->where('estado', 'atendido')->count();
        $desestimados = $requerimientos->where('estado', 'desestimado')->count();

        // El nuevo "total" excluye los requerimientos en estado "creado"
        $total = $requerimientos->count() - $creados;

        $sin_asignar = $requerimientos->where('estado', 'aprobado')->count();

        $asignadosActivos = $requerimientos->filter(
            fn($r) =>
            $r->estado === 'asignado' &&
            empty($r->fecha_fin)
        );

        $vencidos = $asignadosActivos->filter(
            fn($r) =>
            !empty($r->fecha_limite) &&
            Carbon::parse($r->fecha_limite)->isPast()
        )->count();

        $enProceso = $asignadosActivos->filter(
            fn($r) =>
            !empty($r->fecha_limite) &&
            (Carbon::parse($r->fecha_limite)->isFuture() || Carbon::parse($r->fecha_limite)->isToday())
        )->count();

        // Nueva fórmula de eficacia
        $numerador_eficacia = $finalizados + $desestimados;
        $eficacia = $total > 0 ? round(($numerador_eficacia / $total) * 100) : 0;

        return response()->json([
            'total' => $total,
            'sin_asignar' => $sin_asignar,
            'desestimados' => $desestimados,
            'enProceso' => $enProceso,
            'vencidos' => $vencidos,
            'finalizados' => $finalizados,
            'eficacia' => $eficacia,
            // --- Debugging Info ---
            'debug_total_asignados_raw' => $requerimientos->where('estado', 'asignado')->count(),
            'debug_asignados_sin_fin' => $requerimientos->where('estado', 'asignado')->whereNull('fecha_fin')->count(),
            'debug_enProceso_calc' => $enProceso,
            'debug_vencidos_calc' => $vencidos,
        ]);
    }
    public function getResumenGrafico(Request $request)
    {
        $yearRaw = $request->input('year', now()->year);
        $years = is_array($yearRaw) ? $yearRaw : [$yearRaw];

        // --- 2. QUERIES OPTIMIZADAS ---
        // Ahora, cada consulta busca en los años especificados,
        // y agrupa por el NÚMERO de mes (1-12).

        $asignados = Requerimiento::select(
            DB::raw('MONTH(fecha_asignacion) as mes'),
            DB::raw('COUNT(*) as total')
        )
            ->whereIn(DB::raw('YEAR(fecha_asignacion)'), $years)
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $programados = Requerimiento::select(
            DB::raw('MONTH(fecha_limite) as mes'),
            DB::raw('COUNT(*) as total')
        )
            ->whereIn(DB::raw('YEAR(fecha_limite)'), $years)
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $finalizados = Requerimiento::whereNotNull('fecha_fin')
            ->select(
                DB::raw('MONTH(fecha_fin) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->whereIn(DB::raw('YEAR(fecha_fin)'), $years)
            ->groupBy('mes')
            ->pluck('total', 'mes');


        // --- 3. FORMATEO DE MESES (Tu petición) ---

        // Nombres de los meses para las etiquetas del gráfico
        $nombresMeses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

        // --- 4. PREPARACIÓN DE DATOS ---
        // Creamos las 3 series de datos, asegurándonos de que
        // los meses sin datos tengan un '0' en lugar de estar ausentes.

        $asignadosData = [];
        $programadosData = [];
        $finalizadosData = [];

        // Iteramos del 1 al 12 (enero a diciembre)
        foreach (range(1, 12) as $mes) {
            $asignadosData[] = $asignados->get($mes, 0);   // Usa el total del mes, o 0 si no existe
            $programadosData[] = $programados->get($mes, 0); // Usa el total del mes, o 0 si no existe
            $finalizadosData[] = $finalizados->get($mes, 0); // Usa el total del mes, o 0 si no existe
        }

        // --- 5. RESPUESTA JSON ---
        return response()->json([
            'labels' => $nombresMeses,
            'asignados' => $asignadosData,
            'programados' => $programadosData,
            'finalizados' => $finalizadosData,
            'aniosDisponibles' => range(2025, now()->year), // Añadir años disponibles
        ]);
    }
    public function getResumenAlertas(Request $request)
    {
        $type = $request->query('type', 'vencidos');

        // Usamos today() para obtener la fecha de hoy a las 00:00:00
        $hoy = today();

        if ($type === 'vencidos') {
            // Vencidos: Requerimientos donde la fecha límite fue AYER o antes.
            $data = Requerimiento::with('especialista', 'avance')
                ->where('estado', 'asignado')
                // whereDate() compara solo la parte de la FECHA
                ->whereDate('fecha_limite', '<', $hoy)
                ->orderBy('fecha_limite')
                ->paginate(4);

        } else { // enRiesgo
            // En Riesgo: Requerimientos donde la fecha límite es HOY o en el FUTURO.
            $data = Requerimiento::with('especialista', 'avance')
                ->where('estado', 'asignado')
                // whereDate() compara solo la parte de la FECHA
                ->whereDate('fecha_limite', '>=', $hoy)
                ->orderBy('fecha_limite')
                ->paginate(4);
        }

                return response()->json($data);
    }

    public function getDetalleEspecialista(Request $request)
    {
        $especialistaId = $request->query('especialista');
        $anioRaw = $request->input('anio', now()->year);
        $anios = is_array($anioRaw) ? $anioRaw : [$anioRaw];

        $especialista = Especialista::with('user')->find($especialistaId);

        if (!$especialista) {
            return response()->json(['error' => 'Especialista no encontrado'], 404);
        }

        $requerimientos = Requerimiento::with('avance')
            ->where('especialista_id', $especialista->user_id)
            ->whereIn(DB::raw('YEAR(fecha_asignacion)'), $anios)
            ->get();

        return response()->json([
            'especialista' => [
                'nombre' => $especialista->nombres,
                'sigla' => $especialista->user->sigla ?? '',
            ],
            'requerimientos' => $requerimientos,
        ]);
    }
}

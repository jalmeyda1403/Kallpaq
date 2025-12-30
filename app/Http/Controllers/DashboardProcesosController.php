<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Indicador;
use App\Models\Proceso;
use App\Models\PlanificacionSIG;
use App\Models\PlanificacionPEI;
use Illuminate\Support\Facades\DB;

class DashboardProcesosController extends Controller
{
    public function index()
    {
        return view('app');
    }

    public function data(Request $request)
    {
        $year = $request->query('year', now()->year);

        // Fetch and process indicators
        $indicadores = Indicador::with(['proceso', 'objetivoSIG', 'objetivoPEI'])->get();
        $indicadoresProcessed = $this->processIndicadores($indicadores, $year);

        // Calculate and collect metrics
        $processTypesData = $this->groupByProcessType($indicadoresProcessed);

        return response()->json([
            'general' => round($indicadoresProcessed->avg('current_cumplimiento') ?? 0, 2),
            'lowest_type' => collect($processTypesData)->sortBy('promedio')->first(),
            'process_types' => $processTypesData,
            'indicator_types' => $this->getIndicatorTypesData($indicadoresProcessed),
            'sig_objectives' => $this->getSigData($indicadoresProcessed),
            'pei_perspectives' => $this->getPeiData($indicadoresProcessed),
            'years' => range(2023, now()->year + 1)
        ]);
    }

    private function processIndicadores($indicadores, $year)
    {
        return $indicadores->map(function ($indicador) use ($year) {
            $ultimoSeguimiento = $indicador->seguimientos()
                ->where('is_periodo', $year)
                ->orderBy('is_numero_periodo', 'desc')
                ->first();

            $cumplimiento = 0;
            if ($ultimoSeguimiento && $ultimoSeguimiento->is_meta > 0) {
                $cumplimiento = ($ultimoSeguimiento->is_valor / $ultimoSeguimiento->is_meta) * 100;
            }

            $indicador->current_cumplimiento = $cumplimiento;
            $indicador->has_data = (bool) $ultimoSeguimiento;

            return $indicador;
        })->filter(fn($i) => $i->has_data);
    }

    private function getIndicatorTypesData($indicadores)
    {
        $definedTypes = ['producto', 'servicio', 'resultado', 'calidad'];
        $grouped = $indicadores->groupBy('indicador_tipo_indicador');

        return collect($definedTypes)->map(fn($type) => [
            'nombre' => ucfirst($type),
            'promedio' => round($grouped->get($type)?->avg('current_cumplimiento') ?? 0, 2),
            'count' => $grouped->get($type)?->count() ?? 0
        ]);
    }

    private function getSigData($indicadores)
    {
        return $indicadores->groupBy(fn($item) => $item->objetivoSIG ? $item->objetivoSIG->sistema : 'Otras')
            ->map(fn($systemGroup, $systemName) => [
                'sistema' => $systemName,
                'promedio_sistema' => round($systemGroup->avg('current_cumplimiento'), 2),
                'objetivos' => $systemGroup->groupBy('planificacion_sig_id')->map(function ($objGroup) {
                    $sig = $objGroup->first()->objetivoSIG;
                    return [
                        'nombre' => $sig ? $sig->objetivo . ' - ' . $sig->nombre_objetivo : 'Desconocido',
                        'promedio' => round($objGroup->avg('current_cumplimiento'), 2),
                    ];
                })->values()
            ])->values();
    }

    private function getPeiData($indicadores)
    {
        return $indicadores->groupBy('planificacion_pei_id')
            ->map(function ($group) {
                $pei = $group->first()->objetivoPEI;
                return [
                    'nombre' => $pei ? $pei->planificacion_pei_nombre : 'Sin PEI',
                    'promedio' => round($group->avg('current_cumplimiento'), 2),
                ];
            })
            ->values()
            ->filter(fn($i) => $i['nombre'] !== 'Sin PEI');
    }

    private function groupByProcessType($indicadores)
    {
        $types = ['Estratégico', 'Misional', 'Apoyo'];
        $results = [];

        foreach ($types as $type) {
            // Filter indicators belonging to processes of this type
            $filtered = $indicadores->filter(function ($ind) use ($type) {
                return $ind->proceso && $ind->proceso->proceso_tipo === $type;
            });

            // Count Active Processes by Level
            $countN0 = Proceso::where('proceso_tipo', $type)
                ->where('proceso_nivel', 0)
                ->whereNull('inactivated_at')
                ->count();

            $countN1 = Proceso::where('proceso_tipo', $type)
                ->where('proceso_nivel', 1)
                ->whereNull('inactivated_at')
                ->count();

            $countN2 = Proceso::where('proceso_tipo', $type)
                ->where('proceso_nivel', 2)
                ->whereNull('inactivated_at')
                ->count();

            $total = $countN0 + $countN1 + $countN2;

            $results[] = [
                'tipo' => $type,
                'promedio' => round($filtered->avg('current_cumplimiento') ?? 0, 2),
                'cantidad_procesos' => $total,
                'niveles' => [
                    'n0' => $countN0,
                    'n1' => $countN1,
                    'n2' => $countN2
                ]
            ];
        }

        return $results;
    }
}

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

        // Fetch all indicators with their relevant relationships
        $indicadores = Indicador::with(['proceso', 'objetivoSIG', 'objetivoPEI'])
            ->get();

        // Process indicators to attach the latest compliance for the selected year
        $indicadoresProcessed = $indicadores->map(function ($indicador) use ($year) {
            // Get latest tracking for the year
            $ultimoSeguimiento = $indicador->seguimientos()
                ->where('is_periodo', $year)
                ->orderBy('is_numero_periodo', 'desc')
                ->first();

            $cumplimiento = 0;
            if ($ultimoSeguimiento && $ultimoSeguimiento->is_meta > 0) {
                $cumplimiento = ($ultimoSeguimiento->is_valor / $ultimoSeguimiento->is_meta) * 100;
            }

            $indicador->current_cumplimiento = $cumplimiento;
            $indicador->has_data = $ultimoSeguimiento ? true : false;

            return $indicador;
        })->filter(function ($indicador) {
            return $indicador->has_data; // Only consider indicators with data for accurate averages
        });

        // 1. General Metric
        $generalAverage = $indicadoresProcessed->avg('current_cumplimiento') ?? 0;

        // 2. Process Types (Estratégico, Misional, Apoyo)
        $processTypesData = $this->groupByProcessType($indicadoresProcessed);

        // Find lowest performing type for alert
        $lowestType = collect($processTypesData)->sortBy('promedio')->first();

        // 3. Indicator Types
        // Force all types to appear even if empty
        $definedTypes = ['producto', 'servicio', 'resultado', 'calidad'];
        $groupedIndicators = $indicadoresProcessed->groupBy('indicador_tipo_indicador');

        $indicatorTypesData = collect($definedTypes)->map(function ($type) use ($groupedIndicators) {
            $group = $groupedIndicators->get($type);
            return [
                'nombre' => ucfirst($type),
                'promedio' => $group ? round($group->avg('current_cumplimiento'), 2) : 0,
                'count' => $group ? $group->count() : 0
            ];
        });

        // 4. SIG Objectives - Grouped by System
        $sigDataBySystem = $indicadoresProcessed->groupBy(function ($item) {
            return $item->objetivoSIG ? $item->objetivoSIG->sistema : 'Otras';
        })->map(function ($systemGroup, $systemName) {

            // Now within this system, group by specific Objective
            $objectives = $systemGroup->groupBy('planificacion_sig_id')->map(function ($objGroup) {
                $sig = $objGroup->first()->objetivoSIG;
                return [
                    'nombre' => $sig ? $sig->objetivo . ' - ' . $sig->nombre_objetivo : 'Desconocido',
                    'promedio' => round($objGroup->avg('current_cumplimiento'), 2),
                ];
            })->values();

            return [
                'sistema' => $systemName,
                'promedio_sistema' => round($systemGroup->avg('current_cumplimiento'), 2),
                'objetivos' => $objectives
            ];
        });

        $sigData = $sigDataBySystem->values();

        // 5. PEI Perspectives
        $peiData = $indicadoresProcessed->groupBy('planificacion_pei_id')->map(function ($group, $key) {
            $pei = $group->first()->objetivoPEI;
            return [
                'nombre' => $pei ? $pei->planificacion_pei_nombre : 'Sin PEI',
                'promedio' => round($group->avg('current_cumplimiento'), 2),
                'ponderado' => 0
            ];
        })->values()->filter(fn($i) => $i['nombre'] !== 'Sin PEI');

        // Available years for filter (mock or query)
        $years = range(2023, now()->year + 1);

        return response()->json([
            'general' => round($generalAverage, 2),
            'lowest_type' => $lowestType,
            'process_types' => $processTypesData,
            'indicator_types' => $indicatorTypesData,
            'sig_objectives' => $sigData,
            'pei_perspectives' => $peiData,
            'years' => $years
        ]);
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

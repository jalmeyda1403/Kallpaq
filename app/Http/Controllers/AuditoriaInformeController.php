<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaInforme;
use App\Models\AuditoriaEspecifica;
use App\Models\AuditoriaChecklist;
use App\Models\AuditoriaAuditado;
use App\Models\Hallazgo;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditoriaInformeController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index($ae_id)
    {
        $informes = AuditoriaInforme::where('ae_id', '=', $ae_id, 'and')
            ->with(['elaboradoPor', 'aprobadoPor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($informes);
    }

    public function show($id)
    {
        $informe = AuditoriaInforme::with(['auditoria', 'elaboradoPor', 'aprobadoPor'])
            ->findOrFail($id);

        return response()->json($informe);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ae_id' => 'required|exists:auditoria_especifica,id'
        ]);

        // Generar código único
        $auditoria = AuditoriaEspecifica::findOrFail($validated['ae_id']);
        $codigo = $auditoria->ae_codigo . '-INF-' . now()->format('Ymd');

        $informe = AuditoriaInforme::create([
            'ae_id' => $validated['ae_id'],
            'codigo' => $codigo,
            'estado' => 'Borrador',
            'elaborado_por' => Auth::id()
        ]);

        return response()->json($informe, 201);
    }

    public function update(Request $request, $id)
    {
        $informe = AuditoriaInforme::findOrFail($id);

        $validated = $request->validate([
            'estado' => 'sometimes|in:Borrador,En Revisión,Aprobado,Emitido',
            'resumen_ejecutivo' => 'nullable|string',
            'alcance_criterios' => 'nullable|string',
            'hallazgos_conformidad' => 'nullable|array',
            'hallazgos_no_conformidad' => 'nullable|array',
            'oportunidades_mejora' => 'nullable|array',
            'procesos_auditados' => 'nullable|array',
            'auditados' => 'nullable|array',
            'conclusiones' => 'nullable|string',
            'recomendaciones' => 'nullable|string',
            'fecha_emision' => 'nullable|date'
        ]);

        if (isset($validated['estado']) && $validated['estado'] === 'Aprobado' && $informe->estado !== 'Aprobado') {
            // Verificar si ya existen hallazgos generados para este informe para evitar duplicados
            $existeHallazgos = Hallazgo::where('informe_id', '=', $informe->id, 'and')->exists();

            if (!$existeHallazgos) {
                // Obtener checklists con hallazgos (NC u OM)
                $checklists = AuditoriaChecklist::whereHas('agenda', function ($q) use ($informe) {
                    $q->where('ae_id', '=', $informe->ae_id, 'and');
                })
                    ->whereIn('estado_cumplimiento', ['No Conforme', 'OM'], 'and', false)
                    ->with(['agenda'])
                    ->get();

                foreach ($checklists as $item) {
                    // Determinar tipo
                    $tipo = ($item->estado_cumplimiento === 'No Conforme') ? 'No Conformidad' : 'Oportunidad de Mejora';

                    // Usar redacción refinada si existe, sino la original
                    $descripcion = !empty($item->hallazgo_redaccion) ? $item->hallazgo_redaccion : $item->hallazgo_detectado;

                    Hallazgo::create([
                        'informe_id' => $informe->id,
                        'hallazgo_cod' => 'SMP-' . $informe->id . '-' . $item->id, // Código temporal
                        'hallazgo_descripcion' => $descripcion,
                        'hallazgo_evidencia' => $item->evidencia_registrada,
                        'hallazgo_criterio' => $item->requisito_referencia,
                        'hallazgo_clasificacion' => $tipo,
                        'hallazgo_estado' => 'registrado', // Estado inicial
                        'hallazgo_origen' => 'Auditoría Interna',
                        'hallazgo_fecha_identificacion' => now(),
                        // Otros campos pueden quedar nulos o default
                    ]);
                }
            }
        }

        $informe->update($validated);

        return response()->json($informe);
    }

    public function getDatosInforme($ae_id)
    {
        $auditoria = AuditoriaEspecifica::with([
            'agenda.auditados',
            'agenda.checklists',
            'agenda.proceso'
        ])->findOrFail($ae_id);

        // Obtener auditados con su proceso
        $auditados = [];
        foreach ($auditoria->agenda as $agenda) {
            $proceso = $agenda->proceso->pro_nombre ?? $agenda->aea_actividad;
            foreach ($agenda->auditados as $auditado) {
                $auditados[] = [
                    'nombre' => $auditado->nombre,
                    'cargo' => $auditado->cargo,
                    'correo' => $auditado->correo,
                    'proceso' => $proceso
                ];
            }
        }

        // Obtener hallazgos del checklist agrupados por proceso
        $hallazgosPorProceso = [
            'conformidad' => [],
            'no_conformidad' => [],
            'oportunidad_mejora' => []
        ];

        foreach ($auditoria->agenda as $agenda) {
            $proceso = $agenda->proceso->pro_nombre ?? $agenda->aea_actividad;
            $proceso_id = $agenda->proceso_id;

            foreach ($agenda->checklists as $checklist) {
                $hallazgo = [
                    'proceso' => $proceso,
                    'proceso_id' => $proceso_id,
                    'pregunta' => $checklist->pregunta,
                    'norma' => $checklist->norma_referencia,
                    // Priorizar campos redactados
                    'requisito' => !empty($checklist->criterio_redaccion) ? $checklist->criterio_redaccion : $checklist->requisito_referencia,
                    'evidencia' => !empty($checklist->evidencia_redaccion) ? $checklist->evidencia_redaccion : $checklist->evidencia_registrada,
                    'hallazgo' => !empty($checklist->hallazgo_redaccion) ? $checklist->hallazgo_redaccion : $checklist->hallazgo_detectado,
                    'resumen' => $checklist->hallazgo_resumen ?? '',
                    'hallazgo_clasificacion' => $checklist->hallazgo_clasificacion,
                    'checklist_id' => $checklist->id
                ];

                if ($checklist->estado_cumplimiento === 'Conforme') {
                    if (!isset($hallazgosPorProceso['conformidad'][$proceso])) {
                        $hallazgosPorProceso['conformidad'][$proceso] = [
                            'proceso' => $proceso,
                            'proceso_id' => $proceso_id,
                            'items' => []
                        ];
                    }
                    $hallazgosPorProceso['conformidad'][$proceso]['items'][] = $hallazgo;
                } elseif ($checklist->estado_cumplimiento === 'No Conforme') {
                    if (!isset($hallazgosPorProceso['no_conformidad'][$proceso])) {
                        $hallazgosPorProceso['no_conformidad'][$proceso] = [
                            'proceso' => $proceso,
                            'proceso_id' => $proceso_id,
                            'items' => []
                        ];
                    }
                    $hallazgosPorProceso['no_conformidad'][$proceso]['items'][] = $hallazgo;
                } elseif ($checklist->estado_cumplimiento === 'Oportunidad de Mejora' || $checklist->estado_cumplimiento === 'OM') {
                    if (!isset($hallazgosPorProceso['oportunidad_mejora'][$proceso])) {
                        $hallazgosPorProceso['oportunidad_mejora'][$proceso] = [
                            'proceso' => $proceso,
                            'proceso_id' => $proceso_id,
                            'items' => []
                        ];
                    }
                    $hallazgosPorProceso['oportunidad_mejora'][$proceso]['items'][] = $hallazgo;
                }
            }
        }

        // Obtener procesos auditados con checklist al 100%
        $procesosAuditados = [];
        foreach ($auditoria->agenda as $agenda) {
            if ($agenda->aea_tipo === 'ejecucion' && $agenda->proceso_id) {
                // Usamos la relación ya cargada para evitar N+1 queries
                $totalPreguntas = $agenda->checklists->count();
                $preguntasRespondidas = $agenda->checklists
                    ->whereNotNull('estado_cumplimiento')
                    ->count();

                $progreso = $totalPreguntas > 0 ? ($preguntasRespondidas / $totalPreguntas) * 100 : 0;

                // Usamos redondeo para evitar problemas de precisión en punto flotante
                if ($totalPreguntas > 0 && round($progreso, 2) >= 100) {
                    $procesosAuditados[] = [
                        'nombre' => $agenda->proceso->pro_nombre ?? $agenda->aea_actividad,
                        'responsable' => $agenda->proceso->responsable->name ?? 'N/A',
                        'fecha_auditoria' => $agenda->aea_fecha,
                        'total_preguntas' => $totalPreguntas
                    ];
                }
            }
        }

        if (empty($procesosAuditados)) {
            Log::info("No se hallaron procesos al 100% para la auditoría ID: {$ae_id}");
        }

        return response()->json([
            'auditoria' => [
                'codigo' => $auditoria->ae_codigo,
                'objetivo' => $auditoria->ae_objetivo,
                'alcance' => $auditoria->ae_alcance,
                'fecha_inicio' => $auditoria->ae_fecha_inicio,
                'fecha_fin' => $auditoria->ae_fecha_fin
            ],
            'auditados' => array_values(array_unique($auditados, SORT_REGULAR)),
            'procesos_auditados' => $procesosAuditados,
            'hallazgos_conformidad' => array_values($hallazgosPorProceso['conformidad']),
            'hallazgos_no_conformidad' => array_values($hallazgosPorProceso['no_conformidad']),
            'oportunidades_mejora' => array_values($hallazgosPorProceso['oportunidad_mejora'])
        ]);
    }

    public function generarSeccionIA(Request $request)
    {
        $validated = $request->validate([
            'seccion' => 'required|in:resumen_ejecutivo,alcance_criterios,conclusiones,recomendaciones',
            'datos' => 'required|array'
        ]);

        $datos = $validated['datos'];

        // Valores por defecto para evitar errores
        $auditoria = $datos['auditoria'] ?? [];
        $codigo = $auditoria['codigo'] ?? 'N/A';
        $objetivo = $auditoria['objetivo'] ?? 'No especificado';
        $alcance = $auditoria['alcance'] ?? 'No especificado';
        $fechaInicio = $auditoria['fecha_inicio'] ?? 'N/A';
        $fechaFin = $auditoria['fecha_fin'] ?? 'N/A';

        // Preparar resumen de hallazgos para contexto
        $totalConformidades = $datos['total_conformidades'] ?? 0;
        $totalOportunidades = $datos['total_oportunidades'] ?? 0;
        $totalNoConformidades = $datos['total_no_conformidades'] ?? 0;

        $hallazgosNC = $datos['hallazgos_no_conformidad'] ?? [];
        $oportunidades = $datos['oportunidades_mejora'] ?? [];

        $prompts = [
            'resumen_ejecutivo' => "Genera un resumen ejecutivo profesional para un informe de auditoría ISO 9001 e ISO 37001.

CONTEXTO DE LA AUDITORÍA:
- Código: {$codigo}
- Objetivo: {$objetivo}
- Alcance: {$alcance}
- Período: {$fechaInicio} a {$fechaFin}

RESULTADOS:
- Conformidades encontradas: {$totalConformidades}
- Oportunidades de mejora identificadas: {$totalOportunidades}
- No conformidades detectadas: {$totalNoConformidades}

El resumen debe:
1. Ser conciso (máximo 200 palabras)
2. Destacar los hallazgos más relevantes
3. Mencionar el nivel general de cumplimiento
4. Ser objetivo y profesional",

            'alcance_criterios' => "Redacta la sección de Alcance y Criterios para un informe de auditoría.
        
        DATOS DE LA AUDITORÍA:
        - Alcance: {$alcance}
        - Objetivo: {$objetivo}
        
        La sección debe:
        1. Describir claramente el alcance físico y funcional de la auditoría.
        2. Especificar los criterios normativos aplicados (ISO 9001:2015, ISO 37001:2016).
        3. NO listar los procesos auditados uno por uno (ya están en otra sección), solo mencione el grupo de procesos institucional.
        4. Ser clara y específica.",

            'conclusiones' => "Elabora conclusiones profesionales y MUY CONCISAS para un informe de auditoría.
        
        RESULTADOS DE LA AUDITORÍA:
        - Total de conformidades: {$totalConformidades}
        - Total de oportunidades de mejora: {$totalOportunidades}
        - Total de no conformidades: {$totalNoConformidades}
        
        HALLAZGOS DE NO CONFORMIDAD:
        " . (count($hallazgosNC) > 0 ? json_encode($hallazgosNC, JSON_PRETTY_PRINT) : 'No se detectaron no conformidades') . "
        
        Las conclusiones deben:
        1. Ser objetivas, directas y basadas en evidencia.
        2. Evaluar el cumplimiento del sistema de gestión en máximo 2 párrafos cortos.
        3. Destacar fortalezas clave y vulnerabilidades críticas de forma resumida.
        4. Evitar redundancias y lenguaje excesivamente decorativo.",

            'recomendaciones' => "Genera recomendaciones técnicas, accionables y MUY CONCISAS para mejorar el sistema de gestión.
        
        HALLAZGOS IDENTIFICADOS:
        - No conformidades: {$totalNoConformidades}
        - Oportunidades de mejora: {$totalOportunidades}
        
        DETALLE DE HALLAZGOS CRÍTICOS:
        " . (count($hallazgosNC) > 0 ? json_encode($hallazgosNC, JSON_PRETTY_PRINT) : 'No se detectaron no conformidades') . "
        
        Las recomendaciones deben:
        1. Listar un máximo de 5-6 puntos clave.
        2. Ser específicas, directas y accionables.
        3. Estar priorizadas por impacto.
        4. Evitar explicaciones teóricas largas; ir directo a la acción recomendada.",
        ];

        try {
            $contenido = $this->aiService->generateText($prompts[$validated['seccion']]);
            return response()->json(['contenido' => $contenido]);
        } catch (\Exception $e) {
            Log::error('Error generando contenido con IA: ' . $e->getMessage());
            return response()->json(['error' => 'Error generando contenido con IA: ' . $e->getMessage()], 500);
        }
    }

    public function crearSMPsDesdeHallazgos(Request $request, $informe_id)
    {
        $validated = $request->validate([
            'hallazgos_seleccionados' => 'required|array',
            'hallazgos_seleccionados.*.checklist_id' => 'required|exists:auditoria_checklist,id',
            'hallazgos_seleccionados.*.proceso_id' => 'required|exists:procesos,id'
        ]);

        $informe = AuditoriaInforme::findOrFail($informe_id);
        $smpsCreadas = [];

        DB::beginTransaction();
        try {
            foreach ($validated['hallazgos_seleccionados'] as $hallazgoData) {
                $checklist = AuditoriaChecklist::findOrFail($hallazgoData['checklist_id']);

                // Crear Hallazgo (SMP)
                $hallazgo = Hallazgo::create([
                    'hal_codigo' => 'HAL-' . now()->format('YmdHis') . '-' . $hallazgoData['proceso_id'],
                    'hal_descripcion' => $checklist->hallazgo_detectado,
                    'hal_tipo' => 'No Conformidad',
                    'hal_estado' => 'Abierto',
                    'hal_fecha_deteccion' => now(),
                    'hal_origen' => 'Auditoría Interna',
                    'hal_referencia' => $informe->codigo
                ]);

                // Asociar hallazgo con proceso
                DB::table('hallazgo_proceso')->insert([
                    'hallazgo_id' => $hallazgo->id,
                    'proceso_id' => $hallazgoData['proceso_id'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $smpsCreadas[] = $hallazgo;
            }

            DB::commit();
            return response()->json([
                'message' => 'SMPs creadas exitosamente',
                'smps' => $smpsCreadas
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error creando SMPs: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        $informe = AuditoriaInforme::findOrFail($id);

        if ($informe->estado === 'Emitido') {
            return response()->json(['error' => 'No se puede eliminar un informe emitido'], 403);
        }

        AuditoriaInforme::where('id', '=', $id, 'and')->delete();
        return response()->json(['message' => 'Informe eliminado']);
    }

    public function exportPdf($id)
    {
        $informe = AuditoriaInforme::with(['auditoria', 'elaboradoPor', 'aprobadoPor'])->findOrFail($id);

        $hallazgosNC = $this->enriquecerHallazgos($informe->hallazgos_no_conformidad ?? []);
        $oportunidades = $this->enriquecerHallazgos($informe->oportunidades_mejora ?? []);
        $auditoria = $informe->auditoria;

        $pdf = Pdf::loadView('auditorias.pdf.informe', compact('informe', 'auditoria', 'hallazgosNC', 'oportunidades'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Informe_Auditoria_{$informe->codigo}.pdf");
    }

    public function exportWord($id)
    {
        $informe = AuditoriaInforme::with(['auditoria', 'elaboradoPor', 'aprobadoPor'])->findOrFail($id);

        $hallazgosNC = $this->enriquecerHallazgos($informe->hallazgos_no_conformidad ?? []);
        $oportunidades = $this->enriquecerHallazgos($informe->oportunidades_mejora ?? []);
        $auditoria = $informe->auditoria;

        $html = view('auditorias.pdf.informe', compact('informe', 'auditoria', 'hallazgosNC', 'oportunidades'))->render();
        $content = "\xEF\xBB\xBF" . $html;

        $headers = [
            "Content-type" => "text/html; charset=utf-8",
            "Content-Disposition" => "attachment;Filename=Informe_Auditoria_{$informe->codigo}.doc"
        ];

        return response($content, 200, $headers);
    }

    private function enriquecerHallazgos($grupos)
    {
        if (empty($grupos))
            return [];

        $checklistIds = [];
        foreach ($grupos as $grupo) {
            foreach ($grupo['items'] as $item) {
                if (isset($item['checklist_id'])) {
                    $checklistIds[] = $item['checklist_id'];
                }
            }
        }

        if (empty($checklistIds))
            return $grupos;

        $checklists = AuditoriaChecklist::whereIn('id', $checklistIds, 'and', false)->get()->keyBy('id');

        foreach ($grupos as &$grupo) {
            foreach ($grupo['items'] as &$item) {
                if (isset($item['checklist_id']) && isset($checklists[$item['checklist_id']])) {
                    $chk = $checklists[$item['checklist_id']];
                    // Si falta el dato o queremos asegurar el más reciente:
                    $item['norma'] = $chk->norma_referencia;
                    $item['requisito'] = $chk->requisito_referencia;
                    // Opcional: actualizar evidencia o hallazgo si se desea sincronización total
                    // $item['evidencia'] = $chk->evidencia_registrada; 
                }
            }
        }

        return $grupos;
    }
}

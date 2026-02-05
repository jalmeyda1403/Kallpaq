<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaAgenda;
use App\Models\AuditoriaChecklist;
use App\Models\AuditoriaEspecifica;
use App\Models\Proceso;
use App\Models\NormaRequisito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaEjecucionController extends Controller
{
    protected $aiService;

    public function __construct(\App\Services\AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Obtener agenda (ejecuciones) de una auditoría específica.
     */
    public function index($ae_id)
    {
        $agenda = AuditoriaAgenda::where('ae_id', '=', $ae_id, 'and')
            ->withCount([
                'checklists as total_items',
                'checklists as completed_items' => function ($query) {
                    $query->where('estado_cumplimiento', '!=', 'Sin Evaluar');
                }
            ])
            ->with([
                'proceso' => function ($q) {
                    $q->select('id', 'proceso_nombre');
                },
                'auditor' => function ($q) {
                    $q->select('id', 'user_id');
                },
                'auditor.user' => function ($q) {
                    $q->select('id', 'name', 'email');
                }
            ])
            ->orderBy('aea_fecha')
            ->orderBy('aea_hora_inicio')
            ->get();

        return response()->json($agenda);
    }


    /**
     * Start Execution (Change status of Agenda Item)
     */
    public function initExecution(Request $request)
    {
        // We receive agenda_id directly now
        $request->validate([
            'agenda_id' => 'required|exists:auditoria_agenda,id'
        ]);

        $agendaItem = AuditoriaAgenda::with('checklists')->findOrFail($request->agenda_id);

        if ($agendaItem->estado === 'Programada' || !$agendaItem->estado) {
            $agendaItem->estado = 'En Curso';
            // Ensure process_id is linked if missing (best effort from text matching if needed, 
            // but ideally PlanificacionDetallada should fill it)
            if (!$agendaItem->proceso_id && $agendaItem->aea_actividad) {
                $proc = Proceso::where('proceso_nombre', '=', $agendaItem->aea_actividad, 'and')->first();
                if ($proc)
                    $agendaItem->proceso_id = $proc->id;
            }
            $agendaItem->save();
        }

        return response()->json($agendaItem);
    }

    /**
     * Obtener el detalle de una ejecución (Checklist) via Agenda ID
     */
    public function show($id)
    {
        // $id is agenda_id here
        $agendaItem = AuditoriaAgenda::with(['checklists', 'proceso', 'auditor.user', 'auditoria'])
            ->withCount([
                'checklists as total_items',
                'checklists as completed_items' => function ($query) {
                    $query->where('estado_cumplimiento', '!=', 'Sin Evaluar');
                }
            ])
            ->findOrFail($id);

        // Enriquecer aea_requisito con el nombre de la norma
        if (is_array($agendaItem->aea_requisito)) {
            $reqs = $agendaItem->aea_requisito;
            $updatedReqs = [];
            foreach ($reqs as $req) {
                if (isset($req['norma_id'])) {
                    $norma = \App\Models\NormaAuditable::find($req['norma_id']);
                    $req['norma'] = $norma ? $norma->nombre : ('Norma ' . $req['norma_id']);
                }
                $updatedReqs[] = $req;
            }
            $agendaItem->aea_requisito = $updatedReqs;
        }

        return response()->json($agendaItem);
    }


    /**
     * Actualizar un ítem del checklist
     */
    public function updateChecklistItem(Request $request, $id)
    {
        $item = AuditoriaChecklist::findOrFail($id);

        $validated = $request->validate([
            'estado_cumplimiento' => 'nullable|string',
            'evidencia_registrada' => 'nullable|string',
            'hallazgo_detectado' => 'nullable|string',
            'comentarios' => 'nullable|string',
            'tipo_hallazgo' => 'nullable|string'
        ]);

        $item->update(array_filter($validated, function ($value) {
            return $value !== null;
        }));


        // Optional: Update Agenda status to 'Concluida' if all items checked?
        // Or keep it 'En Curso' until explicit close.

        return response()->json($item);
    }

    /**
     * Generar Checklist con soporte para filtro por norma (Tabs)
     */
    public function generateChecklist(Request $request, $id)
    {
        set_time_limit(300); // 5 minutes timeout prevent crash
        try {
            \Log::info("Iniciando generateChecklist para Agenda ID: $id on " . date('Y-m-d H:i:s'));

            // $id is agenda_id
            $agendaItem = AuditoriaAgenda::with(['proceso', 'auditoria'])->findOrFail($id);
            $normaId = $request->input('norma_id');
            $normaFilterName = null;

            if ($normaId) {
                $normaObj = \App\Models\NormaAuditable::find($normaId);
                $normaFilterName = $normaObj ? $normaObj->nombre : null;
            }

            // Si ya existen ítems, los eliminamos para regenerar
            // Si hay filtro de norma, borramos SOLO los de esa norma
            $query = $agendaItem->checklists()->where('ai_generated', true);
            if ($normaFilterName) {
                $query->where('norma_referencia', $normaFilterName);
            }
            $query->delete();


            // Prepare context
            $procName = $agendaItem->proceso ? $agendaItem->proceso->proceso_nombre : ($agendaItem->aea_actividad ?? 'Proceso General');
            $responsable = $agendaItem->proceso && $agendaItem->proceso->responsable ? $agendaItem->proceso->responsable->name : 'N/A';

            // Get Normas from AuditoriaEspecifica OR specific norm filter
            if ($normaFilterName) {
                $normas = $normaFilterName;
            } else {
                $normas = is_array($agendaItem->auditoria->ae_sistema)
                    ? implode(', ', $agendaItem->auditoria->ae_sistema)
                    : ($agendaItem->auditoria->ae_sistema ?? 'ISO 9001');
            }

            // Get Specific Requirements from Agenda and ENRICH with full details
            $requisitos = [];
            if (!empty($agendaItem->aea_requisito)) {
                $rawReqs = $agendaItem->aea_requisito;

                // Filter if requested
                if ($normaId) {
                    $requisitos = array_values(array_filter($rawReqs, function ($r) use ($normaId) {
                        return isset($r['norma_id']) && $r['norma_id'] == $normaId;
                    }));

                    // Inject norm name to filtered reqs so AI knows the context
                    foreach ($requisitos as &$req) {
                        if (!isset($req['norma']) && isset($req['norma_id'])) {
                            $req['norma'] = $normaFilterName; // We already fetched this above
                        }
                    }
                } else {
                    $requisitos = $rawReqs;
                }

                \Log::info('DEBUG Checklist Generation - Raw aea_requisito:', [
                    'agenda_id' => $id,
                    'raw_data' => $requisitos,
                    'is_array' => is_array($requisitos)
                ]);

                // Enrich with denomination and detail from norma_requisito table
                if (is_array($requisitos) && !empty($requisitos)) {
                    $reqIds = array_filter(array_column($requisitos, 'id'));

                    \Log::info('DEBUG Checklist - Extracted IDs:', ['ids' => $reqIds]);

                    if (!empty($reqIds)) {
                        // Load requirements WITH their associated norms
                        $requisitosDetallados = NormaRequisito::with('norma')
                            ->whereIn('nr_id', $reqIds)
                            ->get()
                            ->keyBy('nr_id');

                        // Enrich each requirement with full context including norm name
                        foreach ($requisitos as &$req) {
                            if (isset($req['id']) && isset($requisitosDetallados[$req['id']])) {
                                $detalle = $requisitosDetallados[$req['id']];

                                // Add denomination and detail
                                $req['denominacion'] = $detalle->nr_denominacion;
                                $req['detalle'] = $detalle->nr_detalle;

                                // Add full norm name (override the short code if exists)
                                if ($detalle->norma) {
                                    $req['norma'] = $detalle->norma->nombre; // FIXED: use 'nombre' not 'nombre_norma'
                                }
                            }
                        }
                        unset($req); // Break reference

                        \Log::info('DEBUG Checklist - Enriched requirements:', [
                            'enriched_data' => $requisitos
                        ]);
                    }
                }
            }

            // Call AI Service
            // Ensure requirements is a clean array to avoid JSON nesting issues
            $cleanRequisitos = array_values($requisitos);

            \Log::info("Llamando a AIService::generateChecklist...");
            $items = $this->aiService->generateChecklist($procName, $normas, $responsable, $cleanRequisitos);
            \Log::info("Respuesta recibida de AIService", ['count' => count($items)]);


            foreach ($items as $item) {
                try {
                    // Determine the strictly correct norm name to ensure frontend tabs match
                    $finalNormName = $item['norma_referencia'] ?? '';

                    // 1. Try to match usage of specific requirement to get its exact norm name
                    $refReq = $item['requisito_referencia'] ?? null;
                    if ($refReq) {
                        // Find in $requisitos array
                        foreach ($requisitos as $r) {
                            // Loose comparison for 4.1 vs "4.1"
                            if (isset($r['numeral']) && $r['numeral'] == $refReq) {
                                if (!empty($r['norma'])) {
                                    $finalNormName = $r['norma'];
                                }
                                break;
                            }
                            // Also check encoded keys if needed, but 'numeral' should be standard from enrichment
                            if (isset($r['nr_numeral']) && $r['nr_numeral'] == $refReq && !empty($r['norma'])) {
                                $finalNormName = $r['norma'];
                                break;
                            }
                        }
                    }

                    // 2. If still empty or not matched, and we are filtering by a specific norm, force that norm
                    if ($normaFilterName && (empty($finalNormName) || stripos($finalNormName, $normaFilterName) === false)) {
                        $finalNormName = $normaFilterName;
                    }

                    \Log::info("DEBUG Checklist - Creating Item", [
                        'norma' => $finalNormName,
                        'req' => $item['requisito_referencia'] ?? 'N/A'
                    ]);

                    $agendaItem->checklists()->create([
                        'norma_referencia' => $finalNormName,
                        'requisito_referencia' => $item['requisito_referencia'] ?? '',
                        'pregunta' => $item['pregunta'] ?? '',
                        'evidencia_esperada' => $item['evidencia_esperada'] ?? '',
                        'criterio_auditoria' => $item['criterio_auditoria'] ?? '',
                        'requisito_contenido' => $item['requisito_contenido'] ?? ($item['criterio_auditoria'] ?? ''),
                        'ai_generated' => true
                    ]);
                } catch (\Throwable $innerEx) {
                    \Log::error("ERROR al crear checklist item: " . $innerEx->getMessage());
                }
            }

            if ($agendaItem->checklists()->count() === 0) {
                $agendaItem->checklists()->create([
                    'norma_referencia' => $normas,
                    'pregunta' => 'Verificar cumplimiento general del proceso ' . $procName,
                    'ai_generated' => false
                ]);
            }

            return response()->json([
                'message' => 'Checklist generado correctamente',
                'count' => $agendaItem->checklists()->count()
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error generando checklist IA: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Error backend: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Mejora la redacción de un hallazgo mediante IA.
     */
    public function improveHallazgo(Request $request)
    {
        $request->validate(['text' => 'required|string|max:1000']);

        try {
            $improved = $this->aiService->improveHallazgo($request->text);
            return response()->json(['improved' => $improved]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo mejorar la redacción: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mejora la redacción de una oportunidad de mejora mediante IA.
     */
    public function improveMejora(Request $request)
    {
        $request->validate(['text' => 'required|string|max:1000']);

        try {
            $improved = $this->aiService->improveMejora($request->text);
            return response()->json(['improved' => $improved]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo mejorar la redacción: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Genera un resumen de hallazgo con IA.
     */
    public function generateSummary(Request $request)
    {
        $request->validate(['text' => 'required|string|max:4000']);

        try {
            $summary = $this->aiService->generateHallazgoSummary($request->text);
            return response()->json(['summary' => $summary]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo generar el resumen: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Sube un archivo para una actividad de la agenda (apertura, cierre, gabinete).
     */
    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,png,xls,xlsx|max:10240'
        ]);

        $agendaItem = AuditoriaAgenda::findOrFail($id);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('auditorias/evidencias', $filename, 'public');

            $agendaItem->aea_archivo = $path;
            $agendaItem->estado = 'Concluida';
            $agendaItem->save();

            return response()->json([
                'message' => 'Archivo subido correctamente',
                'aea_archivo' => $path,
                'estado' => $agendaItem->estado
            ]);
        }

        return response()->json(['error' => 'No se pudo subir el archivo'], 400);
    }

    /**
     * Obtener lista de auditados de una actividad.
     */
    public function getAuditados($id)
    {
        $agendaItem = AuditoriaAgenda::with('auditados')->findOrFail($id);
        return response()->json($agendaItem->auditados);
    }

    /**
     * Sincronizar (guardar) auditados de una actividad.
     */
    public function syncAuditados(Request $request, $id)
    {
        $request->validate([
            'auditados' => 'present|array',
            'auditados.*.nombre' => 'required|string',
            'auditados.*.cargo' => 'required|string',
            'auditados.*.correo' => 'nullable|email'
        ]);

        $agendaItem = AuditoriaAgenda::findOrFail($id);

        DB::transaction(function () use ($agendaItem, $request) {
            $agendaItem->auditados()->delete();
            foreach ($request->auditados as $row) {
                $agendaItem->auditados()->create([
                    'nombre' => $row['nombre'],
                    'cargo' => $row['cargo'],
                    'correo' => $row['correo'] ?? null
                ]);
            }
        });

        return response()->json(['message' => 'Auditados guardados correctamente']);
    }
}





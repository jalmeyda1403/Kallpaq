<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaAgenda;
use App\Models\AuditoriaChecklist;
use App\Models\AuditoriaEspecifica;
use App\Models\Proceso;
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
        $agendaItem = AuditoriaAgenda::with(['checklists', 'proceso', 'auditor.user', 'auditoria'])->findOrFail($id);
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
     * Generar Checklist (Stub para Fase 3 AI)
     */
    public function generateChecklist($id)
    {
        // $id is agenda_id
        $agendaItem = AuditoriaAgenda::with(['proceso', 'auditoria'])->findOrFail($id);

        // Si ya existen ítems, los eliminamos para regenerar (solo los generados por IA)
        $agendaItem->checklists()->where('ai_generated', true)->delete();


        // Prepare context
        $procName = $agendaItem->proceso ? $agendaItem->proceso->proceso_nombre : ($agendaItem->aea_actividad ?? 'Proceso General');
        $responsable = $agendaItem->proceso && $agendaItem->proceso->responsable ? $agendaItem->proceso->responsable->name : 'N/A';

        // Get Normas from AuditoriaEspecifica
        $normas = is_array($agendaItem->auditoria->ae_sistema)
            ? implode(', ', $agendaItem->auditoria->ae_sistema)
            : ($agendaItem->auditoria->ae_sistema ?? 'ISO 9001');

        // Get Specific Requirements from Agenda
        $requisitos = [];
        if (!empty($agendaItem->aea_requisito)) {
            $requisitos = $agendaItem->aea_requisito;
        }

        // Call AI Service
        $items = $this->aiService->generateChecklist($procName, $normas, $responsable, $requisitos);


        foreach ($items as $item) {
            $agendaItem->checklists()->create([
                'norma_referencia' => $item['norma_referencia'] ?? '',
                'requisito_referencia' => $item['requisito_referencia'] ?? '',
                'pregunta' => $item['pregunta'] ?? '',
                'evidencia_esperada' => $item['evidencia_esperada'] ?? '',
                'criterio_auditoria' => $item['criterio_auditoria'] ?? '',
                'requisito_contenido' => $item['criterio_auditoria'] ?? '', // Fallback
                'ai_generated' => true
            ]);
        }

        if ($agendaItem->checklists()->count() === 0) {
            $agendaItem->checklists()->create([
                'norma_referencia' => $normas,
                'pregunta' => 'Verificar cumplimiento general del proceso ' . $procName,
                'ai_generated' => false
            ]);
        }

        return response()->json($agendaItem->load('checklists'));
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





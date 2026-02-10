<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaChecklist;
use Illuminate\Http\Request;

class AuditoriaHallazgoController extends Controller
{
    /**
     * Obtener hallazgos (NC y OM) para la Revisión de Gabinete.
     */
    public function getHallazgosRevision($ae_id)
    {
        $hallazgos = AuditoriaChecklist::whereHas('agenda', function ($query) use ($ae_id) {
            $query->where('ae_id', $ae_id);
        })
            ->whereIn('estado_cumplimiento', ['No Conforme', 'OM', 'Oportunidad de Mejora'], 'and', false)
            ->with(['agenda.proceso']) // Para mostrar contexto
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($hallazgos);
    }

    /**
     * Actualizar la redacción refinada del hallazgo.
     */
    public function updateHallazgoRedaccion(Request $request, $id)
    {
        $validated = $request->validate([
            'hallazgo_redaccion' => 'nullable|string',
            'hallazgo_resumen' => 'nullable|string',
            'criterio_redaccion' => 'nullable|string',
            'evidencia_redaccion' => 'nullable|string',
            'hallazgo_clasificacion' => 'nullable|string|in:NCM,NCMe,Odm,Obs,N/A',
            'estado_cumplimiento' => 'nullable|string'
        ]);

        $checklist = AuditoriaChecklist::findOrFail($id);

        $checklist->hallazgo_redaccion = $validated['hallazgo_redaccion'] ?? null;
        $checklist->hallazgo_resumen = $validated['hallazgo_resumen'] ?? null;
        $checklist->criterio_redaccion = $validated['criterio_redaccion'] ?? null;
        $checklist->evidencia_redaccion = $validated['evidencia_redaccion'] ?? null;
        $checklist->hallazgo_clasificacion = $validated['hallazgo_clasificacion'] ?? null;

        if (isset($validated['estado_cumplimiento'])) {
            $checklist->estado_cumplimiento = $validated['estado_cumplimiento'];
        }

        $checklist->save();

        return response()->json(['message' => 'Redacción actualizada correctamente', 'checklist' => $checklist]);
    }

    /**
     * Obtener procesos de la agenda para un AE específico (para select de nuevo hallazgo).
     */
    public function getAgendaProcesos($ae_id)
    {
        $procesos = \App\Models\AuditoriaAgenda::where('ae_id', $ae_id)
            ->with(['proceso:id,proceso_nombre'])
            ->get(['id', 'proceso_id', 'aea_tipo', 'aea_requisito']) // aea_requisito might be useful for default numeral
            ->map(function ($item) {
                return [
                    'agenda_id' => $item->id,
                    'proceso_id' => $item->proceso_id,
                    'proceso_nombre' => $item->proceso ? $item->proceso->proceso_nombre : 'N/A',
                    'tipo' => $item->aea_tipo,
                ];
            });

        return response()->json($procesos);
    }

    /**
     * Obtener normas auditables filtradas por el sistema de gestión de la auditoría.
     */
    public function getNormasByAudit($ae_id)
    {
        $auditoria = \App\Models\AuditoriaEspecifica::findOrFail($ae_id);
        $sistemas = $auditoria->ae_sistema; // Array de nombres de sistemas, ej: ['ISO 9001:2015', 'ISO 45001:2018']

        if (empty($sistemas)) {
            return response()->json([]);
        }

        // Buscar las normas cuyo sistema coincida con los sistemas de la auditoría
        $normas = \App\Models\NormaAuditable::with('requisitos')
            ->whereIn('na_sistema', $sistemas)
            ->select('id', 'na_nombre', 'na_descripcion', 'na_sistema')
            ->get();

        return response()->json($normas);
    }

    /**
     * Guardar nuevo hallazgo desde Gabinete.
     */
    public function storeHallazgoGabinete(Request $request)
    {
        $validated = $request->validate([
            'agenda_id' => 'required|exists:auditoria_agenda,id',
            'norma_referencia' => 'required|string',
            'requisito_referencia' => 'required|string',
            'hallazgo_detectado' => 'required|string',
            'evidencia_registrada' => 'nullable|string',
            'estado_cumplimiento' => 'required|in:No Conforme,Oportunidad de Mejora',
            'hallazgo_clasificacion' => 'nullable|string'
        ]);

        $checklist = new AuditoriaChecklist();
        $checklist->agenda_id = $validated['agenda_id'];
        $checklist->norma_referencia = $validated['norma_referencia'];
        $checklist->requisito_referencia = $validated['requisito_referencia'];
        $checklist->preg_pregunta = 'Hallazgo registrado en Gabinete'; // Valor por defecto
        $checklist->hallazgo_detectado = $validated['hallazgo_detectado'];
        $checklist->evidencia_registrada = $validated['evidencia_registrada'];
        $checklist->estado_cumplimiento = $validated['estado_cumplimiento'];

        // Pre-fill redacción fields to save time
        $checklist->hallazgo_redaccion = $validated['hallazgo_detectado'];
        $checklist->evidencia_redaccion = $validated['evidencia_registrada'];
        $checklist->criterio_redaccion = $validated['requisito_referencia'];

        if (!empty($validated['hallazgo_clasificacion'])) {
            $checklist->hallazgo_clasificacion = $validated['hallazgo_clasificacion'];
        }

        $checklist->save();

        return response()->json(['message' => 'Hallazgo creado correctamente', 'hallazgo' => $checklist]);
    }
}

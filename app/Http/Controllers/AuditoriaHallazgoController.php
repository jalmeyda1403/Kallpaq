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
}

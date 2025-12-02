<?php

namespace App\Http\Controllers;

use App\Models\RiesgoAccion;
use App\Models\Riesgo;
use Illuminate\Http\Request;

class RiesgoAccionController extends Controller
{
    public function index($riesgoId)
    {
        $acciones = RiesgoAccion::where('riesgo_id', $riesgoId)->with('reprogramaciones')->get();
        return response()->json($acciones);
    }

    public function store(Request $request, $riesgoId)
    {
        $validated = $request->validate([
            'ra_descripcion' => 'required|string',
            'ra_comentario' => 'nullable|string',
            'ra_fecha_inicio' => 'nullable|date',
            'ra_fecha_fin_planificada' => 'nullable|date',
            'ra_responsable' => 'nullable|string',
            'ra_responsable_correo' => 'nullable|email',
            'ra_estado' => 'required|in:programada,desestimada,en proceso,implementada',
        ]);

        $validated['riesgo_id'] = $riesgoId;

        // Obtener el ciclo actual del riesgo
        $riesgo = Riesgo::findOrFail($riesgoId);
        $validated['ra_ciclo'] = $riesgo->riesgo_ciclo ?? 1;

        $accion = RiesgoAccion::create($validated);

        return response()->json($accion, 201);
    }

    public function update(Request $request, $id)
    {
        $accion = RiesgoAccion::findOrFail($id);

        $validated = $request->validate([
            'ra_descripcion' => 'required|string',
            'ra_comentario' => 'nullable|string',
            'ra_fecha_inicio' => 'nullable|date',
            'ra_fecha_fin_planificada' => 'nullable|date',
            'ra_fecha_fin_reprogramada' => 'nullable|date',
            'ra_fecha_fin_cancelada' => 'nullable|date',
            'ra_fecha_fin_real' => 'nullable|date',
            'ra_justificacion' => 'nullable|string',
            'ra_evidencia' => 'nullable|string',
            'ra_responsable' => 'nullable|string',
            'ra_responsable_correo' => 'nullable|email',
            'ra_estado' => 'required|in:programada,desestimada,en proceso,implementada',
        ]);

        $accion->update($validated);

        return response()->json($accion);
    }

    public function destroy($id)
    {
        $accion = RiesgoAccion::findOrFail($id);
        $accion->delete();

        return response()->json(null, 204);
    }

    public function reprogramar(Request $request, $id)
    {
        $accion = RiesgoAccion::findOrFail($id);

        $request->validate([
            'actionType' => 'required|string|in:reprogramar,desestimar',
            'ra_justificacion' => 'required|string',
            'ra_fecha_fin_reprogramada' => 'required_if:actionType,reprogramar|date',
            'ra_evidencia' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        if ($request->actionType === 'reprogramar') {
            // Create a reprogramming request
            $reprogramacion = new \App\Models\RiesgoAccionReprogramacion();
            $reprogramacion->riesgo_accion_id = $accion->id;
            $reprogramacion->rar_fecha_anterior = $accion->ra_fecha_fin_reprogramada ?? $accion->ra_fecha_fin_planificada;
            $reprogramacion->rar_fecha_nueva = $request->ra_fecha_fin_reprogramada;
            $reprogramacion->rar_justificacion = $request->ra_justificacion;
            $reprogramacion->rar_estado = 'pendiente';

            if ($request->hasFile('ra_evidencia')) {
                $file = $request->file('ra_evidencia');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('riesgos/acciones/' . $id . '/reprogramaciones', $filename, 'public');
                $reprogramacion->rar_evidencia = $path;
            }

            $reprogramacion->save();

            // Reload action with reprogrammations
            $accion->load('reprogramaciones');

            return response()->json(['message' => 'Solicitud de reprogramación enviada con éxito.', 'accion' => $accion]);

        } else { // desestimar
            $accion->ra_justificacion = $request->ra_justificacion;
            $accion->ra_estado = 'desestimada';
            $accion->save();
            return response()->json(['message' => 'Acción desestimada con éxito.', 'accion' => $accion]);
        }
    }

    public function aprobarReprogramacion(Request $request, $id)
    {
        // $id is the ID of the reprogramming request
        $reprogramacion = \App\Models\RiesgoAccionReprogramacion::findOrFail($id);

        // TODO: Add permission check here (Admin/Especialista)

        $reprogramacion->rar_estado = 'aprobado';
        $reprogramacion->rar_aprobado_por = auth()->id();
        $reprogramacion->rar_fecha_aprobacion = now();
        $reprogramacion->rar_comentario_aprobacion = $request->comentario;
        $reprogramacion->save();

        // Update the parent action
        $accion = $reprogramacion->riesgoAccion;
        $accion->ra_fecha_fin_reprogramada = $reprogramacion->rar_fecha_nueva;
        $accion->ra_justificacion = $reprogramacion->rar_justificacion; // Optional: update latest justification on parent
        $accion->ra_evidencia = $reprogramacion->rar_evidencia; // Optional: update latest evidence on parent
        $accion->save();

        return response()->json(['message' => 'Reprogramación aprobada.', 'accion' => $accion->load('reprogramaciones')]);
    }

    public function rechazarReprogramacion(Request $request, $id)
    {
        $reprogramacion = \App\Models\RiesgoAccionReprogramacion::findOrFail($id);

        // TODO: Add permission check here

        $reprogramacion->rar_estado = 'rechazado';
        $reprogramacion->rar_aprobado_por = auth()->id(); // Rejected by
        $reprogramacion->rar_fecha_aprobacion = now(); // Rejection date
        $reprogramacion->rar_comentario_aprobacion = $request->comentario;
        $reprogramacion->save();

        return response()->json(['message' => 'Reprogramación rechazada.', 'accion' => $reprogramacion->riesgoAccion->load('reprogramaciones')]);
    }
}

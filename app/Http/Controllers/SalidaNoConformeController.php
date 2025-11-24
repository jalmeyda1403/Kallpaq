<?php

namespace App\Http\Controllers;

use App\Models\SalidaNoConforme;
use App\Models\SncAccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalidaNoConformeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SalidaNoConforme::with(['detector', 'responsable', 'procesos', 'acciones.responsable']);

        // Aplicar filtros
        $query->filterByDescripcion($request->buscar_snc)
              ->filterByEstado($request->estado)
              ->filterByTipo($request->tipo)
              ->filterByClasificacion($request->clasificacion);

        // Filtro por rango de fechas
        if ($request->fecha_desde) {
            $query->where('snc_fecha_deteccion', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->where('snc_fecha_deteccion', '<=', $request->fecha_hasta);
        }

        $salidasNC = $query->orderBy('created_at', 'desc')->get();

        return response()->json($salidasNC);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'snc_descripcion' => 'required|string',
            'snc_producto_servicio' => 'required|string',
            'snc_cantidad_afectada' => 'nullable|numeric',
            'snc_fecha_deteccion' => 'required|date',
            'snc_detectado_por' => 'required|exists:users,id',
            'snc_responsable_id' => 'nullable|exists:users,id',
            'snc_tipo' => 'required|in:producto,servicio,proceso',
            'snc_origen' => 'required|in:producción,inspección,cliente,auditoría interna,auditoría externa,otro',
            'snc_clasificacion' => 'required|in:crítica,mayor,menor',
            'snc_tratamiento' => 'nullable|in:corrección,reproceso,reclasificación,rechazo,concesión,pendiente',
            'snc_descripcion_tratamiento' => 'nullable|string',
            'snc_fecha_tratamiento' => 'nullable|date',
            'snc_costo_estimado' => 'nullable|numeric',
            'snc_requiere_accion_correctiva' => 'boolean',
            'snc_observaciones' => 'nullable|string',
            'procesos' => 'nullable|array',
            'procesos.*' => 'exists:procesos,id',
        ]);

        DB::beginTransaction();
        try {
            // Generar código único
            $year = date('Y');
            $lastSnc = SalidaNoConforme::whereYear('created_at', $year)
                                       ->orderBy('id', 'desc')
                                       ->first();
            
            $nextNumber = $lastSnc ? (intval(substr($lastSnc->snc_codigo, -3)) + 1) : 1;
            $validated['snc_codigo'] = sprintf('SNC-%s-%03d', $year, $nextNumber);

            // Crear SNC
            $snc = SalidaNoConforme::create($validated);

            // Asociar procesos
            if ($request->has('procesos')) {
                $snc->procesos()->attach($request->procesos);
            }

            DB::commit();

            return response()->json([
                'message' => 'Salida No Conforme creada exitosamente',
                'data' => $snc->load(['detector', 'responsable', 'procesos'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la Salida No Conforme',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $snc = SalidaNoConforme::with(['detector', 'responsable', 'procesos', 'acciones.responsable'])
                               ->findOrFail($id);

        return response()->json($snc);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $snc = SalidaNoConforme::findOrFail($id);

        $validated = $request->validate([
            'snc_descripcion' => 'sometimes|required|string',
            'snc_producto_servicio' => 'sometimes|required|string',
            'snc_cantidad_afectada' => 'nullable|numeric',
            'snc_fecha_deteccion' => 'sometimes|required|date',
            'snc_responsable_id' => 'nullable|exists:users,id',
            'snc_tipo' => 'sometimes|required|in:producto,servicio,proceso',
            'snc_origen' => 'sometimes|required|in:producción,inspección,cliente,auditoría interna,auditoría externa,otro',
            'snc_clasificacion' => 'sometimes|required|in:crítica,mayor,menor',
            'snc_tratamiento' => 'nullable|in:corrección,reproceso,reclasificación,rechazo,concesión,pendiente',
            'snc_descripcion_tratamiento' => 'nullable|string',
            'snc_fecha_tratamiento' => 'nullable|date',
            'snc_costo_estimado' => 'nullable|numeric',
            'snc_estado' => 'sometimes|required|in:registrada,en análisis,en tratamiento,tratada,cerrada',
            'snc_requiere_accion_correctiva' => 'boolean',
            'snc_fecha_cierre' => 'nullable|date',
            'snc_observaciones' => 'nullable|string',
            'procesos' => 'nullable|array',
            'procesos.*' => 'exists:procesos,id',
        ]);

        DB::beginTransaction();
        try {
            $snc->update($validated);

            // Sincronizar procesos si se enviaron
            if ($request->has('procesos')) {
                $snc->procesos()->sync($request->procesos);
            }

            DB::commit();

            return response()->json([
                'message' => 'Salida No Conforme actualizada exitosamente',
                'data' => $snc->load(['detector', 'responsable', 'procesos'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la Salida No Conforme',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $snc = SalidaNoConforme::findOrFail($id);
            $snc->delete();

            return response()->json([
                'message' => 'Salida No Conforme eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la Salida No Conforme',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new action for a SNC.
     */
    public function storeAccion(Request $request, $id)
    {
        $validated = $request->validate([
            'accion_descripcion' => 'required|string',
            'accion_responsable_id' => 'required|exists:users,id',
            'accion_fecha_planificada' => 'required|date',
            'accion_fecha_real' => 'nullable|date',
            'accion_estado' => 'nullable|in:planificada,en ejecución,completada,cancelada',
            'accion_evidencia' => 'nullable|string',
        ]);

        $validated['snc_id'] = $id;

        $accion = SncAccion::create($validated);

        return response()->json([
            'message' => 'Acción creada exitosamente',
            'data' => $accion->load('responsable')
        ], 201);
    }

    /**
     * Update an action.
     */
    public function updateAccion(Request $request, $accionId)
    {
        $accion = SncAccion::findOrFail($accionId);

        $validated = $request->validate([
            'accion_descripcion' => 'sometimes|required|string',
            'accion_responsable_id' => 'sometimes|required|exists:users,id',
            'accion_fecha_planificada' => 'sometimes|required|date',
            'accion_fecha_real' => 'nullable|date',
            'accion_estado' => 'sometimes|required|in:planificada,en ejecución,completada,cancelada',
            'accion_evidencia' => 'nullable|string',
        ]);

        $accion->update($validated);

        return response()->json([
            'message' => 'Acción actualizada exitosamente',
            'data' => $accion->load('responsable')
        ]);
    }
}

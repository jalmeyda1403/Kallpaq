<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\SalidaNoConforme;
use App\Models\SncAccion;
use App\Models\User;



class SalidaNoConformeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SalidaNoConforme::with(['proceso']);

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
            'snc_cantidad_afectada' => 'nullable|numeric',
            'snc_fecha_deteccion' => 'required|date',
            'snc_responsable' => 'nullable|string',
            'snc_origen' => 'required|in:cliente,auditoría interna,auditoría externa,otro',
            'snc_clasificacion' => 'required|in:crítica,mayor,menor',
            'snc_tratamiento' => 'nullable|in:corrección,reproceso,reclasificación,rechazo,concesión,pendiente',
            'snc_descripcion_tratamiento' => 'nullable|string',
            'snc_fecha_tratamiento' => 'nullable|date',
            'snc_costo_estimado' => 'nullable|numeric',
            'snc_estado' => 'required|in:registrada,en tratamiento,tratada',
            'snc_requiere_accion_correctiva' => 'nullable|boolean',
            'snc_observaciones' => 'nullable|string',
            'proceso_id' => 'nullable|exists:procesos,id',
            'snc_evidencia' => 'sometimes|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Crear SNC primero (sin evidencia)
            $snc = SalidaNoConforme::create(array_filter($validated, function ($key) {
                return $key !== 'snc_evidencia';
            }, ARRAY_FILTER_USE_KEY));

            // Manejar la subida de archivos de evidencia si existen
            if ($request->hasFile('snc_evidencia')) {
                $files = $request->file('snc_evidencia');
                $paths = [];

                if (is_array($files)) {
                    // Son múltiples archivos
                    foreach ($files as $file) {
                        $path = $file->store("snc/{$snc->id}", 'public');
                        $paths[] = $path;
                    }
                } else {
                    // Es un solo archivo
                    $path = $files->store("snc/{$snc->id}", 'public');
                    $paths[] = $path;
                }

                // Si hay múltiples archivos, almacenar como JSON
                if (count($paths) > 1) {
                    $snc->snc_evidencia = json_encode($paths);
                } else {
                    $snc->snc_evidencia = $paths[0];
                }
                $snc->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Salida No Conforme creada exitosamente',
                'data' => $snc->load(['proceso'])
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
        $snc = SalidaNoConforme::with(['proceso'])
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
            'snc_cantidad_afectada' => 'nullable|numeric',
            'snc_fecha_deteccion' => 'sometimes|required|date',
            'snc_responsable' => 'nullable|string',
            'snc_origen' => 'sometimes|required|in:cliente,auditoría interna,auditoría externa,otro',
            'snc_clasificacion' => 'sometimes|required|in:crítica,mayor,menor',
            'snc_tratamiento' => 'nullable|in:corrección,reproceso,reclasificación,rechazo,concesión,pendiente',
            'snc_descripcion_tratamiento' => 'nullable|string',
            'snc_fecha_tratamiento' => 'nullable|date',
            'snc_costo_estimado' => 'nullable|numeric',
            'snc_estado' => 'sometimes|required|in:registrada,en tratamiento,tratada',
            'snc_requiere_accion_correctiva' => 'nullable|boolean',
            'snc_fecha_cierre' => 'nullable|date',
            'snc_observaciones' => 'nullable|string',
            'proceso_id' => 'nullable|exists:procesos,id',
            'snc_evidencia' => 'sometimes|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Manejar la subida de archivos de evidencia si existen
            if ($request->hasFile('snc_evidencia')) {
                $files = $request->file('snc_evidencia');
                $paths = [];

                if (is_array($files)) {
                    // Son múltiples archivos
                    foreach ($files as $file) {
                        $path = $file->store("snc/{$snc->id}", 'public');
                        $paths[] = $path;
                    }
                } else {
                    // Es un solo archivo
                    $path = $files->store("snc/{$snc->id}", 'public');
                    $paths[] = $path;
                }

                // Si hay múltiples archivos, almacenar como JSON
                if (count($paths) > 1) {
                    $validated['snc_evidencia'] = json_encode($paths);
                } else {
                    $validated['snc_evidencia'] = $paths[0];
                }
            } else {
                // Si no hay archivo nuevo pero el campo snc_evidencia contenía un objeto File,
                // debemos asegurarnos de eliminarlo o reemplazarlo para evitar problemas
                if (isset($validated['snc_evidencia']) && is_object($validated['snc_evidencia'])) {
                    unset($validated['snc_evidencia']);  // Remover el objeto File del array
                }
            }

            $snc->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Salida No Conforme actualizada exitosamente',
                'data' => $snc->load(['proceso'])
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

}

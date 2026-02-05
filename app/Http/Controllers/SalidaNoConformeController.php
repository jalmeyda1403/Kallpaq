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

        // Filtro para Facilitador: solo procesos vinculados a su OUO
        if (Auth::user()->hasRole('facilitador')) {
            $userOuoIds = Auth::user()->ouos()->pluck('ouos.id');
            // Obtener los IDs de procesos relacionados con las OUOs del usuario
            $procesoIds = DB::table('procesos_ouo')->whereIn('id_ouo', $userOuoIds)->pluck('id_proceso');
            $query->whereIn('proceso_id', $procesoIds);
        }

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
            'snc_tratamiento' => 'nullable|in:corrección,concesion o aceptación,rechazo,sustitucion',
            'snc_descripcion_tratamiento' => 'nullable|string',
            'snc_fecha_tratamiento' => 'nullable|date',
            'snc_costo_estimado' => 'nullable|numeric',
            'snc_estado' => 'required|in:registrada,en tratamiento,tratada',
            'snc_requiere_accion_correctiva' => 'nullable|boolean',
            'snc_observaciones' => 'nullable|string',
            'proceso_id' => 'nullable|exists:procesos,id',
            'snc_archivos' => 'nullable',
            'snc_archivos.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Crear SNC primero (sin archivos)
            $snc = SalidaNoConforme::create(array_filter($validated, function ($key) {
                return $key !== 'snc_archivos';
            }, ARRAY_FILTER_USE_KEY));

            // Manejar la subida de archivos de registro (snc_archivos)
            if ($request->hasFile('snc_archivos')) {
                $files = $request->file('snc_archivos');
                $fileData = $this->processNewFiles($files, $snc);
                $snc->snc_archivos = $fileData; // El cast 'array' se encarga de la conversión
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
            'snc_tratamiento' => 'nullable|in:corrección,concesion o aceptación,rechazo,sustitucion',
            'snc_descripcion_tratamiento' => 'nullable|string',
            'snc_fecha_tratamiento' => 'nullable|date',
            'snc_costo_estimado' => 'nullable|numeric',
            'snc_estado' => 'sometimes|required|in:registrada,en tratamiento,tratada',
            'snc_requiere_accion_correctiva' => 'nullable|boolean',
            'snc_fecha_cierre' => 'nullable|date',
            'snc_observaciones' => 'nullable|string',
            'proceso_id' => 'nullable|exists:procesos,id',

            // Validación para archivos de registro
            'snc_archivos' => 'nullable',
            'snc_archivos.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',

            // Validación para evidencias de tratamiento
            'snc_evidencias' => 'nullable',
            'snc_evidencias.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // 1. Manejo de archivos de registro (snc_archivos)
            if ($request->has('update_registration')) {
                $this->handleFileUpdate($request, $snc, 'snc_archivos', 'existing_archivos');
            }

            // 2. Manejo de evidencias de tratamiento (snc_evidencias)
            if ($request->has('update_treatment')) {
                $this->handleFileUpdate($request, $snc, 'snc_evidencias', 'existing_evidencias');
            }

            // Actualizar el resto de campos
            $snc->update(array_filter($validated, function ($key) {
                return !in_array($key, ['snc_archivos', 'snc_evidencias']);
            }, ARRAY_FILTER_USE_KEY));

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

    /**
     * Helper to process new file uploads
     */
    private function processNewFiles($files, $snc)
    {
        $sncId = $snc->id;
        $procesoId = $snc->proceso_id ?? 'sin-proceso';
        $fileData = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                $path = $file->store("satisfaccion/snc/{$procesoId}/{$sncId}", 'public');
                $fileData[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName()
                ];
            }
        } else {
            $path = $files->store("satisfaccion/snc/{$procesoId}/{$sncId}", 'public');
            $fileData[] = [
                'path' => $path,
                'name' => $files->getClientOriginalName()
            ];
        }
        return $fileData;
    }

    /**
     * Helper to handle file updates (deletion and addition)
     */
    private function handleFileUpdate(Request $request, $snc, $dbField, $inputField)
    {
        $currentFiles = [];
        if ($snc->$dbField) {
            // El modelo tiene cast 'array', así que ya viene como array
            $decoded = $snc->$dbField;
            if (is_array($decoded)) {
                // Normalizar la estructura: asegurar que todos los elementos tengan 'path' y 'name'
                foreach ($decoded as $item) {
                    if (is_array($item) && isset($item['path'])) {
                        $currentFiles[] = $item;
                    } elseif (is_string($item)) {
                        $currentFiles[] = ['path' => $item, 'name' => basename($item)];
                    }
                }
            }
        }

        $keptPaths = $request->input($inputField, []);
        if (!is_array($keptPaths)) {
            $keptPaths = [];
        }

        $finalFiles = [];
        foreach ($currentFiles as $file) {
            if (in_array($file['path'], $keptPaths)) {
                $finalFiles[] = $file;
            } else {
                if (Storage::disk('public')->exists($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }

        // Verificar si hay archivos nuevos para subir
        if ($request->hasFile($dbField)) {
            $files = $request->file($dbField);
            $newFiles = $this->processNewFiles($files, $snc);
            $finalFiles = array_merge($finalFiles, $newFiles);
        }

        // Guardar los archivos (el cast 'array' del modelo se encargará de convertir a JSON)
        if (count($finalFiles) > 0) {
            $snc->$dbField = $finalFiles;
        } else {
            $snc->$dbField = null;
        }
        $snc->save();
    }
}

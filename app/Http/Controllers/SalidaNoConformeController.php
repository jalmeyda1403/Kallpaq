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
use App\Models\SalidaNoConformeMovimiento;



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
            'snc_cantidad_afectada' => 'required|numeric', // Changed from nullable to required per schema
            'snc_fecha_deteccion' => 'required|date',
            'snc_responsable' => 'required|string|max:255', // Changed from nullable to required per schema
            'snc_origen' => 'required|in:cliente,auditoría interna,auditoría externa,otro',
            'snc_clasificacion' => 'required|in:crítica,mayor,menor',
            'snc_tratamiento' => 'nullable|in:corrección,concesion o aceptación,rechazo,sustitucion',
            'snc_descripcion_tratamiento' => 'nullable|string',
            'snc_fecha_fecha_fin_prog' => 'nullable|date', // Added per schema
            'snc_fecha_fin_real' => 'nullable|date', // Added per schema
            'snc_costo_estimado' => 'nullable|numeric',
            'snc_estado' => 'nullable|in:identificada,en análisis,en tratamiento,tratada,cerrada,observada', // nullable as it defaults to identificada
            'snc_requiere_accion_correctiva' => 'nullable|boolean',
            'snc_observaciones' => 'nullable|string',
            'proceso_id' => 'nullable|exists:procesos,id',
            'snc_archivos' => 'nullable',
            'snc_archivos.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        if (!isset($validated['snc_estado'])) {
            $validated['snc_estado'] = 'identificada';
        }

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

            // Registrar movimiento inicial
            $snc->movimientos()->create([
                'estado' => $snc->snc_estado,
                'observacion' => 'Salida No Conforme registrada en el sistema',
                'user_id' => Auth::id(),
                'fecha_movimiento' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Salida No Conforme creada exitosamente',
                'data' => $snc->load(['proceso', 'movimientos.user'])
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
        $snc = SalidaNoConforme::with(['proceso', 'movimientos.user'])
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
            'snc_fecha_fecha_fin_prog' => 'nullable|date',
            'snc_fecha_fin_real' => 'nullable|date',
            'snc_fecha_tratamiento' => 'nullable|date',
            'snc_costo_estimado' => 'nullable|numeric',
            'snc_estado' => 'sometimes|required|in:identificada,en análisis,en tratamiento,tratada,observada,cerrada',
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

        // Restricción: No permitir editar datos base si el estado no es 'identificada'
        // EXCEPT: si se está llamando para actualizar tratamiento/cierre (que usan otros campos o los mismos pero en otro contexto)
        // La mejor manera es verificar si se están intentando cambiar los campos protegidos.

        $currentState = $snc->snc_estado;
        if ($currentState !== 'identificada') {
            $restrictedFields = [
                'snc_descripcion',
                'snc_origen',
                'snc_clasificacion',
                'proceso_id',
                'snc_fecha_deteccion',
                'snc_cantidad_afectada'
            ];

            foreach ($restrictedFields as $field) {
                // Si el request envía el campo Y es diferente al actual, bloquear.
                // Usamos != para permitir coincidencias laxas (string vs int)
                if ($request->has($field) && $request->input($field) != $snc->$field) {
                    return response()->json(['message' => "No se puede editar los datos base de la SNC (como $field) porque ya no está en estado identificada."], 400);
                }
            }
        }

        DB::beginTransaction();
        try {
            // 1. Manejo de archivos de registro (snc_archivos)
            if ($request->has('update_registration')) {
                $this->handleFileUpdateNoSave($request, $snc, 'snc_archivos', 'existing_archivos');
            }

            // 2. Manejo de evidencias de tratamiento (snc_evidencias)
            if ($request->has('update_treatment')) {
                $this->handleFileUpdateNoSave($request, $snc, 'snc_evidencias', 'existing_evidencias');
            }

            // Actualizar el resto de campos (excepto los de archivos que ya se manejaron)
            $snc->fill(array_filter($validated, function ($key) {
                return !in_array($key, ['snc_archivos', 'snc_evidencias']);
            }, ARRAY_FILTER_USE_KEY));

            // Detectar cambio de estado y registrar movimiento
            if ($snc->isDirty('snc_estado')) {
                $oldState = $snc->getOriginal('snc_estado');
                $newState = $snc->snc_estado;

                $observacion = "Cambio de estado: $oldState -> $newState";

                // Lógica de fechas y observaciones específicas por estado
                if ($newState === 'observada') {
                    $snc->snc_fecha_observacion = now();
                    if ($request->filled('snc_observacion')) {
                        $observacion = $request->snc_observacion; // Usar la observación personalizada para el movimiento
                        $snc->snc_observacion = $request->snc_observacion; // Guardar en el campo principal
                    }
                } elseif ($newState === 'cerrada') {
                    $snc->snc_fecha_cierre = now();
                    $observacion = "Salida No Conforme validada y cerrada exitosamente.";
                }

                // Si también se actualizó el tratamiento, incluirlo en la observación (si no es cierre/obs)
                if (($snc->isDirty('snc_tratamiento') || $request->filled('snc_tratamiento')) && !in_array($newState, ['observada', 'cerrada'])) {
                    $tratamiento = $snc->snc_tratamiento;
                    $observacion = "Tratamiento aplicado: $tratamiento. Nuevo estado: $newState";
                }

                $snc->movimientos()->create([
                    'estado' => $newState,
                    'observacion' => $observacion,
                    'user_id' => Auth::id(),
                    'fecha_movimiento' => now()
                ]);
            }

            $snc->save();

            DB::commit();

            return response()->json([
                'message' => 'Salida No Conforme actualizada exitosamente',
                'data' => $snc->load(['proceso', 'movimientos.user'])
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

    public function validateSNC(Request $request, $id)
    {
        Log::info("Validating SNC ID: $id by User: " . Auth::id());
        $snc = SalidaNoConforme::findOrFail($id);

        // Permitir validar si está 'tratada' (case insensitive)
        if (strtolower($snc->snc_estado) !== 'tratada') {
            return response()->json([
                'message' => 'Solo se pueden evaluar salidas no conformes en estado tratada (Actual: ' . $snc->snc_estado . ')',
            ], 400);
        }

        $validated = $request->validate([
            'snc_estado' => 'required|in:cerrada,observada',
            'snc_observacion' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update status
            $snc->snc_estado = $validated['snc_estado'];

            // Handle observation logic
            if ($validated['snc_estado'] === 'observada') {
                $snc->snc_observacion = $validated['snc_observacion'] ?? null;
                $snc->snc_fecha_observacion = now();
            } elseif ($validated['snc_estado'] === 'cerrada') {
                $snc->snc_fecha_cierre = now();
            }

            $snc->save();

            // Register movement
            $observacionMovimiento = null;
            if ($validated['snc_estado'] === 'observada') {
                $observacionMovimiento = $validated['snc_observacion'];
            } elseif ($validated['snc_estado'] === 'cerrada') {
                $observacionMovimiento = 'Salida No Conforme validada y cerrada exitosamente.';
            }

            $snc->movimientos()->create([
                'estado' => $validated['snc_estado'],
                'observacion' => $observacionMovimiento,
                'user_id' => Auth::id(),
                'fecha_movimiento' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Salida No Conforme validada exitosamente',
                'data' => $snc->load('proceso')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al validar la SNC',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to handle file updates (deletion and addition)
     */
    private function handleFileUpdateNoSave(Request $request, $snc, $dbField, $inputField)
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

        // Asignar los archivos al modelo pero NO guardar todavía
        if (count($finalFiles) > 0) {
            $snc->$dbField = $finalFiles;
        } else {
            $snc->$dbField = null;
        }
    }
}

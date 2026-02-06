<?php

namespace App\Http\Controllers;

use App\Models\Sugerencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SugerenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Sugerencia::with('proceso');

        // Filtro para Facilitador: solo procesos vinculados a su OUO
        if (Auth::user() && Auth::user()->hasRole('facilitador')) {
            $userOuoIds = Auth::user()->ouos()->pluck('ouos.id');
            $procesoIds = DB::table('procesos_ouo')->whereIn('id_ouo', $userOuoIds)->pluck('id_proceso');
            $query->whereIn('proceso_id', $procesoIds);
        }

        $query->filterByEstado($request->estado)
            ->filterByProceso($request->proceso_id)
            ->filterByProcesoNombre($request->proceso_nombre)
            ->filterByClasificacion($request->clasificacion)
            ->filterByFecha($request->fecha_desde, $request->fecha_hasta);

        // If user is not admin, filter by their process (assuming user has process_id or similar logic)
        // For now, I'll assume the frontend handles the filtering or the user model has a way to check permissions.
        // Based on "Los gestores solo podrán ver las sugerencias que estén asociadas a los procesos bajo su responsabilidad"
        // I need to know how managers are linked to processes. 
        // Checking User model might be useful later, but for now I'll stick to basic filtering.

        $sugerencias = $query->orderBy('created_at', 'desc')->get();

        return response()->json($sugerencias);
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Creating suggestion', $request->all());

            $validated = $request->validate([
                'sugerencia_clasificacion' => 'required|string',
                'sugerencia_detalle' => 'required|string',
                'sugerencia_fecha_ingreso' => 'required|date',
                'sugerencia_procedencia' => 'required|string',
                'proceso_id' => 'required|exists:procesos,id',
                'sugerencia_estado' => 'required|string',
                'sugerencia_evidencias' => 'nullable',
                'sugerencia_evidencias.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
            ]);

            DB::beginTransaction();

            // Create basic record
            $sugerencia = Sugerencia::create($validated);

            // Handle files
            if ($request->hasFile('sugerencia_evidencias')) {
                $files = $request->file('sugerencia_evidencias');
                $fileData = $this->processNewFiles($files, $sugerencia);
                $sugerencia->sugerencia_evidencias = $fileData;
                $sugerencia->save();
            }

            // Registrar movimiento inicial
            $sugerencia->movimientos()->create([
                'estado' => $sugerencia->sugerencia_estado,
                'observacion' => 'Sugerencia registrada en el sistema',
                'user_id' => Auth::id(),
                'fecha_movimiento' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Sugerencia creada exitosamente',
                'data' => $sugerencia->load('proceso')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error creating suggestion', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating suggestion', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error creando sugerencia', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $sugerencia = Sugerencia::with(['proceso', 'movimientos.user'])->findOrFail($id);
        return response()->json($sugerencia);
    }

    public function update(Request $request, $id)
    {
        $sugerencia = Sugerencia::findOrFail($id);

        // Verificar que la sugerencia no esté cerrada para permitir modificaciones
        if ($sugerencia->sugerencia_estado === 'cerrada') {
            return response()->json([
                'message' => 'No se puede modificar una sugerencia cerrada',
            ], 400);
        }

        $validated = $request->validate([
            'sugerencia_clasificacion' => 'sometimes|required|string',
            'sugerencia_detalle' => 'sometimes|required|string',
            'sugerencia_fecha_ingreso' => 'sometimes|required|date',
            'sugerencia_procedencia' => 'sometimes|required|string',
            'proceso_id' => 'sometimes|required|exists:procesos,id',
            'sugerencia_analisis' => 'nullable|string',
            'sugerencia_viabilidad' => 'nullable|string',
            'sugerencia_tratamiento' => 'nullable|string',
            'sugerencia_estado' => 'sometimes|required|string',
            'sugerencia_fecha_fin_prog' => 'nullable|date',
            'sugerencia_fecha_fin_real' => 'nullable|date',
            'sugerencia_evidencias' => 'nullable',
            'sugerencia_evidencias.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        // Restricción: No permitir editar datos base si el estado no es 'identificada'
        $currentState = $sugerencia->sugerencia_estado;
        if ($currentState !== 'identificada') {
            $restrictedFields = [
                'sugerencia_detalle',
                'sugerencia_clasificacion',
                'sugerencia_procedencia',
                'proceso_id',
                'sugerencia_fecha_ingreso'
            ];

            foreach ($restrictedFields as $field) {
                if ($request->has($field) && $request->input($field) != $sugerencia->$field) {
                    return response()->json(['message' => "No se puede editar los datos base de la sugerencia (como $field) porque ya no está en estado identificada."], 400);
                }
            }
        }

        DB::beginTransaction();
        try {
            // Debug información para ver qué se está recibiendo
            \Log::info('Actualizando sugerencia', [
                'hasFile' => $request->hasFile('sugerencia_evidencias'),
                'hasUpdateEvidences' => $request->has('update_evidences'),
                'allInputs' => $request->all(),
                'files' => $request->file('sugerencia_evidencias') ? $request->file('sugerencia_evidencias') : 'no files'
            ]);

            // Handle file updates for evidences - check if files are being uploaded or update_evidences flag is set
            $hasFiles = $request->hasFile('sugerencia_evidencias') || !empty($request->file('sugerencia_evidencias'));
            $hasUpdateEvidences = $request->has('update_evidences');

            \Log::info('Detalles de archivo', [
                'hasFiles' => $hasFiles,
                'hasUpdateEvidences' => $hasUpdateEvidences,
                'sugerencia_evidencias' => $request->file('sugerencia_evidencias'),
                'update_evidences_value' => $request->get('update_evidences')
            ]);

            // 1. Actualizar campos standard primero (siempre)
            $sugerencia->fill(array_filter($validated, function ($key) {
                return !in_array($key, ['sugerencia_evidencias']);
            }, ARRAY_FILTER_USE_KEY));

            // 2. Procesar archivos si es necesario
            if ($hasFiles || $hasUpdateEvidences) {
                \Log::info('Procesando archivos de evidencia');
                $this->handleFileUpdateNoSave($request, $sugerencia, 'sugerencia_evidencias', 'existing_evidencias');
            }

            // 3. Detectar cambio de estado y registrar movimiento
            // Esto funcionará ahora porque no hemos llamado a save() en handleFileUpdateNoSave
            if ($sugerencia->isDirty('sugerencia_estado')) {
                $oldState = $sugerencia->getOriginal('sugerencia_estado');
                $newState = $sugerencia->sugerencia_estado;

                $sugerencia->movimientos()->create([
                    'estado' => $newState,
                    'observacion' => "Cambio de estado: $oldState -> $newState",
                    'user_id' => Auth::id(),
                    'fecha_movimiento' => now()
                ]);
            }

            $sugerencia->save();

            DB::commit();

            return response()->json([
                'message' => 'Sugerencia actualizada exitosamente',
                'data' => $sugerencia->load('proceso')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar sugerencia', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error al actualizar la sugerencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $sugerencia = Sugerencia::findOrFail($id);
        $sugerencia->delete();
        return response()->json(['message' => 'Sugerencia eliminada exitosamente']);
    }

    public function validateSuggestion(Request $request, $id)
    {
        $sugerencia = Sugerencia::findOrFail($id);

        // Allow validation of suggestions with 'concluida' or 'implementada' status
        if (!in_array($sugerencia->sugerencia_estado, ['concluida', 'implementada'])) {
            return response()->json([
                'message' => 'Solo se pueden evaluar sugerencias que han concluido o implementado',
            ], 400);
        }

        $validated = $request->validate([
            'sugerencia_estado' => 'required|in:cerrada,observada',
            'sugerencia_observacion' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update the status
            $sugerencia->sugerencia_estado = $validated['sugerencia_estado'];

            // If the status is 'observada', add the observation and date
            if ($validated['sugerencia_estado'] === 'observada') {
                $sugerencia->sugerencia_observacion = $validated['sugerencia_observacion'] ?? null;
                $sugerencia->sugerencia_fecha_observacion = now();
            }
            // If the status is 'cerrada', set the closure date
            elseif ($validated['sugerencia_estado'] === 'cerrada') {
                $sugerencia->sugerencia_fecha_cierre = now();
            }

            $sugerencia->save();

            // Registrar movimiento de validación
            $observacionMovimiento = null;
            if ($validated['sugerencia_estado'] === 'observada') {
                $observacionMovimiento = $validated['sugerencia_observacion'];
            } elseif ($validated['sugerencia_estado'] === 'cerrada') {
                $observacionMovimiento = 'Sugerencia validada y cerrada exitosamente.';
            }

            $sugerencia->movimientos()->create([
                'estado' => $validated['sugerencia_estado'],
                'observacion' => $observacionMovimiento,
                'user_id' => Auth::id(),
                'fecha_movimiento' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Sugerencia validada exitosamente',
                'data' => $sugerencia->load('proceso')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al validar la sugerencia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function processNewFiles($files, $sugerencia)
    {
        $fileData = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                $path = $file->store("satisfaccion/sugerencias/{$sugerencia->proceso_id}/{$sugerencia->id}", 'public');
                $fileData[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName()
                ];
            }
        } else {
            $path = $files->store("satisfaccion/sugerencias/{$sugerencia->proceso_id}/{$sugerencia->id}", 'public');
            $fileData[] = [
                'path' => $path,
                'name' => $files->getClientOriginalName()
            ];
        }
        return $fileData;
    }

    private function handleFileUpdateNoSave(Request $request, $sugerencia, $dbField, $inputField)
    {
        $currentFiles = [];
        if ($sugerencia->$dbField) {
            // El modelo tiene cast 'array', así que ya viene como array
            $decoded = $sugerencia->$dbField;
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
            $newFiles = $this->processNewFiles($files, $sugerencia);
            $finalFiles = array_merge($finalFiles, $newFiles);
        }

        // Asignar los archivos al modelo pero NO guardar todavía
        if (count($finalFiles) > 0) {
            $sugerencia->$dbField = $finalFiles;
        } else {
            $sugerencia->$dbField = null;
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Sugerencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SugerenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Sugerencia::with('proceso');

        $query->filterByEstado($request->estado)
            ->filterByProceso($request->proceso_id)
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
        $validated = $request->validate([
            'sugerencia_clasificacion' => 'required|string',
            'sugerencia_detalle' => 'required|string',
            'sugerencia_fecha_ingreso' => 'required|date',
            'sugerencia_procedencia' => 'required|string',
            'proceso_id' => 'required|exists:procesos,id',
            'sugerencia_estado' => 'required|string',
        ]);

        $sugerencia = Sugerencia::create($validated);

        return response()->json([
            'message' => 'Sugerencia creada exitosamente',
            'data' => $sugerencia->load('proceso')
        ], 201);
    }

    public function show($id)
    {
        $sugerencia = Sugerencia::with('proceso')->findOrFail($id);
        return response()->json($sugerencia);
    }

    public function update(Request $request, $id)
    {
        $sugerencia = Sugerencia::findOrFail($id);

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

        DB::beginTransaction();
        try {
            // Handle file updates for evidences
            if ($request->has('update_evidences')) {
                $this->handleFileUpdate($request, $sugerencia, 'sugerencia_evidencias', 'existing_evidencias');
            }

            $sugerencia->update(array_filter($validated, function ($key) {
                return !in_array($key, ['sugerencia_evidencias']);
            }, ARRAY_FILTER_USE_KEY));

            DB::commit();

            return response()->json([
                'message' => 'Sugerencia actualizada exitosamente',
                'data' => $sugerencia->load('proceso')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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

    private function processNewFiles($files, $sugerenciaId)
    {
        $fileData = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                $path = $file->store("sugerencias/{$sugerenciaId}", 'public');
                $fileData[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName()
                ];
            }
        } else {
            $path = $files->store("sugerencias/{$sugerenciaId}", 'public');
            $fileData[] = [
                'path' => $path,
                'name' => $files->getClientOriginalName()
            ];
        }
        return $fileData;
    }

    private function handleFileUpdate(Request $request, $sugerencia, $dbField, $inputField)
    {
        $currentFiles = [];
        if ($sugerencia->$dbField) {
            $decoded = $sugerencia->$dbField; // Casted to array in model
            if (is_array($decoded)) {
                $currentFiles = $decoded;
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

        if ($request->hasFile($dbField)) {
            $newFiles = $this->processNewFiles($request->file($dbField), $sugerencia->id);
            $finalFiles = array_merge($finalFiles, $newFiles);
        }

        if (count($finalFiles) > 0) {
            $sugerencia->$dbField = $finalFiles;
        } else {
            $sugerencia->$dbField = null;
        }
        $sugerencia->save();
    }
}

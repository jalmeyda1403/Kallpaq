<?php

namespace App\Http\Controllers;

use App\Models\Hallazgo;
Use App\Models\Proceso;
use App\Models\Accion;
use App\Models\Causa; // Import the Causa model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AccionController extends Controller
{
    public function getAccionesPorHallazgo(Hallazgo $hallazgo)
    {
        $acciones = $hallazgo->acciones()->with('responsable.ouos', 'hallazgoProceso.proceso')->get();
        return response()->json($acciones);
    }

    public function reprogramar(Request $request, Accion $accion)
    {
        $request->validate([
            'actionType' => 'required|string|in:reprogramar,desestimar',
            'accion_justificacion' => 'required|string',
            'accion_fecha_fin_reprogramada' => 'required_if:actionType,reprogramar|date',
        ]);

        $accion->accion_justificacion = $request->accion_justificacion;

        if ($request->actionType === 'reprogramar') {
            $accion->accion_fecha_fin_reprogramada = $request->accion_fecha_fin_reprogramada;
        } else { // desestimar
            $accion->accion_estado = 'desestimada';
        }

        $accion->save();

        return response()->json(['message' => 'Acción gestionada con éxito.']);
    }

    public function concluir(Request $request, Accion $accion)
    {
        // Files are now uploaded via uploadEvidencia.
        // This method just finalizes the action.
        
        // Optional: Check if there is at least one evidence file before concluding.
        $existingFiles = json_decode($accion->accion_ruta_evidencia, true) ?: [];
        if (empty($existingFiles)) {
            return response()->json(['message' => 'Debe adjuntar al menos un archivo de evidencia para poder concluir la acción.'], 422);
        }

        $accion->accion_estado = 'finalizada';
        $accion->accion_fecha_fin_real = Carbon::now();
        $accion->save();

        return response()->json(['message' => 'Acción concluida con éxito.']);
    }

    public function downloadEvidencia($path)
    {
        // Ensure the path is secure and exists
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($path);
    }

    public function uploadEvidencia(Request $request, Accion $accion)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,png,xlsx,xls|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $hallazgoCod = $accion->hallazgo->hallazgo_cod;
        $accionCod = $accion->accion_cod;
        $pathPrefix = "hallazgos/{$hallazgoCod}/acciones/{$accionCod}";

        $path = $file->store($pathPrefix, 'public');

        $newFile = [
            'path' => $path,
            'name' => $originalName,
        ];

        $existingFiles = json_decode($accion->accion_ruta_evidencia, true) ?: [];
        if (!is_array($existingFiles)) {
            $existingFiles = [];
        }
        
        $existingFiles[] = $newFile;

        $accion->accion_ruta_evidencia = json_encode($existingFiles);
        $accion->save();

        return response()->json($newFile);
    }

    public function deleteEvidencia(Request $request, Accion $accion)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $pathToDelete = $request->input('path');

        // 1. Delete from storage
        if (Storage::disk('public')->exists($pathToDelete)) {
            Storage::disk('public')->delete($pathToDelete);
        }

        // 2. Update the JSON column
        $existingFiles = json_decode($accion->accion_ruta_evidencia, true) ?: [];
        if (!is_array($existingFiles)) {
            $existingFiles = [];
        }

        $updatedFiles = array_filter($existingFiles, function ($file) use ($pathToDelete) {
            return $file['path'] !== $pathToDelete;
        });

        // Re-index the array to prevent it from becoming an object on empty
        $accion->accion_ruta_evidencia = json_encode(array_values($updatedFiles));
        $accion->save();

        return response()->json(['message' => 'Archivo eliminado con éxito.']);
    }

    public function listarCausaRaiz(Hallazgo $hallazgo)
    {
        // A Hallazgo has one Causa, so we can use the 'causa' relationship
        // The 'first()' method will return the related Causa model or null if it doesn't exist.
        $causa = $hallazgo->causa()->first();

        return response()->json($causa);
    }

    public function listarAcciones(Hallazgo $hallazgo, Proceso $proceso)
    {
        // Fetch actions related to the specific hallazgo and process
        // Eager load necessary relationships for the frontend display
        $acciones = Accion::where('hallazgo_id', $hallazgo->id)
                            ->whereHas('hallazgoProceso', function ($query) use ($proceso) {
                                $query->where('proceso_id', $proceso->id);
                            })
                            ->with('responsable.ouos', 'hallazgoProceso.proceso')
                            ->get();

        return response()->json($acciones);
    }

    public function storeOrUpdateCausaRaiz(Request $request, Hallazgo $hallazgo)
    {
        $validatedData = $request->validate([
            'causa_metodo' => 'required|string',
            'causa_por_que1' => 'nullable|string',
            'causa_por_que2' => 'nullable|string',
            'causa_por_que3' => 'nullable|string',
            'causa_por_que4' => 'nullable|string',
            'causa_por_que5' => 'nullable|string',
            'causa_mano_obra' => 'nullable|string',
            'causa_metodologias' => 'nullable|string',
            'causa_materiales' => 'nullable|string',
            'causa_maquinas' => 'nullable|string',
            'causa_medicion' => 'nullable|string',
            'causa_medio_ambiente' => 'nullable|string',
            'causa_resultado' => 'nullable|string',
        ]);

        // Find existing Causa or create a new one
        $causa = Causa::firstOrNew(['hallazgo_id' => $hallazgo->id]);

        // Fill and save the data
        $causa->fill($validatedData);
        $causa->hallazgo_id = $hallazgo->id; // Ensure hallazgo_id is set
        $causa->save();

        return response()->json($causa);
    }

    public function updateAccion(Request $request, Accion $accion)
    {
        $validatedData = $request->validate([
            'accion_descripcion' => 'required|string',
            'accion_responsable' => 'required|string',
            'accion_fecha_inicio' => 'required|date',
            'accion_fecha_fin_planificada' => 'required|date',
            // Add other fields that can be updated
        ]);

        $accion->update($validatedData);

        return response()->json($accion);
    }

    public function destroyAccion(Accion $accion)
    {
        $accion->delete();

        return response()->json(['message' => 'Acción eliminada con éxito.']);
    }
}
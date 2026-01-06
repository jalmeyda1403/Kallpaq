<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DocumentoAnexo;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocumentoAnexoController extends Controller
{
    public function index(Request $request, $documentoId)
    {
        $query = DocumentoAnexo::where('documento_id', $documentoId);

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        } else {
            $query->where('da_estado', 'VIGENTE');
        }

        $anexos = $query->orderBy('id', 'desc')->get();
        return response()->json($anexos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'documento_id' => 'required|exists:documentos,id',
            'da_nombre' => 'required|string|max:255',
            'da_tipo' => 'required|in:FORMATO,MATRIZ,INFOGRAFIA,LISTADO,OTROS',
            'da_fecha_publicacion' => 'required|date',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            DB::beginTransaction();

            $documento = Documento::findOrFail($request->documento_id);

            // Generate Code: Count unique codes (groups) rather than all rows
            // Logic: Count distinct codes for this document to generate next sequential F00X
            $existingCodesCount = DocumentoAnexo::where('documento_id', $documento->id)
                ->distinct('da_codigo')
                ->count('da_codigo');
            
            $suffix = sprintf('F%03d', $existingCodesCount + 1);
            $parentCode = $documento->cod_documento ?? 'DOC'; 
            $codigo = $parentCode . '-' . $suffix;

            // Handle File Upload
            $file = $request->file('file');
            $filename = $codigo . '_v1.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('anexos/' . $documento->id, $filename, 'public');

            $anexo = DocumentoAnexo::create([
                'documento_id' => $documento->id,
                'da_codigo' => $codigo,
                'da_nombre' => $request->da_nombre,
                'da_tipo' => $request->da_tipo,
                'da_archivo_ruta' => $path,
                'da_version' => 1,
                'da_estado' => 'VIGENTE',
                'da_observacion' => $request->da_observacion ?? 'Creación inicial',
                'da_fecha_publicacion' => $request->da_fecha_publicacion
            ]);

            DB::commit();
            return response()->json($anexo, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear anexo: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'da_nombre' => 'sometimes|required|string|max:255',
            'da_tipo' => 'sometimes|required|in:FORMATO,MATRIZ,INFOGRAFIA,LISTADO,OTROS',
            'da_fecha_publicacion' => 'required|date',
            'file' => 'nullable|file|max:10240',
            'da_observacion' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            $oldAnexo = DocumentoAnexo::findOrFail($id);

            // If file is present -> New Version (Create new row, deprecate old)
            if ($request->hasFile('file')) {
                // Mark old as OBSOLETE
                $oldAnexo->update(['da_estado' => 'OBSOLETO']);
                
                // Create New Version
                $newVersion = $oldAnexo->da_version + 1;
                $file = $request->file('file');
                $filename = $oldAnexo->da_codigo . '_v' . $newVersion . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('anexos/' . $oldAnexo->documento_id, $filename, 'public');

                $newAnexo = DocumentoAnexo::create([
                    'documento_id' => $oldAnexo->documento_id,
                    'da_codigo' => $oldAnexo->da_codigo, // Keep same code
                    'da_nombre' => $request->da_nombre ?? $oldAnexo->da_nombre,
                    'da_tipo' => $request->da_tipo ?? $oldAnexo->da_tipo,
                    'da_archivo_ruta' => $path,
                    'da_version' => $newVersion,
                    'da_estado' => 'VIGENTE',
                    'da_observacion' => $request->da_observacion,
                    'da_fecha_publicacion' => $request->da_fecha_publicacion ?? $oldAnexo->da_fecha_publicacion
                ]);
                
                DB::commit();
                return response()->json($newAnexo);

            } else {
                // Normal update (metadata only)
                if ($request->has('da_nombre')) {
                    $oldAnexo->da_nombre = $request->da_nombre;
                }
                if ($request->has('da_tipo')) {
                    $oldAnexo->da_tipo = $request->da_tipo;
                }
                if ($request->has('da_observacion')) {
                    $oldAnexo->da_observacion = $request->da_observacion;
                }
                if ($request->has('da_fecha_publicacion')) {
                    $oldAnexo->da_fecha_publicacion = $request->da_fecha_publicacion;
                }

                $oldAnexo->save();
                DB::commit();
                return response()->json($oldAnexo);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar anexo: ' . $e->getMessage()], 500);
        }
    }

    public function history($codigo)
    {
        // Decode logic if needed, but da_codigo is stored as is.
        // We probably need to pass documento_id as well or just search strictly by code if it's unique enough (it combines doc code).
        // Let's filter by code.
        $history = DocumentoAnexo::where('da_codigo', $codigo)
            ->where('da_estado', '!=', 'VIGENTE') // Show only past versions? Or all? User usually wants history list.
            ->orderBy('da_version', 'desc')
            ->get();
            
        return response()->json($history);
    }

    public function destroy($id)
    {
        $anexo = DocumentoAnexo::findOrFail($id);
        // Soft delete logic
        $anexo->delete(); 
        // Or update status if we prefer
        // $anexo->update(['da_estado' => 'OBSOLETO']); 
        
        return response()->json(['message' => 'Anexo eliminado']);
    }

    public function restore($id)
    {
        $anexo = DocumentoAnexo::withTrashed()->findOrFail($id);
        
        // Optional: Check if a "VIGENTE" version currently exists with the same code to avoid conflicts
        // For now, we just restore it. If you want to enforce one VIGENTE per code, logic would go here.
        // Assuming Restore brings it back as it was (likely VIGENTE or OBSOLETO depending on when it was deleted, 
        // but physically it was VIGENTE when deleted usually).
        
        $anexo->restore();

        return response()->json(['message' => 'Anexo restaurado']);
    }
}

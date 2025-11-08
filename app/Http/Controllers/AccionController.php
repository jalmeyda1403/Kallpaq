<?php

namespace App\Http\Controllers;

use App\Models\Hallazgo;
use App\Models\Proceso;
use App\Models\Accion;
use App\Models\Causa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class AccionController extends Controller
{

    public function listarAcciones(Hallazgo $hallazgo, Proceso $proceso)
    {
        $acciones = Accion::where('hallazgo_id', $hallazgo->id)
            ->where('proceso_id', $proceso->id)
            ->get();
        return response()->json($acciones);
    }

    public function storeAccion(Request $request, Hallazgo $hallazgo, Proceso $proceso)
    {
        // Validación actualizada según tu nuevo SQL
        $validatedData = $request->validate([
            'tipo_accion' => 'required|in:1,2', // Asumiendo 1=inmediata, 2=correctiva para TINYINT
            'accion_descripcion' => 'required|string',
            'accion_estado' => 'required|in:pendiente,en ejecucion,finalizada,cancelada',
            'accion_cod' => 'nullable|string|max:255',
            'accion_comentario' => 'nullable|string',
            'accion_fecha_inicio' => 'nullable|date',
            'accion_fecha_fin_planificada' => 'nullable|date',
            'accion_responsable' => 'nullable|string|max:255',
            'accion_responsable_correo' => 'nullable|email|max:255',
        ]);

        try {
            $data = array_merge($validatedData, [
                'hallazgo_id' => $hallazgo->id,
                'proceso_id' => $proceso->id,
            ]);
            $accion = Accion::create($data);
            return response()->json($accion, 201);
        } catch (\Throwable $e) {
            Log::error('Error al crear acción: ' . $e->getMessage());
            return response()->json(['message' => 'Ocurrió un error al crear la acción.'], 500);
        }
    }

    public function updateAccion(Request $request, Accion $accion)
    {
        $validatedData = $request->validate([
            'tipo_accion' => 'required|in:1,2',
            'accion_descripcion' => 'required|string',
            'accion_estado' => 'required|in:pendiente,en ejecucion,finalizada,cancelada',
            'accion_cod' => 'nullable|string|max:255',
            'accion_comentario' => 'nullable|string',
            'accion_fecha_inicio' => 'nullable|date',
            'accion_fecha_fin_planificada' => 'nullable|date',
            'accion_fecha_fin_reprogramada' => 'nullable|date',
            'accion_fecha_cancelada' => 'nullable|date',
            'accion_fecha_fin_real' => 'nullable|date',
            'accion_justificacion' => 'nullable|string',
            'accion_responsable' => 'nullable|string|max:255',
            'accion_responsable_correo' => 'nullable|email|max:255',
        ]);
        $accion->update($validatedData);
        return response()->json($accion);
    }

    public function destroyAccion(Accion $accion)
    {
        $accion->delete();
        return response()->json(['message' => 'Acción eliminada con éxito.'], 200);
    }

    //METODO SEGUIMIENTO DE ACCIONES
    public function update_seguimiento(Request $request, $hallazgo_id, $id)
    {

        $accion = Accion::findOrFail($id);

        // Validar la solicitud
        $request->validate([
            'estado' => 'required|string',
        ]);

        // Actualizar los campos
        $accion->comentario = $request->comentario;
        $accion->estado = $request->estado;
        $hallazgo = Hallazgo::findOrFail($hallazgo_id);
        $smp_cod = $hallazgo->smp_cod;

        // Crear la carpeta SMP si no existe
        $folderPath = 'evidencias/' . $smp_cod;
        if (!Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->makeDirectory($folderPath);
        }
        // Crear la carpeta Accion si no existe
        $folderPath = 'evidencias/' . $smp_cod . '/' . $accion->accion_cod;
        if (!Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->makeDirectory($folderPath);
        }


        // Manejar la subida de archivos
        if ($request->hasFile('archivos')) {

            foreach ($request->file('archivos') as $archivo) {
                $filename = $smp_cod . '-' . sprintf('%03d', $id) . '_' . now()->format('YmdHis') . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
                $archivo->storeAs($folderPath, $filename, 'public');
            }
        }

        $accion->save();

        return back()->with('success', '¡La acción ha sido actualizada correctamente!');

    }

    public function listarArchivos($hallazgo_id, $id)
    {
        $hallazgo = Hallazgo::findOrFail($hallazgo_id);
        $accion = Accion::findOrFail($id);
        $smp_cod = $hallazgo->smp_cod;
        $folderPath = 'evidencias/' . $smp_cod . '/' . $accion->accion_cod;

        // Obtiene todos los archivos en la carpeta
        $files = Storage::disk('public')->files($folderPath);

        $fileData = [];
        foreach ($files as $file) {
            $fileData[] = [
                'name' => basename($file),
                'url' => Storage::url($file),
                'size' => Storage::disk('public')->size($file),
                'lastModified' => Storage::disk('public')->lastModified($file),
            ];
        }

        return response()->json($fileData);
    }

    public function eliminarArchivo(Request $request, $hallazgo_id, $id)
    {
        $fileUrl = $request->input('fileUrl');
        // Extraer la ruta del archivo del URL
        $filePath = parse_url($fileUrl, PHP_URL_PATH);

        // Transformar la URL pública a una ruta del sistema de archivos
        $filePath = str_replace('/storage/', '', $filePath);
        $filePath = 'public/' . $filePath;

        // Depuración: Verificar la ruta completa del archivo
        $fullPath = storage_path('app/' . $filePath);
        \Log::info('Ruta completa del archivo a eliminar: ' . $fullPath);

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            return redirect()->back()->with('success', 'Archivo eliminado correctamente');
        } else {
            \Log::error('Archivo no encontrado en la ruta: ' . $fullPath);
            return redirect()->back()->with('error', 'Archivo no encontrado');
        }
    }


    // --- MÉTODOS PARA EL ANÁLISIS DE CAUSA RAÍZ ---

    public function listarCausaRaiz(Hallazgo $hallazgo)
    {
        return response()->json($hallazgo->causaRaiz);
    }

    public function storeOrUpdateCausaRaiz(Request $request, Hallazgo $hallazgo)
    {
        $validatedData = $request->validate([
            'causa_metodo' => 'required|in:ishikawa,cinco_porques',
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

        try {
            $causaRaiz = $hallazgo->causaRaiz()->updateOrCreate(
                ['hallazgo_id' => $hallazgo->id],
                $validatedData
            );
            return response()->json($causaRaiz, 200);
        } catch (\Throwable $e) {
            Log::error('Error al guardar análisis de causa: ' . $e->getMessage());
            return response()->json(['message' => 'Ocurrió un error al guardar el análisis de causa.'], 500);
        }
    }

}

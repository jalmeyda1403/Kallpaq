<?php

namespace App\Http\Controllers;

use App\Models\Accion;
use App\Models\Hallazgo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccionController extends Controller
{


    public function index($hallazgo_id)
    {
        $hallazgo = Hallazgo::findOrFail($hallazgo_id);
        $acciones = Accion::where('hallazgo_id', $hallazgo_id)->get();
        return view('acciones.index', compact('hallazgo', 'acciones'));
    }

    public function create()
    {
        $responsables = User::all(); // Obtener todos los usuarios como opciones para responsables
        return view('acciones.create', compact('responsables'));
    }

    public function store(Request $request)
    {

        $smp = Hallazgo::find($request->hallazgo_id);
        // Contar la cantidad de acciones existentes para el hallazgo
        $conteoAcciones = Accion::where('hallazgo_id', $request->hallazgo_id)->count();

        // Generar el nuevo correlativo basado en el conteo de acciones
        $correlativo = $conteoAcciones + 1;
        $accion_cod = $smp->smp_cod . '-' . sprintf('%03d', $correlativo);

        // Validar los datos del formulario
        
        $request->validate([

        ]);

        $accion = new Accion();
        $accion->hallazgo_id = $request->hallazgo_id;
        $accion->accion_cod = $accion_cod;
        $accion->accion = $request->accion;
        $accion->fecha_inicio = $request->fecha_inicio;
        $accion->fecha_fin = $request->fecha_fin;
        $accion->comentario = $request->comentario;    
        $accion->es_correctiva = $request->es_correctiva;
        $accion->responsable_id = $request->responsable_id;
        $accion->responsable_correo = $request->responsable_correo;


        $accion->save();

        return back()->with('success', '¡El plan de acción ha sido creado correctamente!');

    }

    public function show($id)
    {
        $planAccion = Accion::findOrFail($id);
        return view('acciones.show', compact('planAccion'));
    }

    public function edit($id)
    {
        $planAccion = Accion::findOrFail($id);
        $responsables = User::all(); // Obtener todos los usuarios como opciones para responsables
        return response()->json($planAccion);
    }

    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $request->validate([
            // Aquí van las reglas de validación para cada campo
        ]);


        // Actualizar el plan de acción en la base de datos
        $accion = Accion::findOrFail($id);
        $accion->accion = $request->accion;
        $accion->fecha_inicio = $request->fecha_inicio;
        $accion->fecha_fin = $request->fecha_fin;
        $accion->comentario = $request->comentario;
        $accion->estado = $request->estado;
        $accion->es_correctiva = $request->es_correctiva;
        $accion->responsable_id = $request->responsable_id;
        $accion->responsable_correo = $request->responsable_correo;
        $accion->save();

        return back()->with('success', '¡El plan de acción ha sido creado correctamente!');
    }

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
        $folderPath = 'evidencias/' . $smp_cod.'/'.$accion->accion_cod;
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
        $folderPath = 'evidencias/' . $smp_cod.'/'.$accion->accion_cod;

        // Obtiene todos los archivos en la carpeta
        $files = Storage::disk('public')->files($folderPath);

        $fileData = [];
        foreach ($files as $file) {
            $fileData[] = [
                'name' => basename($file),
                'url' => Storage::disk('public')->url($file),
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

    public function destroy($id)
    {
        // Eliminar el plan de acción de la base de datos
        $planAccion = Accion::findOrFail($id);
        $planAccion->delete();

        return back()->with('success', '¡La acción ha sido creado eliminada!');
    }
}

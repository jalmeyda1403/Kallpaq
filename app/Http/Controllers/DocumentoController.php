<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use App\Models\Proceso;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function buscar(Request $request)
    {
        $buscar_documento = $request->get('buscar_documento');
        $buscar_proceso = $request->get('buscar_proceso');

        // Iniciar la consulta para obtener los documentos
        $documentos = Documento::query();

        // Si se busca por nombre de documento, agregar filtro
        if ($buscar_documento) {
            $documentos->where('nombre', 'LIKE', "%$buscar_documento%");
        }

        // Si se busca por nombre de proceso, agregar filtro
        if ($buscar_proceso) {
            // Buscar los procesos que coinciden con el nombre
            $procesos = Proceso::where('proceso_nombre', 'LIKE', "%$buscar_proceso%")->get();

            // Filtrar los documentos según los procesos encontrados
            $documentos->whereIn('proceso_id', $procesos->pluck('id'));
        }

        // Obtener los documentos con las relaciones de proceso y tipo de documento
        $documentos = $documentos->with('proceso', 'tipo_documento')->get();

        // Devolver la vista con los documentos encontrados
        return view('procesos.buscar', compact('documentos'));
    }
    public function store(Request $request)
    {
       
        try {
            // Intentar crear la obligación con los datos proporcionados
            $documento = Documento::create($request->all());

            // Verificar si la creación fue exitosa
            if ($documento) {
                // Redirigir a la lista de obligaciones con un mensaje de éxito
                return redirect()->back()->with('success', 'Obligación creada con éxito');
            } else {
                // Si por alguna razón la creación falla, retornar un error
                return redirect()->back()->with('error', 'Hubo un problema al crear la obligación. Inténtelo nuevamente.');
            }
        } catch (\Exception $e) {
            // Si ocurre una excepción, capturar el error y devolver un mensaje
            return redirect()->back()->with('error', 'Error al crear la obligación: ');
        }

    }

    public function edit($id)
    {
        $documento = Documento::findOrFail($id);
        return view('procesos.buscar', compact('documento'));
    }
    public function update(Request $request, $id)
    {
        // Buscar el documento por ID
        $documento = Documento::findOrFail($id);



        // Datos a actualizar (sin archivo)
        $data = $request->only([
            'cod_documento',
            'proceso_id',
            'tipo_documento_id',
            'nombre',
            'fuente',
            'estado',
        ]);
        $proceso = Proceso::find($request->proceso_id);
        // Si subieron archivo, manejar almacenamiento
        if ($request->hasFile('archivo')) {
            // Obtener el código del proceso para carpeta dinámica

            $codProceso = $proceso->cod_proceso;
            $tipoDocumento = $documento->tipo_documento->nombre;

            // Carpeta dentro del disco 'documentos'
            $carpeta = $codProceso . '/' . $tipoDocumento;

            // Crear carpeta si no existe
            if (!Storage::disk('documentos')->exists($carpeta)) {
                Storage::disk('documentos')->makeDirectory($carpeta);
            }

            // Guardar archivo en disco 'documentos' en carpeta proceso
            $path = $request->file('archivo')->store($carpeta, 'documentos');

            // Actualizar enlace con la ruta relativa del archivo
            $data['enlace'] = $path;
        }


        // Actualizar el documento con los datos nuevos
        $documento->update($data);

        return redirect()->back()->with('success', 'Documento actualizado correctamente.');
    }
    public function destroy($id)
    {
        $documento = Documento::findOrFail($id);
        $documento->delete();

        return response()->json(['success' => true, 'message' => 'Documento eliminado correctamente.']);
    }
    public function descargarArchivo($id)
    {
        // Buscar el documento por ID
        $documento = Documento::findOrFail($id);

        // Obtener la ruta relativa guardada en BD, ejemplo: "PR001/manual.pdf"
        $path = $documento->ultimaVersion->archivo_path;

        // Validar que el archivo exista en el disco privado
        if (!Storage::disk('documentos')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        // Aquí puedes validar permisos de usuario si es necesario

        // Descargar archivo
        return Storage::disk('documentos')->download($path);
    }

    public function mostrarArchivo($path)
    {
        $url = urldecode($path);

        if (!Storage::disk('documentos')->exists($url)) {
            abort(404);
        }
    
        $path = Storage::disk('documentos')->path($url);
        $mimeType = mime_content_type($path);
    
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }


}


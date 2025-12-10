<?php

namespace App\Http\Controllers;

use App\Models\DocumentoMovimiento;
use App\Models\DocumentoVersion;
use App\Models\DocumentoRelacionado;
use Illuminate\Http\Request;
use App\Models\Documento;
use App\Models\Proceso;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentoController extends Controller
{

    private function obtenerDocumentosPorProcesos(string $buscar_proceso)
    {
        $documentos = collect();
        $procesos = Proceso::where('proceso_nombre', 'LIKE', "%{$buscar_proceso}%")->get();

        foreach ($procesos as $proceso) {
            $documentos = $documentos->merge($proceso->descendiente_documentos());
        }
        $documentos = Collection::make($documentos);
        return $documentos->unique('id')->values();
    }
    public function buscar(Request $request)
    {
        $query = $request->input('query');

        $documentos = Documento::where('nombre_documento', 'LIKE', "%{$query}%")->get();
        $documentos = $documentos->map(function ($documento) {
            return [
                'id' => $documento->id,
                'descripcion' => $documento->nombre_documento,
            ];
        });

        return response()->json($documentos);
    }
    public function findDocumento(Request $request)
    {
        $buscar_documento = $request->get('buscar_documento');
        $buscar_proceso = $request->get('buscar_proceso');
        $buscar_fuente = $request->get('fuente');
        $buscar_tipo_documento = $request->get('tipo_documento');
        $buscar_estado = $request->input('estado');
        $perPage = 8;


        // Si se busca por proceso (requiere lógica especial por jerarquía)
        if ($buscar_proceso) {
            // Recibes una colección
            $documentos = $this->obtenerDocumentosPorProcesos($buscar_proceso);
            $documentos = $documentos->reject(fn($d) => $d->tipo_documento_id == 9);
            // Aplicar filtros sobre la colección
            $documentos = $documentos->when(
                $buscar_documento,
                fn($docs) => $docs->filter(fn($d) => str_contains(strtolower($d->nombre), strtolower($buscar_documento)))
            )->when(
                    $buscar_fuente,
                    fn($docs) => $docs->filter(fn($d) => $d->fuente == $buscar_fuente)
                )->when(
                    $buscar_tipo_documento,
                    fn($docs) => $docs->filter(fn($d) => in_array($d->tipo_documento_id, $buscar_tipo_documento))
                );




            // Paginación manual
            $page = LengthAwarePaginator::resolveCurrentPage();

            $total = $documentos->count();
            $items = $documentos->slice(($page - 1) * $perPage, $perPage)->values();

            $documentos = new LengthAwarePaginator($items, $total, $perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        } else {
            // Uso del builder de Eloquent directamente
            $documentos = Documento::query();
            $documentos->where('tipo_documento_id', '!=', 9);

            if ($buscar_documento) {
                $documentos->where('nombre_documento', 'LIKE', "%{$buscar_documento}%");
            }

            if ($buscar_fuente) {
                $documentos->where('fuente_documento', $buscar_fuente);
            }

            if ($buscar_tipo_documento) {
                $documentos->whereIn('tipo_documento_id', $buscar_tipo_documento);
            }



            $documentos = $documentos->with('procesos', 'tipo_documento')->paginate($perPage);
        }

        return view('procesos.buscar', compact('documentos'));
    }

    public function apiFindDocumento(Request $request)
    {
        $buscar_documento = $request->get('buscar_documento');
        $buscar_proceso = $request->get('buscar_proceso');
        $buscar_fuente = $request->get('fuente');
        $buscar_tipo_documento = $request->get('tipo_documento');
        $buscar_estado = $request->input('estado');
        $perPage = 8;


        // Si se busca por proceso (requiere lógica especial por jerarquía)
        if ($buscar_proceso) {
            // Recibes una colección
            $documentos = $this->obtenerDocumentosPorProcesos($buscar_proceso);
            $documentos = $documentos->reject(fn($d) => $d->tipo_documento_id == 9);
            // Aplicar filtros sobre la colección
            $documentos = $documentos->when(
                $buscar_documento,
                fn($docs) => $docs->filter(fn($d) => str_contains(strtolower($d->nombre), strtolower($buscar_documento)))
            )->when(
                    $buscar_fuente,
                    fn($docs) => $docs->filter(fn($d) => $d->fuente == $buscar_fuente)
                )->when(
                    $buscar_tipo_documento,
                    fn($docs) => $docs->filter(fn($d) => in_array($d->tipo_documento_id, $buscar_tipo_documento))
                );




            // Paginación manual
            $page = LengthAwarePaginator::resolveCurrentPage();

            $total = $documentos->count();
            $items = $documentos->slice(($page - 1) * $perPage, $perPage)->values();

            $documentos = new LengthAwarePaginator($items, $total, $perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        } else {
            // Uso del builder de Eloquent directamente
            $documentos = Documento::query();
            $documentos->where('tipo_documento_id', '!=', 9);

            if ($buscar_documento) {
                $documentos->where('nombre_documento', 'LIKE', "%{$buscar_documento}%");
            }

            if ($buscar_fuente) {
                $documentos->where('fuente_documento', $buscar_fuente);
            }

            if ($buscar_tipo_documento) {
                $documentos->whereIn('tipo_documento_id', $buscar_tipo_documento);
            }



            $documentos = $documentos->with('procesos', 'tipo_documento', 'ultimaVersion', 'area_compliance')->paginate($perPage);
        }

        return response()->json($documentos);
    }


    public function listar(Request $request)
    {
        return view('documentos.index');
    }
    // Y crea un nuevo método para la API
    public function apiListar(Request $request)
    {
        Log::info('apiListar request data: ', $request->all());
        try {
            // Inicia la consulta con las relaciones necesarias
            $query = Documento::with('tipo_documento', 'ultimaVersion', 'procesos');

            // Filtrar por nombre de documento
            if ($request->filled('buscar_documento')) {
                $query->where('nombre_documento', 'like', '%' . $request->input('buscar_documento') . '%');
            }

            // Filtrar por nombre de proceso
            if ($request->filled('buscar_proceso')) {
                $query->whereHas('procesos', function ($q) use ($request) {
                    $q->where('proceso_nombre', 'like', '%' . $request->input('buscar_proceso') . '%');
                });
            }

            // Filtrar por fuente
            if ($request->filled('fuente')) {
                $query->where('fuente_documento', $request->input('fuente'));
            }

            // Filtrar por tipo de documento
            if ($request->filled('tipo_documento')) {
                $query->whereIn('tipo_documento_id', (array)$request->input('tipo_documento'));
            }

            // Obtener los documentos con los filtros aplicados
            $documentos = $query->get();
            Log::info('apiListar documents count: ' . $documentos->count());

            return response()->json($documentos);
        } catch (\Exception $e) {
            Log::error('Error in apiListar: ' . $e->getMessage());
            return response()->json(['message' => 'Error al cargar los documentos.'], 500);
        }
    }

    protected function obtenerIdsProcesosAncestros(Proceso $proceso): array
    {
        $ids = [$proceso->id];
        $current = $proceso;

        while ($current && $current->padre) {
            $ids[] = $current->padre->id;
            $current = $current->padre;
        }

        return $ids;
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

    public function show($documento_id)
    {
        $documento = Documento::with(['area_compliance', 'subarea_compliance', 'tags', 'versiones'])->findOrFail($documento_id);

        return response()->json($documento);
    }
    public function updateInfoGeneral(Request $request, $id)
    {
        try {
            // 1. Encontrar el documento
            $documento = Documento::findOrFail($id);

            // 2. Obtener los datos del formulario
            $data = $request->only([
                'cod_documento',
                'tipo_documento_id',
                'nombre_documento',
                'resumen_documento',
                'fuente_documento',
                'estado_documento',
                'usa_versiones_documento',
                'instrumento_aprueba_documento',
                'fecha_aprobacion_documento',
                'frecuencia_revision_documento',
                'enlace_valido',
                'archivo_path_documento',
            ]);

            $origenDocumento = $request->input('origen_documento');
            $archivoPathDocumento = $documento->archivo_path_documento;

            // 3. Lógica para manejar el archivo o la URL
            if ($request->input('usa_versiones_documento') == 1) {
                // Si usa versiones, el archivo_path_documento se anula
                if ($archivoPathDocumento && !Str::startsWith($archivoPathDocumento, ['http://', 'https://'])) {
                    Storage::disk('documentos')->delete($archivoPathDocumento);
                }
                $data['archivo_path_documento'] = null;
            } else {
                // Si NO usa versiones, se procesa el origen del documento
                if ($origenDocumento === 'file') {
                    if ($request->hasFile('archivo')) {
                        // Eliminar el archivo antiguo si existe y no es una URL
                        if ($archivoPathDocumento && !Str::startsWith($archivoPathDocumento, ['http://', 'https://'])) {
                            Storage::disk('documentos')->delete($archivoPathDocumento);
                        }

                        // Subir el nuevo archivo
                        $file = $request->file('archivo');
                        $fileName = $request->input('cod_documento') . '.' . $file->getClientOriginalExtension();
                        $path = Storage::putFileAs('', $file, $fileName, ['disk' => 'documentos']);
                        $data['archivo_path_documento'] = $path;
                    } else {
                        // Si no se sube un nuevo archivo, mantener el path existente si es un archivo local
                        if (!Str::startsWith($archivoPathDocumento, ['http://', 'https://'])) {
                            $data['archivo_path_documento'] = $archivoPathDocumento;
                        } else {
                            $data['archivo_path_documento'] = null; // Si el origen era URL, se borra el path
                        }
                    }
                } elseif ($origenDocumento === 'url') {
                    // Eliminar el archivo antiguo si es local (no una URL)
                    if ($archivoPathDocumento && !Str::startsWith($archivoPathDocumento, ['http://', 'https://'])) {
                        Storage::disk('documentos')->delete($archivoPathDocumento);
                    }
                    $data['archivo_path_documento'] = $request->input('archivo_path_documento');
                }
            }

            // 4. Actualizar el documento
            $documento->update($data);

            return response()->json([
                'message' => 'Información general del documento actualizada correctamente.',
                'data' => $documento
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al actualizar el documento.',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function updateMetadatos(Request $request, $id)
    {
        try {
            $documento = DB::transaction(function () use ($request, $id) {

                $documento = Documento::findOrFail($id);

                $documento->update([
                    'area_compliance_id' => $request->input('area_compliance_id'),
                    'subarea_compliance_id' => $request->input('subarea_compliance_id'),
                    'palabras_clave_documento' => $request->input('palabras_clave_documento'),
                ]);

                if ($request->filled('tags')) {
                    // --- INICIO DE LA CORRECCIÓN ---
                    // Le decimos a Eloquent que seleccione el ID de la tabla 'tags'.
                    $tagsAntiguosIds = $documento->tags()->pluck('tags.id')->sort()->values()->toArray();
                    // --- FIN DE LA CORRECCIÓN ---

                    $tagIds = collect($request->input('tags'))
                        ->map(function ($tagInput) {
                            if (is_numeric($tagInput))
                                return (int) $tagInput;
                            $nombre = ucfirst(strtolower(trim($tagInput)));
                            return Tag::firstOrCreate(['nombre' => $nombre])->id;
                        })->sort()->values()->toArray();

                    $documento->tags()->sync($tagIds);

                    if ($tagsAntiguosIds !== $tagIds) {
                        DocumentoMovimiento::create([
                            'documento_id' => $documento->id,
                            'usuario_id' => Auth::id(),
                            'accion' => 'ACTUALIZACIÓN',
                            'descripcion' => 'Se modificaron las etiquetas (Tags) asociadas.'
                        ]);
                    }
                } else {
                    if ($documento->tags()->exists()) {
                        $documento->tags()->detach();
                        DocumentoMovimiento::create([
                            'documento_id' => $documento->id,
                            'usuario_id' => Auth::id(),
                            'accion' => 'ACTUALIZACIÓN DE METADATOS',
                            'descripcion' => 'Se eliminaron todas las etiquetas (Tags) asociadas.'
                        ]);
                    }
                }

                return $documento;
            });

            return response()->json([
                'message' => 'Metadatos del documento actualizados correctamente.',
                'data' => $documento->load('tags')
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error al actualizar metadatos: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al actualizar los metadatos. Ningún cambio fue guardado.'
            ], 500);
        }
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
        $fullPath = storage_path('app/documentos/' . $path);
        return response()->download($fullPath);
    }
    public function mostrarArchivo($path)
    {
        $url = urldecode($path);

        if (!Storage::disk('documentos')->exists($url)) {
            abort(404);
        }

        $absolutePath = storage_path('app/documentos/' . $url);
        $mimeType = mime_content_type($absolutePath);

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($absolutePath) . '"',
        ]);
    }


    public function validarUrl(Request $request)
    {
        try {
            $isValid = $this->urlEsValida($request->input('url'));

            return response()->json(['isValid' => $isValid]);

        } catch (\Exception $e) {
            return response()->json(['isValid' => false]);
        }
    }
    private function urlEsValida($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true); // Solo cabeceras
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ⚠️ Ignora errores SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // ⚠️ Ignora verificación de host

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 400;
    }

    public function versiones($documento_id)
    {
        $documento = Documento::with(['versiones'])->findOrFail($documento_id);

        return response()->json($documento->versiones);
    }

    public function listarProcesosAsociados(Documento $documento)
    {
        // Gracias a la relación definida en el modelo, esto es todo lo que se necesita.
        // Se devuelven los procesos con todos sus campos.
        return response()->json($documento->procesos);
    }
    public function asociarProceso(Request $request, Documento $documento)
    {
        // Validación para asegurar que el proceso_id es enviado y válido.
        $request->validate([
            'proceso_id' => 'required|exists:procesos,id',
        ]);

        // Usamos syncWithoutDetaching para añadir la relación.
        // Esto evita errores si la relación ya existe, a diferencia de attach().
        $documento->procesos()->syncWithoutDetaching($request->proceso_id);

        return response()->json(['message' => 'Proceso asociado con éxito.'], 201);
    }
    public function disociarProceso(Documento $documento, Proceso $proceso)
    {
        // Usamos detach() para eliminar el registro de la tabla pivote.
        $documento->procesos()->detach($proceso->id);

        return response()->json(['message' => 'Asociación eliminada con éxito.']);
    }

    public function listarRelacionados(Documento $documento)
    {
        return response()->json([
            'salientes' => $documento->relacionesSalientes,
            'entrantes' => $documento->relacionesEntrantes,
        ]);
    }

    public function asociarRelacionado(Request $request, Documento $documento)
    {

        // Usamos el segundo argumento de attach para guardar los datos del pivote
        $documento->relacionesSalientes()->attach($request['relacionado_id'], [
            'tipo_relacion' => $request['tipo_relacion']
        ]);

        return response()->json(['message' => 'Documento relacionado con éxito.'], 201);
    }


    public function disociarRelacionado(Documento $documento, Documento $relacionado)
    {
        $documento->relacionesSalientes()->detach($relacionado->id);
        return response()->json(['message' => 'Relación eliminada con éxito.']);
    }

    public function listarHistorial(Documento $documento)
    {
        // Asumiendo que tienes un modelo DocumentoHistorial
        $historial = DocumentoMovimiento::where('documento_id', $documento->id)
            ->with('usuario:id,name,codigo') // Carga el nombre del usuario para mostrarlo
            ->latest() // Ordena por fecha de creación, del más nuevo al más antiguo
            ->get();

        return response()->json($historial);
    }
    //Methodos Jerarquia
    public function getJerarquia(Documento $documento)
    {
        // Usamos with() para cargar las relaciones de forma eficiente (Eager Loading)
        $documento->load('padre', 'hijos');

        return response()->json([
            'padre' => $documento->padre,
            'hijos' => $documento->hijos,
        ]);
    }

    public function setPadre(Request $request, Documento $documento)
    {
        $data = $request->validate(['documento_padre_id' => 'required|exists:documentos,id']);
        $documento->update(['documento_padre_id' => $data['documento_padre_id']]);
        return response()->json(['message' => 'Padre asignado con éxito.']);
    }

    public function removePadre(Documento $documento)
    {
        $documento->update(['documento_padre_id' => null]);
        return response()->json(['message' => 'Padre desvinculado con éxito.']);
    }

    public function setHijo(Request $request, Documento $documento)
    {
        $data = $request->validate([
            'hijo_id' => 'required|exists:documentos,id',
        ]);

        $hijo = Documento::find($data['hijo_id']);

        $hijo->update(['documento_padre_id' => $documento->id]);

        return response()->json(['message' => 'Hijo asignado con éxito.']);
    }

    public function removeHijo(Documento $documento, Documento $hijo)
    {
        // Solo desvincula si el documento actual es realmente el padre
        if ($hijo->documento_padre_id === $documento->id) {
            $hijo->update(['documento_padre_id' => null]);
        }
        return response()->json(['message' => 'Hijo desvinculado con éxito.']);
    }
    //methodos Dependencias
    public function crearNuevaVersionConDependencias(Request $request, Documento $documento)
    {
        $data = $request->validate([
            'dv_version' => 'required|string|max:255',
            'dv_control_cambios' => 'nullable|string',
            'dv_fecha_aprobacion' => 'nullable|date',
            // ... (añade aquí las validaciones para otros campos que envíes desde el modal de nueva versión)
        ]);

        try {
            $nuevaVersion = DB::transaction(function () use ($documento, $data) {
                $ultimaVersion = $documento->versiones()->latest('id')->first();
                $nuevaVersion = $documento->versiones()->create($data);
                if ($ultimaVersion) {
                    $dependenciasIds = $ultimaVersion->dependencias()->pluck('documentos.id');
                    if ($dependenciasIds->isNotEmpty()) {
                        $nuevaVersion->dependencias()->attach($dependenciasIds);
                    }
                }

                return $nuevaVersion;
            });

            return response()->json($nuevaVersion, 201);

        } catch (\Throwable $e) {
            \Log::error('Error al crear nueva versión con dependencias: ' . $e->getMessage());
            return response()->json(['message' => 'Ocurrió un error al crear la nueva versión.'], 500);
        }
    }

    public function getDependencias(DocumentoVersion $version)
    {
        return response()->json($version->dependencias);
    }

    public function setDependencia(Request $request, DocumentoVersion $version)
    {
        $data = $request->validate([
            'hijo_id' => 'required|exists:documentos,id'
        ]);

        $version->dependencias()->syncWithoutDetaching($data['hijo_id']);
        return response()->json($version->dependencias()->get());
    }

    public function removeDependencia(DocumentoVersion $version, Documento $hijo)
    {
     
        $version->dependencias()->detach($hijo->id);
        return response()->json($version->dependencias()->get());
    }


}


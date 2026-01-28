<?php

namespace App\Http\Controllers;

use App\Models\Hallazgo;
use App\Models\Proceso;
use App\Models\Accion;
use App\Models\User;
use App\Models\ProcesoOuo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\ActionApprovedNotificacion;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HallazgoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $clasificacion)
    {
        $breadcrumb = [];
        $clasificacionArray = explode(',', $clasificacion);
        $query = Hallazgo::query()
            ->filterBySig($request->sig)
            ->filterByInformeId($request->informe_id)
            ->filterByYear($request->year)
            ->filterByClasificacion($clasificacionArray);

        $hallazgos = $query->get();

        if ($clasificacion == 'Ncm') {
            $breadcrumb['nombre'] = "Listado de SMP";
            $breadcrumb['codigo'] = "Ncm";
        } elseif ($clasificacion == 'Obs') {
            $breadcrumb['nombre'] = "Listado de Observaciones";
            $breadcrumb['codigo'] = "Obs";
        } elseif ($clasificacion == 'Odm') {
            $breadcrumb['nombre'] = "Listado de Oportunidades de mejora";
            $breadcrumb['codigo'] = "Odm";
        } else {
            $breadcrumb['nombre'] = "Listado de SMP";
            $breadcrumb['codigo'] = $clasificacion;
        }


        return view('smp.index', compact('hallazgos', 'breadcrumb'));
    }

    public function listar(Request $request)
    {
        return view('smp.index');
    }

    public function create($clasificacion = null)
    {

    }
    public function apiListar(Request $request)
    {
        // Inicia la consulta base, cargando la relación con 'proceso' 
        // para tener acceso al nombre del proceso en el frontend.
        $query = Hallazgo::with('procesos', 'acciones')->latest(); // `latest()` ordena por fecha de creación descendente

        // --- Aplicación de Filtros Dinámicos ---

        // 1. Filtrar por descripción o resumen
        if ($request->filled('descripcion')) {
            $searchTerm = '%' . $request->descripcion . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('hallazgo_resumen', 'like', $searchTerm)
                    ->orWhere('hallazgo_descripcion', 'like', $searchTerm);
            });
        }

        // 2. Filtrar por nombre del proceso (a través de la relación)
        if ($request->filled('proceso')) {
            $query->whereHas('procesos', function ($q) use ($request) {
                $q->where('proceso_nombre', 'like', '%' . $request->proceso . '%');
            });
        }

        // 3. Filtrar por clasificación
        if ($request->filled('clasificacion')) {
            $query->where('hallazgo_clasificacion', $request->clasificacion);
        }

        // 4. Filtrar por estado
        if ($request->filled('estado')) {
            $query->where('hallazgo_estado', $request->estado);
        }

        // --- Ejecución y Respuesta ---

        // Ejecuta la consulta construida y obtiene los resultados
        $hallazgos = $query->get();

        // Devuelve los resultados en formato JSON
        return response()->json($hallazgos);
    }
    public function apiMyHallazgos(Request $request)
    {
        try {
            $user = Auth::user();

            // Obtener hallazgos de TODOS los procesos asociados a las OUOs del usuario
            $procesoIds = $user->getAllProcesosAsociadosIds();

            // Iniciar query
            $query = Hallazgo::with('procesos', 'acciones');

            // Filtrar por los procesos permitidos (Facilitador Scope)
            if (!empty($procesoIds)) {
                $query->whereHas('procesos', function ($q) use ($procesoIds) {
                    $q->whereIn('procesos.id', $procesoIds);
                });
            } else {
                // Si no hay procesos asociados, retornar vacío (o verificar si es Admin?)
                // Por ahora asumimos estricto control.
                $query->whereRaw('1 = 0');
            }

            // Aplicar filtros adicionales si existen
            if ($request->filled('descripcion')) {
                $searchTerm = '%' . $request->descripcion . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('hallazgo_resumen', 'like', $searchTerm)
                        ->orWhere('hallazgo_descripcion', 'like', $searchTerm);
                });
            }

            if ($request->filled('proceso_id')) {
                $query->whereHas('procesos', function ($q) use ($request) {
                    $q->where('procesos.id', $request->proceso_id);
                });
            }

            if ($request->filled('clasificacion')) {
                $query->where('hallazgo_clasificacion', $request->clasificacion);
            }

            if ($request->filled('estado')) {
                $query->where('hallazgo_estado', $request->estado);
            }

            $perPage = $request->input('rows', 10);
            $hallazgos = $query->paginate($perPage);

            return response()->json($hallazgos);
        } catch (\Throwable $e) {
            \Log::error("Error en apiMyHallazgos: " . $e->getMessage() . " line " . $e->getLine());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Generar el valor para hallazgo_cod
        $proceso = null;
        $sigla = 'GEN';
        $correlativo = 1;

        if ($request->filled('proceso_id')) {
            $proceso = Proceso::find($request->proceso_id);
            if ($proceso) {
                $sigla = $proceso->proceso_sigla;
                // Buscar último hallazgo de este proceso para correlativo
                $ultimoHallazgo = Hallazgo::where('hallazgo_cod', 'like', "%-{$sigla}-%")->latest()->first();
                if ($ultimoHallazgo) {
                    $parts = explode('-', $ultimoHallazgo->hallazgo_cod);
                    if (count($parts) >= 4) {
                        $correlativo = (int) $parts[3] + 1;
                    }
                }
            }
        } else {
            // Correlativo para GEN
            $ultimoHallazgo = Hallazgo::where('hallazgo_cod', 'like', "%-GEN-%")->latest()->first();
            if ($ultimoHallazgo) {
                $parts = explode('-', $ultimoHallazgo->hallazgo_cod);
                if (count($parts) >= 4) {
                    $correlativo = (int) $parts[3] + 1;
                }
            }
        }

        $clasificacion = ($request->clasificacion === 'NCM' || $request->clasificacion === 'Ncme') ? 'SMP' : $request->clasificacion;
        $hallazgo_cod = $clasificacion . '-' . $sigla . '-' . $request->hallazgo_origen . '-' . sprintf('%03d', $correlativo);

        // Agregar hallazgo_cod al arreglo de datos
        // Usamos $request->except para evitar pasar campos que no existen o son calculados si no están en fillable
        $data = $request->except(['proceso_id']);
        $data['hallazgo_cod'] = $hallazgo_cod;

        // Crear un nuevo hallazgo con los datos del formulario
        $hallazgo = Hallazgo::create($data);

        // Si se envió un proceso_id válido, asociarlo en la tabla pivote
        if ($proceso) {
            $hallazgo->procesos()->attach($proceso->id);
        }

        // Redirigir a alguna vista o página después de guardar los datos
        return response()->json($hallazgo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $hallazgo = Hallazgo::with([
            'procesos:id,proceso_nombre,proceso_sigla,cod_proceso',
            'especialista:id,name,email',
            'auditor:id,name,email'
        ])->find($id);

        if (!$hallazgo) {
            return response()->json(['error' => 'Hallazgo no encontrado'], 404);
        }

        return response()->json($hallazgo);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hallazgo_id)
    {
        $hallazgo = Hallazgo::findOrFail($hallazgo_id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hallazgo $hallazgo)
    {
        // 1. Validar los datos de entrada (en update, 'unique' debe ignorar el registro actual)
        $validatedData = $request->validate([
            'hallazgo_cod' => [
                'required',
                'string',
                'max:255',
                Rule::unique('hallazgos', 'hallazgo_cod')->ignore($hallazgo->id, 'id')
            ],
            'informe_id' => 'nullable|string|max:255',
            'proceso_id' => 'nullable|exists:procesos,id',
            'especialista_id' => 'nullable|exists:users,id',
            'auditor_id' => 'nullable|exists:users,id',
            'hallazgo_resumen' => 'required|string|max:500',
            'hallazgo_descripcion' => 'required|string',
            'hallazgo_criterio' => 'nullable|string',
            'hallazgo_clasificacion' => 'required|string',
            'hallazgo_origen' => 'required|string',
            'hallazgo_fecha_identificacion' => 'required|date',
            'hallazgo_evidencia' => 'nullable|string',
            'hallazgo_sig' => 'nullable|array', // Add validation for hallazgo_sig

            // Añade aquí el resto de validaciones
        ]);

        // 2. Actualizar el hallazgo con los datos validados
        $hallazgo->update($validatedData);

        // 3. Devolver el hallazgo actualizado como respuesta JSON
        return response()->json($hallazgo);
    }

    public function destroy(Hallazgo $hallazgo)
    {
        //
    }


    public function imprimir(Request $request, $id)
    {
        $hallazgo = Hallazgo::findOrFail($id);

        $planesAccion = Accion::where('hallazgo_id', '=', $id, 'and')->get();
        $correctivas = Accion::where('hallazgo_id', '=', $id, 'and')->where('es_correctiva', '=', 1, 'and')->count();
        $preventivas = Accion::where('hallazgo_id', '=', $id, 'and')->where('es_correctiva', '=', 0, 'and')->count();
        $logoPath = public_path('images/logo.png');

        $pdf = PDF::loadView('smp.planPDF', compact('logoPath', 'planesAccion', 'hallazgo', 'correctivas', 'preventivas'));
        $pdf->output();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "Página $pageNumber de $pageCount";
            $font = $fontMetrics->getFont('Arial', 'normal');
            $size = 10;
            $width = $fontMetrics->getTextWidth($text, $font, $size);
            $canvas->text($canvas->get_width() - $width - 65, 50, $text, $font, $size);
        });

        //return view('smp.planPDF', compact('planesAccion', 'hallazgo', 'correctivas', 'preventivas'));
        return $pdf->stream('hallazgo_' . $hallazgo->hallazgo_cod . '.pdf');

    }

    public function aprobar(Request $request, $id)
    {
        $hallazgo = Hallazgo::findOrFail($id);

        // Validar que solo se pueda aprobar si el estado actual es 'creado' o 'modificado'
        $estadosPermitidos = ['creado', 'modificado'];
        if (!in_array(strtolower($hallazgo->hallazgo_estado), $estadosPermitidos)) {
            return response()->json([
                'error' => 'No se puede aprobar el hallazgo. Solo se pueden aprobar hallazgos con estado: ' . implode(', ', $estadosPermitidos)
            ], 400);
        }

        // Actualizar el estado a 'aprobado'
        $hallazgo->hallazgo_estado = 'aprobado';
        $hallazgo->hallazgo_fecha_aprobacion = now();

        // Obtener las acciones relacionadas con el hallazgo
        $acciones = $hallazgo->acciones()->get();
        $hallazgo->save();

        // Enviar notificación a los correos de los responsables
        foreach ($acciones as $accion) {
            $responsableCorreo = $accion->responsable_correo;

            // Enviar la notificación por correo
            Notification::route('mail', $responsableCorreo)->notify(new ActionApprovedNotificacion($accion));
        }

        return response()->json([
            'message' => 'Se ha aprobado el plan de acción',
            'hallazgo' => $hallazgo
        ]);
    }

    public function subirAdjunto(Request $request, $id)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:10240', // Máximo 10MB
        ]);

        $hallazgo = Hallazgo::findOrFail($id);

        // Obtener el archivo
        $archivo = $request->file('archivo');

        // Crear un nombre único para el archivo
        $nombreArchivo = 'plan_accion_' . $hallazgo->id . '_' . time() . '.' . $archivo->getClientOriginalExtension();

        // Directorio donde se guardarán los archivos
        $rutaDirectorio = 'hallazgos/' . $hallazgo->hallazgo_cod;

        // Guardar el archivo en el directorio público
        $ruta = $archivo->storeAs($rutaDirectorio, $nombreArchivo, 'public');

        // Aquí puedes guardar la ruta del archivo en la base de datos si es necesario
        // Por ejemplo, podrías tener un campo en la tabla hallazgos o una tabla relacionada
        $hallazgo->ruta_plan_accion = $ruta;
        $hallazgo->save();

        return response()->json([
            'message' => 'Archivo subido exitosamente',
            'ruta' => $ruta
        ]);
    }





    public function porProceso($id, $clasificacion)
    {
        $proceso = Proceso::with('hallazgos')->findOrFail($id);
        $clasificacionArray = explode(',', $clasificacion);
        $query = $proceso->hallazgos()->filterByClasificacion($clasificacionArray);

        $hallazgos = $query->get();

        $breadcrumb = [];
        $breadcrumb['nombre'] = "Procesos " . $proceso->proceso_nombre . " - Listado de SMP";
        $breadcrumb['codigo'] = $clasificacion;

        return view('smp.index', compact('hallazgos', 'breadcrumb'));


    }

    //Methodo Asociar Procesos
    public function listarProcesosAsociados(Hallazgo $hallazgo)
    {
        // Gracias a la relación definida en el modelo, esto es todo lo que se necesita.
        // Se devuelven los procesos con todos sus campos.
        return response()->json($hallazgo->procesos);
    }
    public function asociarProceso(Request $request, Hallazgo $hallazgo)
    {
        // Validación para asegurar que el proceso_id es enviado y válido.
        $request->validate([
            'proceso_id' => 'required|exists:procesos,id',
        ]);

        // Esto evita errores si la relación ya existe, a diferencia de attach().
        $hallazgo->procesos()->syncWithoutDetaching($request->proceso_id);

        return response()->json(['message' => 'Proceso asociado con éxito.'], 201);
    }
    public function disociarProceso(Hallazgo $hallazgo, Proceso $proceso)
    {
        // Usamos detach() para eliminar el registro de la tabla pivote.
        $hallazgo->procesos()->detach($proceso->id);

        return response()->json(['message' => 'Asociación eliminada con éxito.']);
    }

    //Método para obtener hallazgos basados en la OUO del usuario
    public function getSmpByUserOuo(Request $request)
    {
        $user = Auth::user();

        // Obtener las OUOs del usuario con sus roles
        $userOuos = $user->ouos()->withPivot('role_in_ouo', 'activo')->get();

        // Obtener los IDs de OUOs del usuario
        $ouoIds = $userOuos->pluck('id');

        // Obtener los procesos asociados a esas OUOs
        $procesoIds = ProcesoOuo::whereIn('id_ouo', $ouoIds, 'and', false)->pluck('id_proceso')->unique();

        // Filtrar hallazgos relacionados con esos procesos
        $query = Hallazgo::with('procesos', 'acciones');

        if ($procesoIds->isNotEmpty()) {
            $query->whereHas('procesos', function ($q) use ($procesoIds) {
                $q->whereIn('procesos.id', $procesoIds);
            });
        } else {
            // Si no hay procesos asociados, retornar colección vacía
            $query->whereRaw('1 = 0');
        }

        // Aplicar filtros adicionales si existen
        if ($request->filled('descripcion')) {
            $searchTerm = '%' . $request->descripcion . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('hallazgo_resumen', 'like', $searchTerm)
                    ->orWhere('hallazgo_descripcion', 'like', $searchTerm);
            });
        }

        if ($request->filled('proceso')) {
            $query->whereHas('procesos', function ($q) use ($request) {
                $q->where('proceso_nombre', 'like', '%' . $request->proceso . '%');
            });
        }

        if ($request->filled('clasificacion')) {
            $query->where('hallazgo_clasificacion', $request->clasificacion);
        }

        if ($request->filled('estado')) {
            $query->where('hallazgo_estado', $request->estado);
        }

        $hallazgos = $query->get();

        return response()->json($hallazgos);
    }

    //Methodo Asignar Especialista
    public function listarAsignaciones(Hallazgo $hallazgo)
    {
        return response()->json([
            // Cargamos la relación 'especialista' para obtener los datos del usuario actual
            'actual' => $hallazgo->load('especialista')->especialista,

            // Cargamos el historial y las relaciones anidadas para obtener los nombres
            'historial' => $hallazgo->movimientos()->with('usuario:id,name')->latest()->get()
        ]);
    }
    public function asignarEspecialista(Request $request, Hallazgo $hallazgo)
    {
        $validatedData = $request->validate([
            'especialista_id' => 'required|exists:users,id',
            'assigned_by_user_id' => 'required|exists:users,id', // Validate the user who made the assignment
            'assigned_by_user_name' => 'required|string',
        ]);

        try {
            $nuevoEspecialista = DB::transaction(function () use ($hallazgo, $validatedData) {
                // Update the Hallazgo's specialist
                $hallazgo->update([
                    'especialista_id' => $validatedData['especialista_id']
                ]);

                // Get the name of the newly assigned specialist
                $specialistName = User::find($validatedData['especialista_id'], ['*'])->name;

                // Create a HallazgoMovimientos record
                $hallazgo->movimientos()->create([ // Assuming 'movimientos' is the relationship to HallazgoMovimientos
                    'hm_estado' => 'asignado',
                    'hm_comentario' => "Hallazgo asignado a {$specialistName} por {$validatedData['assigned_by_user_name']}.",
                    'user_id' => $validatedData['assigned_by_user_id'], // The user who made the assignment
                ]);

                return $hallazgo->fresh()->load('especialista');
            });

            return response()->json([
                'actual' => $nuevoEspecialista->especialista,
                'historial' => $hallazgo->movimientos()->with('usuario:id,name')->latest()->get() // Load 'usuario' relationship for HallazgoMovimientos
            ]);

        } catch (\Throwable $e) {
            \Log::error("Error al asignar especialista: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
            return response()->json(['message' => 'Ocurrió un error al asignar el especialista.', 'error' => $e->getMessage()], 500);
        }
    }

    // Método para listar hallazgos pendientes de verificación de eficacia
    public function apiListarEficacia(Request $request)
    {
        $user = Auth::user();

        // Iniciar consulta con relaciones necesarias
        // Iniciar consulta con relaciones necesarias
        $query = Hallazgo::with(['procesos', 'acciones.reprogramaciones', 'especialista', 'evaluaciones']);
        // ->where('hallazgo_estado', 'concluido'); // Comentado para mostrar todos los asignados

        // Filtrar SIEMPRE por el especialista actual (Mis SMP Asignadas)
        $query->where('especialista_id', $user->id);

        // Aplicar filtros adicionales si existen
        if ($request->filled('descripcion')) {
            $searchTerm = '%' . $request->descripcion . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('hallazgo_resumen', 'like', $searchTerm)
                    ->orWhere('hallazgo_descripcion', 'like', $searchTerm);
            });
        }

        if ($request->filled('proceso_id')) {
            $query->whereHas('procesos', function ($q) use ($request) {
                $q->where('procesos.id', $request->proceso_id);
            });
        }

        if ($request->filled('clasificacion')) {
            $query->where('hallazgo_clasificacion', $request->clasificacion);
        }

        if ($request->filled('estado')) {
            $query->where('hallazgo_estado', $request->estado);
        }

        return response()->json($query->latest()->get());
    }

    // Método para guardar la evaluación de eficacia
    public function storeEvaluacion(Request $request, Hallazgo $hallazgo)
    {
        // Solo el especialista asignado puede evaluar eficacia
        if (auth()->id() !== $hallazgo->especialista_id) {
            return response()->json(['error' => 'Solo el especialista asignado puede registrar la evaluación de eficacia.'], 403);
        }

        $validatedData = $request->validate([
            'resultado' => 'required|in:con eficacia,sin eficacia',
            'observaciones' => 'required|string',
            'fecha_evaluacion' => 'required|date',
            'evidencias' => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($hallazgo, $validatedData) {
                // Buscar si ya existe una evaluación (en el ciclo actual)
                $evaluacion = $hallazgo->evaluaciones()->first();

                if ($evaluacion) {
                    $evaluacion->update([
                        'he_responsable_id' => Auth::id(),
                        'he_resultado' => $validatedData['resultado'],
                        'he_comentario' => $validatedData['observaciones'],
                        'he_fecha' => $validatedData['fecha_evaluacion'],
                        'he_evidencias' => $validatedData['evidencias'] ?? $evaluacion->he_evidencias,
                    ]);
                } else {
                    $hallazgo->evaluaciones()->create([
                        'he_responsable_id' => Auth::id(),
                        'he_resultado' => $validatedData['resultado'],
                        'he_comentario' => $validatedData['observaciones'],
                        'he_fecha' => $validatedData['fecha_evaluacion'],
                        'he_evidencias' => $validatedData['evidencias'] ?? [],
                    ]);
                }

                if ($validatedData['resultado'] === 'sin eficacia') {
                    $hallazgo->hallazgo_ciclo = ($hallazgo->hallazgo_ciclo ?? 0) + 1;
                    $hallazgo->hallazgo_estado = 'evaluado';
                }

                $hallazgo->save();
                $hallazgo->verificarYActualizarEstado();
            });

            return response()->json(['message' => 'Evaluación registrada correctamente.']);

        } catch (\Throwable $e) {
            \Log::error("Error al registrar evaluación: " . $e->getMessage());
            return response()->json(['message' => 'Error al registrar la evaluación.', 'error' => $e->getMessage()], 500);
        }
    }

    public function uploadEvaluacionEvidencia(Request $request, Hallazgo $hallazgo)
    {
        // Solo el especialista asignado
        if (auth()->id() !== $hallazgo->especialista_id) {
            return response()->json(['error' => 'No tiene permisos para subir evidencias de evaluación.'], 403);
        }

        $request->validate([
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240', // 10MB max
        ]);

        try {
            $paths = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('evidencias_eficacia', $filename, 'public');
                    $paths[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => '/storage/' . $path,
                    ];
                }
            }

            // Actualizar o crear la evaluación con las nuevas evidencias
            $evaluacion = $hallazgo->evaluaciones()->first();

            if ($evaluacion) {
                $currentEvidencias = $evaluacion->he_evidencias ?? [];
                $evaluacion->he_evidencias = array_merge($currentEvidencias, $paths);
                $evaluacion->save();
            } else {
                $hallazgo->evaluaciones()->create([
                    'he_responsable_id' => Auth::id(), // O null si queremos
                    'he_evidencias' => $paths,
                    // resultado y observaciones son nullable ahora
                ]);
            }

            return response()->json(['message' => 'Archivos subidos correctamente.', 'evidencias' => $paths]);

        } catch (\Throwable $e) {
            \Log::error("Error al subir evidencias: " . $e->getMessage());
            return response()->json(['message' => 'Error al subir archivos.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getEvaluacion(Hallazgo $hallazgo)
    {
        $hallazgo->verificarYActualizarEstado();
        return response()->json($hallazgo->evaluaciones()->with('evaluador:id,name')->latest()->first());
    }

    public function uploadPlanAccion(Request $request, Hallazgo $hallazgo)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480', // 20MB Max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Limpiar nombre de archivo
            $originalName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $file->getClientOriginalName());
            $filename = time() . '_' . $originalName;

            // Guardar en disco publico
            $path = $file->storeAs('hallazgos/planes', $filename, 'public');

            $hallazgo->ruta_plan_accion = $path;
            $hallazgo->save();

            return response()->json([
                'path' => $path,
                'url' => asset('storage/' . $path),
                'message' => 'Plan de acción subido correctamente.'
            ]);
        }

        return response()->json(['error' => 'No se ha subido ningún archivo.'], 400);
    }

    public function enviarPlanAccion(Hallazgo $hallazgo)
    {
        if (!$hallazgo->ruta_plan_accion) {
            return response()->json(['error' => 'Debe adjuntar el plan de acción firmado antes de enviar.'], 422);
        }

        // Cambiar estado a 'aprobado'
        $hallazgo->hallazgo_estado = 'aprobado';
        $hallazgo->save();

        // Verificar si transita a 'en proceso' inmediatamente
        $hallazgo->verificarYActualizarEstado();

        return response()->json(['message' => 'Plan de acción enviado correctamente.', 'estado' => $hallazgo->hallazgo_estado]);
    }

    public function desestimar(Hallazgo $hallazgo)
    {
        // Solo se puede desestimar si no está cerrado
        if ($hallazgo->hallazgo_estado === 'cerrado') {
            return response()->json(['error' => 'No se puede desestimar un hallazgo cerrado.'], 422);
        }

        $hallazgo->hallazgo_estado = 'desestimado';
        $hallazgo->hallazgo_fecha_desestimacion = now();
        $hallazgo->save();

        return response()->json(['message' => 'Hallazgo desestimado correctamente.', 'estado' => $hallazgo->hallazgo_estado]);
    }
}

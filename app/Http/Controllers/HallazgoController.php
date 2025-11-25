<?php

namespace App\Http\Controllers;

use App\Models\Hallazgo;
use App\Models\Proceso;
use App\Models\Accion;
use App\Models\User;
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
        return view('smp.create', compact('breadcrumb'));
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
        $user = Auth::user();

        // 1. Verificar si el usuario tiene OUOs asignadas
        if ($user->ouos()->count() === 0) {
            // Buscar la OUO "Subgerencia de Modernización"
            $ouoModernizacion = \App\Models\OUO::where('ouo_nombre', 'like', '%Subgerencia de Modernización%')->first();

            if ($ouoModernizacion) {
                // Asignar el usuario a esta OUO
                $user->ouos()->attach($ouoModernizacion->id, ['activo' => 1, 'role_in_ouo' => 'miembro']);
            }
        }

        // Recargar las OUOs del usuario
        $user->load('ouos');
        $userOuos = $user->ouos;
        $ouoIds = $userOuos->pluck('id');

        // Obtener los procesos asociados a esas OUOs
        $procesoIds = DB::table('procesos_ouo')->whereIn('id_ouo', $ouoIds)->pluck('id_proceso')->unique();

        // Verificar si hay hallazgos para estos procesos
        $hallazgosCount = Hallazgo::whereHas('procesos', function ($q) use ($procesoIds) {
            $q->whereIn('procesos.id', $procesoIds);
        })->count();

        // Si no hay hallazgos y tenemos procesos, crear 2 hallazgos de prueba
        if ($hallazgosCount === 0 && $procesoIds->isNotEmpty()) {
            $procesoId = $procesoIds->first();
            $proceso = Proceso::find($procesoId);

            if ($proceso) {
                for ($i = 1; $i <= 2; $i++) {
                    // Generar código simple para prueba
                    $correlativo = Hallazgo::where('proceso_id', $procesoId)->count() + $i;
                    $hallazgo_cod = 'SMP-' . $proceso->proceso_sigla . '-INT-' . sprintf('%03d', $correlativo);

                    Hallazgo::create([
                        'hallazgo_cod' => $hallazgo_cod,
                        'proceso_id' => $procesoId,
                        'hallazgo_resumen' => "Hallazgo de prueba generado automáticamente $i",
                        'hallazgo_descripcion' => "Este es un hallazgo generado automáticamente porque la unidad orgánica no tenía hallazgos previos.",
                        'hallazgo_clasificacion' => 'NCM',
                        'hallazgo_origen' => 'Auditoría Interna',
                        'hallazgo_fecha_identificacion' => now(),
                        'hallazgo_estado' => 'creado',
                        'informe_id' => 'INF-AUTO-' . date('Y'),
                    ]);
                }
            }
        }

        // Filtrar hallazgos relacionados con esos procesos (Lógica original recuperada)
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

        $hallazgos = $query->get();

        return response()->json($hallazgos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Generar el valor para hallazgo_cod
        $proceso = Proceso::find($request->proceso_id);
        $ultimoHallazgo = Hallazgo::where('proceso_id', $request->proceso_id)->latest()->first();
        $correlativo = $ultimoHallazgo ? (int) explode('-', $ultimoHallazgo->hallazgo_cod)[3] + 1 : 1;
        $clasificacion = ($request->clasificacion === 'NCM' || $request->clasificacion === 'Ncme') ? 'SMP' : $request->clasificacion;
        $hallazgo_cod = $clasificacion . '-' . $proceso->proceso_sigla . '-' . $request->origen . '-' . sprintf('%03d', $correlativo);

        // Agregar hallazgo_cod al arreglo de datos
        $data = $request->all();
        $data['hallazgo_cod'] = $hallazgo_cod;

        // Crear un nuevo hallazgo con los datos del formulario
        $hallazgo = Hallazgo::create($data);


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

        $planesAccion = Accion::where('hallazgo_id', $id)->get();
        $correctivas = Accion::where('hallazgo_id', $id)->where('es_correctiva', 1)->count();
        $preventivas = Accion::where('hallazgo_id', $id)->where('es_correctiva', 0)->count();
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
        $hallazgo->hallazgo_fecha_aprobacion = Carbon::now()->format('Y-m-d');

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

    public function planes($id)
    {

        $hallazgo = Hallazgo::with('causa')->findOrFail($id);

        $planesAccion = Accion::where('hallazgo_id', $id)->get();
        $correctivas = Accion::where('hallazgo_id', $id)->where('es_correctiva', 1)->count();
        $preventivas = Accion::where('hallazgo_id', $id)->where('es_correctiva', 0)->count();

        $clasificacion = $hallazgo->hallazgo_clasificacion;

        if ($clasificacion == 'NCM' || $clasificacion == "Ncme") {
            $breadcrumb['nombre'] = "Listado de SMP";
            $breadcrumb['codigo'] = "Ncm";
        } elseif ($hallazgo->hallazgo_clasificacion == 'Obs') {
            $breadcrumb['nombre'] = "Listado de Observaciones";
            $breadcrumb['codigo'] = "Obs";
        } elseif ($hallazgo->hallazgo_clasificacion == 'Odm') {
            $breadcrumb['nombre'] = "Listado de Oportunidades de mejora";
            $breadcrumb['codigo'] = "Odm";
        }

        return view('smp.plan', compact('planesAccion', 'hallazgo', 'correctivas', 'preventivas', 'breadcrumb'));
    }

    public function dashboard(Request $request)
    {
        $sig = $request->sig;
        // Crear una consulta base con el filtro de clasificación para grafico pie
        $classifications = ['Ncme', 'NCM'];

        $query = Hallazgo::whereIn('hallazgo_clasificacion', $classifications);
        $queryBase = $query->filterBySig($sig);


        $smpAbiertas = (clone $queryBase)
            ->where('estado', 'Abierto')
            ->count();

        $smpImplementacion = (clone $queryBase)
            ->whereIn('estado', ['En implementación', 'Aprobado'])
            ->count();

        $smpPendientes = (clone $queryBase)
            ->where('estado', 'Pendiente')
            ->count();

        $smpCerradas = (clone $queryBase)
            ->where('estado', 'Cerrado')
            ->count();

        // Crear una consulta de clasificacion para tabla proceso 

        $clasificacionFiltro = function ($query) {
            $query->whereIn('hallazgo_clasificacion', ['Ncme', 'NCM']);
        };

        $estadoSmpData = Proceso::select('id', 'cod_proceso', 'proceso_nombre as proceso')
            ->withCount([
                'hallazgos as abiertos' => function ($query) use ($clasificacionFiltro, $sig) {
                    $query->where('estado', 'Abierto')->where($clasificacionFiltro)->filterBySig($sig);
                },
                'hallazgos as pendientes' => function ($query) use ($clasificacionFiltro, $sig) {
                    $query->where('estado', 'Pendiente')->where($clasificacionFiltro)->filterBySig($sig);
                },
                'hallazgos as implementaciones' => function ($query) use ($clasificacionFiltro, $sig) {
                    $query->whereIn('estado', ['En implementación', 'Aprobado'])->where($clasificacionFiltro)->filterBySig($sig);
                },
                'hallazgos as cerradas' => function ($query) use ($clasificacionFiltro, $sig) {
                    $query->where('estado', 'Cerrado')->where($clasificacionFiltro)->filterBySig($sig);
                }
            ])
            ->get()
            ->filter(function ($proceso) {
                return $proceso->abiertos > 0 || $proceso->pendientes > 0 || $proceso->implementaciones > 0 || $proceso->cerradas > 0;
            });

        $estadoSmpData->each(function ($proceso) {
            $proceso->total = $proceso->abiertos + $proceso->pendientes + $proceso->implementaciones + $proceso->cerradas;
        });


        $totalAbiertas = $estadoSmpData->sum('abiertos');
        $totalPendientes = $estadoSmpData->sum('pendientes');
        $totalImplementacion = $estadoSmpData->sum('implementaciones');
        $totalCerradas = $estadoSmpData->sum('cerradas');
        $totalHallazgos = $totalAbiertas + $totalPendientes + $totalImplementacion + $totalCerradas;

        // Crear una consulta de clasificacion para gráfico SMP 
        $hallazgos = Hallazgo::where('sig', $sig)->get();

        $estados = ['Abierto', 'En implementación', 'Pendiente', 'Cerrado'];
        $smp = [];
        foreach ($classifications as $clasificacion) {
            $smp[$clasificacion] = [
                'Abierto' => 0,
                'En implementación' => 0, // Combina estos dos estados
                'Pendiente' => 0,
                'Cerrado' => 0
            ];

            foreach ($estados as $estado) {
                if ($estado == 'En implementación') {
                    // Combina las cuentas de 'Aprobado' y 'En implementación'
                    $smp[$clasificacion]['En implementación'] = $hallazgos->where('hallazgo_clasificacion', $clasificacion)
                        ->whereIn('estado', ['En implementación', 'Aprobado'])->count();

                } else {
                    $smp[$clasificacion][$estado] = $hallazgos->where('hallazgo_clasificacion', $clasificacion)
                        ->where('estado', $estado)->count();

                }
            }
        }

        // Crear consulta para la tabla observaciones.
        $clasificacionFiltro = function ($query) {
            $query->whereIn('hallazgo_clasificacion', ['Obs', 'Odm']);
        };
        $estadoObsData = Proceso::select('id', 'cod_proceso', 'proceso_nombre as proceso')
            ->withCount([
                'hallazgos as abiertos' => function ($query) use ($clasificacionFiltro, $sig) {
                    $query->where('estado', 'Abierto')->where($clasificacionFiltro)->filterBySig($sig);
                },
                'hallazgos as pendientes' => function ($query) use ($clasificacionFiltro, $sig) {
                    $query->where('estado', 'Pendiente')->where($clasificacionFiltro)->filterBySig($sig);
                },
                'hallazgos as implementaciones' => function ($query) use ($clasificacionFiltro, $sig) {
                    $query->whereIn('estado', ['En implementación', 'Aprobado'])->where($clasificacionFiltro)->filterBySig($sig);
                },
                'hallazgos as cerradas' => function ($query) use ($clasificacionFiltro, $sig) {
                    $query->where('estado', 'Cerrado')->where($clasificacionFiltro)->filterBySig($sig);
                    ;
                }
            ])
            ->get()
            ->filter(function ($proceso) {
                return $proceso->abiertos > 0 || $proceso->pendientes > 0 || $proceso->implementaciones > 0 || $proceso->cerradas > 0;
            });

        $estadoObsData->each(function ($proceso) {
            $proceso->total = $proceso->abiertos + $proceso->pendientes + $proceso->implementaciones + $proceso->cerradas;
        });

        $ObsAbiertas = $estadoObsData->sum('abiertos');
        $ObsPendientes = $estadoObsData->sum('pendientes');
        $ObsImplementacion = $estadoObsData->sum('implementaciones');
        $ObsCerradas = $estadoObsData->sum('cerradas');
        $ObsTotal = $ObsAbiertas + $ObsPendientes + $ObsImplementacion + $ObsCerradas;


        return view(
            'smp.dashboard',
            compact(
                'smpAbiertas',
                'smpImplementacion',
                'smpPendientes',
                'smpCerradas',
                'estadoSmpData',
                'estadoObsData',
                'totalAbiertas',
                'totalPendientes',
                'totalImplementacion',
                'totalCerradas',
                'totalHallazgos',
                'ObsAbiertas',
                'ObsPendientes',
                'ObsImplementacion',
                'ObsCerradas',
                'ObsTotal',
                'smp'
            )
        );
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
        $procesoIds = ProcesoOuo::whereIn('id_ouo', $ouoIds)->pluck('id_proceso')->unique();

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
                $specialistName = User::find($validatedData['especialista_id'])->name;

                // Create a HallazgoMovimientos record
                $hallazgo->movimientos()->create([ // Assuming 'movimientos' is the relationship to HallazgoMovimientos
                    'estado' => 'asignado',
                    'comentario' => "Hallazgo asignado a {$specialistName} por {$validatedData['assigned_by_user_name']}.",
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
        $query = Hallazgo::with(['procesos', 'acciones', 'especialista', 'evaluaciones'])
            ->where('hallazgo_estado', 'concluido');

        // Si no es admin, filtrar solo los asignados al especialista
        if (!$user->hasRole('admin')) {
            $query->where('especialista_id', $user->id);
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

        return response()->json($query->latest()->get());
    }

    // Método para guardar la evaluación de eficacia
    public function storeEvaluacion(Request $request, Hallazgo $hallazgo)
    {
        $validatedData = $request->validate([
            'resultado' => 'required|in:con eficacia,sin eficacia',
            'observaciones' => 'required|string',
            'fecha_evaluacion' => 'required|date',
            'evidencias' => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($hallazgo, $validatedData) {
                // Buscar si ya existe una evaluación
                $evaluacion = $hallazgo->evaluaciones()->first();

                if ($evaluacion) {
                    $evaluacion->update([
                        'evaluador_id' => Auth::id(),
                        'resultado' => $validatedData['resultado'],
                        'observaciones' => $validatedData['observaciones'],
                        'fecha_evaluacion' => $validatedData['fecha_evaluacion'],
                        'evidencias' => $validatedData['evidencias'] ?? $evaluacion->evidencias,
                    ]);
                } else {
                    $hallazgo->evaluaciones()->create([
                        'evaluador_id' => Auth::id(),
                        'resultado' => $validatedData['resultado'],
                        'observaciones' => $validatedData['observaciones'],
                        'fecha_evaluacion' => $validatedData['fecha_evaluacion'],
                        'evidencias' => $validatedData['evidencias'] ?? [],
                    ]);
                }

                // Determinar el nuevo estado según el resultado
                if ($validatedData['resultado'] === 'sin eficacia') {
                    // Incrementar el ciclo
                    $hallazgo->hallazgo_ciclo = ($hallazgo->hallazgo_ciclo ?? 0) + 1;
                    $hallazgo->hallazgo_estado = 'evaluado';
                    // NO establecer fecha de cierre
                } else { // 'con eficacia'
                    $hallazgo->hallazgo_estado = 'cerrado';
                    $hallazgo->hallazgo_fecha_cierre = now();
                }

                $hallazgo->save();

                // El HallazgoObserver se encargará de registrar el movimiento automáticamente
            });

            return response()->json(['message' => 'Evaluación registrada correctamente.']);

        } catch (\Throwable $e) {
            \Log::error("Error al registrar evaluación: " . $e->getMessage());
            return response()->json(['message' => 'Error al registrar la evaluación.', 'error' => $e->getMessage()], 500);
        }
    }

    public function uploadEvaluacionEvidencia(Request $request, Hallazgo $hallazgo)
    {
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
                $currentEvidencias = $evaluacion->evidencias ?? [];
                $evaluacion->evidencias = array_merge($currentEvidencias, $paths);
                $evaluacion->save();
            } else {
                $hallazgo->evaluaciones()->create([
                    'evaluador_id' => Auth::id(), // O null si queremos
                    'evidencias' => $paths,
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
        return response()->json($hallazgo->evaluaciones()->first());
    }
}

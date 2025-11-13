<?php

namespace App\Http\Controllers;

use App\Models\Hallazgo;
use App\Models\Proceso;
use App\Models\Accion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\ActionApprovedNotificacion;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Add this line
class HallazgoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $clasificacion)
    {
        $breadcrumb = [];
        $clasificacionArray = explode(',', $clasificacion);
        $hallazgos = Hallazgo::query()
            ->filterBySig($request->sig)
            ->filterByInformeId($request->informe_id)
            ->filterByYear($request->year)
            ->filterByClasificacion($clasificacionArray)
            ->get();

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
        $query = Hallazgo::with('procesos')->latest(); // `latest()` ordena por fecha de creación descendente

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

        // --- Ejecución y Respuesta ---

        // Ejecuta la consulta construida y obtiene los resultados
        $hallazgos = $query->get();

        // Devuelve los resultados en formato JSON
        return response()->json($hallazgos);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Generar el valor para smp_cod
        $proceso = Proceso::find($request->proceso_id);
        $ultimoHallazgo = Hallazgo::where('proceso_id', $request->proceso_id)->latest()->first();
        $correlativo = $ultimoHallazgo ? (int) explode('-', $ultimoHallazgo->smp_cod)[3] + 1 : 1;
        $clasificacion = ($request->clasificacion === 'NCM' || $request->clasificacion === 'Ncme') ? 'SMP' : $request->clasificacion;
        $smp_cod = $clasificacion . '-' . $proceso->sigla . '-' . $request->origen . '-' . sprintf('%03d', $correlativo);

        // Agregar smp_cod al arreglo de datos
        $data = $request->all();
        $data['smp_cod'] = $smp_cod;

        // Crear un nuevo hallazgo con los datos del formulario
        $hallazgo = Hallazgo::create($data);


        // Redirigir a alguna vista o página después de guardar los datos
        return response()->json($hallazgo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Hallazgo $hallazgo)
    {
        // Devuelve el hallazgo como JSON
        return response()->json($hallazgo->load('procesos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hallazgo_id)
    {

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
        return $pdf->stream('hallazgo_' . $hallazgo->smp_cod . '.pdf');

    }

    public function aprobar(Request $request, $id)
    {
        $hallazgo = Hallazgo::findOrFail($id);
        // Actualizar el estado a 'enviado'
        $hallazgo->estado = 'Aprobado';
        $hallazgo->fecha_aprobacion = Carbon::now()->format('Y-m-d');


        // Obtener las acciones relacionadas con el hallazgo
        $acciones = $hallazgo->acciones()->get();
        $hallazgo->save();
        // Enviar notificación a los correos de los responsables
        foreach ($acciones as $accion) {
            $responsableCorreo = $accion->responsable_correo;

            // Enviar la notificación por correo
            Notification::route('mail', $responsableCorreo)->notify(new ActionApprovedNotificacion($accion));
        }
        return redirect()->back()->with('success', 'Se ha aprobado el plan de acción');
    }

    public function planes($id)
    {

        $hallazgo = Hallazgo::with('causa')->findOrFail($id);

        $planesAccion = Accion::where('hallazgo_id', $id)->get();
        $correctivas = Accion::where('hallazgo_id', $id)->where('es_correctiva', 1)->count();
        $preventivas = Accion::where('hallazgo_id', $id)->where('es_correctiva', 0)->count();

        $clasificacion = $hallazgo->clasificacion;

        if ($clasificacion == 'NCM' || $clasificacion == "Ncme") {
            $breadcrumb['nombre'] = "Listado de SMP";
            $breadcrumb['codigo'] = "Ncm";
        } elseif ($hallazgo->clasificacion == 'Obs') {
            $breadcrumb['nombre'] = "Listado de Observaciones";
            $breadcrumb['codigo'] = "Obs";
        } elseif ($hallazgo->clasificacion == 'Odm') {
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

        $query = Hallazgo::whereIn('clasificacion', $classifications);
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
            $query->whereIn('clasificacion', ['Ncme', 'NCM']);
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
                    $smp[$clasificacion]['En implementación'] = $hallazgos->where('clasificacion', $clasificacion)
                        ->whereIn('estado', ['En implementación', 'Aprobado'])->count();

                } else {
                    $smp[$clasificacion][$estado] = $hallazgos->where('clasificacion', $clasificacion)
                        ->where('estado', $estado)->count();

                }
            }
        }

        // Crear consulta para la tabla observaciones.
        $clasificacionFiltro = function ($query) {
            $query->whereIn('clasificacion', ['Obs', 'Odm']);
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
        $hallazgos = $proceso->hallazgos()->filterByClasificacion($clasificacionArray)->get();

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


}

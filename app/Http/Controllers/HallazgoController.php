<?php

namespace App\Http\Controllers;

use App\Models\Hallazgo;
use App\Models\Proceso;
use App\Models\Accion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class HallazgoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $clasificacion = null)
    {
        $breadcrumb = [];
        $hallazgos = Hallazgo::query()
            ->filterBySig($request->sig)
            ->filterByInformeId($request->informe_id)
            ->filterByYear($request->year)
            ->filterByClasificacion($clasificacion)
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

    /**
     * Show the form for creating a new resource.
     */
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
        Hallazgo::create($data);

        $clasificacion = ($request->clasificacion === 'NCM' || $request->clasificacion === 'Ncme') ? 'Ncm' : $request->clasificacion;

        // Redirigir a alguna vista o página después de guardar los datos
        return redirect()->route('smp.index', ['clasificacion' => $request->clasificacion])
            ->with('success', 'SMP creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hallazgo_id)
    {
        $hallazgo = Hallazgo::findOrFail($hallazgo_id);
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

        return view('smp.edit', compact('hallazgo', 'breadcrumb'));
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
    public function update(Request $request, $hallazgo_id)
    {
        $hallazgo = Hallazgo::findOrFail($hallazgo_id);
        $hallazgo->resumen = $request->resumen;
        $hallazgo->descripcion = $request->descripcion;
        $hallazgo->origen = $request->origen;
        $hallazgo->informe_id = $request->informe_id;
        $hallazgo->proceso_id = $request->proceso_id;
        $hallazgo->criterio = $request->criterio;
        $hallazgo->auditor = $request->auditor;
        $hallazgo->auditor_tipo = $request->auditor_tipo;
        $hallazgo->clasificacion = $request->clasificacion;
        $hallazgo->sig = $request->sig;
        $hallazgo->evidencia = $request->evidencia;
        $hallazgo->fecha_solicitud = $request->fecha_solicitud;

        $hallazgo->save();
        return redirect()->route('smp.index', ['clasificacion' => $request->clasificacion])
            ->with('success', 'SMP actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hallazgo $hallazgo)
    {
        //
    }
    public function asignarEspecialista(Request $request, $hallazgo_id)
    {
        $hallazgo = Hallazgo::findOrFail($hallazgo_id);
        $especialista_id = $request->especialista_id;
        $motivo_asignacion = "15";

        try {
            // Sincronizar el especialista con el hallazgo

            $hallazgo->especialistas()->sync([$especialista_id => ['fecha_asignacion' => now(), 'motivo_asignacion' => $motivo_asignacion]], false);


            // Si la sincronización fue exitosa, devolver un mensaje de éxito
            return redirect()->route('smp.index')->with('success', 'Hallazgo actualizado correctamente');
        } catch (\Exception $e) {
            // Si ocurrió un error durante la sincronización, devolver un mensaje de error
            return redirect()->route('smp.index')->with('error', 'Error al asignar especialista: ' . $e->getMessage());
        }
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
        $hallazgo->save();
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

    public function dashboard()
    {
        $smpAbiertas = Hallazgo::where('estado', 'Abierto')->count();
        $smpImplementacion = Hallazgo::whereIn('estado', ['En implementación', 'Aprobado'])->count();
        $smpPendientes = Hallazgo::where('estado', 'Pendiente')->count();
        $smpCerradas = Hallazgo::where('estado', 'Cerrado')->count();


        $estadoSmpData = Proceso::select('nombre as proceso')
            ->withCount([
                'hallazgos as abiertos' => function ($query) {
                    $query->where('estado', 'Abierto');
                },
                'hallazgos as pendientes' => function ($query) {
                    $query->where('estado', 'Pendiente');
                },
                'hallazgos as implementaciones' => function ($query) {
                    $query->whereIn('estado', ['En implementación', 'Aprobado']);
                },
                'hallazgos as cerradas' => function ($query) {
                    $query->where('estado', 'Cerrado');
                }
            ])
            ->get()
            ->filter(function ($proceso) {
                return $proceso->abiertos > 0 || $proceso->pendientes > 0 || $proceso->implementaciones > 0 || $proceso->cerradas > 0;
            });

        $totalAbiertas = $estadoSmpData->sum('abiertos');
        $totalPendientes = $estadoSmpData->sum('pendientes');
        $totalImplementacion = $estadoSmpData->sum('implementaciones');
        $totalCerradas = $estadoSmpData->sum('cerradas');
        $totalHallazgos = $totalAbiertas + $totalPendientes + $totalImplementacion + $totalCerradas;

        $hallazgos = Hallazgo::all();

        $clasificaciones = ['NCM', 'Ncme', 'Obs', 'Odm']; // Agrega las clasificaciones que necesites
        $estados = ['Abierto', 'Aprobado', 'En implementación', 'Pendiente', 'Cerrado'];

        $smp = [];
        foreach ($clasificaciones as $clasificacion) {
            $smp[$clasificacion] = [
                'Abierto' => 0,
                'En implementación' => 0, // Combina estos dos estados
                'Pendiente' => 0,
                'Cerrado' => 0
            ];

            foreach ($estados as $estado) {
                if ($estado == 'Aprobado' || $estado == 'En implementación') {
                    // Combina las cuentas de 'Aprobado' y 'En implementación'
                    $smp[$clasificacion]['En implementación'] += $hallazgos->where('clasificacion', $clasificacion)->where('estado', $estado)->count();
                } else {
                    $smp[$clasificacion][$estado] = $hallazgos->where('clasificacion', $clasificacion)->where('estado', $estado)->count();
                }
            }
        }

        return view(
            'smp.dashboard',
            compact(
                'smpAbiertas',
                'smpImplementacion',
                'smpPendientes',
                'smpCerradas',
                'estadoSmpData',
                'totalAbiertas',
                'totalPendientes',
                'totalImplementacion',
                'totalCerradas',
                'totalHallazgos',
                'smp'
            )
        );
    }

}

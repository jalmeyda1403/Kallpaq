<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Indicador;
use App\Models\IndicadorHistorico;
use App\Models\IndicadorSeguimiento;
use App\Models\Configuracion;
use App\Models\Proceso;
use App\Models\PlanificacionSIG;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class IndicadorController extends Controller
{
    public function index()
    {
        $user = User::find(auth()->user()->id);
        $configuracion = new Configuracion();
        $fecha_inicio_bloqueo = Configuracion::getFechaInicioBloqueada();
        $fecha_fin_bloqueo = Configuracion::getFechaFinBloqueada();
        $procesos = $user->procesos()->with('indicadores')->get();
        $procesos->transform(function ($proceso) {
            return $proceso->setRelation('indicadores', $proceso->indicadores->sortBy('id'));
        });

        return view('indicadores.index', compact('procesos', 'fecha_inicio_bloqueo', 'fecha_fin_bloqueo'));
    }


    public function listarIndicadores($proceso_id = null)
    {
        $proceso = Proceso::with('subprocesos.indicadores')->findOrFail($proceso_id);
        $indicadores = $proceso->indicadores;
        
        // Obtener los indicadores de los procesos hijos
        foreach ($proceso->subprocesos as $hijo) {
            $indicadores = $indicadores->merge($hijo->indicadores);
        }
       
        return view('indicadores.index', compact('proceso', 'indicadores'));
    }

    public function create($proceso_id = null)
    {
        $frecuencias = Config::get('opciones.frecuencias');
        $tiposIndicador = Config::get('opciones.tipos_indicador');
        $tiposAgregacion = Config::get('opciones.tipos_agregacion');
        $parametroMedida = Config::get('opciones.parametro_medida');
        $sentido = Config::get('opciones.sentido');

        return view('indicadores.create', compact('proceso_id', 'frecuencias', 'tiposIndicador', 'tiposAgregacion', 'parametroMedida', 'sentido'));
    }

    public function store(Request $request)
    {
        // Validación y almacenamiento de los datos
        Indicador::create($request->all());
        return redirect()->route('indicadores.index')->with('success', 'Indicador creado exitosamente.');
    }

    public function edit($id)
    {
        $indicador = Indicador::findOrFail($id);
        $frecuencias = Config::get('opciones.frecuencias');
        $tiposIndicador = Config::get('opciones.tipos_indicador');
        $tiposAgregacion = Config::get('opciones.tipos_agregacion');
        $parametroMedida = Config::get('opciones.parametro_medida');
        $sentido = Config::get('opciones.sentido');


        // Código para cargar los datos necesarios para la edición

        return view('indicadores.edit', compact('indicador', 'frecuencias', 'tiposIndicador', 'tiposAgregacion', 'parametroMedida', 'sentido'));
    }

    public function update(Request $request, $id)
    {
        // Validación y actualización de los datos

        $data = $request->except(['proceso_nombre', 'indicador_id']);
        $indicador = Indicador::findOrFail($id);
        // Actualizar el indicador con los datos filtrados
        $indicador->update($data);

        $indicador->update($request->all());
        return redirect()->route('indicadores.index')->with('success', 'Indicador actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $indicador = Indicador::findOrFail($id);
        $indicador->delete();
        return redirect()->route('indicadores.index')->with('success', 'Indicador eliminado exitosamente.');
    }
    public function generarFrecuencia($id)
    {
        $periodo_actual = Config::get('opciones.periodo.actual');


        try {

            DB::statement("CALL generar_frecuencias(?, ?)", [$id, $periodo_actual]);

            return redirect()->route('indicadores.index')->with('success', 'Procedimiento almacenado ejecutado correctamente.');
        } catch (\Exception $e) {

            return redirect()->route('indicadores.index')->with('error', 'Hubo un error al ejecutar el procedimiento almacenado.');
        }
    }

    public function limpiar_frecuencia($frecuencia, $id)
    {

    }

    public function showHistorico($id)
    {  // Obtener los datos históricos asociados a este indicador
        $indicador = Indicador::findOrFail($id);
        $historicalData = IndicadorHistorico::where('indicador_id', $id)->get();
        return response()->json($historicalData);

    }
    // Indicador_Seguimiento
    public function showDatos($id)
    {
        $configuracion = new Configuracion();
        $fecha_inicio_bloqueo = Configuracion::getFechaInicioBloqueada();
        $fecha_fin_bloqueo = Configuracion::getFechaFinBloqueada();
        $indicador = Indicador::findOrFail($id);

        $data = IndicadorSeguimiento::where('indicador_id', $id)->get();

        $data->each(function ($item) use ($fecha_inicio_bloqueo, $fecha_fin_bloqueo) {
            $item->editable = !($item->fecha <= $fecha_fin_bloqueo && $item->fecha >= $fecha_inicio_bloqueo);
        });

        return response()->json($data);


    }

    public function editDatos($id)
    {
        $indicadorSeguimiento = IndicadorSeguimiento::with('indicador')->findOrFail($id);
        $indicadorFields = $indicadorSeguimiento->indicador->getAttributes();

        $indicadorSeguimientoFields = $indicadorSeguimiento->getAttributes();

        $mergedFields = array_merge_recursive($indicadorFields, $indicadorSeguimientoFields);


        return response()->json($mergedFields);
    }

    public function updateDatos(Request $request, $id)
    {
        try {
            $indicadorSeguimiento = IndicadorSeguimiento::findOrFail($id);
            // Actualizar datos del indicadorSeguimiento    
            $indicadorSeguimiento->meta = $request->meta;
            $indicadorSeguimiento->var1 = $request->var1;
            $indicadorSeguimiento->var2 = $request->var2;
            $indicadorSeguimiento->var3 = $request->var3;
            $indicadorSeguimiento->var4 = $request->var4;
            $indicadorSeguimiento->var5 = $request->var5;
            $indicadorSeguimiento->var6 = $request->var6;
            $indicadorSeguimiento->estado = $request->var6;

            // Obtener los valores de las variables y la fórmula ingresados por el usuario
            $var1Value = $indicadorSeguimiento->var1;
            $var2Value = $indicadorSeguimiento->var2;
            $var3Value = $indicadorSeguimiento->var3;
            $var4Value = $indicadorSeguimiento->var4;
            $var5Value = $indicadorSeguimiento->var5;
            $var6Value = $indicadorSeguimiento->var6;
            $formula = $indicadorSeguimiento->indicador->formula;

            // Reemplazar los nombres de las variables en la fórmula con sus valores
            $formula = str_replace('var1', $var1Value, $formula);
            $formula = str_replace('var2', $var2Value, $formula);
            $formula = str_replace('var3', $var3Value, $formula);
            $formula = str_replace('var4', $var4Value, $formula);
            $formula = str_replace('var5', $var5Value, $formula);
            $formula = str_replace('var6', $var6Value, $formula);

            // calcular valor segun formula
            $indicadorSeguimiento->valor = eval ("return $formula;");
            $valor = $indicadorSeguimiento->valor;

            // Evaluar y asignar el estado del indicador
            if ($valor >= $indicadorSeguimiento->meta) {
                $indicadorSeguimiento->estado = 'bueno';
            } elseif ($valor >= 0.85 * $indicadorSeguimiento->meta) {
                $indicadorSeguimiento->estado = 'regular';
            } else {
                $indicadorSeguimiento->estado = 'malo';
            }
            // Guarda los cambios en el indicadorSeguimiento
            $indicadorSeguimiento->save();

            $message = "Los datos del indicador se han actualizado correctamente";
            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Throwable $e) {
            $message = "Error al guardar los datos: " . $e->getMessage();
            return response()->json(['success' => false, 'message' => $message]);
        }
    }
    // formula
    public function formula($id)
    {
        $indicador = Indicador::findOrFail($id);
        return view('indicadores.formula', compact('indicador'));
    }


    public function validarFormula(Request $request)
    {
        // Obtener los valores de las variables y la fórmula ingresados por el usuario
        $var1Value = $request->input('var1_value', 0);
        $var2Value = $request->input('var2_value', 0);
        $var3Value = $request->input('var3_value', 0);
        $var4Value = $request->input('var4_value', 0);
        $var5Value = $request->input('var5_value', 0);
        $var6Value = $request->input('var6_value', 0);
        $formula = $request->input('formula');

        // Validar fórmula
        try {

            // Reemplazar los nombres de las variables en la fórmula con sus valores
            $formula = str_replace('var1', $var1Value, $formula);
            $formula = str_replace('var2', $var2Value, $formula);
            $formula = str_replace('var3', $var3Value, $formula);
            $formula = str_replace('var4', $var4Value, $formula);
            $formula = str_replace('var5', $var5Value, $formula);
            $formula = str_replace('var6', $var6Value, $formula);

            if (!preg_match('/^[0-9+\-*\/().\s]+$/', $formula)) {
                throw new Exception("La fórmula contiene caracteres no permitidos.");
            }
            // Evaluar fórmula
            $result = eval ("return $formula;");
            $message = "La fórmula es válida. El resultado es: $result";
            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Throwable $e) {
            $message = "Error al evaluar la fórmula: " . $e->getMessage();
            return response()->json(['success' => false, 'message' => $message]);
        }
    }


    public function actualizarFormula(Request $request, $indicadorId)
    {
        $indicador = Indicador::findOrFail($indicadorId);

        $indicador->formula = $request->formula;
        $indicador->var1 = $request->var1_definition;
        $indicador->var2 = $request->var2_definition;
        $indicador->var3 = $request->var3_definition;
        $indicador->var4 = $request->var4_definition;
        $indicador->var5 = $request->var5_definition;
        $indicador->var6 = $request->var6_definition;
        try {
            $indicador->save();
            $message = "La fórmula se grabo correctamente";
            $request->session()->flash('success', $message);

        } catch (\Throwable $e) {
            $message = "Error al grabar la fórmula: " . $e->getMessage();
            $request->session()->flash('error', $message);
        }

        // Pasar los datos de vuelta a la vista
        return redirect()->route('indicadores.index')->with('success', $message);
    }


}
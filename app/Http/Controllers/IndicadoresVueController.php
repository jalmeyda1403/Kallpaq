<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use App\Models\Proceso;
use App\Models\IndicadorSeguimiento;
use App\Models\OUO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IndicadoresVueController extends Controller
{
    public function index()
    {
        return view('indicadores.vue_index');
    }

    // Obtener procesos donde el usuario es líder (Gestor)
    public function getProcesosGestion()
    {
        $user = Auth::user();
        
        // Asumiendo que el usuario tiene una OUO asociada y verificamos si es propietario en la tabla pivote
        // O si tiene un rol específico que le permite ver todos.
        // Por ahora, basándonos en la lógica de "Propietario" en procesos_ouo
        
        $procesos = Proceso::whereHas('ouos', function ($query) use ($user) {
            $query->where('ouo_id', $user->ouo_id) // Asumiendo que el user tiene ouo_id, si no, hay que buscarlo
                  ->where('propietario', 1);
        })->get();

        // Si el usuario no tiene OUO directa en la tabla users, buscamos sus OUOs
        if ($procesos->isEmpty()) {
             // Lógica alternativa si la relación es diferente, por ejemplo a través de ouo_user
             $ouoIds = $user->ouos->pluck('id');
             $procesos = Proceso::whereHas('ouos', function ($query) use ($ouoIds) {
                $query->whereIn('ouo_id', $ouoIds)
                      ->where('propietario', 1);
            })->get();
        }

        return response()->json($procesos);
    }

    public function getIndicadoresByProceso($procesoId)
    {
        $indicadores = Indicador::where('proceso_id', $procesoId)->get();
        return response()->json($indicadores);
    }

    public function storeIndicador(Request $request)
    {
        $validated = $request->validate([
            'proceso_id' => 'required|exists:procesos,id',
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'formula' => 'nullable|string',
            'frecuencia' => 'required|string',
            'meta' => 'required|numeric',
            // Agregar validaciones para variables
        ]);

        $indicador = Indicador::create($request->all());
        return response()->json(['message' => 'Indicador creado correctamente', 'indicador' => $indicador]);
    }

    public function updateIndicador(Request $request, $id)
    {
        $indicador = Indicador::findOrFail($id);
        $indicador->update($request->all());
        return response()->json(['message' => 'Indicador actualizado correctamente', 'indicador' => $indicador]);
    }

    // Obtener indicadores que la OUO del usuario debe reportar
    public function getIndicadoresForOUO()
    {
        $user = Auth::user();
        // Aquí necesitamos saber qué indicadores aplican a la OUO del usuario.
        // Asumimos que si la OUO está asociada al proceso (como ejecutor o delegado), debe reportar.
        
        $ouoIds = $user->ouos->pluck('id');
        
        $procesosIds = DB::table('procesos_ouo')
            ->whereIn('id_ouo', $ouoIds)
            ->pluck('id_proceso');

        $indicadores = Indicador::whereIn('proceso_id', $procesosIds)
            ->with('proceso') // Para mostrar nombre del proceso
            ->get();

        return response()->json($indicadores);
    }

    public function storeSeguimiento(Request $request, $id)
    {
        try {
            $indicador = Indicador::findOrFail($id);
            
            // Buscar si ya existe un seguimiento para este mes/periodo o crear uno nuevo
            // Por simplicidad para este MVP, creamos uno nuevo o actualizamos el de la fecha actual si existe
            // Ajustar según lógica de negocio real (ej: un registro por mes)
            
            $seguimiento = IndicadorSeguimiento::where('indicador_id', $id)
                ->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->first();

            if (!$seguimiento) {
                $seguimiento = new IndicadorSeguimiento();
                $seguimiento->indicador_id = $id;
                $seguimiento->fecha = now();
            }

            $seguimiento->meta = $indicador->meta; // Usamos la meta del indicador
            $seguimiento->var1 = $request->var1;
            $seguimiento->var2 = $request->var2;
            $seguimiento->var3 = $request->var3;
            $seguimiento->var4 = $request->var4;
            $seguimiento->var5 = $request->var5;
            $seguimiento->var6 = $request->var6;

            // Calcular valor
            $formula = $indicador->formula;
            // Reemplazar variables
            for ($i = 1; $i <= 6; $i++) {
                $val = $request->input('var' . $i, 0);
                $formula = str_replace('var' . $i, $val, $formula);
            }

            // Evaluar fórmula (con precaución, idealmente usar un parser seguro)
            // Aquí usamos eval como en el controlador original
            try {
                $valor = eval("return $formula;");
            } catch (\Throwable $e) {
                return response()->json(['message' => 'Error en la fórmula: ' . $e->getMessage()], 422);
            }

            $seguimiento->valor = $valor;

            // Evaluar estado
            if ($valor >= $seguimiento->meta) {
                $seguimiento->estado = 'bueno';
            } elseif ($valor >= 0.85 * $seguimiento->meta) {
                $seguimiento->estado = 'regular';
            } else {
                $seguimiento->estado = 'malo';
            }

            $seguimiento->save();
            
            return response()->json(['message' => 'Seguimiento registrado correctamente', 'data' => $seguimiento]);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al guardar: ' . $e->getMessage()], 500);
        }
    }
}

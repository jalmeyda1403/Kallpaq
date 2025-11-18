<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\InventarioProceso; // Asegúrate de que este modelo exista
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    // Endpoint API para obtener procesos con OUOs de un inventario específico
    public function apiProcesosConOuos($idInventario)
    {
        $inventario = Inventario::findOrFail($idInventario);
        $ultimoInventario = Inventario::latest('vigencia')->first(); // O por ID si vigencia no es confiable para ordenar versiones

        // Asumiendo que el modelo Proceso existe y la tabla es 'procesos'
        // Asumiendo que el modelo OUO existe y la tabla es 'ouos'
        // Construir la consulta con joins para obtener los nombres de las OUOs y el conteo de subprocesos
        $procesosConOuos = \App\Models\Proceso::join('inventario_procesos', 'procesos.id', '=', 'inventario_procesos.id_proceso')
            ->leftJoin('ouos as ouo_p', 'inventario_procesos.id_ouo_propietario', '=', 'ouo_p.id') // Join para Propietario
            ->leftJoin('ouos as ouo_d', 'inventario_procesos.id_ouo_delegado', '=', 'ouo_d.id')   // Join para Delegado
            ->leftJoin('ouos as ouo_e', 'inventario_procesos.id_ouo_ejecutor', '=', 'ouo_e.id')   // Join para Ejecutor
            ->leftJoin(\DB::raw('(SELECT cod_proceso_padre, COUNT(*) as subprocesos_count FROM procesos GROUP BY cod_proceso_padre) as subprocs'), 'procesos.id', '=', 'subprocs.cod_proceso_padre') // Join para contar subprocesos
            ->where('inventario_procesos.id_inventario', $idInventario) // Filtrar por inventario objetivo
            ->select(
                'procesos.*', // Todos los campos de proceso
                'inventario_procesos.estado as pivot_estado', // Alias para el estado del pivote
                'inventario_procesos.inactive_at as pivot_inactive_at',
                'inventario_procesos.created_at as pivot_created_at',
                'inventario_procesos.updated_at as pivot_updated_at',
                // Alias para los IDs y Nombres de OUOs
                'inventario_procesos.id_ouo_propietario',
                'ouo_p.ouo_nombre as nombre_ouo_propietario', // Nombre del propietario
                'inventario_procesos.id_ouo_delegado',
                'ouo_d.ouo_nombre as nombre_ouo_delegado', // Nombre del delegado
                'inventario_procesos.id_ouo_ejecutor',
                'ouo_e.ouo_nombre as nombre_ouo_ejecutor',  // Nombre del ejecutor
                // Conteo de subprocesos
                \DB::raw('COALESCE(subprocs.subprocesos_count, 0) as subprocesos_count') // COALESCE para manejar procesos sin subprocesos
            )
            ->get();

        // Calcular estado_en_ultimo
        foreach ($procesosConOuos as $proceso) {
             $proceso->estado_en_ultimo = $ultimoInventario && InventarioProceso::where('id_inventario', $ultimoInventario->id)->where('id_proceso', $proceso->id)->exists() ? 'vigente' : 'eliminado';
        }

        return response()->json($procesosConOuos);
    }

    // Endpoint para obtener la lista de inventarios
    public function apiInventarios()
    {
        // Obtener solo los campos necesarios
        $inventarios = Inventario::select('id', 'nombre', 'descripcion', 'documento_aprueba', 'vigencia', 'enlace', 'estado', 'created_at', 'updated_at')
                                  ->orderBy('vigencia', 'desc') // O el orden que prefieras
                                  ->get();

        return response()->json($inventarios);
    }
}

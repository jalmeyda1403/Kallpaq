<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\InventarioProceso; // Asegúrate de que este modelo exista
use App\Models\Proceso;
use App\Models\ProcesoOuo;
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
        $procesosConOuos = Proceso::join('inventario_procesos', 'procesos.id', '=', 'inventario_procesos.id_proceso')
            ->leftJoin('ouos as ouo_p', 'inventario_procesos.id_ouo_propietario', '=', 'ouo_p.id') // Join para Propietario
            ->leftJoin('ouos as ouo_d', 'inventario_procesos.id_ouo_delegado', '=', 'ouo_d.id')   // Join para Delegado
            ->leftJoin('ouos as ouo_e', 'inventario_procesos.id_ouo_ejecutor', '=', 'ouo_e.id')   // Join para Ejecutor

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
                // Conteo de subprocesos optimizado (subconsulta correlacionada)
                \DB::raw('(SELECT COUNT(*) FROM procesos as p_count WHERE p_count.cod_proceso_padre = procesos.id) as subprocesos_count')
            )
            ->get();

        // Optimización N+1: Obtener todos los IDs del último inventario en una sola consulta
        $idsUltimoInventario = [];
        if ($ultimoInventario) {
            $idsUltimoInventario = InventarioProceso::where('id_inventario', $ultimoInventario->id)
                ->pluck('id_proceso')
                ->flip() // Convertir valores a claves para búsqueda O(1)
                ->toArray();
        }

        // Calcular estado_en_ultimo en memoria
        foreach ($procesosConOuos as $proceso) {
            $proceso->estado_en_ultimo = isset($idsUltimoInventario[$proceso->id]) ? 'vigente' : 'eliminado';
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

    // 1. Listar inventarios (indexApi)
    public function indexApi(Request $request)
    {
        // Opcional: Agregar paginación, búsqueda, etc.
        $inventarios = Inventario::withCount('procesos')
            ->orderBy('created_at', 'desc') // O por vigencia
            ->get();

        return response()->json($inventarios);
    }

    // 2. Crear inventario (storeApi)
    public function storeApi(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'documento_aprueba' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt,rtf,odt,ods,odp|max:10240', // Máx 10MB, tipos comunes
            'enlace' => 'nullable|url',
            'vigencia' => 'required|date',
            // 'estado' => 'sometimes|boolean', // O dejarlo como 1 por defecto en DB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $inventario = new Inventario;
        $inventario->nombre = $request->nombre;
        $inventario->descripcion = $request->descripcion;
        $inventario->enlace = $request->enlace;
        $inventario->vigencia = $request->vigencia;
        // Archivo: Guardar y asignar ruta
        if ($request->hasFile('documento_aprueba')) {
            $path = $request->file('documento_aprueba')->store('documentos_inventario', 'public');
            $inventario->documento_aprueba = \Storage::url($path);
        }
        $inventario->estado = 1; // Nuevo inventario, estado 1 (activo/vigente)
        $inventario->estado_flujo = 'borrador'; // Estado inicial del flujo
        $inventario->save();

        return response()->json($inventario, 201);
    }

    // 3. Actualizar inventario (updateApi) - Limitado a borradores
    public function updateApi(Request $request, Inventario $inventario)
    {
        // Validar que solo se pueda editar si está en borrador
        if ($inventario->estado_flujo !== 'borrador') {
            return response()->json(['error' => 'No se puede editar un inventario que no está en borrador'], 400);
        }

        $validator = \Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'documento_aprueba' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt,rtf,odt,ods,odp|max:10240',
            'enlace' => 'nullable|url',
            'vigencia' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Archivo: Si se sube uno nuevo, reemplazar el anterior
        if ($request->hasFile('documento_aprueba')) {
            // Opcional: Borrar archivo anterior si existe y no se usa en otro lado
            // if ($inventario->documento_aprueba) {
            //      // \Storage::disk('public')->delete(str_replace(\Storage::url(''), '', $inventario->documento_aprueba));
            //      // No lo borramos ahora por simplicidad.
            // }
            $path = $request->file('documento_aprueba')->store('documentos_inventario', 'public');
            $inventario->documento_aprueba = \Storage::url($path);
        }

        $inventario->fill($request->only(['nombre', 'descripcion', 'enlace', 'vigencia']));
        $inventario->save();

        return response()->json($inventario, 200);
    }

    // 4. Eliminar inventario (destroyApi) - Solo borradores
    public function destroyApi(Inventario $inventario)
    {
        if ($inventario->estado_flujo !== 'borrador') {
            return response()->json(['error' => 'No se puede eliminar un inventario que no está en borrador'], 400);
        }

        // Opcional: Borrar archivo asociado
        // if ($inventario->documento_aprueba) { ... }

        $inventario->delete();

        return response()->json(['message' => 'Inventario eliminado'], 200);
    }

    // 7. Sincronizar Procesos (syncProcesos) - implementación anterior eliminada para evitar redeclaración;
    // Se utiliza la implementación actualizada más abajo en este controlador.

    // 8. Aprobar Inventario (aprobar)
    public function aprobar(Request $request, Inventario $inventario)
    {
        // Validar que solo se pueda aprobar si está en borrador
        if ($inventario->estado_flujo !== 'borrador') {
            return response()->json(['error' => 'No se puede aprobar un inventario que no está en borrador'], 400);
        }

        // Validar que tenga procesos asociados
        if ($inventario->procesos()->count() === 0) {
            return response()->json(['error' => 'No se puede aprobar un inventario sin procesos asociados'], 400);
        }

        \DB::beginTransaction();
        try {
            // a. Obtener el último inventario aprobado (si existe)
            $ultimoInventarioAprobado = Inventario::where('estado_flujo', 'aprobado')->latest('vigencia')->first(); // O por ID si vigencia no es confiable para ordenar versiones

            // b. Marcar el inventario actual como aprobado
            $inventario->estado_flujo = 'aprobado';
            $inventario->save();

            // c. Si existía un inventario anterior aprobado
            if ($ultimoInventarioAprobado) {
                // i. Marcarlo como cerrado
                $ultimoInventarioAprobado->estado_flujo = 'cerrado';
                $ultimoInventarioAprobado->inventario_cierre = $inventario->id; // Este nuevo lo cierra
                $ultimoInventarioAprobado->fecha_cierre = \Carbon\Carbon::now();
                $ultimoInventarioAprobado->save();

                // ii. Desactivar sus procesos en inventario_procesos
                InventarioProceso::where('id_inventario', $ultimoInventarioAprobado->id)
                    ->update(['estado' => 0, 'inactive_at' => \Carbon\Carbon::now()]);
            }

            // d. Confirmar transacción
            \DB::commit();

            return response()->json(['message' => 'Inventario aprobado correctamente'], 200);

        } catch (\Exception $e) {
            \DB::rollback();

            return response()->json(['error' => 'Error al aprobar el inventario'], 500);
        }
    }

    /**
     * Calcula recursivamente todos los procesos hijos (nivel 2+) dados una lista de IDs de procesos padres (nivel 0/1).
     */
    private function calcularProcesosConHijos(array $procesosPadreIds): array
    {
        if (empty($procesosPadreIds)) {
            return [];
        }

        $procesos = Proceso::whereIn('id', $procesosPadreIds)->get();
        $resultado = $procesos->pluck('id')->toArray();
        $cola = $procesos->pluck('cod_proceso')->toArray();
        $procesados = [];

        while (! empty($cola)) {
            $procesados = array_merge($procesados, $cola);
            $hijos = Proceso::whereIn('cod_proceso_padre', $cola)->get();

            if ($hijos->isEmpty()) {
                break;
            }

            $resultado = array_merge($resultado, $hijos->pluck('id')->toArray());
            $nuevos_codigos = $hijos->pluck('cod_proceso')->toArray();
            $cola = array_diff($nuevos_codigos, $procesados);
        }

        return array_unique($resultado);
    }

    /**
     * Obtiene todos los IDs de procesos (padres e hijos calculados) asociados a un inventario.
     *
     * @return \Illuminate\Support\Collection
     */
    private function obtenerProcesosAsociadosConHijos(int $inventarioId)
    {
        // Obtiene solo los IDs de procesos ya asociados a este inventario
        $procesosDirectosIds = InventarioProceso::where('id_inventario', $inventarioId)
            ->pluck('id_proceso')
            ->toArray();

        // Calcular hijos de los procesos directos
        $procesosIdsConHijos = $this->calcularProcesosConHijos($procesosDirectosIds);

        // Devolver una colección de objetos con solo el ID (simula el comportamiento de pluck)
        // En realidad, devolvemos un array directamente para la comparación
        return collect($procesosIdsConHijos)->map(function ($id) {
            return (object) ['id' => $id]; // Convierte a objeto con propiedad 'id'
        });
    }

    // Método 'obtenerProcesosAsociadosConHijos' (alternativa que devuelve solo array)
    private function obtenerProcesosAsociadosConHijosArray(int $inventarioId): array
    {
        // Obtiene solo los IDs de procesos ya asociados a este inventario
        $procesosDirectosIds = InventarioProceso::where('id_inventario', $inventarioId)
            ->pluck('id_proceso')
            ->toArray();

        // Calcular hijos de los procesos directos
        return $this->calcularProcesosConHijos($procesosDirectosIds);
    }

    // Actualizar 'syncProcesos' (método principal)
    public function syncProcesos(Request $request, Inventario $inventario)
    {
        // Validar que solo se pueda editar si está en borrador
        if ($inventario->estado_flujo !== 'borrador') {
            return response()->json(['error' => 'No se puede modificar un inventario que no está en borrador'], 400);
        }

        $validator = \Validator::make($request->all(), [
            'procesos_ids' => 'required|array',
            'procesos_ids.*' => 'integer|exists:procesos,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nuevosProcesosIdsNivelBase = $request->procesos_ids; // Los IDs seleccionados (nivel 0 y 1)

        // Calcular la lista completa de IDs de procesos (padres + hijos) a asociar
        $nuevosProcesosIdsCompletos = $this->calcularProcesosConHijos($nuevosProcesosIdsNivelBase);

        // Obtener lista completa de IDs de procesos (padres + hijos) actualmente asociados a este inventario
        $procesosActualesIdsCompletos = $this->obtenerProcesosAsociadosConHijosArray($inventario->id);

        // Calcular diferencias
        $idsAgregar = array_diff($nuevosProcesosIdsCompletos, $procesosActualesIdsCompletos);
        $idsRemover = array_diff($procesosActualesIdsCompletos, $nuevosProcesosIdsCompletos);

        \DB::beginTransaction();
        try {
            // Remover procesos (padres e hijos calculados)
            if (! empty($idsRemover)) {
                InventarioProceso::where('id_inventario', $inventario->id)
                    ->whereIn('id_proceso', $idsRemover)
                    ->delete(); // Elimina la relación
            }

            // Agregar procesos nuevos (padres e hijos calculados)
            if (! empty($idsAgregar)) {
                $procesosParaSync = [];
                foreach ($idsAgregar as $id) {
                    $procesoOuos = ProcesoOuo::where('id_proceso', $id)->get();
                    $id_ouo_propietario = $procesoOuos->where('propietario', 1)->first()->id_ouo ?? null;
                    $id_ouo_delegado = $procesoOuos->where('delegado', 1)->first()->id_ouo ?? null;
                    $id_ouo_ejecutor = $procesoOuos->where('ejecutor', 1)->first()->id_ouo ?? null;

                    $procesosParaSync[] = [
                        'id_inventario' => $inventario->id,
                        'id_proceso' => $id,
                        'id_ouo_propietario' => $id_ouo_propietario,
                        'id_ouo_ejecutor' => $id_ouo_ejecutor,
                        'id_ouo_delegado' => $id_ouo_delegado,
                        'estado' => 1,
                        'inactive_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                InventarioProceso::insert($procesosParaSync);
            }

            \DB::commit();

            return response()->json(['message' => 'Procesos sincronizados correctamente'], 200);

        } catch (\Exception $e) {
            \DB::rollback();

            return response()->json(['error' => 'Error al sincronizar procesos: '.$e->getMessage()], 500);
        }
    }

    public function addProcesos(Request $request, Inventario $inventario)
    {
        // Validar que solo se pueda editar si está en borrador
        if ($inventario->estado_flujo !== 'borrador') {
            return response()->json(['error' => 'No se puede modificar un inventario que no está en borrador'], 400);
        }

        $validator = \Validator::make($request->all(), [
            'procesos_ids' => 'required|array',
            'procesos_ids.*' => 'integer|exists:procesos,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nuevosProcesosIdsNivelBase = $request->procesos_ids; // Los IDs seleccionados (nivel 0 y 1)

        // Calcular la lista completa de IDs de procesos (padres + hijos) a asociar
        $nuevosProcesosIdsCompletos = $this->calcularProcesosConHijos($nuevosProcesosIdsNivelBase);

        // Obtener lista completa de IDs de procesos (padres + hijos) actualmente asociados a este inventario
        $procesosActualesIdsCompletos = $this->obtenerProcesosAsociadosConHijosArray($inventario->id);

        // Calcular diferencias
        $idsAgregar = array_diff($nuevosProcesosIdsCompletos, $procesosActualesIdsCompletos);

        \DB::beginTransaction();
        try {
            // Agregar procesos nuevos (padres e hijos calculados)
            if (! empty($idsAgregar)) {
                $procesosParaSync = [];
                foreach ($idsAgregar as $id) {
                    $procesoOuos = ProcesoOuo::where('id_proceso', $id)->get();
                    $id_ouo_propietario = $procesoOuos->where('propietario', 1)->first()->id_ouo ?? null;
                    $id_ouo_delegado = $procesoOuos->where('delegado', 1)->first()->id_ouo ?? null;
                    $id_ouo_ejecutor = $procesoOuos->where('ejecutor', 1)->first()->id_ouo ?? null;

                    $procesosParaSync[] = [
                        'id_inventario' => $inventario->id,
                        'id_proceso' => $id,
                        'id_ouo_propietario' => $id_ouo_propietario,
                        'id_ouo_ejecutor' => $id_ouo_ejecutor,
                        'id_ouo_delegado' => $id_ouo_delegado,
                        'estado' => 1,
                        'inactive_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                InventarioProceso::insert($procesosParaSync);
            }

            \DB::commit();

            return response()->json(['message' => 'Procesos agregados correctamente'], 200);

        } catch (\Exception $e) {
            \DB::rollback();

            return response()->json(['error' => 'Error al agregar procesos: '.$e->getMessage()], 500);
        }
    }

    public function disassociateProcess(Inventario $inventario, Proceso $proceso)
    {
        try {
            // Validar que solo se pueda editar si está en borrador
            if ($inventario->estado_flujo !== 'borrador') {
                return response()->json(['error' => 'No se puede modificar un inventario que no está en borrador'], 400);
            }

            $inventarioProceso = InventarioProceso::where('id_inventario', $inventario->id)
                ->where('id_proceso', $proceso->id)
                ->first();

            if ($inventarioProceso) {
                $inventarioProceso->delete();

                return response()->json(['message' => 'Proceso desasociado correctamente'], 200);
            }

            return response()->json(['error' => 'El proceso no está asociado a este inventario'], 404);
        } catch (\Exception $e) {
            \Log::error('Error al desasociar el proceso: '.$e->getMessage());

            return response()->json(['error' => 'Error al desasociar el proceso: '.$e->getMessage()], 500);
        }
    }

    // Actualizar 'procesosDisponibles' (método)
    public function procesosDisponibles(Inventario $inventario)
    {
        // Obtener IDs de procesos ya asociados (y sus hijos) a este inventario
        $procesosAsociadosIds = $this->obtenerProcesosAsociadosConHijosArray($inventario->id);

        if (empty($procesosAsociadosIds)) {
            // Si no hay procesos asociados, devolver todos los de nivel 0 y 1
            $procesosDisponibles = Proceso::where('proceso_nivel', '<=', 1)
                ->select('id', 'cod_proceso', 'proceso_nombre', 'proceso_nivel', 'cod_proceso_padre', 'proceso_estado')
                ->orderBy('cod_proceso')
                ->get();
        } else {
            // Si hay procesos asociados, excluirlos (y sus hijos) de la lista de disponibles
            $procesosDisponibles = Proceso::where('proceso_nivel', '<=', 1)
                ->whereNotIn('id', $procesosAsociadosIds)
                ->select('id', 'cod_proceso', 'proceso_nombre', 'proceso_nivel', 'cod_proceso_padre', 'proceso_estado')
                ->orderBy('cod_proceso')
                ->get();
        }

        return response()->json($procesosDisponibles);
    }

    // Actualizar 'procesosAsociados' (método)
    public function procesosAsociados(Inventario $inventario)
    {
        // Obtener IDs de procesos asociados a este inventario (ya tiene hijos incluidos si se usó syncProcesos correctamente)
        $procesosIdsAsociados = $this->obtenerProcesosAsociadosConHijosArray($inventario->id);

        if (empty($procesosIdsAsociados)) {
            return response()->json([]);
        }

        // Obtener los detalles de los procesos asociados (incluyendo hijos)
        // Incluimos los campos de OUO de la tabla pivote `inventario_procesos`
        $procesosAsociados = Proceso::whereIn('procesos.id', $procesosIdsAsociados)
            ->join('inventario_procesos', 'procesos.id', '=', 'inventario_procesos.id_proceso')
            ->select(
                'procesos.*', // Todos los campos del proceso
                // Campos de la tabla pivote `inventario_procesos`
                'inventario_procesos.id_ouo_propietario',
                'inventario_procesos.id_ouo_ejecutor',
                'inventario_procesos.id_ouo_delegado',
                'inventario_procesos.estado',
                'inventario_procesos.inactive_at',
                'inventario_procesos.created_at as ip_created_at',
                'inventario_procesos.updated_at as ip_updated_at'
            )
            ->where('inventario_procesos.id_inventario', $inventario->id) // Asegurar filtro por inventario
            ->orderBy('procesos.cod_proceso') // O el orden deseado
            ->get();

        return response()->json($procesosAsociados);
    }
}

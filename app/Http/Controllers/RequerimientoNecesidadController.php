<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequerimientoNecesidad;

class RequerimientoNecesidadController extends Controller
{
    public function store(Request $request)
    {
        // Obtener los datos de las necesidades enviados desde el formulario
        $necesidadesData = $request->input('necesidades');
        // Iterar sobre los datos de la grilla y guardarlos en la base de datos
        foreach ($necesidadesData as $dato) {
            $necesidad = new RequerimientoNecesidad();
            $necesidad->requerimiento_id = $dato['requerimiento_id'];
            $necesidad->tipo_documento_id = $dato['tipo_documento_id']; // Ajusta el nombre del campo según tu estructura de datos
            $necesidad->estado = $dato['estado']; // Ajusta el nombre del campo según tu estructura de datos
            $necesidad->nombre_documento = $dato['nombre_documento']; // Ajusta el nombre del campo según tu estructura de datos
            // Asigna otros campos según sea necesario
            $necesidad->save();
        }
        session()->flash('success', 'requerimiento creado exitosamente.');
        // Redirigir a la vista deseada
        return response()->json(['success' => 'Requerimiento creado exitosamente.'], 200);
   
    }
}
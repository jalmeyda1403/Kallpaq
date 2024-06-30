<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requerimiento;
use App\Models\User;
use App\Models\RequerimientoMovimiento;
use Illuminate\Support\Facades\Mail;

class RequerimientoController extends Controller
{
    // Método para mostrar la lista de requerimientos
    public function index()
    {
        $requerimientos = Requerimiento::all();
        return view('requerimientos.index', compact('requerimientos'));
    }

    // Método para mostrar el formulario de creación de requerimientos
    public function create()
    {
        $usuarios = User::all(); // Obtener todos los usuarios disponibles
        return view('requerimientos.create', compact('usuarios'));
    }

    // Método para almacenar un nuevo requerimiento
    public function store(Request $request)
    {
        // Validar los datos del formulario
     
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'proceso_id' => 'required|exists:procesos,id',
            'justificacion' => 'required|string',
            'descripcion' => 'required|string'
                      // Agrega más reglas según tus necesidades
        ]);
    
        // Crear un nuevo requerimiento
        $requerimiento = new Requerimiento();
        $requerimiento->proceso_id = $request->proceso_id;
        $requerimiento->user_id = $request->user_id;
        $requerimiento->justificacion = $request->justificacion;
        $requerimiento->descripcion = $request->descripcion;
        $requerimiento->estado = 'creado'; // Establecer el estado inicial
        $requerimiento->prioridad = $request->prioridad;
        $requerimiento->complejidad = $request->complejidad;
        $requerimiento->save();
       

        // Enviar notificación por correo electrónico al propietario del proceso
       // Mail::to($propietario->email)->send(new RequerimientoCreado($requerimiento));

        // Redirigir a la página de lista de requerimientos con un mensaje de éxito
        return response()->json(['requerimiento_id' => $requerimiento->id]);
    }

    public function trazabilidad($id)
    {
    $requerimiento = Requerimiento::with('proceso')->findOrFail($id);
    $movimientos = RequerimientoMovimiento::where('requerimiento_id', $id)->orderBy('created_at', 'desc')->get();
  
    // Pasos
    $pasos = [
        [
            'titulo' => null,
            'icono' => '<i class="fas fa-pencil-alt"></i>',
            'completado' => false,
            'fecha' => null,
        ],
        [
            'titulo' => null,
            'icono' =>  '<i class="fas fa-check"></i>',
            'completado' => false,
            'fecha' => null,
        ],
        [
            'titulo' => null,
            'icono' => '<i class="fas fa-user"></i>',
            'completado' => false,
            'fecha' => null,
        ],
        [
            'titulo' => null,
            'icono' =>  '<i class="fas fa-flag-checkered"></i>',
            'completado' => false,
            'fecha' => null,
        ],
    ];

    // Obtener los pasos completados

    foreach ($movimientos as $movimiento) {
        switch ($movimiento->estado) {
            case 'creado':
                $pasos[0]['titulo'] = 'Creado';             
                $pasos[0]['completado'] = true;
                $pasos[0]['icono'] = '<i class="fas fa-pencil-alt"></i>';
                $pasos[0]['fecha'] = $movimiento->created_at;
                break;
            case 'aprobado':
                $pasos[1]['titulo'] = 'Aprobado';   
                $pasos[1]['completado'] = true;
                $pasos[1]['icono'] = '<i class="fas fa-check"></i>';
                $pasos[1]['fecha'] = $movimiento->created_at;
                break;
            case 'desestimado':
                $pasos[2]['titulo'] = 'Desestimado';   
                $pasos[2]['completado'] = true;
                $pasos[2]['icono'] = '<i class="fas fa-times"></i>';
                $pasos[2]['fecha'] = $movimiento->created_at;
                break;
            case 'asignado':
                $pasos[2]['titulo'] = 'Asignado';   
                $pasos[2]['completado'] = true;
                $pasos[2]['icono'] = '<i class="fas fa-user"></i>';
                $pasos[2]['fecha'] = $movimiento->created_at;
                break;
           
            case 'atendido':
                $pasos[3]['titulo'] = 'Atendido';  
                $pasos[3]['completado'] = true;
                $pasos[3]['icono'] = '<i class="fas fa-bell"></i>';
                $pasos[3]['fecha'] = $movimiento->created_at;
                break;
        }
    }

    // Aquí puedes realizar las operaciones necesarias para obtener la información de trazabilidad del requerimiento

    return response()->json([
        'requerimiento' => $requerimiento,
        'pasos'=>$pasos, // Ajusta esto según tu modelo de datos
    ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ProgramaAuditoria;
use Illuminate\Http\Request;

class ProgramaAuditoriaController extends Controller
{
    public function index()
    {
        $programas = ProgramaAuditoria::all();
        return view('programa.index', compact('programas'));
    }

    public function create()
    {
        return view('programa.create');
    }

    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'version' => 'required',
            'periodo' => 'required',
            'presupuesto' => 'required',
            'fecha_aprobacion' => 'required',
        ]);

        // Crear un nuevo programa de auditoría
        ProgramaAuditoria::create($request->all());

        // Redirigir a la lista de programas de auditoría con un mensaje de éxito
        return redirect()->route('programa.index')
            ->with('success', 'Programa de auditoría creado exitosamente.');
    }

    public function edit(ProgramaAuditoria $programa)
    {
        return view('programa.edit', compact('programa'));
    }

    public function update(Request $request, ProgramaAuditoria $programa)
    {
        // Validar los datos del formulario
        $request->validate([
            'version' => 'required',
            'periodo' => 'required',
            'presupuesto' => 'required',
            'fecha_aprobacion' => 'required',
        ]);

        // Actualizar el programa de auditoría
        $programa->update($request->all());

        // Redirigir a la lista de programas de auditoría con un mensaje de éxito
        return redirect()->route('programa.index')
            ->with('success', 'Programa de auditoría actualizado exitosamente.');
    }

    public function aprobar(ProgramaAuditoria $programa)
    {
        // Lógica para aprobar el programa de auditoría
        // Aquí podrías bloquear el programa y realizar otras acciones necesarias

        // Redirigir a la lista de programas de auditoría con un mensaje de éxito
        return redirect()->route('programa.index')
            ->with('success', 'Programa de auditoría aprobado exitosamente.');
    }

    public function reprogramar(ProgramaAuditoria $programa)
    {
         return redirect()->route('programa.index')
            ->with('success', 'Programa de auditoría reprogramado exitosamente.');
    }
    
    public function showHistory(ProgramaAuditoria $programa)
    {
        // Obtener el historial de cambios de estados para el programa de auditoría
      //  $cambios_estado = $programa->cambiosEstado;

        //return view('programa_auditoria.history', compact('programa', 'cambios_estado'));
    }
}

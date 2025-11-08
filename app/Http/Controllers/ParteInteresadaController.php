<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ParteInteresada;
use Illuminate\Support\Facades\DB;

class ParteInteresadaController extends Controller
{
    public function index()
    {
        $partes = ParteInteresada::orderBy('pi_nombre')->get();
        return view('partes_interesadas.listar', compact('partes'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'pi_nombre' => 'required|string|max:255',
            'pi_tipo' => 'required|in:interna,externa,cliente,proveedor,regulador',
            'pi_nivel_influencia' => 'nullable|in:bajo,medio,alto',
            'pi_nivel_interes' => 'nullable|in:bajo,medio,alto',
            'pi_descripcion' => 'nullable|string',
        ]);

        ParteInteresada::create([
            'pi_nombre' => $request->input('pi_nombre'),
            'pi_tipo' => $request->input('pi_tipo'),
            'pi_nivel_influencia' => $request->input('pi_nivel_influencia'),
            'pi_nivel_interes' => $request->input('pi_nivel_interes'),
            'pi_descripcion' => $request->input('pi_descripcion'),
        ]);

        return redirect()->route('partes.index')->with('success', 'Parte interesada registrada.');
    }

    public function update(Request $request, $id)
    {
        $parte = ParteInteresada::findOrFail($id);

        $request->validate([
            'pi_nombre' => 'required|string|max:255',
            'pi_descripcion' => 'required|string',
            'pi_nivel_influencia' => 'required|in:alto,medio,bajo',
            'pi_nivel_interes' => 'nullable|in:bajo,medio,alto',
            'pi_tipo' => 'required|in:interna,externa,cliente,proveedor,regulador',
        ]);

        $parte->update([
            'pi_nombre' => $request->input('pi_nombre'),
            'pi_descripcion' => $request->input('pi_descripcion'),
            'pi_nivel_influencia' => $request->input('pi_nivel_influencia'),
            'pi_nivel_interes' => $request->input('pi_nivel_interes'),
            'pi_tipo' => $request->input('pi_tipo'),
        ]);


        return redirect()->route('partes.index')->with('success', 'Parte interesada actualizada.');
    }

    public function destroy($id)
    {
        try {
            $parteInteresada = ParteInteresada::findOrFail($id);

            DB::transaction(function () use ($parteInteresada) {
                $parteInteresada->delete(); // Ya se actualiza pi_activo en el evento
            });

            return redirect()->route('partes.index')
                ->with('message', 'Parte Interesada eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('partes.index')
                ->with('error', 'Error al eliminar la Parte Interesada: ' . $e->getMessage());
        }
    }

}

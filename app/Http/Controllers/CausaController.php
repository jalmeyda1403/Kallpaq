<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Causa;
use App\Models\Hallazgo;


class CausaController extends Controller
{
    public function index($hallazgo_id)
    {
        $hallazgo = Hallazgo::findOrFail($hallazgo_id);
        $analisisCausaRaiz = Causa::where('hallazgo_id', $hallazgo_id)->get();       
        return back()->with('success', '¡El plan de acción ha sido creado correctamente!');
    }

    public function create($hallazgo_id)
    {
        $hallazgo = Hallazgo::findOrFail($hallazgo_id);
        return view('analisis.create', compact('hallazgo'));
    }

    public function store(Request $request, $hallazgo_id)
    {
        $request->validate([
            'metodo' => 'required|string',
            'mano_obra' => 'nullable|string',
            'metodologias' => 'nullable|string',
            'materiales' => 'nullable|string',
            'maquinas' => 'nullable|string',
            'medicion' => 'nullable|string',
            'medio_ambiente' => 'nullable|string',
            'por_que_1' => 'nullable|string',
            'por_que_2' => 'nullable|string',
            'por_que_3' => 'nullable|string',
            'por_que_4' => 'nullable|string',
            'por_que_5' => 'nullable|string',
            'resultado' => 'required|string'
        ]);

        $analisisData = [
            'hallazgo_id' => $hallazgo_id,
            'metodo' => $request->metodo,
            'resultado' => $request->resultado,
        ];

        if ($request->metodo === 'ishikawa') {
            $analisisData['mano_obra'] = $request->mano_obra;
            $analisisData['metodologias'] = $request->metodologias;
            $analisisData['materiales'] = $request->materiales;
            $analisisData['maquinas'] = $request->maquinas;
            $analisisData['medicion'] = $request->medicion;
            $analisisData['medio_ambiente'] = $request->medio_ambiente;
        } elseif ($request->metodo === 'cinco_porques') {
            $analisisData['por_que_1'] = $request->por_que_1;
            $analisisData['por_que_2'] = $request->por_que_2;
            $analisisData['por_que_3'] = $request->por_que_3;
            $analisisData['por_que_4'] = $request->por_que_4;
            $analisisData['por_que_5'] = $request->por_que_5;
        }

        Causa::create($analisisData);

        return back()->with('success', '¡analiss de causa ha sido creado correctamente!');

    }

    public function edit($hallazgo_id, $id)
    {
        $hallazgo = Hallazgo::findOrFail($hallazgo_id);
        $analisisCausaRaiz = Causa::findOrFail($id);
        return response()->json($analisisCausaRaiz);
    }

    public function update(Request $request, $hallazgo_id, $id)
    {
       
        $request->validate([
            'metodo' => 'required|string',
            'mano_obra' => 'nullable|string',
            'metodologias' => 'nullable|string',
            'materiales' => 'nullable|string',
            'maquinas' => 'nullable|string',
            'medicion' => 'nullable|string',
            'medio_ambiente' => 'nullable|string',
            'por_que_1' => 'nullable|string',
            'por_que_2' => 'nullable|string',
            'por_que_3' => 'nullable|string',
            'por_que_4' => 'nullable|string',
            'por_que_5' => 'nullable|string',
            'resultado' => 'required|string'
        ]);

        $analisisCausaRaiz = Causa::findOrFail($id);

        $analisisData = [          
            'metodo' => $request->metodo,
            'resultado' => $request->resultado,
        ];

        if ($request->metodo === 'ishikawa') {
            $analisisData['mano_obra'] = $request->mano_obra;
            $analisisData['metodologias'] = $request->metodologias;
            $analisisData['materiales'] = $request->materiales;
            $analisisData['maquinas'] = $request->maquinas;
            $analisisData['medicion'] = $request->medicion;
            $analisisData['medio_ambiente'] = $request->medio_ambiente;
        } elseif ($request->metodo === 'cinco_porques') {
            $analisisData['por_que_1'] = $request->por_que_1;
            $analisisData['por_que_2'] = $request->por_que_2;
            $analisisData['por_que_3'] = $request->por_que_3;
            $analisisData['por_que_4'] = $request->por_que_4;
            $analisisData['por_que_5'] = $request->por_que_5;
        }

        $analisisCausaRaiz->update($analisisData);

        return back()->with('success', '¡analiss de causa ha sido creado correctamente!');
    }

    public function destroy($hallazgo_id, $id)
    {
        $analisisCausaRaiz = Causa::findOrFail($id);
        $analisisCausaRaiz->delete();

        return back()->with('success', '¡analiss de causa ha sido creado eliminado!');
    }
}

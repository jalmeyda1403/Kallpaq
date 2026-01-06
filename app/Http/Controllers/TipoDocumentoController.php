<?php

namespace App\Http\Controllers;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
      public function findTipoDocumento(Request $request)
    {
  
        $tipoDocumento = TipoDocumento::select('id', 'td_nombre')->orderBy("id")->get();       

        return response()->json($tipoDocumento);
    }  
}

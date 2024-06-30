<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;


class SharePointController extends Controller
{
    public function createFolder($id)
    {
        // Configura tus credenciales de autenticación y URL de SharePoint
        $baseUri = 'https://contraloriape.sharepoint.com/sites/grupomoder';
        $username = '5f38b785-6595-471a-b08a-3a38861dff4d';
        $password = 'tmboWMMED04TxNjb00QRfe6Um5Q+DZbgVnhxlixCCoc=';

        $client = new Client([
            'base_uri' => $baseUri,
            'auth' => [$username, $password],
        ]);

        $response = $client->post('', [
            'json' => [
                '__metadata' => ['type' => 'SP.Folder'],
                'ServerRelativeUrl' => "Shared Documents/$id",
            ],
        ]);

        return response()->json(['message' => 'Folder created successfully']);
    }

    public function uploadFile(Request $request, $id)
    {
        // Similar a la función anterior, configura las credenciales y URL de SharePoint
        // ...

        $client->post("Shared Documents/$id/Files/add(url='{$request->file->getClientOriginalName()}')", [
            'headers' => ['X-RequestDigest' => 'FormDigestValue'],
            'body' => $request->file->get(),
        ]);

        return response()->json(['message' => 'File uploaded successfully']);
    }

    public function show($id)
    {
       return view('indicadores.sharepoint', compact('id'));
    }
}

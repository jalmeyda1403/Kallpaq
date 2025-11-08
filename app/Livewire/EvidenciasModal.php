<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;

class EvidenciasModal extends Component
{

    use WithFileUploads;

    public $requerimiento_id;
    public $archivos = [];
    public $archivosExistentes = [];
    public $randomId;

    protected $listeners = ['mostrarArchivos' => 'mostrarArchivos'];

    public function mostrarArchivos($id)
    {
        $this->requerimiento_id = $id;
        $this->randomId = rand();
        $ruta = storage_path("app/public/requerimientos/{$this->requerimiento_id}/evidencias");

        if (!File::exists($ruta)) {
            File::makeDirectory($ruta, 0775, true);
        }

        $this->archivosExistentes = collect(File::files($ruta))->map(function ($file) {
            return [
                'nombre' => $file->getFilename(),
                'url' => asset("storage/requerimientos/{$this->requerimiento_id}/evidencias/" . $file->getFilename()),
            ];
        })->toArray();


    }

    public function subirArchivos()
    {
        $this->validate([
            'archivos.*' => 'file|max:10240', // 10 MB por archivo
        ]);
        if ($this->requerimiento_id) {
            $ruta = "requerimientos/{$this->requerimiento_id}/evidencias";
            foreach ($this->archivos as $archivo) {
                $archivo->storeAs($ruta, $archivo->getClientOriginalName(), 'public');
            }

            $this->archivos = [];
            session()->flash('message', 'Archivos subidos correctamente.');
            $this->mostrarArchivos($this->requerimiento_id);
            $this->dispatch('archivos-subidos');
        }

    }



    public function eliminarArchivo($nombreArchivo)
    {
        $ruta = storage_path("app/public/requerimientos/{$this->requerimiento_id}/evidencias/{$nombreArchivo}");

        if (File::exists($ruta)) {
            File::delete($ruta);
            $this->mostrarArchivos($this->requerimiento_id);
            session()->flash('message', 'Archivo eliminado correctamente.');
        }
    }
    public function render()
    {
        return view('livewire.evidencias-modal');
    }
}

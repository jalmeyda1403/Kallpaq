<?php

namespace App\Livewire;

use Livewire\Component;

class DocumentoArchivoModal extends Component
{
    public $archivo_path, $modalTitle;
    public $esExterno = false;

    protected $listeners = ['mostrarArchivo'];

    public function mostrarArchivo($url)
    {
     
        $this->modalTitle = 'Documento Abierto';
        $this->archivo_path = $url;
        $this->esExterno = filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }
    public function render()
    {
        return view('livewire.documento-archivo-modal');
    }
}

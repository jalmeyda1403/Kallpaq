<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Proceso;
use Illuminate\Support\Facades\Http;

class BusquedaProcesosModal extends Component
{
    public string $eventoRetorno;
    public $procesos = [];
    public $modalTitle = 'Buscar Procesos';

    
    public function mount()
    {
        $this->loadProcesos();
    }
    public function selectItem($id, $nombre)
    {
        // Despacha al evento indicado por el padre
        $this->dispatch($this->eventoRetorno, [
            'id' => $id,
            'nombre' => $nombre,
        ]);

    }
   
    public function loadProcesos()
    {
        // Tu lógica para cargar procesos
        // Ejemplo:
        $query = Proceso::query();

        $this->procesos = $query->get();
    }


    public function render()
    {
        return view('livewire.busqueda-procesos-modal');
    }
}
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Proceso;
use Illuminate\Support\Facades\Http;

class BusquedaProcesosModal extends Component
{
    use WithPagination;

    public $modalTitle;
    public $proceso_id;
    public $proceso_nombre;
    public $ruta;
    public $procesos = [];
    public $selectedId;
    public $busqueda = '';
    protected $listeners = ['cargarModal'];
    public $perPage = 10;

    public function mount()
    {
        $this->modalTitle = "Buscar";

    }
    public function cargarModal()
    {
        $this->loadItems();

    }
    public function updatedBusqueda()
    {
        $this->loadItems();
    }


    public function loadItems()
    {

        if ($this->busqueda) {
            $this->procesos = Proceso::select('id', 'cod_proceso', 'proceso_nombre')
                ->where('proceso_nombre', 'like', '%' . $this->busqueda . '%')
                ->get();
        } else {
            $this->procesos = Proceso::select('id', 'cod_proceso', 'proceso_nombre')
                ->orderBy('cod_proceso')
                ->get();
        }
    }




    public function selectItem($id, $nombre)
    {
        $this->dispatch('selecItem', [
            'procesoId' => $id,
            'procesoNombre' => $nombre
        ]);
     }

    public function render()
    {
        return view('livewire.busqueda-procesos-modal');
    }
}
<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalBusqueda extends Component
{
    public $ruta;
    public $campoId;
    public $campoNombre;
    public $modalId;
    public $modalTitulo;


    public function __construct($modalId, $ruta, $campoId, $campoNombre,$modalTitulo)
    {
        $this->ruta = $ruta;    
        $this->campoId = $campoId;
        $this->campoNombre = $campoNombre;
        $this->modalId = $modalId;
        $this->modalTitulo = $modalTitulo;
       // Guardar el nombre del modal
    }


    public function render()
    {
        return view('components.modal-busqueda');
    }
}

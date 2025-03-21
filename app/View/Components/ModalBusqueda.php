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
    public $modalBgcolor; // Agregar la propiedad para el color de fondo
    public $modalTxtcolor; // Agregar la propiedad para el color del texto


    public function __construct($modalId, $ruta, $campoId, $campoNombre, $modalTitulo, $modalBgcolor = '#FFFFFF', $modalTxtcolor = '#000000')
      {
        $this->ruta = $ruta;    
        $this->campoId = $campoId;
        $this->campoNombre = $campoNombre;
        $this->modalId = $modalId;
        $this->modalTitulo = $modalTitulo;
        $this->modalBgcolor = $modalBgcolor; // Guardar el color de fondo
        $this->modalTxtcolor = $modalTxtcolor; // Guardar el color del texto

       // Guardar el nombre del modal
    }


    public function render()
    {
        return view('components.modal-busqueda');
    }
}

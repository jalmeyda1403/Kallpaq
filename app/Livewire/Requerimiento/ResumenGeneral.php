<?php

namespace App\Livewire\Requerimiento;

use Livewire\Component;
use App\Models\Requerimiento;
use Carbon\Carbon;

class ResumenGeneral extends Component
{
    public $total = 0;
    public $enProceso = 0;
    public $vencidos = 0;
    public $finalizados = 0;

    public $desestimados = 0;

    public $sin_asignar = 0;
    public $eficacia = 0;

    public $rendimiento = 0;

    public function mount()
    {
        $this->calcularResumen();
    }

    public function calcularResumen()
    {
        // Obtener usuario actual (puedes ajustar si se trata de otro especialista)


        $requerimientos = Requerimiento::All();

        $this->total = $requerimientos->count();

        $this->finalizados = $requerimientos->where('estado', 'finalizado')->count();
        
        $this->finalizados = $requerimientos->where('estado', 'finalizado')->count();

        $this->desestimados = $requerimientos->where('estado', 'desestimado')->count();
        
        $this->sin_asignar = $requerimientos->where('estado', 'aprobado')->count();


        $this->vencidos = $requerimientos->filter(
            fn($r) =>
            $r->estado === 'en proceso' &&
            !empty($r->fecha_limite) &&
            Carbon::parse($r->fecha_limite)->isPast()
        )->count();

        $this->enProceso = $requerimientos->where('estado', 'en proceso')->count();
        // Efectividad como porcentaje de requerimientos finalizados
        $this->eficacia = $this->total > 0 ? round(($this->finalizados / $this->total) * 100) : 0;


    }

    public function render()
    {
        return view('livewire.requerimiento.resumen-general');
    }
}

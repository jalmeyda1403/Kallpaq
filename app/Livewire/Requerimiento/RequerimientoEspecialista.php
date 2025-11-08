<?php

namespace App\Livewire\Requerimiento;

use Livewire\Component;
use App\Models\User;
use App\Models\Requerimiento;

class RequerimientoEspecialista extends Component
{

    public $especialista;
    public $anio;
    public $requerimientos = [];
    public $estadisticasComplejidad = [];
    protected $listeners = ['mostrarDetalleEspecialista'];

    public function mostrarDetalleEspecialista($id, $anio)
    {
        $this->anio = $anio;
        $this->especialista = User::find($id);

        $this->requerimientos = Requerimiento::with('avance')
            ->where('especialista_id', $id)
            ->whereYear('fecha_asignacion', $anio)
            ->orderByDesc('fecha_asignacion')
            ->get();

        $niveles = ['baja', 'media', 'alta', 'muy alta'];
        $this->estadisticasComplejidad = [];


        foreach ($niveles as $nivel) {
            // Todos los requerimientos del nivel
            $items = $this->requerimientos->filter(fn($r) => $r->complejidad === $nivel);


            // Solo los que están en proceso (activos)
            $activos = $items->filter(fn($r) => in_array($r->estado, ['en proceso', 'asignado']));
            $cantidad = $activos->count();
            $promedio = $activos->count() > 0
                ? round($activos->avg(fn($r) => $r->avance->avance_registrado ?? 0))
                : 0;

            $this->estadisticasComplejidad[$nivel] = [
                'cantidad' => $cantidad,  // total asignados en esa categoría
                'promedio' => $promedio,  // solo de los en proceso
            ];
        }
    }
    public function render()
    {
        return view('livewire.requerimiento.requerimiento-especialista');
    }
}

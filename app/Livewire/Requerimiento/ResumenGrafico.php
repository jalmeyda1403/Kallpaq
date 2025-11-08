<?php

namespace App\Livewire\Requerimiento;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Requerimiento;
use Illuminate\Support\Facades\DB;
class ResumenGrafico extends Component
{
    public $labels = [];
    public $asignados = [];
    public $finalizados = [];
    public $programados = [];

    public function mount()
    {
        $this->generarDatos();
        $this->dispatch(
            'renderGrafico',
            labels: $this->labels,
            asignados: $this->asignados,
            finalizados: $this->finalizados,
            programados: $this->programados
        );
    }

    public function generarDatos()
    {
        $asignados = Requerimiento::select(
            DB::raw('DATE_FORMAT(fecha_asignacion, "%Y-%m") as mes'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $programados = Requerimiento::select( 
            DB::raw('DATE_FORMAT(fecha_limite, "%Y-%m") as mes'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $finalizados = Requerimiento::whereNotNull('fecha_fin')
            ->select(
                DB::raw('DATE_FORMAT(fecha_fin, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Unificar los meses para que ambos datasets tengan el mismo eje X
        $meses = collect($asignados->pluck('mes'))
            ->merge($finalizados->pluck('mes'))
            ->merge($programados->pluck('mes'))
            ->unique()
            ->sort()
            ->values();

        $this->labels = $meses;

        $this->asignados = $meses->map(function ($mes) use ($asignados) {
            return $asignados->firstWhere('mes', $mes)?->total ?? 0;
        });

        $this->programados = $meses->map(function ($mes) use ($programados) {
            return $programados->firstWhere('mes', $mes)?->total ?? 0;
        });

        $this->finalizados = $meses->map(function ($mes) use ($finalizados) {
            return $finalizados->firstWhere('mes', $mes)?->total ?? 0;
        });
    }

    public function render()
    {
        return view('livewire.requerimiento.resumen-grafico');
    }
}

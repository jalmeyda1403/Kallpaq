<?php

namespace App\Livewire\Requerimiento;

use Livewire\Component;
use App\Models\Requerimiento;
use Illuminate\Support\Carbon;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ResumenAlertas extends Component
{
    use WithPagination;

    public $vencidos = [];
    public $enRiesgo = [];
    protected $paginationTheme = 'bootstrap';
    private function manualPaginate(Collection $items, int $perPage, string $pageName = 'page'): LengthAwarePaginator
    {
        $page = request()->get($pageName, 1);
        $start = ($page - 1) * $perPage;

        return new LengthAwarePaginator(
            $items->slice($start, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['pageName' => $pageName]
        );
    }


    public function render()
    {

        // Requerimientos vencidos (ya venció la fecha y no tiene fecha de cierre)
        $vencidosPaginados = Requerimiento::with('avance', 'especialista')
            ->where('fecha_limite', '<', now())
            ->whereNull('fecha_fin')
            ->orderBy('fecha_limite')
            ->paginate(4, ['*'], 'vencidosPage'); // 👈 importante

        // Requerimientos en riesgo
        $enRiesgoPaginados = Requerimiento::select('requerimientos.*')
            ->with('especialista', 'avance')
            ->leftJoin('requerimiento_avances', 'requerimiento_avances.requerimiento_id', '=', 'requerimientos.id')
            ->whereDate('requerimientos.fecha_limite', '>=', now())
            ->whereNull('requerimientos.fecha_fin')
            ->where(function ($query) {
                $query->whereRaw("
                IFNULL(requerimiento_avances.avance_registrado, 0) < (
                    ((DATEDIFF(CURDATE(), requerimientos.fecha_asignacion) / 
                    NULLIF(DATEDIFF(requerimientos.fecha_limite, requerimientos.fecha_asignacion), 0)) * 100) - 20
                )
            ")
                    ->orWhereRaw("
                DATEDIFF(requerimientos.fecha_limite, CURDATE()) <= 15
                AND IFNULL(requerimiento_avances.avance_registrado, 0) < 90
            ");
            })
            ->orderBy('requerimientos.fecha_limite')
            ->paginate(4, ['*'], 'enRiesgoPage');

        return view('livewire.requerimiento.resumen-alertas', [
            'vencidosPaginados' => $vencidosPaginados,
            'enRiesgoPaginados' => $enRiesgoPaginados
        ]);
    }
}



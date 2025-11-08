<?php

namespace App\Livewire\Requerimiento;

use Livewire\Component;
use App\Models\User;
use App\Models\Requerimiento;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ResumenEspecialistas extends Component
{
    public Collection $especialistas;
    public $anioSeleccionado;
    public $aniosDisponibles = [];

    public function mount()
    {

        $this->anioSeleccionado = now()->year;

        // Años disponibles desde 2022 hasta el actual (ajusta si quieres más)
        $this->aniosDisponibles = range(2025, now()->year);

        $this->actualizarDatos();
    }
    public function updatedAnioSeleccionado()
    {
        $this->actualizarDatos();
    }
    public function actualizarDatos()
    {
        // Obtener todos los usuarios con rol 'especialista'
        $especialistas = User::role('especialista')->get();

        // Obtener requerimientos agrupados por especialista_id

        $requerimientos = Requerimiento::with('avance')
            ->whereNotNull('especialista_id')
            ->whereNot('estado', 'desestimado')
            ->whereYear('fecha_asignacion', $this->anioSeleccionado) // 👈 filtro por año
            ->get()
            ->groupBy('especialista_id');

        // Construir la colección final con datos agregados
        $this->especialistas = $especialistas->map(function ($user) use ($requerimientos) {

            $delUsuario = $requerimientos->get($user->id, collect());

            $totalAsignados = $delUsuario->count();
            $totalVencidos = $delUsuario->filter(function ($req) {
                return $req->estado !== 'finalizado' && $req->fecha_limite < Carbon::today();
            })->count();

            $totalFinalizados = $delUsuario->where('estado', 'finalizado')->count();
            $efectividad = $totalAsignados > 0 ? round(($totalFinalizados / $totalAsignados) * 100) : 0;


            $enProceso = $delUsuario->filter(fn($req) => $req->estado === 'en proceso');

            $totalAvance = $enProceso->sum(fn($req) => $req->avance->avance_registrado ?? 0);

            $promedioAvance = $enProceso->count() > 0
                ? round($totalAvance / $enProceso->count())
                : 0;

            return (object) [
                'id' => $user->id,
                'nombre' => $user->name,
                 'sigla' => $user->sigla,
                'foto_url' => $user->foto_url ?? asset('images/user-default.png'),
                'total_asignados' => $totalAsignados,
                'total_vencidos' => $totalVencidos,
                'total_finalizados' => $totalFinalizados,
                'efectividad' => $efectividad,
                'promedioAvance' => $promedioAvance,
            ];
        });
    }
    public function render()
    {
        return view('livewire.requerimiento.resumen-especialistas');
    }
}

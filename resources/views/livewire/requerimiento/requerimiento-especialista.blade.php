@push('styles')
    <style>
        .table-requerimientos {
            font-size: 13px;
        }
    </style>
@endpush

<div wire:ignore.self class="modal fade" id="EspecialistarRequerimientoModal" tabindex="-1" role="dialog"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    Requerimientos de {{ $especialista->nombre_completo ?? '' }} ({{ $anio }})
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">

                {{-- Tabla de requerimientos --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm table-requerimientos">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Estado</th>
                                <th>Fecha Asignación</th>
                                <th>Fecha Límite</th>
                                <th>Complejidad</th>
                                <th>Avance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requerimientos as $r)
                                <tr class="text-center">
                                    <td>{{ $r->id }}</td>
                                    <td>{{ ucfirst($r->estado) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($r->fecha_asignacion)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($r->fecha_limite)->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($r->complejidad) }}</td>
                                    <td>
                                        <div class="text-center">
                                            <strong>{{ $r->avance->avance_registrado ?? 0 }}%</strong>
                                            <div class="progress mt-1" style="height: 10px;">
                                                <div class="progress-bar 
                                                        {{ ($r->avance->avance_registrado ?? 0) >= 80
                                                            ? 'bg-success'
                                                            : (($r->avance->avance_registrado ?? 0) >= 50
                                                                ? 'bg-warning'
                                                                : 'bg-danger') }}"
                                                    role="progressbar"
                                                    style="width: {{ $r->avance->avance_registrado ?? 0 }}%;"
                                                    aria-valuenow="{{ $r->avance->avance_registrado ?? 0 }}"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay requerimientos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>



            </div>
        </div>
    </div>
</div>

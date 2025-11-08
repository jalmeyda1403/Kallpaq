@push('styles')
    <style>
        .d-sm-flex>div:first-child>p.small.text-muted {
            display: none !important;
        }
    </style>
@endpush
<div class="row">
    <div class="col-md-6">
        <div class="h-100 d-flex flex-column">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white"> Requerimientos Vencidos</div>
                <ul class="list-group list-group-flush small">
                    @forelse ($vencidosPaginados as $r)
                        <li class="list-group-item">
                            <strong>Req:{{ $r->id ?? 'Sin título' }}</strong> - Avance:
                            {{ $r->avance->avance_registrado ?? 0 }}% <br>
                            <strong> Inicio: </strong>{{ $r->fecha_asignacion->format('d/m/Y') }} -
                            <strong>Venció: </strong>{{ $r->fecha_limite->format('d/m/Y') }}
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Sin requerimientos vencidos</li>
                    @endforelse
                    @for ($i = $vencidosPaginados->count(); $i < 4; $i++)
                        <li class="list-group-item invisible">
                            &nbsp;
                        </li>
                    @endfor
                </ul>
                <div class="card-footer">

                    {{ $vencidosPaginados->links() }}

                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="h-100 d-flex flex-column">
            <div class="card border-warning">
                <div class="card-header bg-warning"> Requerimientos en Riesgo</div>
                <ul class="list-group list-group-flush small">
                    @forelse ($enRiesgoPaginados as $r)
                        <li class="list-group-item">
                            <strong>Req:{{ $r->id ?? 'Sin título' }}</strong> - Avance:
                            {{ $r->avance->avance_registrado ?? 0 }}%<br>
                            <strong>Asignado a:</strong>
                            {{ $r->especialista->sigla ?? 'Sin asignar' }}<br>
                            <strong> Fecha: </strong>
                            {{ \Carbon\Carbon::parse($r->fecha_asignacion)->format('d/m/Y') }}</strong>
                             - 
                            {{ \Carbon\Carbon::parse($r->fecha_limite)->format('d/m/Y') }}
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Sin requerimientos en riesgo</li>
                    @endforelse
                    @for ($i = $enRiesgoPaginados->count(); $i < 4; $i++)
                        <li class="list-group-item invisible">
                            &nbsp;
                        </li>
                    @endfor

                </ul>
                <div class="card-footer">

                    {{ $enRiesgoPaginados->links() }}

                </div>
            </div>
        </div>
    </div>
</div>

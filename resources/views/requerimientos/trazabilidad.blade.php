
<div class="modal fade" id="modalTrazabilidad" tabindex="-1" role="dialog" aria-labelledby="modalTrazabilidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTrazabilidadLabel">Trazabilidad del Requerimiento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">         
                <div class="card mb-3">
                    <div class="card-header bg-primary">
                        <div class="row">
                            <div class="col-6 text-left">
                                <span class="text-medium">Proceso:</span> {{ $requerimiento->proceso->proceso_nombre }}
                            <p>
                                <span class="text-medium">Requerimiento N°</span> <span class="text-medium">{{ str_pad($requerimiento->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="col-6 text-right">
                                <span class="text-medium">Complejidad:</span> 
                                <span class="badge 
                                    @if($requerimiento->complejidad == 'baja') badge-success
                                    @elseif($requerimiento->complejidad == 'media') badge-warning
                                    @elseif($requerimiento->complejidad == 'alta') badge-danger
                                    @elseif($requerimiento->complejidad == 'muy alta') badge-danger
                                    @else badge-info
                                    @endif">
                                    {{ $requerimiento->complejidad }}
                                </span>
                            </div>
                        </div>
                    </div>      
                <!-- Cuerpo -->
                <div class="card-body">
                    <!-- Steps -->
                    <div class="steps d-flex flex-wrap flex-sm-nowrap justify-content-between padding-top-2x padding-bottom-1x">
                        @foreach ($pasos as $paso)
                            <div class="step{{ $paso['completado'] ? ' completed' : '' }}">
                                <div class="step-icon-wrap">
                                    <div class="step-icon">{!! $paso['icono'] !!}</div>
                                </div>
                                <h4 class="step-title">{{ $paso['titulo'] }}</h4>
                                <h4 class="step-title">{{ $paso['fecha'] }}</h4>
                            </div>
                        @endforeach
                    </div>
                </div>

            <!-- Botón de Volver -->
                
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>    
           
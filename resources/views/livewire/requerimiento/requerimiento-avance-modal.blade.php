<div wire:ignore.self class="modal fade" id="AvanceRequerimientoModal" tabindex="-1" role="dialog" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Registro de Avance</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('message') }}
                    </div>
                @endif

                <table class="table table-sm table-bordered small mb-0 align-middle">
                    <thead class="thead-light text-center">
                        <tr>
                            <th style="width: 45%">Avance</th>
                            <th>Comentarios</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        @foreach ($etapas as $campo => $info)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input  bg-primary border-primary" type="checkbox"
                                            wire:model.live="estadoAvance.{{ $campo }}" id="{{ $campo }}">
                                        <label class="form-check-label font-weight-bold" for="{{ $campo }}">
                                            {{ $info['titulo'] }} ({{ $info['peso'] }}%)
                                            <div class="text-muted small mb-0">{{ $info['descripcion'] }}</div>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <textarea wire:model.defer="comentarios.{{ $campo }}" rows="2" class="form-control form-control-sm w-100"
                                        style="resize: vertical;" placeholder="Comentario..."></textarea>
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="2">
                                <div class="form-group mt-4 mb-0">
                                    <label><strong>Avance Registrado</strong></label>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-fill pr-2">
                                            <div class="progress" style="height: 24px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $avance_registrado }}%;">
                                                    {{ $avance_registrado }}%
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="#" class="btn btn-warning btn-sm ml-2"
                                                onclick="Livewire.dispatchTo('evidencias-modal', 'mostrarArchivos', { id: {{ $requerimiento_id }} })"
                                                data-toggle="modal" data-target="#ModalEvidencias">
                                                <i class="fas fa-folder-open"></i> Abrir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-primary btn-sm" wire:click="guardarAvance">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div wire:ignore.self class="modal fade" tabindex="-1" role="dialog" id="busquedaModal"
    aria-labelledby="busquedaProcesosModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h6>{{ $modalTitle }}</h6>
                <button type="button" class="close" aria-label="Close" id="busquedaModalClose">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div wire:loading class="loading-spinner">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
                <input type="text" wire:model.live="busqueda" class="form-control mb-3"
                    placeholder="Buscar procesos...">

                @if (count($procesos))
                    <table class="table table-bordered table-hover table-sm table-procesos">
                        <thead class="table-header">
                            <tr>
                                <th>Seleccionar</th>
                                <th>Código</th>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($procesos as $proceso)
                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="seleccion"
                                            wire:click="selectItem('{{ $proceso->id }}', '{{ $proceso->proceso_nombre }}')">
                                    </td>
                                    <td>{{ $proceso->cod_proceso }}</td>
                                    <td>{{ $proceso->proceso_nombre }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center text-muted">
                        No se encontraron procesos.
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .table-procesos {
            font-size: 13px;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $('#busquedaModalClose').on('click', function() {
            $('#busquedaModal').modal('hide');
        });
        Livewire.on('cerrar-busqueda-modal', () => {
            document.activeElement.blur();
            $('#busquedaModal').modal('hide');
        });
    </script>
@endpush

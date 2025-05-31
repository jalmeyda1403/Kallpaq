<div wire:ignore.self class="modal fade" id="sipocModal" tabindex="-1" role="dialog" aria-labelledby="procesoModalLabel"
    data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h5 class="modal-title">{{ $modalTitle }}</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div wire:loading class="loading-spinner">
                    <div class="spinner-border text-danger" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
                <form method="POST" action="{{ $actionRoute }}">
                    @csrf
                    <input type="hidden" name="_method" value="{{ $method }}">
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label">Proceso: <label>
                                    <h6>{{ $proceso_nombre }}</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <input type="hidden" name="proceso_id" id="proceso_id" value={{ $proceso_id }}
                                wire:model.defer="proceso_id" class="form-select">
                        </div>

                        <div class="col-4">
                            <label class="form-label">
                                <i class="fas fa-truck"></i> Proveedores
                            </label>
                            <div wire:ignore>
                                <textarea id="proveedores" class="form-control" rows="5" name="proveedores" wire:model.defer="proveedores"></textarea>
                            </div>
                            @error('proveedores')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-4">
                            <label class="form-label"> <i class="fas fa-box"></i> Entradas</label>
                            <textarea rows="5" class="form-control" name="entradas" wire:model.defer="entradas"></textarea>
                            @error('entradas')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-4" wire:ignore>
                            <label class="form-label"> <i class="fas fa-users"></i> Clientes</label>
                            <textarea id="clientes" class="form-control" rows="5" name="clientes" wire:model.defer="clientes"></textarea>
                            @error('clientes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="modal-footer justify-content-center w-100">
                            <button type="submit" class="btn bg-dark btn-sm">
                                {{ $btnName }}
                            </button>

                            <a href="#" class="px-1 btn btn-primary btn-sm" data-toggle="modal"
                                onclick="Livewire.dispatchTo('sipoc-salida-modal', 'nuevaSalida')"
                                data-target="#salidaModal">
                                <i class="fas fa-plus-circle"></i> Salida
                            </a>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <table class="table table-bordered table-hover table-versiones table-sm">
                        <label class="form-label"> <i class="fas fa-box-open"></i> Productos</label>
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th>N°</th>
                                <th class="text-left">Proceso</th>
                                <th class="text-left">Salida</th>
                                <th>Tipo Salida</th>
                                <th>Requisitos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salidas as $salida)
                                <tr class="text-center">
                                    <td>{{ $salida->id }}</td>
                                    <td class="text-left">{{ $salida->proceso->proceso_nombre }}</td>
                                    <td class="text-left">{{ $salida->salida }}</td>
                                    <td>{{ $salida->tipo }}</td>
                                    <td>{{ $salida->requisitos->count() }}</td>
                                    <td>
                                        <a href="#" class="px-1 btnEditarSalida" data-toggle="modal"
                                            onclick="Livewire.dispatchTo('sipoc-salida-modal', 'editarSalida', { id: {{ $salida->id }} })"
                                            data-target="#salidaModal">
                                            <i class="fas fa-pencil-alt text-dark"></i>
                                        </a>
                                        <a href="#" class="px-3 btnEliminarVersion"
                                            wire:click="eliminarVersion({{ $salida->id }})">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </a>

                                    </td>
                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @livewire('sipoc-salida-modal')
</div>

@push('scripts')
    <script>
        Livewire.on('salidaGuardada', () => {
            @this.call('obtenerSalidas');
        });
    </script>
@endpush

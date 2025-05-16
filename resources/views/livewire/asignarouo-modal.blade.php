@push('styles')
    <style>
        .loading-spinner {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 100;

        }

        .loading-spinner.show {
            display: block;
        }

        .table-ouos {
            font-size: 13px
        }

        .btn-eliminar {
            background-color: #d60000;
            color: white;
        }

        .btn-responsable {
            background-color: #007bff;
            color: white;
        }

        .btn-delegado {
            background-color: #848383;
            color: white;
        }

        .table-ouos {
            fonr-size: 13px;
        }
    </style>
@endpush

<!-- Modal para asociar/disociar OUOs -->
<div wire:ignore.self class="modal fade" id="ModalOUO" tabindex="-1" role="dialog" aria-labelledby="verModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger color-palette">
                <h5 class="modal-title">{{ $modalTitle }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div wire:loading class="loading-spinner">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>

                @csrf

                <form>
                    <!-- Select2 con búsqueda y opción múltiple -->
                    <div class="input-group">
                        <!-- Campo oculto para el ID del proceso -->
                        <input type="hidden" id="ouo_id" wire:model ="ouo_id">

                        <!-- Campo de texto para el nombre del proceso -->
                        <input type="text" class="form-control" id="ouo_nombre" wire:model.defer="ouo_nombre"
                            readonly placeholder="Seleccione el Órgano o Unidad Orgánica a Asociar">


                        <!-- Botón para abrir el modal -->
                        <div class="input-group-append">
                            <a href="#" class="btn bg-navy color-palette" data-toggle="modal"
                                data-target="#ouoModal">
                                <i class="fas fa-search"></i>
                            </a>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-dark" style="border-radius: 25px; padding: 8px 16px;">
                            <i class="fas fa-link"></i> Asociar
                        </button>
                    </div>
                </form>

                <x-modal-busqueda :ruta="route('ouos.listar')" campo-id="ouo_id" campo-nombre="ouo_nombre" modal-titulo="OUO"
                    modal-id="ouoModal" :modalBgcolor="'#001f3f'" :modalTxtcolor="'#FFFFFF'">
                </x-modal-busqueda>

                <hr>

                @if ($proceso)
                    <h3 class="card-title mb-0"> {{ $proceso->proceso_nombre }} -> Unidades Orgánicas Asociadas</h3>
                    <p></p>
                    <table class="table table-bordered table-hover table-sm  table-ouos" id="documentosTable">
                        <thead class="table-header">
                            <tr>
                                <th>Código OUO</th>
                                <th>Nombre OUO</th>
                                <th>R</th>
                                <th>D</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ouos as $ouo)
                                <tr>
                                    <td>{{ $ouo->ouo_codigo }}</td>
                                    <td>{{ $ouo->ouo_nombre }}</td>
                                    <td>{{ $ouo->pivot->responsable ?: 0 }}</td>
                                    <td>{{ $ouo->pivot->delegada ?: 0 }}</td>
                                    <td>
                                        <button class="btn btn-eliminar btn-sm" data-id="{{ $ouo->id ?: null }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <button class="btn btn-responsable btn-sm" data-id="{{ $ouo->id ?: null }}">
                                            R
                                        </button>
                                        <button class="btn btn-delegado btn-sm" data-id="{{ $ouo->id ?: null }}">
                                            D
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No hay unidades orgánicas asociadas a este proceso.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                @else
                    <p>No se encontró el proceso.</p>
                @endif

            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        let cantidadFilasInicial = 0;
        document.addEventListener('DOMContentLoaded', function() {
            // Escucha el evento de clic en el botón "Asociar"
            $(document).on('click', '.btn-dark', function(e) {
                // Evita que el formulario se envíe automáticamente
                e.preventDefault();

                // Obtén los valores seleccionados en el modal de búsqueda
                var ouoId = $('#ouo_id').val();
                var ouoNombre = $('#ouo_nombre').val();

                // Actualiza manualmente los valores de Livewire en el formulario principal
                @this.set('ouo_id', ouoId);
                @this.set('ouo_nombre', ouoNombre);


                // Envía el formulario manualmente
                @this.call('asociarOUO');

            });

            $(document).on('click', '.btn-eliminar', function() {
                var ouoId = $(this).data('id');
                // Llama a la función de disociación en el componente Livewire
                @this.call('desasociarOUO', ouoId);

            });
            $(document).on('click', '.btn-responsable', function() {
                var ouoId = $(this).data('id');
                // Llama a la función de disociación en el componente Livewire
                @this.call('actualizarResponsable', ouoId);

            });
            $(document).on('click', '.btn-delegado', function() {
                var ouoId = $(this).data('id');
                // Llama a la función de disociación en el componente Livewire
                @this.call('actualizarDelegada', ouoId);

            });



        });
    </script>
@endpush

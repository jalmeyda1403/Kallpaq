<div wire:ignore.self class="modal fade" id="documentoModal" tabindex="-1" role="dialog"
    aria-labelledby="documentoModalLabel" data-backdrop="static" data-keyboard="false">


    @push('styles')
        <style>
            /* Estilos específicos para el modal */
            .card-footer small {
                font-size: 0.875rem;
                color: #888;
            }

            .form-control {
                border-radius: 5px;
                width: 100%;
                font-size: small;
            }

            textarea::placeholder {
                color: #d5f6d9;
            }

            .color-palette {
                color: white;
                /* Color del texto dentro del botón */
            }


            .required-field::after {
                content: ' (*)';
                color: red;

            }

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

            .modal-header {
                font-size: 10px;
                padding: 10px;

            }
        </style>
    @endpush
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h6 class="modal-title">{{ $modalTitle }}</h6>

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
                <form method="POST" action="{{ $actionRoute }}"  enctype="multipart/form-data">
                    @csrf
                    @if ($method === 'PUT')
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <div class="form-group small">
                                <label for="proceso_id">Proceso ID</label>
                                <!-- Campo oculto para el ID del proceso -->
                                <input type="hidden" id="proceso_id" name="proceso_id" value= {{$proceso_id}}>
                                <div class="input-group">
                                    <!-- Campo de texto para el nombre del proceso -->
                                    <input type="text" class="form-control" id="proceso_nombre" name="proceso_nombre"
                                        wire:model.live="proceso_nombre" readonly>
                                    <!-- Botón para abrir el modal -->
                                    <button type="button" class="btn btn-dark" data-toggle="modal"
                                        data-target="#procesoModal">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <x-modal-busqueda :ruta="route('procesos.buscar')" campo-id="proceso_id" campo-nombre="proceso_nombre"
                            modal-titulo="Proceso" modal-id="procesoModal" :modalBgcolor="'#001f3f'" :modalTxtcolor="'#FFFFFF'">>
                        </x-modal-busqueda>
                        <div class="col-md-6 mb-1">
                            <div class="form-group small">
                                <label for="tipo_documento_id">Tipo Documento</label>
                                <select id="tipo_documento_id" name="tipo_documento_id" wire:model.live="tipo_documento_id" class="form-control">
                                    <option value="">Seleccione un tipo</option>
                                    @foreach ($tiposDocumento as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_documento_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <div class="form-group small">
                                <label for="cod_documento" class="required-field">Código Documento</label>
                                <input type="text" id="cod_documento" name="cod_documento" wire:model="cod_documento"
                                    class="form-control" maxlength="255" required>
                                @error('cod_documento')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-1">
                            <div class="form-group small">
                                <label for="version" class="required-field">Versión</label>
                                <input type="number" id="version" name="version" wire:model="version" min="00"
                                    class="form-control" required>
                                @error('version')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="form-group small">
                            <label for="nombre" class="required-field">Nombre</label>
                            <input type="text" id="nombre" name="nombre" wire:model="nombre"
                                class="form-control">
                            @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="form-group small">
                            <label for="fuente" class="required-field">Fuente</label>
                            <select id="fuente" name="fuente" wire:model="fuente" class="form-control" required>
                                <option value="">Seleccione</option>
                                <option value="interno">Interno</option>
                                <option value="externo">Externo</option>
                            </select>
                            @error('fuente')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="form-group small">
                            <label for="archivo">Archivo (opcional)</label>
                            <div class="input-group">
                                <input type="file" name="archivo" id="archivo" wire:model.live="archivo" class="form-control">
                                @if ($archivo)
                                    <button type="button" class="btn btn-sm btn-danger"
                                        wire:click="$set('archivo', null)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
                            </div>

                            {{-- Mostrar spinner de carga mientras sube --}}
                            <div wire:loading wire:target="archivo">Subiendo archivo...</div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="form-group small">
                            <label for="enlace">Enlace (puede ser URL externa o ruta del archivo subido)</label>
                            <input type="text" id="enlace" name="enlace" wire:model.live="enlace"
                                class="form-control" placeholder="Ingresa enlace o deja vacío si subes archivo"
                                @if ($archivo || ($enlace && Str::startsWith($enlace, asset('storage')))) disabled @endif>
                            @error('enlace')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="form-group small">
                            <label for="estado" class="required-field">Estado</label>
                            <select id="estado" name="estado" wire:model="estado" class="form-control" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            @error('estado')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center w-100">
                        <button type="submit" class="btn bg-dark">{{ $btnName }}</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

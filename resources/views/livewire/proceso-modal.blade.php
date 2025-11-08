<div wire:ignore.self class="modal fade" id="procesoModal" tabindex="-1" role="dialog" aria-labelledby="procesoModalLabel"
    data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-danger">
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
                <form method="POST" action="{{ $actionRoute }}">
                    @csrf
                    <input type="hidden" name="_method" value="{{ $method }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group small">
                                <label for="Tipo" class="required-field">Tipo</label>
                                <select wire:model.live="proceso_tipo" name="proceso_tipo" id="proceso_tipo"
                                    class="form-control" required>
                                    <option value="Misional">Misional</option>
                                    <option value="Estratégico">Estratégico</option>
                                    <option value="Apoyo">Apoyo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group small">
                                <label for="Estado"> Estado</label>
                                <select wire:model.live="proceso_estado" name="proceso_estado" id="proceso_estado"
                                    class="form-control" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group small">
                                <label for="codigoProceso" class="required-field">Código de Proceso</label>
                                <input type="text" name="cod_proceso" wire:model="cod_proceso" id="cod_proceso"
                                    value="{{ $cod_proceso }}" class="form-control" maxlength="13"
                                    placeholder="Escribe un codigo para el proceso" required>
                                @error('cod_proceso')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group small">
                                <div class="d-flex justify-content-between">
                                    <div class="text-start">
                                        <label for="sigla" class="required-field">Sigla</label>
                                    </div>
                                    <div class="text-end">
                                        <small id="charCountSigla"
                                            class="form-text text-muted">{{ $charCountSigla }}/5</small>
                                    </div>
                                </div>
                                <input type="text" name = "proceso_sigla" wire:model.live="proceso_sigla"
                                    id="proceso_sigla" value="{{ $proceso_sigla }}" class="form-control" maxlength="5"
                                    placeholder="Escribe una sigla para el proceso" required>
                                <div class="d-flex justify-content-between">
                                    <div class="text-start">
                                        @error('proceso_sigla')
                                            <span class="form-text text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                    <x-modal-busqueda :ruta="route('objetivoPEI.buscar')" campo-id="planificacion_pei_id"
                        campo-nombre="planificacion_pei_nombre" modal-titulo="Objetivo" modal-id="objetivoPEIModal"
                        :modalBgcolor="'#001f3f'" :modalTxtcolor="'#FFFFFF'">>
                    </x-modal-busqueda>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group small">
                                <div class="d-flex justify-content-between">
                                    <div class="text-start">
                                        <label for="nombre" class="required-field">Nombre</label>
                                    </div>
                                    <div class="text-end">
                                        <small id="charCountNombre"
                                            class="form-text text-muted">{{ $charCountNombre }}/200</small>
                                    </div>
                                </div>

                                <textarea wire:model.live="proceso_nombre" name="proceso_nombre" id="proceso_nombre" class="form-control"
                                    value="{{ $proceso_nombre }}" rows="2" maxlength="200"
                                    placeholder="Escribe el nombre del proceso en no más de 200 caracteres." required></textarea>

                                <div class="d-flex justify-content-between">
                                    <div class="text-start">
                                        @error('proceso_nombre')
                                            <span class="form-text text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>


                            </div>

                        </div>
                    </div>
                    <div class="form-group small">
                        <div class="d-flex justify-content-between">
                            <div class="text-start">
                                <label for="objetivo" class="required-field">Objetivo</label>
                            </div>
                            <div class="text-end">
                                <small id="charCountObjetivo"
                                    class="form-text text-muted">{{ $charCountObjetivo }}/500</small>
                            </div>
                        </div>

                        <div>
                            <textarea wire:model.live="proceso_objetivo" name="proceso_objetivo" id="proceso_objetivo"
                                value="{{ $proceso_objetivo }}" class="form-control" rows="4" maxlength="500"
                                placeholder="Describe el objetivo del proceso en no más de 1000 caracteres." required></textarea>

                            <div class="d-flex justify-content-between">
                                <div class="text-start">
                                    @error('proceso_objetivo')
                                        <span class="form-text text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group small">
                                <label for="Objetivo"> Objetivo Estratégico</label>
                                <!-- Campo oculto para el ID del proceso -->
                                <input type="hidden" wire:model.live="planificacion_pei_id" name="planificacion_pei_id"
                                    id="planificacion_pei_id" required disabled>
                                <div class="input-group">
                                    <!-- Campo de texto para el nombre del proceso -->
                                    <input type="text" class="form-control" id="planificacion_pei_nombre"
                                        name="objetivo_pei_nombre" wire:model.live="planificacion_pei_nombre" readonly>
                                    <!-- Botón para abrir el modal -->
                                    <button type="button" class ="btn btn-dark" data-toggle="modal"
                                        data-target="#objetivoPEIModal">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group small">
                                <label for="Nivel" class="required-field">Nivel</label>
                                <select wire:model.live="proceso_nivel" name="proceso_nivel" id="proceso_nivel"
                                    class="form-control" required>
                                    @for ($i = 0; $i <= 3; $i++)
                                        <option value="{{ $i }}">
                                            @if ($i == 0)
                                                Macroproceso
                                            @else
                                                Proceso nivel {{ $i }}
                                            @endif
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group small">
                        <label for="Padre"> Proceso Padre</label>
                        <!-- Campo oculto para el ID del proceso -->
                        <input type="hidden" wire:model.live="cod_proceso_padre" name="cod_proceso_padre"
                            id="cod_proceso_padre" required disabled>
                        <div class="input-group">
                            <!-- Campo de texto para el nombre del proceso -->
                            <input type="text" class="form-control" id="proceso_nombre_padre"
                                name="proceso_nombre_padre" wire:model.live="proceso_nombre_padre" readonly>
                            <!-- Botón para abrir el modal -->
                            <button type="button" class ="btn btn-dark" data-toggle="modal"
                                data-target="#procesopadreModal" {{ $isMacroproceso == 1 ? 'disabled' : '' }}>
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <x-modal-busqueda :ruta="route('procesos.buscar')" campo-id="cod_proceso_padre"
                        campo-nombre="proceso_nombre_padre" modal-titulo="Proceso" modal-id="procesopadreModal"
                        :modalBgcolor="'#001f3f'" :modalTxtcolor="'#FFFFFF'">>
                    </x-modal-busqueda>




                    <div class="text-muted">
                        <small>(*) Es obligatorio completar los campos.</small>
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

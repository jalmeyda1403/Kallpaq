<div>
    <hr>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><strong>Requerimientos por Especialista</strong></h6>
        <div>
            <label for="anioSeleccionado" class="me-2">Seleccionar Año:</label>
            <select wire:model.live ="anioSeleccionado" id="anioSeleccionado"
                class="form-control form-control-sm d-inline-block" style="width: auto;">
                @foreach ($aniosDisponibles as $anio)
                    <option value="{{ $anio }}">{{ $anio }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover table-bordered align-middle text-center table-requerimientos">
            <thead class="thead bg-danger">
                <tr>
                    <th>Especialista</th>
                    <th>Asignados</th>
                    <th>Vencidos</th>
                    <th>Finalizados</th>
                    <th>Avance Pendientes</th>
                    <th>Eficacia</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($especialistas as $esp)
                    <tr>

                        <td class="text-left"><img src="{{ asset($esp->foto_url) }}" alt="foto"
                                class="img-circle elevation-2" width="40" height="40"
                                style="margin-right: 10px;"> {{ $esp->nombre }} ( {{ $esp->sigla }})</td>
                        <td>


                            {{ $esp->total_asignados }}

                        </td>
                        <td>
                            <span class="badge bg-danger">
                                {{ $esp->total_vencidos }}
                            </span>
                        </td>
                        <td>
                            {{ $esp->total_finalizados }}
                        </td>
                        <td>
                            <div class="text-center">
                                <strong>{{ $esp->promedioAvance }}%</strong>
                                <div class="progress" style="height: 8px; margin-top: 5px;">
                                    <div class="progress-bar 
                                    {{ $esp->promedioAvance >= 80 ? 'bg-success' : ($esp->promedioAvance >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                        role="progressbar" style="width: {{ $esp->promedioAvance }}%;"
                                        aria-valuenow="{{ $esp->promedioAvance }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <strong>{{ $esp->efectividad }}%</strong>
                                <div class="progress" style="height: 8px; margin-top: 5px;">
                                    <div class="progress-bar 
                                    {{ $esp->efectividad >= 80 ? 'bg-success' : ($esp->efectividad >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                        role="progressbar" style="width: {{ $esp->efectividad }}%;"
                                        aria-valuenow="{{ $esp->efectividad }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>

                            <a href="#" class="btn btn-outline-info btn-sm"
                                onclick="Livewire.dispatchTo('requerimiento.requerimiento-especialista', 'mostrarDetalleEspecialista', { id: {{ $esp->id }}, anio: {{ $anioSeleccionado }} })"
                                data-toggle="modal" data-target="#EspecialistarRequerimientoModal">
                                 <i class="fas fa-eye"></i> Ver detalle
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No hay especialistas registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

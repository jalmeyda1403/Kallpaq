@extends('layout.master')
@section('title', 'SIG')
@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('indicadores.listar', $indicador->proceso_id ?? '') }}">Gestión
                        de Desempeño</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ isset($indicador) ? 'Editar' : 'Crear' }} Indicador
                </li>
            </ol>
        </nav>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-right bg-navy">
                        <div class="col-md-6">
                            <h5>{{ __('Ficha Técnica del Indicador') }}</h5>
                        </div>
                        @isset($indicador)
                            <div class="col-md-6" style="text-align: right">
                                <span>{{ str_pad($indicador->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        @endisset
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ isset($indicador) ? route('indicadores.update', $indicador->id) : route('indicadores.store') }}">
                            @csrf
                            @isset($indicador)
                                @method('PUT')
                            @endisset
                            <!-- Sección 1: Datos del Proceso -->
                            <div class="row">
                                <h6 class="text-left"><b><i class="nav-icon fas fa-cog"></i> 1.Datos del Proceso</b></h6>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="proceso_id">Código de Proceso</label>
                                        <div class="input-group">
                                            <!-- Campo oculto para el ID del proceso -->
                                            <input type="hidden" name="proceso_id" id="proceso_id"
                                                value="{{ $proceso->id ?? old('proceso_id') }}">

                                            <!-- Campo de texto para el nombre del proceso -->
                                            <input type="text" class="form-control" id="proceso_nombre"
                                                name="proceso_nombre"
                                                value="{{ $proceso->proceso_nombre ?? old('proceso_nombre') }}" required
                                                readonly>

                                            <!-- Botón para abrir el modal -->
                                            <a href="#" class="btn btn-primary" data-toggle="modal"
                                                data-target="#procesoModal"><i class="fas fa-search"></i></a>
                                        </div>
                                    </div>
                                    @isset($indicador)
                                        <x-modal-busqueda :ruta="route('procesos.buscar', ['proceso_id' => $proceso->id])" campo-id="proceso_id" campo-nombre="proceso_nombre"
                                            modal-titulo="Proceso" modal-id="procesoModal">
                                        </x-modal-busqueda>
                                    @else
                                        <!-- Componente alternativo cuando no existe el indicador -->
                                        <x-modal-busqueda :ruta="route('procesos.buscar')" campo-id="proceso_id" campo-nombre="proceso_nombre"
                                            modal-titulo="Proceso" modal-id="procesoModal">
                                        </x-modal-busqueda>
                                    @endisset
                                    <!-- Objetivo del SIG -->
                                    <div class="form-group">
                                        <label for="ObjSIG">Objetivo del SIG</label>
                                        <div class="input-group">
                                            <input type="hidden" name="planificacion_sig_id" id="planificacion_sig_id"
                                                value="{{ $indicador->planificacion_sig_id ?? old('planificacion_sig_id') }}">
                                            <input type="text" class="form-control" id="objetivo_nombre_sig"
                                                name="objetivo_nombre_sig"
                                                value="{{ $indicador->objetivoSIG->objetivo_nombre_sig ?? old('objetivo_nombre_sig') }}"
                                                required readonly>
                                            <a href="#" class="btn btn-primary" data-toggle="modal"
                                                data-target="#objetivoSIGModal">
                                                <i class="fas fa-search"></i></a>
                                        </div>
                                    </div>
                                    <x-modal-busqueda :ruta="route('objetivoSIG.buscar')" campo-id="planificacion_sig_id"
                                        campo-nombre="objetivo_nombre_sig" modal-titulo="Objetivo SIG"
                                        modal-id="objetivoSIGModal">
                                    </x-modal-busqueda>


                                    <!-- Objetivo del PEI -->
                                    <div class="form-group">
                                        <label for="ObjPEI">Objetivo del PEI</label>
                                        <div class="input-group">
                                            <input type="hidden" name="planificacion_pei_id" id="planificacion_pei_id"
                                                value="{{ $indicador->planificacion_pei_id ?? old('planificacion_pei_id') }}">
                                            <input type="text" class="form-control" id="objetivo_nombre_pei"
                                                name="objetivo_nombre_pei"
                                                value="{{ $indicador->objetivoPEI->objetivo_nombre_pei ?? old('objetivo_nombre_pei') }}"
                                                required readonly>
                                            <a href="#" class="btn btn-primary" data-toggle="modal"
                                                data-target="#objetivoPEIModal"><i class="fas fa-search"></i></a>
                                        </div>
                                    </div>

                                    <x-modal-busqueda :ruta="route('objetivoPEI.buscar')" campo-id="planificacion_pei_id"
                                        campo-nombre="objetivo_nombre_pei" modal-titulo="Objetivo PEI"
                                        modal-id="objetivoPEIModal">
                                    </x-modal-busqueda>

                                </div>
                                <!-- Sistemas de Gestión -->
                                <div class="col-md-6">
                                    <div class="form-group ml-5">
                                        <label for="sistemas">Sistemas de Gestión Relacionado</label><br>
                                        @foreach (['sgc' => 'SGC (Sistema de Gestión de Calidad)', 'sgas' => 'SGAS (Sistema de Gestión Antisoborno)', 'sgcm' => 'SGCM (Sistema de Gestión de Cumplimiento)', 'sgsi' => 'SGSI (Sistema de Gestión de Seguridad de la Información)', 'sgce' => 'SGCE (Sistema de Gestión de Calidad Educativa)'] as $key => $value)
                                            <!-- Campo oculto para asegurarse de que el valor 0 se envíe si el checkbox no está marcado -->
                                            <input type="hidden" name="{{ $key }}" value="0">

                                            <div class="form-check mb-3 ml-3">
                                                <input type="checkbox" class="form-check-input"
                                                    style="transform: scale(1.3);" id="{{ $key }}"
                                                    name="{{ $key }}" value="1"
                                                    {{ isset($indicador) && $indicador->$key == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="{{ $key }}">{{ $value }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <!-- Sección 2: Datos del Indicador-->
                            <div class="row">
                                <h6 class="text-left"><b><i class="fas fa-chart-bar"></i> 2. Datos del Indicador</b></h6>
                                <input type="hidden" class="form-control" id="indicador_id" name="indicador_id"
                                    value="{{ $indicador->id ?? old('indicador_id') }}" required readonly>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del Indicador</label>
                                        <textarea class="form-control" id="nombre" name="nombre" rows="3" required>{{ $indicador->nombre ?? old('nombre') }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_indicador">Tipo de Indicador</label>
                                        <select class="form-control" id="tipo_indicador" name="tipo_indicador" required>
                                            @foreach ($tiposIndicador as $clave => $valor)
                                                <option value="{{ $clave }}"
                                                    {{ isset($indicador) && $indicador->tipo_indicador === $clave ? 'selected' : '' }}>
                                                    {{ $valor }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sentido">Sentido</label>
                                        <select class="form-control" id="sentido" name="sentido" required>
                                            @foreach ($sentido as $clave => $valor)
                                                <option value="{{ $clave }}"
                                                    {{ isset($indicador) && $indicador->sentido === $clave ? 'selected' : '' }}>
                                                    {{ $valor }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="descripcion">Descripción del indicador</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required>{{ $indicador->descripcion ?? old('descripcion') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="tipo_agregacion">Tipo Agregación</label>
                                        <select class="form-control" id="tipo_agregacion" name="tipo_agregacion"
                                            required>
                                            @foreach ($tiposAgregacion as $clave => $valor)
                                                <option value="{{ $clave }}"
                                                    {{ isset($indicador) && $indicador->tipo_agregacion === $clave ? 'selected' : '' }}>
                                                    {{ $valor }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="parametro_medida">Parametro de Medida</label>
                                        <select class="form-control" id="parametro_medida" name="parametro_medida"
                                            required>
                                            @foreach ($parametroMedida as $clave => $valor)
                                                <option value="{{ $clave }}"
                                                    {{ isset($indicador) && $indicador->parametro_medida === $clave ? 'selected' : '' }}>
                                                    {{ $valor }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label for="descripcion">Fuente de Información</label>
                                        <textarea class="form-control" id="fuente" name="fuente" rows="3" required>{{ $indicador->fuente ?? old('fuente') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="formula">Fórmula del Indicador</label>
                                        <textarea class="form-control" id="formula" name="formula" rows="2" required readonly>{{ $indicador->formula ?? old('formula') }}  </textarea>
                                        <div class="d-flex justify-content-end mt-2">
                                            <a href="#" class="btn btn-primary" data-toggle="modal"
                                                id="btn_validar" data-target="#formulaModal">
                                                <i class="fas fa-calculator"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="formula">Frecuencia de medición</label>
                                        <select name="frecuencia" class="form-control" id="frecuencia">
                                            @foreach ($frecuencias as $clave => $valor)
                                                <option value="{{ $clave }}"
                                                    {{ isset($indicador) && $indicador->frecuencia === $clave ? 'selected' : '' }}>
                                                    {{ $valor }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="meta">Meta</label>
                                        <input type="number" step="0.01" class="form-control" id="meta"
                                            name="meta" value ="{{ $indicador->meta ?? old('meta') }}" required>
                                    </div>
                                </div>
                            </div>
                            <!-- Resto de los campos -->
                            @foreach (range(1, 6) as $i)
                                <input type="hidden" id="var{{ $i }}" name="var{{ $i }}"
                                    value="{{ $indicador->{'var' . $i} ?? old('var' . $i) }}">
                            @endforeach

                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-center">
                                    <button type="submit"
                                        class="btn btn-primary mr-2">{{ isset($indicador) ? 'Actualizar' : 'Crear' }}</button>

                                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
                                </div>

                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>




        <!-- Modal para calcular Formula -->
        <div class="modal fade" id="formulaModal" tabindex="-1" role="dialog" aria-labelledby="formulaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-secondary">
                        <h5 class="modal-title" id="exampleModalLabel">Validar Formula</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formulaForm" action="{{ route('indicadores.validarFormula') }}" method="POST">
                            @csrf
                            <div class="row">
                                @foreach (range(1, 6) as $i)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="var{{ $i }}">Variable {{ $i }}
                                                (var{{ $i }})
                                            </label>
                                            <input type="text" class="form-control"
                                                id="var{{ $i }}_definition_modal"
                                                name="var{{ $i }}_definition" placeholder="Definición"
                                                value="{{ $indicador->{'var' . $i} ?? old('var' . $i . '_definition') }}">

                                            <input type="text" class="form-control" id="var{{ $i }}_value"
                                                name="var{{ $i }}_value" placeholder="Valor de Prueba"
                                                value="{{ $indicador->{'var' . $i} ?? old('var' . $i) }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="formula">Fórmula</label>
                                        <input type="text" class="form-control" id="formula_modal" name="formula"
                                            placeholder="Ingrese la fórmula"
                                            value="{{ $indicador->formula ?? old('formula') }}" required>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="validateFormulaBtn">Validar</button>
                        <button type="button" class="btn btn-primary d-none" id="saveFormulaBtn">Guardar
                            Fórmula</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>

            </div>
        </div>

    </div>

@stop
@section('js')
    <script>
        function toggleValueField(definitionField, valueField) {
            $(definitionField).on('input', function() {
                if ($(this).val().trim() === '') {
                    $(valueField).prop('disabled', true);
                } else {
                    $(valueField).prop('disabled', false);
                }
            }).trigger('input'); // Disparar el evento input para la validación inicial
        }

        $(document).ready(function() {
            $('#validateFormulaBtn').click(function(e) {
                e.preventDefault();

                $.ajax({
                    url: $('#formulaForm').attr('action'),
                    method: 'POST',
                    data: $('#formulaForm').serialize(),
                    success: function(response) {
                        if (response.success) {
                            $('#success-alert').remove();
                            $('#error-alert').remove();
                            $('#saveFormulaBtn').removeClass('d-none');
                            $('#formulaModal .modal-body').prepend(
                                '<div class="alert alert-success" id="success-alert">' +
                                response.message + '</div>');
                        } else {
                            $('#success-alert').remove();
                            $('#error-alert').remove();
                            $('#saveFormulaBtn').addClass('d-none');
                            $('#formulaModal .modal-body').prepend(
                                '<div class="alert alert-danger" id="error-alert">' +
                                response.message + '</div>');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        $('#success-alert').remove();
                        $('#error-alert').remove();
                        $('#saveFormulaBtn').addClass('d-none');
                        var errorHtml = '<div class="alert alert-danger" id="error-alert"><ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul></div>';
                        $('#formulaModal .modal-body').prepend(errorHtml);
                    }
                });
            });


        });

        $(document).ready(function() {
            $('#btn_validar').click(function(e) {
                $('#success-alert').remove();
                $('#error-alert').remove();
                $('#saveFormulaBtn').addClass('d-none');
            });
        });

        $('#saveFormulaBtn').click(function() {
            // Transfiere las definiciones de variables al formulario principal
            $('#formula').val($('#formula_modal').val());
            $('#var1').val($('#var1_definition_modal').val());
            $('#var2').val($('#var2_definition_modal').val());
            $('#var3').val($('#var3_definition_modal').val());
            $('#var4').val($('#var4_definition_modal').val());
            $('#var5').val($('#var5_definition_modal').val());
            $('#var6').val($('#var6_definition_modal').val());
            $('#formulaModal').modal('hide');
        });
    </script>
@stop

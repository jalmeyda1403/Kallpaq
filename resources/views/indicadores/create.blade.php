@extends('layout.master')
@section('title', 'SIG')
@section('content')
<div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"> <a href="{{ route('indicadores.index') }}">Gestión de
                        Resultados</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crear Indicador</li>
            </ol>
        </nav>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-right bg-navy">
                        <div class="col-md-6">
                            <h5>{{ __('Crea Indicador') }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('indicadores.store') }}">
                            @csrf
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
                                            <input type="hidden" name="proceso_id" id="proceso_id" value="">
                                            <input type="text" class="form-control" id="proceso_nombre"
                                                name="proceso_nombre" value="" required readonly>
                                            <a href="#" class="btn btn-primary" data-toggle="modal"
                                                data-target="#procesoModal"><i class="fas fa-search"></i></a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="producto">Producto</label>
                                        <textarea class="form-control" rows="3" class="form-control" id="producto" name="producto" required></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ObjSIG">Objetivo del SIG</label>
                                        <div class="input-group">
                                            <input type="hidden" name="planificacion_sig_id" id="planificacion_sig_id"
                                                value="">
                                            <input type="text" class="form-control" id="objetivo_nombre"
                                                name="objetivo_nombre" value="" required readonly>
                                            <a href="#" class="btn btn-primary" data-toggle="modal"
                                                data-target="#ObjSIGModal"><i class="fas fa-search"></i></a>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="cliente">Cliente</label>
                                        <textarea class="form-control" rows="3" class="form-control" id="cliente" name="cliente" required></textarea>
                                    </div>

                                </div>

                            </div>
                            <hr>
                            <!-- Sección 2: Datos del Indicador-->
                            <div class="row">
                                <h6 class="text-left"><b><i class="fas fa-chart-bar"></i> 2. Datos del Indicador</b></h6>
                                <input type="hidden" class="form-control" id="indicador_id" name="indicador_id"
                                    value="" required readonly>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre del Indicador</label>
                                        <textarea class="form-control" id="nombre" name="nombre" rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_indicador">Tipo de Indicador</label>
                                        <select class="form-control" id="tipo_indicador" name="tipo_indicador" required>
                                            @foreach ($tiposIndicador as $clave => $valor)
                                                <option value="{{ $clave }}">
                                                    {{ $valor }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="sentido">Sentido</label>
                                        <select class="form-control" id="sentido" name="sentido" required>
                                            @foreach ($sentido as $clave => $valor)
                                                <option value="{{ $clave }}">
                                                    {{ $valor }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="descripcion">Descripción del indicador</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="tipo_agregacion">Tipo Agregación</label>
                                        <select class="form-control" id="tipo_agregacion" name="tipo_agregacion"
                                            required>
                                            @foreach ($tiposAgregacion as $clave => $valor)
                                                <option value="{{ $clave }}">
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
                                                <option value="{{ $clave }}">
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
                                        <label for="formula">Fórmula del Indicador</label>
                                        <textarea class="form-control" id="formula" name="formula" rows="2" required readonly> </textarea>
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
                                                <option value="{{ $clave }}">
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
                                            name="meta" value ="" required>
                                    </div>
                                </div>
                            </div>
                            <!-- Resto de los campos -->
                            <input type="hidden" id="var1" name="var1" value="">
                            <input type="hidden" id="var2" name="var2" value="">
                            <input type="hidden" id="var3" name="var3" value="">
                            <input type="hidden" id="var4" name="var4" value="">
                            <input type="hidden" id="var5" name="var5" value="">
                            <input type="hidden" id="var6" name="var6" value="">

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Grabar') }}
                                    </button>
                                    <a href="{{ route('indicadores.listar', $proceso_id) }}" class="btn btn-secondary">
                                        {{ __('Cancelar') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para seleccionar Proceso -->
        <div class="modal fade" id="procesoModal" tabindex="-1" role="dialog" aria-labelledby="procesoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="procesoModalLabel">Seleccionar Proceso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Coloca aquí el contenido del formulario para seleccionar el proceso -->
                        <input type="text" id="procesoSearch" class="form-control" placeholder="Buscar proceso...">
                        <div id="procesoResults"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary">Seleccionar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para seleccionar Objetivos SIG -->
        <div class="modal fade" id="ObjSIGModal" tabindex="-1" role="dialog" aria-labelledby="ObjSIGModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ObjSIGModalLabel">Seleccionar Objetivos SIG</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Coloca aquí el contenido del formulario para seleccionar los Objetivos SIG -->
                        <input type="text" id="objetivoSearch" class="form-control"
                            placeholder="Buscar objetivos...">
                        <div id="objetivoResults"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="var1">Variable 1 (var1)</label>
                                        <input type="text" class="form-control" id="var1_definition_modal"
                                            name="var1_definition" placeholder="Definición"
                                            value="{{ old('var1_definition') }}">
                                        <input type="text" class="form-control" id="var1_value" name="var1_value"
                                            placeholder="Valor de Prueba" value="{{ old('var1_value') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="var3">Variable 3 (var3)</label>
                                        <input type="text" class="form-control" id="var3_definition_modal"
                                            name="var3_definition" placeholder="Definición"
                                            value="{{ old('var3_definition') }}">
                                        <input type="text" class="form-control" id="var3_value" name="var3_value"
                                            placeholder="Valor de Prueba" value="{{ old('var3_value') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="var5">Variable 5 (var5)</label>
                                        <input type="text" class="form-control" id="var5_definition_modal"
                                            name="var5_definition" placeholder="Definición"
                                            value="{{ old('var5_definition') }}">
                                        <input type="text" class="form-control" id="var5_value" name="var5_value"
                                            placeholder="Valor de Prueba" value="{{ old('var5_value') }}">
                                    </div>


                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="var2">Variable 2 (var2)</label>
                                        <input type="text" class="form-control" id="var2_definition_modal"
                                            name="var2_definition" placeholder="Definición"
                                            value="{{ old('var2_definition') }}">
                                        <input type="text" class="form-control" id="var2_value" name="var2_value"
                                            placeholder="Valor de Prueba" value="{{ old('var2_value') }}">
                                    </div>


                                    <div class="form-group">
                                        <label for="var4">Variable 4 (var4)</label>
                                        <input type="text" class="form-control" id="var4_definition_modal"
                                            name="var4_definition" placeholder="Definición"
                                            value="{{ old('var4_definition') }}">
                                        <input type="text" class="form-control" id="var4_value" name="var4_value"
                                            placeholder="Valor de Prueba" value="{{ old('var4_value') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="var6">Variable 6 (var6)</label>
                                        <input type="text" class="form-control" id="var6_definition_modal"
                                            name="var6_definition" placeholder="Definición"
                                            value="{{ old('var6_definition') }}">
                                        <input type="text" class="form-control" id="var6_value" name="var6_value"
                                            placeholder="Valor de Prueba" value="{{ old('var6_value') }}">
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="formula">Fórmula</label>
                                        <input type="text" class="form-control" id="formula_modal" name="formula"
                                            placeholder="Ingrese la fórmula" value="{{ old('formula') }}" required>
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
        var procesos = [];
        var objetivos = [];

        $(document).ready(function() {
            loadProcesos();
            loadObjetivos();

            $('#procesoSearch').on('input', function() {
                var inputText = $(this).val().toLowerCase();
                var filteredProcesos = procesos.filter(function(proceso) {
                    return proceso.nombre.toLowerCase().includes(inputText);
                });
                displayProcesos(filteredProcesos);
            });

            $('#procesoResults').on('click', 'input[type=radio]', function() {
                var id = $(this).data('id');
                var nombre = $(this).data('nombre');
                $('#proceso_id').val(id);
                $('#proceso_nombre').val(nombre);
                $('#procesoModal').modal('hide');
            });

            $('#objetivoSearch').on('input', function() {
                var inputText = $(this).val().toLowerCase();
                var filteredObjetivos = objetivos.filter(function(objetivo) {
                    return objetivo.nombre_objetivo.toLowerCase().includes(inputText);
                });
                displayObjetivos(filteredObjetivos);
            });

            $('#objetivoResults').on('click', 'input[type=radio]', function() {
                var id = $(this).data('id');
                var nombre = $(this).data('nombre');
                $('#planificacion_sig_id').val(id);
                $('#objetivo_nombre').val(nombre);
                $('#ObjSIGModal').modal('hide');
            });

            toggleValueField('#var1_definition_modal', '#var1_value');
            toggleValueField('#var2_definition_modal', '#var2_value');
            toggleValueField('#var3_definition_modal', '#var3_value');
            toggleValueField('#var4_definition_modal', '#var4_value');
            toggleValueField('#var5_definition_modal', '#var5_value');
            toggleValueField('#var6_definition_modal', '#var6_value');

        });

        function loadProcesos() {
            $.ajax({
                url: '/buscarprocesos', // Reemplaza por la URL correcta para obtener los procesos
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    procesos = data;
                    displayProcesos(procesos);
                },
                error: function(error) {
                    console.log('Error al cargar los procesos:', error);
                }
            });
        }

        function displayProcesos(filteredProcesos) {
            var $procesoResults = $('#procesoResults');
            $procesoResults.empty();

            filteredProcesos.forEach(function(proceso) {
                var html = '<div class="form-check">';
                html += '<input class="form-check-input" type="radio" name="proceso_id" data-id="' + proceso.id +
                    '" data-nombre="' + proceso.nombre + '">';
                html += '<label class="form-check-label" for="' + proceso.cod_proceso + '">' + proceso.cod_proceso +
                    ' - ' + proceso.nombre + '</label>';
                html += '</div>';
                $procesoResults.append(html);
            });

        }

        function loadObjetivos() {
            $.ajax({
                url: '/buscarobjetivos', // Reemplaza por la URL correcta para obtener los procesos
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    objetivos = data;
                    displayObjetivos(objetivos);
                },
                error: function(error) {
                    console.log('Error al cargar los procesos:', error);
                }
            });
        }

        function displayObjetivos(filteredObjetivos) {
            var $objetivoResults = $('#objetivoResults');
            $objetivoResults.empty();

            filteredObjetivos.forEach(function(objetivo) {
                var html = '<div class="form-check">';
                html += '<input class="form-check-input" type="radio" name="id" data-id="' +
                    objetivo.id + '" data-nombre="' + objetivo.nombre_objetivo + '">';
                html += '<label class="form-check-label" for="' + objetivo.id + '">' + objetivo.id + ' - ' +
                    objetivo.nombre_objetivo + '</label>';
                html += '</div>';
                $objetivoResults.append(html);
            });

        }

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

        // Al hacer clic en "Guardar Cambios" en el modal de Proceso
    </script>
@stop

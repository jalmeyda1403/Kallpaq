@extends('facilitador.layout.master')
@section('title', 'SIG')
@section('content')
<div class="container-fluid">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('indicadores.listar', $indicador->proceso_id) }}">Gestión de Resultados</a></li>
          <li class="breadcrumb-item active" aria-current="page">Editar Fórmula</li>
        </ol>
      </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" id="cardFormula">
                <div class="card-header">
                    <h4><b>{{ __('Definir y validar Indicador') }}</h4>
                    <input type="hidden" id="id" value="{{ $indicador->id }}">
                </div>

                <div class="card-body">
                    <form id="formulaForm" action="{{ route('indicadores.validarFormula') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="var1">Variable 1 (var1)</label>
                                    <input type="text" class="form-control" id="var1_definition" name="var1_definition"
                                        placeholder="Definición" value="{{ old('var1_definition', $indicador->var1) }}">
                                    <input type="text" class="form-control" id="var1_value" name="var1_value"
                                        placeholder="Valor de Prueba" value="{{ old('var1_value') }}">
                                </div>
                                <div class="form-group">
                                    <label for="var3">Variable 3 (var3)</label>
                                    <input type="text" class="form-control" id="var3_definition" name="var3_definition"
                                        placeholder="Definición" value="{{ old('var3_definition', $indicador->var3) }}">
                                    <input type="text" class="form-control" id="var3_value" name="var3_value"
                                        placeholder="Valor de Prueba" value="{{ old('var3_value') }}">
                                </div>
                                <div class="form-group">
                                    <label for="var5">Variable 5 (var5)</label>
                                    <input type="text" class="form-control" id="var5_definition" name="var5_definition"
                                        placeholder="Definición" value="{{ old('var5_definition', $indicador->var5) }}">
                                    <input type="text" class="form-control" id="var5_value" name="var5_value"
                                        placeholder="Valor de Prueba" value="{{ old('var5_value') }}">
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="var2">Variable 2 (var2)</label>
                                    <input type="text" class="form-control" id="var2_definition" name="var2_definition"
                                        placeholder="Definición" value="{{ old('var2_definition', $indicador->var2) }}">
                                    <input type="text" class="form-control" id="var2_value" name="var2_value"
                                        placeholder="Valor de Prueba" value="{{ old('var2_value') }}">
                                </div>
                                <div class="form-group">
                                    <label for="var4">Variable 4 (var4)</label>
                                    <input type="text" class="form-control" id="var4_definition" name="var4_definition"
                                        placeholder="Definición" value="{{ old('var4_definition', $indicador->var4) }}">
                                    <input type="text" class="form-control" id="var4_value" name="var4_value"
                                        placeholder="Valor de Prueba" value="{{ old('var4_value') }}">
                                </div>

                                <div class="form-group">
                                    <label for="var6">Variable 6 (var6)</label>
                                    <input type="text" class="form-control" id="var6_definition" name="var6_definition"
                                        placeholder="Definición" value="{{ old('var6_definition', $indicador->var6) }}">
                                    <input type="text" class="form-control" id="var6_value" name="var6_value"
                                        placeholder="Valor de Prueba" value="{{ old('var6_value') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="formula">Fórmula</label>
                                    <input type="text" class="form-control" id="formula" name="formula"
                                        placeholder="Ingrese la fórmula"
                                        value="{{ old('formula', $indicador->formula) }}" required>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-footer">
                        <button type="button" class="btn btn-success" id="validateFormulaBtn">Validar</button>
                        <button type="button" class="btn btn-primary d-none" id="grabarFormula">Guardar
                            Fórmula</button>
                        <a href="{{ route('indicadores.listar', $indicador->proceso_id) }}"
                            class="btn btn-secondary">Regresar</a>
                    </div>


                </div>

            </div>
        </div>
    </div>
    </div>

@stop
@section('js')
    <script>
        $('#grabarFormula').on('click', function() {
            var indicadorId = $('#id').val();
            var formAction = '/indicadores/' + indicadorId + '/actualizar-formula';
            $('#formulaForm').attr('action', formAction);
            $('#formulaForm').submit();
        });

        $(document).ready(function() {
            toggleValueField('#var1_definition', '#var1_value');
            toggleValueField('#var2_definition', '#var2_value');
            toggleValueField('#var3_definition', '#var3_value');
            toggleValueField('#var4_definition', '#var4_value');
            toggleValueField('#var5_definition', '#var5_value');
            toggleValueField('#var6_definition', '#var6_value');

            function toggleValueField(definitionField, valueField) {
                $(definitionField).on('input', function() {
                    if ($(this).val().trim() === '') {
                        $(valueField).prop('disabled', true);
                    } else {
                        $(valueField).prop('disabled', false);
                    }
                }).trigger('input'); // Disparar el evento input para la validación inicial
            }
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
                            $('#grabarFormula').removeClass('d-none');
                            $('#cardFormula .card-body').prepend(
                                '<div class="alert alert-success" id="success-alert">' +
                                response.message + '</div>');
                        } else {
                            $('#success-alert').remove();
                            $('#error-alert').remove();
                            $('#grabarFormula').addClass('d-none');
                            $('#cardFormula .card-body').prepend(
                                '<div class="alert alert-danger" id="error-alert">' +
                                response.message + '</div>');
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        $('#success-alert').remove();
                        $('#error-alert').remove();
                        $('#grabarFormula').addClass('d-none');
                        var errorHtml = '<div class="alert alert-danger" id="error-alert"><ul>';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul></div>';
                        $('#cardFormula .card-body').prepend(errorHtml);
                    }
                });
            });
        });
    </script>

@stop

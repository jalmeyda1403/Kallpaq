@extends('layout.master')
@section('title', 'SIG')
@section('css')

    <style>
        .selected {
            background-color: #ECECEC;
            /* Light gray background for selected row */
        }
    </style>


@endsection
@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Gestión de Resultados</a></li>
                <li class="breadcrumb-item active" aria-current="page">Listado Indicadores</li>
            </ol>
        </nav>
        <div id="successMessage"></div>

        @if (session('success'))
            <div class="alert alert-success" id="success-alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" id="error-alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header ui-sortable-handle">
                        <h3 class="card-title">Lista de indicadores</h3>
                        <div class="card-tools">
                            <a href="{{ route('indicadores.create') }}" class="btn btn-primary" data-toggle="tooltip"
                                title="Nuevo Indicador">Nuevo Indicador</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th style="display: none;">Id</th>
                                    <th>Proceso</th>
                                    <th>Indicador</th>
                                    <th>Descripción</th>
                                    <th>Fórmula</th>
                                    <th>Frecuencia</th>
                                    <th>Meta</th>
                                
                                    <th style="width:12%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $item = 1;
                                @endphp
                           
                                    @foreach ($indicadores as $indicador)
                                        <tr class="clickable-row">
                                            <td>{{ $item++ }}</td>
                                            <td style="display: none;">{{ $indicador->id }}</td>
                                            <td>{{ $indicador->proceso->nombre }}</td>
                                            <td>{{ $indicador->nombre }}</td>
                                            <td>{{ $indicador->descripcion }}</td>
                                            <td>{{ $indicador->formula }}</td>
                                            <td>{{ $indicador->frecuencia }}</td>
                                            <td>{{ $indicador->meta }}</td>
                                           
                                            <td>
                                                <a href="#" class="view-btn btn-info btn-sm"
                                                    data-indicador-id="{{ $indicador->id }}" data-toggle="tooltip"
                                                    title="Ver Datos"><i class="fa fa-eye"></i></a>
                                                <a href="{{ route('indicadores.edit', $indicador->id) }}"
                                                    class="btn-warning btn-sm" data-toggle="tooltip"
                                                    title="Actualizar Indicador"><i class="fas fa-pencil-alt"></i></a>
                                                <a href="{{ route('indicadores.formula', $indicador->id) }}"
                                                    class="btn-primary btn-sm" data-toggle="tooltip"
                                                    title="Establecer Formula"><i class="fas fa-calculator"></i></a>
                                                <a href="{{ route('indicadores.frecuencia', $indicador->id) }}"
                                                    class="btn-danger btn-sm" data-toggle="tooltip"
                                                    title="Generar Frecuencia"><i class="fa fa-magic"
                                                        aria-hidden="true"></i></a>
                                                <a href="#" class="btn-dark btn-sm" data-toggle="tooltip"
                                                    title="Aprobar"><i class="fa fa-check" aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach                                    
                       
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <section class="col-lg-7">
                <div class="card">
                    <div class="card-header ui-sortable-handle" style="cursor: move;">
                        <h3 class="card-title">
                            <i class="fa fa-table mr-1" aria-hidden="true"></i>
                            Datos del indicador
                        </h3>
                        <div class="card-tools">
                            <ul class="nav nav-pills ml-auto">
                                <li class="nav-item">
                                    <a id="btnPeriodoActual"class="nav-link active btn-sm" href="#"
                                        data-toggle="tab">Periodo actual</a>
                                </li>
                                <li class="nav-item">
                                    <a id="btnHistorico" class="nav-link btn-sm" href="#" data-toggle="tab">Histórico
                                        anual</a>
                                </li>
                                <li class="nav-item">
                                    <a id="btnPlanes" class="nav-link btn-sm" href="#" data-toggle="tab">Planes
                                        acción</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- ... contenido posterior ... -->

                        <div class="panel">

                            <table id="historical-data-table" class="table">
                                <thead>
                                    <tr>
                                        <th>Año</th>
                                        <th>Meta</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Datos históricos se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>

                        <div class="panel2">

                            <table id="indicador-data-table" class="table">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Fecha</th>
                                        <th>Meta</th>
                                        <th>Valor</th>
                                        <th>Indice</th>
                                        <th style="width: 100px">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Datos históricos se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section class="col-lg-5">
                <div class="card">
                    <div class="card-header ui-sortable-handle" style="cursor: move;">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Evolución indicador
                        </h3>
                        <div class="card-tools">
                            <ul class="nav nav-pills ml-auto">
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart" width="400" height="400"></canvas>
                    </div>
                </div>
            </section>
        </div>

        <!-- Modal Indicador Seguimiento -->
        <div class="modal fade" id="ModalIndicadores" tabindex="-1" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editDataForm" action="#" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Editar Indicador Seguimiento</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <input type="hidden" name="id" id="id">
                            <div class="form-group">
                                <label for="fecha">Fecha</label>
                                <input type="date" class="form-control" name="fecha" id="fecha" readonly>
                            </div>
                            <div class="form-group">
                                <label for="meta">Meta</label>
                                <input type="number" class="form-control" name="meta" id="meta" step="any"
                                    readonly>
                            </div>
                            <div class="form-group" id ="var1_group">
                                <label for="var1">val1</label>
                                <input type="number" class="form-control" name="var1" id="var1"step="any">
                            </div>
                            <div class="form-group" id ="var2_group">
                                <label for="var2">val2</label>
                                <input type="number" class="form-control" name="var2" id="var2"
                                    step="any">
                            </div>
                            <div class="form-group" id ="var3_group">
                                <label for="var3">val3</label>
                                <input type="number" class="form-control" name="var3" id="var3"
                                    step="any">
                            </div>
                            <div class="form-group" id ="var4_group">
                                <label for="var4">val4</label>
                                <input type="number" class="form-control" name="var4" id="var4"
                                    step="any">
                            </div>
                            <div class="form-group" id ="var5_group">
                                <label for="var5">val5</label>
                                <input type="number" class="form-control" name="var5" id="var5"
                                    step="any">
                            </div>
                            <div class="form-group" id ="var6_group">
                                <label for="var6">val6</label>
                                <input type="number" class="form-control" name="var6" id="var6"
                                    step="any">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script>
        $(document).ready(function() {
            const btnPeriodoActual = $("#btnPeriodoActual");
            const btnHistorico = $("#btnHistorico");

            var indicadorId = "";
            $('.panel').hide();
            $('.panel2').hide();

            $('.view-btn').click(function() {
                indicadorId = $(this).data('indicador-id');
                loadData(indicadorId);
                loadChart(indicadorId);
            });

            $('#edit-btn').click(function() {
                console.log("Me diste click");
            });

            btnPeriodoActual.click(function() {
                loadData(indicadorId)
            });

            btnHistorico.click(function() {
                loadHistoricalData(indicadorId)

            });

            function loadHistoricalData(indicadorId) {
                $('#historical-data-table tbody').empty();
                $.ajax({
                    url: '/indicadores/' + indicadorId + '/historico-datos',
                    type: 'GET',
                    success: function(data) {

                        // Agregar datos históricos a la tabla
                        data.forEach(function(item) {
                            var row = '<tr><td>' + item.año +
                                '</td><td>' + item.meta +
                                '</td><td>' + item.valor +

                                '</td></tr>';
                            $('#historical-data-table tbody').append(row);
                        });

                        // Mostrar el panel de datos históricos
                        $('.panel').show();
                        $('.panel2').hide();
                    }
                });
            };

            function loadData(indicadorId) {

                $.ajax({
                    url: '/indicadores/' + indicadorId + '/datos',
                    type: 'GET',
                    success: function(data) {
                        $('#indicador-data-table tbody').empty();
                      
                        // Agregar datos históricos a la tabla
                        data.forEach(function(item,i) {
                            var estadoIcon = '';

                            if (item.estado === 'bueno') {
                                estadoIcon =
                                    '<i class="fas fa-circle text-success"></i>'; // Verde
                            } else if (item.estado === 'regular') {
                                estadoIcon =
                                    '<i class="fas fa-circle text-warning"></i>'; // Amarillo
                            } else if (item.estado === 'malo') {
                                estadoIcon =
                                    '<i class="fas fa-circle text-danger"></i>'; // Rojo
                            }

                            var row = '<tr>' +
                                '<td>' + (i +1)+ '</td>' +
                                '<td>' + item.fecha + '</td>' +
                                '<td>' + item.meta.toFixed(2) + '</td>' +
                                '<td>' + item.valor.toFixed(2) + '</td>' +
                                '<td>' + estadoIcon + '</td>' +
                                '<td>';
                            if (item.editable) {
                                row +=
                                    '<a href="#" id="edit-btn" class="btn-primary edit-button btn-sm" style="margin-right: 2px" data-id="' +
                                    item.id +
                                    '"> <i class="fas fa-pencil-alt" ></i> </a> ' +
                                    ' <a href="#" target="_blank" class="btn-primary btn-sm" style="margin-right: 2px"><i class="fas fa-folder"></i> </a> ';
                            } else {
                                row +=
                                    '<a href="#" class="btn-secondary  btn-sm" style="margin-right: 2px" disabled> <i class="fas fa-pencil-alt" ></i> </a> ' +
                                    ' <a href="#" class="btn-secondary  btn-sm" style="margin-right: 2px" disabled> <i class="fas fa-folder"></i> </a>';
                            }

                            row += '</td></tr>';
                            $('#indicador-data-table tbody').append(row);
                        });

                        // Mostrar el panel de datos históricos
                        $('.panel').hide();
                        $('.panel2').show();
                    }
                });
            };
            //BarChart
            function loadChart(indicadorId) {
                $.ajax({
                    url: '/indicadores/' + indicadorId + '/historico-datos',
                    type: 'GET',
                    success: function(response) {
                        const historicalData = response;

                        // Prepara los datos para el gráfico
                        const years = historicalData.map(data => data.año);
                        const values = historicalData.map(data => data.valor);
                        const meta = historicalData.map(data => data
                            .meta); // Tomamos la meta del primer registro histórico
                        // Paleta de colores
                        const colorPalette = generateColorPalette(years.length);
                        // Configuración del gráfico
                        const ctx = document.getElementById('myChart').getContext('2d');
                        const myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: years,
                                datasets: [{
                                    z: 2,
                                    type: 'line',
                                    label: 'Meta',
                                    data: meta, // Llena el arreglo con la meta
                                    backgroundColor: 'rgba(0, 0, 0, 0)', // Fondo transparente
                                    borderColor: 'red',
                                    borderDash: [5, 2],
                                    pointRadius: 0, // No muestra puntos en la línea
                                    fill: true

                                }, {
                                    z: 1,
                                    type: 'bar',
                                    label: 'Valores',
                                    data: values,
                                    backgroundColor: colorPalette, // Utiliza la función generateColorPalette
                                    fill: false

                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    xAxes: [{
                                        scaleLabel: {
                                            display: true,
                                            labelString: 'Año'
                                        }
                                    }],
                                    yAxes: [{
                                        scaleLabel: {
                                            display: false,
                                            labelString: 'Valor'
                                        },
                                        ticks: {
                                            beginAtZero: true,
                                            stepSize: 0.2 // Aumento del eje Y
                                        }
                                    }]
                                },
                                legend: {
                                    display: true // Ocultar la leyenda
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    datalabels: {
                                        anchor: 'end',
                                        align: 'top',
                                        display: 'true', // Mostrar las etiquetas automáticamente
                                        color: 'black', // Color del texto de las etiquetas
                                        formatter: value => value.toFixed(
                                            2) // Formatear valores en formato 0.00
                                    }
                                }
                            }
                        });
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            };

            function generateColorPalette(count) {
                const palette = [];
                const baseColors = [
                    '#7FBCD2', '#A5F1E9', '#D0F5BE', '#FFEEAF', '#9b59b6', '#1abc9c', '#e67e22',
                    '#95a5a6', '#d35400'
                ];
                for (let i = 0; i < count; i++) {
                    palette.push(baseColors[i % baseColors.length]);
                }
                return palette;
            };
            //Grabar Seguimiento Indicadores

            $('#editDataForm').submit(function(event) {
                event.preventDefault(); // Evitar el envío normal del formulario
                var id = $('#id').val();
                var formData = $(this).serialize(); // Obtener datos del formulario
                console.log(id);
                $.ajax({
                    url: '/indicadores_seguimiento/' + id, // URL del controlador
                    type: 'POST', // Método POST
                    data: formData, // Enviar datos del formulario
                    success: function(response) {
                        $('#successMessage').empty().append(
                            '<div class="alert alert-success" id="success-alert">' +
                            response.message + '</div>');
                        loadData(indicadorId);
                        // Ocultar el mensaje después de 5 segundos
                        setTimeout(function() {
                            $('#successMessage').empty();
                        }, 5000);
                    }
                });

                $('#ModalIndicadores').modal('hide');
            });

            $('.clickable-row').click(function() {
                // Remove "selected" class from all rows
                $('.clickable-row').removeClass('selected');

                // Add "selected" class to the clicked row
                $(this).addClass('selected');

                // Change opacity of the selected row
                $(this).find('td:not(:last-child)').css('opacity',
                0.8); // Set opacity to 0.7 (adjust as needed)
            });



        });
        // alertas
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener el elemento por su id
            var successAlert = document.getElementById('success-alert');

            var errorAlert = document.getElementById('error-alert');

            // Verificar si el elemento existe
            if (successAlert) {
                // Ocultar el mensaje de éxito después de 5 segundos
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 5000); // 5000 milisegundos = 5 segundos
            }


            if (errorAlert) {
                // Ocultar el mensaje de éxito después de 5 segundos
                setTimeout(function() {
                    errorAlert.style.display = 'none';
                }, 5000); // 5000 milisegundos = 5 segundos
            }
        });

        //Modal Seguimiento Indicadores
        $(document).on('click', '.edit-button', function() {
            event.preventDefault();
            const id = $(this).data('id');
            const $form = $('#editDataForm');
            $.ajax({
                url: '/indicadores/' + id + '/editdatos',
                type: 'GET',
                success: function(data) {
                    // Asigna los valores a los campos del formulario modal
                    $('#id').val(data.id[1]);
                    $('#fecha').val(data.fecha);
                    $('#meta').val(data.meta[1]);

                    // Función para llenar los campos de las variables y ocultar grupos si es necesario
                    function fillVariableField(varName, varData) {
                        var label = $('label[for="' + varName + '"]');
                        if (varData[0] !== null) {
                            $('#' + varName).val(varData[1]);
                            label.text(varData[0]);
                        } else {
                            $('#' + varName + '_group').hide();
                        }
                    }

                    fillVariableField('var1', data.var1);
                    fillVariableField('var2', data.var2);
                    fillVariableField('var3', data.var3);
                    fillVariableField('var4', data.var4);
                    fillVariableField('var5', data.var5);
                    fillVariableField('var6', data.var6);

                    $('#ModalIndicadores').modal('show');
                }
            });

        });
    </script>
@stop

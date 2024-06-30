@extends('facilitador.layout.master')
@section('title', 'SIG')
@section('css')
    <style>
        .table-condensed th,
        .table-condensed td {
            padding: 4px !important;
            margin: 0 !important;
            font-size: 0.875rem;
        }
    </style>
@endsection
@section('content')

    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard Mejora de Procesos</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title">Estado SMP</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr class="p-0 m-0">
                                    <th>Proceso</th>
                                    <th>SMP Abiertas</th>
                                    <th>SMP En implementación</th>
                                    <th>SMP Pendientes</th>
                                    <th>SMP Cerradas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estadoSmpData as $data)
                                    <tr class="p-0 m-0">
                                        <td>{{ $data->proceso }}</td>
                                        <td>{{ $data->abiertos }}</td>
                                        <td>{{ $data->implementaciones }}</td>
                                        <td>{{ $data->pendientes }}</td>
                                        <td>{{ $data->cerradas }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td>{{ $totalAbiertas }}</td>
                                    <td>{{ $totalImplementacion }}</td>
                                    <td>{{ $totalPendientes }}</td>
                                    <td>{{ $totalCerradas }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title">SMP por Estado</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="smpEstadoChart" style="width:300; height:300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title">Clasificación de SMP</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="hallazgosChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
        <script>
            var colores = {
                'Abierto': '#e9ecef',
                'En implementación': '#e4a35e',
                'Pendiente': '#df4666',
                'Cerrado': '#7a9ebb'
            };

            var smpEstadoCtx = document.getElementById('smpEstadoChart').getContext('2d');
            var smpEstadoChart = new Chart(smpEstadoCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Abiertas', 'En implementación', 'Pendientes', 'Cerradas'],
                    datasets: [{
                        data: [{{ $smpAbiertas }}, {{ $smpImplementacion }}, {{ $smpPendientes }},
                            {{ $smpCerradas }}
                        ],
                        backgroundColor: Object.values(colores),
                        borderColor: '#cccccc',
                        borderWidth: 1
                    }]
                },
                options: {
                    cutout: '60%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            color: '#fff',
                            formatter: (value, ctx) => {
                                let sum = 0;
                                let dataArr = ctx.chart.data.datasets[0].data;
                                dataArr.map(data => {
                                    sum += data;
                                });
                                let percentage = (value * 100 / sum).toFixed(1) + "%";
                                return value + ', (' + percentage + ')';
                            },
                            anchor: 'end',
                            align: 'center',
                            offset: -5,
                            borderWidth: 1,
                            borderColor: '#000',
                            borderRadius: 0,
                            backgroundColor: '#000',
                            font: {
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.raw !== null) {
                                        label += context.raw;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels] // Agrega el plugin ChartDataLabels
            });


            var ctx2 = document.getElementById('hallazgosChart').getContext('2d');
            // Datos del controlador
            var hallazgosData = @json($smp);
            // Obtiene las clasificaciones y estados
            var clasificaciones = Object.keys(hallazgosData);;
            var estados = Object.keys(hallazgosData[clasificaciones[0]]);

            // Crea los datasets para cada estado
            var datasets = estados.map(function(estado) {
                return {
                    label: estado,
                    data: clasificaciones.map(function(clasificacion) {
                        return hallazgosData[clasificacion][estado];
                    }),
                    backgroundColor: colores[estado] || '#cccccc',
                    borderColor: '#cccccc',
                    borderWidth: 1,
                    stack: 0
                };
            });

            // Configuración del gráfico
            var hallazgosChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: clasificaciones,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.raw !== null) {
                                        label += context.raw;
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: true,
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: false
                        }
                    }
                }
            });
        </script>
    @endsection

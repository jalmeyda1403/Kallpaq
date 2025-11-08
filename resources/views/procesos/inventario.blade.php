@extends('layout.master')
@section('title', 'SIG')
@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('procesos.index') }}">Procesos</a></li>

            </ol>
        </nav>
        <div class="card">
            <div class="card-header">
                <form action="{{ route('documento.buscar') }}" method="GET">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Título de la lista de procesos -->
                        <div class="col-md-6 text-md-left">
                            <h3 class="card-title mb-0">Mapa de Procesos Vigente</h3>
                        </div>
                        <div class="input-group">
                            <input type="text" name="buscar_proceso" id="buscar_proceso" class="form-control me-2"
                                placeholder="Buscar Proceso">

                            <!-- Botón de Filtrar -->
                            <button type="submit" class="btn bg-black">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="row d-flex align-items-stretch">
                    <div class="col-md-1">
                        <div class="card h-100 d-flex flex-column">
                            <div class="card-body clientes">Requisitos del Cliente <p> <i
                                        class="fa fa-arrow-circle-left fa-2x" aria-hidden="true"></i>
                            </div>

                        </div>

                    </div>
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-header bg-estrategico">
                                <h6>Procesos Estratégicos</h6>
                            </div>
                            <div class="card-body card-procesos ">
                                <div class="row justify-content-center">
                                    @foreach ($procesos as $proceso)
                                        @if ($proceso->proceso_tipo === 'Estratégico')
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-6">
                                                <div class="small-box bg-estrategico">
                                                    <div class="inner">
                                                        {{ $proceso->proceso_nombre }}
                                                    </div>
                                                    <a href="{{ route('procesos.nivel', ['proceso_id' => $proceso->id]) }}"
                                                        class="small-box-footer">
                                                        {{ $proceso->cod_proceso }} <i
                                                            class="fas fa-arrow-circle-right"></i></a>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <p>
                        </div>

                        <div class="text-center mt-3">
                            <div class="circle-arrow">
                                <i class="fa fa-arrow-circle-down fa-2x" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-misional">
                                <h6>Procesos Misionales</h6>
                            </div>
                            <div class="card-body card-procesos ">
                                <div class="row justify-content-center">
                                    @foreach ($procesos as $proceso)
                                        @if ($proceso->proceso_tipo === 'Misional')
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-6">
                                                <div class="small-box bg-misional" style="top: 10px;margin-top: 10px;">
                                                    <div class="inner">
                                                        {{ $proceso->proceso_nombre }}
                                                    </div>
                                                    <a href="{{ route('procesos.nivel', ['proceso_id' => $proceso->id]) }}"
                                                        class="small-box-footer">
                                                        {{ $proceso->cod_proceso }} <i
                                                            class="fas fa-arrow-circle-right"></i></a>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <p>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <div class="circle-arrow">
                                <i class="fa fa-arrow-circle-up fa-2x" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-apoyo">
                                <h6>Procesos de Apoyo</h6>
                            </div>
                            <div class="card-body card-procesos ">
                                <div class="row justify-content-center">
                                    @foreach ($procesos as $proceso)
                                        @if ($proceso->proceso_tipo === 'Apoyo')
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-6"
                                                style="top: 10px;margin-top: 10px;">
                                                <div class="small-box bg-apoyo" style="top: 10px;">
                                                    <div class="inner">
                                                        {{ $proceso->proceso_nombre }}
                                                    </div>
                                                    <a href="{{ route('procesos.nivel', ['proceso_id' => $proceso->id]) }}"
                                                        class="small-box-footer">{{ $proceso->cod_proceso }}
                                                        <i class="fas fa-arrow-circle-right"></i></a>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="card h-100 d-flex flex-column">
                            <div class="card-body productos"><i class="fa fa-arrow-circle-left fa-2x"
                                    aria-hidden="true"></i>
                                <p>Productos de la CGR
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Inventario de Procesos</h4>
            </div>
            <div class="card-body">
                <!-- Tabla para mostrar los datos relevantes -->
                <table class="table table-bordered table-inventario">
                    <thead class="table-header">
                        <tr>
                            <th>Nombre</th>
                            <th>Documento Aprueba</th>
                            <th>Vigencia</th>
                            <th>Enlace</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventarios as $inventario)
                            <tr>
                                <td>{{ $inventario->nombre }}</td>
                                <td>{{ $inventario->documento_aprueba }}</td>
                                <td>{{ $inventario->vigencia->format('d-m-Y') }}</td>
                                <td class="text-center">
                                    <a href onclick="openModal('{{ $inventario->enlace }}')" data-toggle="modal"
                                        data-target="#pdfModal">
                                        <i class="fas fa-file-pdf fa-lg" style="color:red;">
                                        </i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Modal para mostrar el PDF -->
        <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="pdfModalLabel">Documento PDF</h5>
                        <!-- Botón de cerrar con data-dismiss para cerrar el modal -->
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Aquí se mostrará el PDF -->
                        <embed id="pdfEmbed" type="application/pdf" width="100%" height="550px">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function openModal(url) {
            // Cambiar la URL del PDF en el modal
            document.getElementById('pdfEmbed').src = url;
        }
    </script>
@endpush

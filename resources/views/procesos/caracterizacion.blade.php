@extends('layout.master')
@section('title', 'Matriz de Caracterización')
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

        .bg-olivo {
            background-color: #3d9970;
            /* El color oliva que deseas */
            color: white;
            /* Asegúrate de que el texto sea visible en el botón */
        }

        .bg-olivo:hover {
            background-color: #14ab14;
            color: white;
            /* Un tono más oscuro al pasar el mouse sobre el botón */
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

        .modal-content {
            border-radius: 10px;
            overflow: hidden;
        }

       

        /* Estilo de la celda adicional con información */

        .proceso {
            color: #3d3c3c;
            font-size: 13px;
        }

        .table tbody tr td {

            padding: 5px;
            border-radius: 3px;
            font-size: 12px;
        }

        .table thead tr th {
            font-size: 13px;
        }

        ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        li {
            margin-bottom: 5px;
            /* Espacio entre los elementos de la lista */
        }

        a {
            text-decoration: none;
            /* Eliminar subrayado en los enlaces */
            color: #2881da;
            /* Color azul del enlace */
        }

        a:hover {
            color: #b3000c;
            /* Cambiar color del enlace cuando se pasa el mouse */
        }
        .table-requisitos{
            font-size: 13px;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('procesos.index') }}">Procesos</a></li>
                <li class="breadcrumb-item">Matriz de Caracterización</a></li>

            </ol>
        </nav>
        <div class="row d-flex align-items-stretch">

            <div class="col-md-6">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header bg-danger">
                        <h6>Diagrama de Contexto</h6>
                    </div>
                    <div class="card-body">
                        @if ($diagrama)
                            <div id="image-gallery" class="lightGallery">
                                <a href="{{ asset('storage/' . $diagrama->archivo) }}" data-lightbox="image-1">
                                    <img src="{{ asset('storage/' . $diagrama->archivo) }}" alt="Diagrama de Contexto"
                                        class="img-fluid" style="max-width: 100%; cursor: zoom-in;">
                                </a>
                            </div>
                        @else
                            <h3>Agregar Nuevo Diagrama de Contexto</h3>
                            <form action="{{ route('caracterizacion.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" class="form-control" id="proceso_id" name="proceso_id"
                                    value="{{ $proceso_id }}">
                                <input type="hidden" name="archivo">
                                <div class="form-group">
                                    <label for="archivo">Subir Diagrama</label>
                                    <input type="file" class="form-control" id="archivo" name="archivo">
                                </div>
                                <div class="form-group">
                                    <label for="version">Versión</label>
                                    <input type="text" class="form-control" id="version" name="version"
                                        placeholder="Ingrese la versión">
                                </div>
                                <div class="form-group">
                                    <label for="vigencia">Vigencia</label>
                                    <input type="date" class="form-control" id="vigencia" name="vigencia"
                                        placeholder="Ingrese la vigencia">
                                </div>
                                <button type="submit" class="btn btn-info">Agregar
                                    Diagrama</button>
                            </form>
                        @endif
                    </div>
                    <div class="card-footer mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Coloca Versión y Vigencia en una columna -->
                            @if ($diagrama)
                                <div class="d-flex flex-column">

                                    <small>Versión: {{ $diagrama->version }}</small>
                                    <small>Vigencia:
                                        {{ \Carbon\Carbon::parse($diagrama->vigencia)->format('d-m-Y') }}</small>

                                </div>

                                <!-- El botón se coloca a la derecha -->

                                <a href="#" class="btn bg-navy color-palette ms-auto" data-toggle="modal"
                                    data-target="#ActualizarDiagrama">Actualizar</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="card shadow-sm h-100 d-flex flex-column">
                    <!-- Card Header con un fondo diferente y texto centrado -->
                    <div class="card-header bg-navy text-white">
                        <h6>Datos del Proceso</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Proceso: {{ $proceso->cod_proceso }}</h6>
                            <p class="proceso">{{ $proceso->proceso_nombre }} - ({{ $proceso->proceso_sigla }}) </p>
                        </div>
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Tipo de Proceso:</h6>
                            <p class="proceso">{{ $proceso->proceso_tipo }}</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Objetivo del Proceso:</h6>
                            <p class="proceso text-justify">{{ $proceso->proceso_objetivo }}</p>
                        </div>
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Objetivo Estratégico:</h6>
                            @if ($proceso->planificacion_pei)
                                <p class="proceso text-justify">{{ $proceso->planificacion_pei->cod_objetivo }} -
                                    {{ $proceso->planificacion_pei->objetivo_nombre }}</p>
                            @endif
                        </div>
                        <div class="mb-3">
                            <h6 class="font-weight-bold">Dueño del Proceso:</h6>
                            <p class="proceso">Vicecontraloria de Control Sectorial y Territorial</p>
                        </div>
                        <!-- Aquí puedes agregar más definiciones del proceso -->
                    </div>

                    <div class="card-footer mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Coloca Versión y Vigencia en una columna -->
                            <div class="d-flex flex-column">
                                <small>Última actualización: {{ $proceso->updated_at}}</small>
                            </div>

                            <a href="#" class="btn bg-navy color-palette ms-auto" data-toggle="modal"
                                data-target="#ActualizarDiagrama">Actualizar</a>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Section SIPOC -->
    <div class="row d-flex align-items-stretch">
        <div class="col-md-12">
            <div class="card h-100 d-flex flex-column">
                <div class="card-header bg-navy">
                    <h6>SIPOC</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Proveedores</th>
                                <th>Entradas</th>
                                <th>Procesos</th>
                                <th>Salidas</th>
                                <th>Clientes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sipocs as $sipoc)
                                <tr>
                                    <td>{{ $sipoc->proveedores }}</td>
                                    <td>{{ $sipoc->entradas }}</td>
                                    <td>
                                        @foreach ($procesos as $subproceso)
                                            <a href="#" data-toggle="modal"
                                                data-target="#requisitosModal{{ $subproceso->id }}">
                                                {{ $subproceso->cod_proceso }} - {{ $subproceso->proceso_nombre }}
                                            </a><br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($salidas as $salida)
                                            <a href="#" class="load-requisitos" data-id="{{ $salida->id }}">
                                                {{ $salida->salida }} ( {{ $salida->tipo }})
                                            </a><br>
                                        @endforeach

                                    </td>
                                    <td>{{ $sipoc->clientes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No Se ha generado algún SIPOC para este proceso.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <!-- Section Documentacion -->
    <div class="row d-flex align-items-stretch">
        <div class="col-md-12">
            <div class="card h-100 d-flex flex-column">
                <div class="card-header bg-navy">
                    <h6>Documentación del Proceso</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Lista Maestra de documentos Internos</th>
                                <th>Lista Maestra de documentos Externos </th>

                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td class="col-md-6">
                                    <ul style="padding-left: 20px;">

                                        @foreach ($documentos as $documento)
                                            @if ($documento->fuente == 'interno')
                                                <li>
                                                    <a href="{{ $documento->enlace }}">
                                                        {{ $documento->cod_documento }} - {{ $documento->nombre }}<label  class="required-field" class="weight-normal">.</label>                                                
                                                         v {{ str_pad(optional($documento->ultimaVersion)->version ?? 0, 2, '0', STR_PAD_LEFT) }}
                                                      </a>
                                                </li>
                                            @endif
                                        @endforeach

                                    </ul>
                                </td>
                                <td>
                                    @foreach ($documentos as $documento)
                                        @if ($documento->fuente == 'externo')
                                            <a href="{{ $documento->enlace }}">{{ $documento->cod_documento }} -
                                                {{ $documento->nombre }} </a><br>
                                        @endif
                                    @endforeach
                                </td>

                            </tr>


                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>


    <!-- Modal para actualizar el diagrama -->
    <div class="modal fade" id="ActualizarDiagrama" tabindex="-1" role="dialog"
        aria-labelledby="verRiesgosModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="verRiesgosModalLabel">Actualizar Diagrama
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($diagrama)
                        <h3>Actualizar Diagrama de Contexto</h3>
                        <form action="{{ route('caracterizacion.update', $diagrama->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="archivo">Nuevo Diagrama</label>
                                <input type="file" class="form-control" id="archivo" name="archivo">
                            </div>
                            <div class="form-group">
                                <label for="version">Versión</label>
                                <input type="text" class="form-control" id="version" name="version"
                                    value="{{ $diagrama->version }}">
                            </div>
                            <div class="form-group">
                                <label for="vigencia">Vigencia</label>
                                <input type="date" class="form-control" id="vigencia" name="vigencia"
                                    value="{{ $diagrama->vigencia }}">
                            </div>
                            <button type="submit" class="btn btn-info">Actualizar
                                Diagrama</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Requisitos -->
    <div class="modal fade" id="requisitosModal" tabindex="-1" role="dialog" aria-labelledby="requisitosModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="requisitosModalLabel">Requisitos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Tarjeta para mostrar los requisitos -->


                    <!-- Tabla para mostrar los requisitos -->
                    <table class="table table-bordered table-hover table-sm table-requisitos" id="procesos">
                        <thead class="thead-light">
                            <tr>
                                <th>Id</th>
                                <th>Requisito</th>
                                <th class="text-nowrap">Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="modal-body">
                            <!-- Los requisitos se cargarán aquí -->
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            lightGallery(document.getElementById('image-gallery'), {
                selector: 'a',
                download: false,
                zoom: true // Desactivar la opción de descarga si no la necesitas
            });
        });

        $(document).ready(function() {
            // Cuando se haga clic en el enlace de salida
            $('.load-requisitos').on('click', function(e) {
                e.preventDefault();
                var salidaId = $(this).data('id'); // Obtenemos el id de la salida

                // Hacer una solicitud AJAX al controlador
                $.ajax({
                    url: '/salida/' + salidaId + '/requisitos', // La URL que llamaremos
                    method: 'GET',
                    success: function(data) {
                        // Comprobamos si hay requisitos en la respuesta
                        if (data.requisitos.length > 0) {
                            var requisitosHtml = ''; // Inicializamos el HTML para la tabla
                            var num = 1;
                            // Iteramos sobre los requisitos recibidos en el JSON
                            data.requisitos.forEach(function(requisito) {

                                requisitosHtml += '<tr>';
                                requisitosHtml += '<td>' +
                                    num++
                                    '</td>';
                                requisitosHtml += '<td class="text-justify">' +
                                    requisito.descripcion +
                                    '</td>';

                                requisitosHtml +=
                                    '<td class="text-nowrap" data-toggle="tooltip" data-placement="top" title="Documento: ' +
                                    requisito.documento + '">' + requisito.fecha_requisito +
                                    '</td>';
                                requisitosHtml += '</tr>';
                            });

                            // Cargamos los requisitos en la tabla
                            $('#modal-body').html(requisitosHtml);
                        } else {
                            $('#modal-body').html(
                                '<tr><td colspan="3">No hay requisitos disponibles.</td></tr>'
                            );
                        }
                        // Mostrar el modal
                        $('#requisitosModal').modal('show');
                    },
                    error: function() {
                        alert('Hubo un error al cargar los requisitos.');
                    }
                });
            });
        });
    </script>
@endpush

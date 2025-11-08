@extends('layout.master')
@section('title', 'SIG')
@push('styles')
    <style>
        .table-procesos {
            font-size: 13px;
        }

        /* Estilo general de opciones */
        .select2-container--bootstrap4 .select2-results__option {
            font-size: 12px !important;
        }

        /* Hover en opciones */
        .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
            background-color: #6c757d !important;
            /* grey */
            color: white !important;
        }

        /* Estilo de etiquetas seleccionadas */
        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            background-color: #dc3545 !important;
            /* danger */
            border-color: #dc3545 !important;
            color: white !important;
            font-size: 12px !important;
        }

        /* Estilo del botón de cerrar en etiquetas */
        .select2-container--bootstrap4 .select2-selection__choice__remove {
            color: white !important;
            font-size: 12px !important;
        }

        .select2-container--bootstrap4 .select2-selection__choice__remove:hover {
            color: #ffcccc !important;
        }

        /* Estilo del campo de búsqueda interno (placeholder y texto) */
        .select2-container--bootstrap4 .select2-selection--multiple .select2-search__field {
            font-size: 13px !important;
        }

        /* Para el contenedor de selección múltiple en Bootstrap 4 */
        .select2-container--bootstrap4 .select2-selection--multiple {
            min-height: calc(2.25rem + 2px) !important;
            /* Altura estándar de form-control en Bootstrap 4 */

            border: 1px solid #ced4da !important;
            border-radius: .25rem !important;
        }

        /* Para el focus/activo */
        .select2-container--bootstrap4.select2-container--focus .select2-selection--multiple {
            border-color: #0874e8 !important;
            /* Color de borde de focus de Bootstrap */
            box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25) !important;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Partes Interesadas</a></li>
            </ol>
        </nav>

        <div class= "card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title">Listado de Parte Interesadas</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                     {{-- Botón Modal --Agregar --}}
   
                        <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" id="addParteInteresadaButton"
                            onclick="Livewire.dispatchTo('interesado.parte-interesada-form','nuevoParteInteresada')"
                            data-target="#parteInteresadaModal">
                            <i class="fas fa-plus-circle"></i> Agregar
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if ($partes->isEmpty())
                    <p>No hay partes interesadas registradas.</p>
                @else
                    <table class="table table-bordered table-hover table-sm table-procesos" id="partesTable">
                        <thead class="table-header">
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th style="width: 50%">Descripción</th>
                                <th>Cuadrante</th>
                                <th class="text-center">Acciones </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($partes as $parte)
                                <tr>
                                    <td>{{ $parte->pi_nombre }}</td>
                                    <td>{{ ucfirst($parte->pi_tipo) }}</td>
                                    <td>{{ $parte->pi_descripcion }}</td>
                                    <td>{{ ucfirst($parte->valoracion) }}</td>
                                    <td class="text-center justify-content-center">
                                        <a href="#"  class="px-1" data-toggle="modal"
                                            onclick="Livewire.dispatchTo('interesado.expectativas-form','mostrarExpectativas', { id: {{ $parte->id }} })"
                                            data-target="#expectativasModal" title="Abrir Expectativas">
                                            <i class="fas fa-clipboard-list fa-lg"></i>
                                        </a>
                                   
                                        <a href="#" class="px-3" data-toggle="modal"
                                            onclick="Livewire.dispatchTo('interesado.parte-interesada-form','verParteInteresada', { id: {{ $parte->id }} })"
                                            data-target="#parteInteresadaModal" title="Editar">
                                            <i class="fas fa-pencil-alt text-dark"></i>
                                        </a>

                                        <form action="{{ route('partes.destroy', $parte->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE') {{-- Esto simula una petición DELETE --}}
                                            <button type="submit" class="btn btn-link px-1"
                                                onclick="return confirm('¿Estás seguro de eliminar esta parte interesada? Se marcará como eliminada, no se borrará permanentemente.')">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>


                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>



        </div>
        @livewire('interesado.expectativas-form', ['parteId' => null])
        @livewire('interesado.parte-interesada-form')
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            const $partesTable = $('#partesTable').DataTable({
                dom: "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Descargar',
                        className: 'btn btn-success btn-sm'
                    },

                ],

                "language": {
                    "search": "Buscar:",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "paginate": {
                        "first": "Primero", // Botón de la primera página
                        "last": "Último", // Botón de la última página
                        "next": "Siguiente", // Botón para la siguiente página
                        "previous": "Anterior" // Botón para la página anterior
                    },
                    "info": "",
                }


            });

        });

        function editarParteInteresada(parte) {

            // Rellenar campos
            $('#pi_id').val(parte.id);
            $('#pi_nombre').val(parte.pi_nombre);
            $('#pi_descripcion').val(parte.pi_descripcion);
            $('#pi_nivel_influencia').val(parte.pi_nivel_influencia);
            $('#pi_nivel_interes').val(parte.pi_nivel_interes);
            $('#pi_tipo').val(parte.pi_tipo);

            // Cambiar ruta y método
            let updateUrl = `{{ route('partes.update', ['id' => '__ID__']) }}`.replace('__ID__', parte.id);
            $('#formPartes').attr('action', updateUrl);
            $('#formMethod').val('PUT');

            // Cambiar botón a "Actualizar"
            $('#btnSubmit').html('<i class="fas fa-save mr-2"></i> Actualizar').removeClass('btn-primary').addClass(
                'btn-warning');

            // Mostrar botón "Nuevo"
            $('#btnNuevo').removeClass('d-none');

            // Abrir formulario si está cerrado

            $('#collapseFormPartes').collapse('show');

        }

        // Función para resetear el formulario
        $('#btnNuevo').on('click', function() {
            $('#formPartes').trigger('reset');
            $('#formPartes').attr('action', '{{ route('partes.store') }}');
            $('#formMethod').val('POST');
            $('#btnSubmit').html('<i class="fas fa-save mr-2"></i> Guardar').removeClass('btn-warning').addClass(
                'btn-primary');
            $('#btnNuevo').addClass('d-none');
        });
    </script>
@endpush

@extends('layout.master')
@section('css')
    <style type="text/css">
        /* Cambiar el color de fondo y el borde de los elementos seleccionados */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e74c3c !important;
            /* Cambia el color de fondo */
            border: 1px solid #e74c3c !important;
            /* Cambia el borde */
            color: white !important;
            /* Cambia el color del texto */
        }

        /* Cambiar el color del icono de eliminar (x) */
        .select2-selection__choice__remove {
            color: white !important;
            /* Cambia el color del icono de eliminar */
        }

        .select2-checkbox {
            margin-right: 10px;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Asociar OUOs al Proceso: {{ $proceso->nombre }}</h3>
                    </div>
                    <div class="card-body">

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <!-- Botón para agregar nueva OUO -->
                        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addOUOModal">
                            <i class="fas fa-plus"></i> Asociar Nueva OUO
                        </button>

                        <!-- Tabla de OUO asociadas -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Código OUO</th>
                                    <th>Nombre OUO</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ouos as $ouo)
                                    <tr>
                                        <td>{{ $ouo->codigo }}</td>
                                        <td>{{ $ouo->nombre }}</td>
                                        <td>
                                            <!-- Aquí puedes agregar botones para eliminar o editar la asociación si es necesario -->
                                            <form
                                                action="{{ route('procesos.disociarOUO', ['proceso_id' => $proceso->id, 'ouo_id' => $ouo->id]) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip"
                                                    title="Eliminar Asociación">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal para asociar OUOs -->
        <div class="modal fade" id="addOUOModal" tabindex="-1" role="dialog" aria-labelledby="addOUOModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAsociarOUOLabel">Asociar OUOs al Proceso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('procesos.asociarOUO', $proceso->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="ouos">Seleccionar OUOs:</label>
                                <!-- Select2 con búsqueda y opción múltiple -->
                                <select name="ouos[]" id="ouos" class="form-control" multiple="multiple">
                                    @foreach ($allouos as $ouo)
                                        <option value="{{ $ouo->id }}">{{ $ouo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Asociar OUOs</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#ouos').select2({
                placeholder: "Buscar OUOs...",
                allowClear: true,
                multiple: true,
                width: '100%', // Asegurar que ocupe el ancho completo del contenedor
                templateResult: formatOption
            });

            function formatOption(state) {
                if (!state.id) {
                    return state.text;
                }
                var $state = $('<span><input type="checkbox" class="select2-checkbox"> ' + state.text + '</span>');
                return $state;
            }
        });

        $(document).ready(function() {
        // Si hay una alerta con el mensaje de éxito, se cierra automáticamente después de 3 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 3000);
    });
    </script>
@endsection

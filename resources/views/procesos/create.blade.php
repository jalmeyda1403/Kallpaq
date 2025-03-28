@extends('layout.master')
@section('title', 'Crear Proceso')
@section('css')
    <style type="text/css">
        .card-footer small {
            font-size: 0.875rem;
            color: #888;
        }

        .card-footer {
            background-color: #f1f1f1;
            padding: 10px;
            text-align: center;
        }




        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 5px;
            padding: 5px;
            width: 100%;
        }

        textarea.form-control {
            resize: vertical;
        }

        textarea::placeholder {
            color: #aaa;
        }

        .btn {
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
        }

        .input-group .btn {
            width: auto;
            /* Hace que el botón tenga el tamaño del contenido (icono) */
            padding: 0.375rem 0.75rem;
            /* Ajusta el tamaño del botón */
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
@endsection
@section('content')

    <div class="container">
        <p>
        <div class="card">
            <div class="card-header bg-olive color-palette">
                <h3>Crear Proceso</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('procesos.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>
                            <h6>Código de Proceso</h6>
                        </label>
                        <input type="text" name="cod_proceso" id="cod_proceso" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>
                            <h6>Nombre del Proceso</h6>
                        </label>
                        <textarea name="nombre" id="nombre" class="form-control" rows="2" maxlength="400"
                        placeholder="Escribe el nombre del proceso en no más de 400 caracteres." required></textarea>
                        <small id="charCountNombre" class="form-text text-muted">0/400</small>
                    </div>
                    <div class="form-group">
                        <label>
                            <h6>Objetivo</h6>
                        </label>
                        <textarea name="objetivo" id="objetivo" class="form-control" rows="3" maxlength="1000"
                            placeholder="Describe el objetivo en no más de 1000 caracteres." required></textarea>
                        <small id="charCountObjetivo" class="form-text text-muted">0/1000</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <h6>Tipo</h6>
                                </label>
                                <select name="tipo_proceso" id="tipo_proceso" class="form-control" required>
                                    <option value="Misional">Misional</option>
                                    <option value="Estratégico">Estratégico</option>
                                    <option value="Apoyo">Apoyo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <h6>Nivel</h6>
                                </label>
                                <select name="nivel_proceso" id="nivel_proceso" class="form-control" required>
                                    @for ($i = 0; $i <= 3; $i++)
                                        @if ($i == 0)
                                            <option value="{{ $i }}">Macroproceso</option>
                                        @else
                                            <option value="{{ $i }}">Proceso nivel {{ $i }}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            <h6>Proceso</h6>
                        </label>
                        <!-- Campo oculto para el ID del proceso -->
                        <input type="hidden" name="cod_proceso_padre" id="cod_proceso_padre" required disabled>
                        <div class="input-group">
                            <!-- Campo de texto para el nombre del proceso -->
                            <input type="text" class="form-control" id="proceso_nombre" name="proceso_nombre" required readonly>
                            <!-- Botón para abrir el modal -->
                            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#procesoModal"><i class="fas fa-search"></i></a>
                        </div>
                    </div>
                    <x-modal-busqueda :ruta="route('procesos.buscar')" campo-id="cod_proceso_padre" campo-nombre="proceso_nombre"
                        modal-titulo="Proceso" modal-id="procesoModal">
                    </x-modal-busqueda>
                    <div class="form-group">
                        <label>
                            <h6>Estado</h6>
                        </label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('procesos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
            <div class="card-footer text-muted">
                <small>NOTA: En algunas asociaciones es obligatorio completar los campos.</small>
            </div>
        </div>

    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            // Obtener el campo de texto y el contador
            const objetivo = $('#objetivo');
            const nombre = $('#nombre');
            const charCountObjetivo = $('#charCountObjetivo');
            const charCountNombre = $('#charCountNombre');

             // Función para actualizar el contador de caracteres
             function updateCharCount(input, counter, maxLength) {
                input.on('input', function() {
                    const currentLength = input.val().length;
                    counter.text(`${currentLength}/${maxLength}`);
                });
            }

            updateCharCount(objetivo, charCountObjetivo, 1000);
            updateCharCount(nombre, charCountNombre, 400);
        });
    </script>
@endsection

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
                <li class="breadcrumb-item"> <a href="{{ route('smp.index', ['clasificacion' => $breadcrumb['codigo']]) }}">{{$breadcrumb['nombre']}}</a>
                <li class="breadcrumb-item active" aria-current="page">{{ $hallazgo->smp_cod }}</li>
            </ol>
        </nav>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-right bg-navy">
                        <div class="mr-auto">
                            <h5 class="card-title"> I. Solicitud de Mejora de Procesos (Modo Edición)</h5>
                        </div>
                        <div class="ml-auto">
                            <h5 class="card-title">{{ $hallazgo->smp_cod }}</h5>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('smp.update', $hallazgo->id) }}" id="myform">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <!-- Sección 1: Datos del Proceso -->
                            <div class="row">
                                <h5 class="text-left"><b>1.1. Identificación de la Mejora</b></h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Campo para el origen -->
                                    <div class="form-group">
                                        <label for="origen">Origen</label>
                                        <select class="form-control" id="origen" name="origen" required>
                                            @foreach (config('opciones.origen') as $clave => $valor)
                                                <option value="{{ $clave }}"
                                                {{ $hallazgo->origen === $clave ? 'selected' : '' }}>
                                                {{ $valor }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Campo para el ID del proceso -->
                                    <div class="form-group">
                                        <label for="proceso_id">Proceso</label>
                                        <div class="input-group">
                                            <input type="hidden" name="proceso_id" id="proceso_id" value="{{ $hallazgo->proceso_id }}" required>
                                            <input type="text" class="form-control" id="proceso_nombre"
                                                name="proceso_nombre" value="{{ $hallazgo->proceso->proceso_nombre }}" required readonly>
                                            <a href="#" class="btn btn-primary" data-toggle="modal"
                                                data-target="#procesoModal"><i class="fas fa-search"></i></a>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-6">
                                      <!-- Campo para la clasificación -->
                                      <div class="form-group">
                                        <label for="clasificacion">Clasificación </label>
                                        <select class="form-control" id="clasificacion" name="clasificacion" required>
                                            @foreach (config('opciones.clasificacion') as $clave => $valor)
                                            <option value="{{ $clave }}"
                                            {{ $hallazgo->clasificacion === $clave ? 'selected' : '' }}>
                                            {{ $valor }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                      <!-- Campo para Sistema de Gestión Impactado -->
                                      <div class="form-group">
                                        <label for="sig">Sistema de Gestión Impactado</label>
                                        <select class="form-control" id="sig" name="sig" required>
                                            @foreach (config('opciones.sig') as $clave => $valor)
                                            <option value="{{ $clave }}"
                                            {{ $hallazgo->sig === $clave ? 'selected' : '' }}>
                                            {{ $valor }}
                                        </option>
                                            @endforeach
                                        </select>
                                    </div>
                                   
                                    
                                   
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Campo para el Auditor_Tipo del informe -->
                                    <div class="form-group">
                                        <label for="auditor_tipo">Tipo de Auditor</label>
                                        <select class="form-control" id="auditor_tipo" name="auditor_tipo" required>
                                            @foreach (config('opciones.auditor_tipo') as $clave => $valor)
                                                <option value="{{ $clave }}"
                                                {{ $hallazgo->auditor_tipo === $clave ? 'selected' : '' }}>
                                                {{ $valor }}
                                            
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    
                                    <!-- Campo para el Auditor -->
                                    <div class="form-group">
                                        <label for="auditor">Auditor o colaborador que identificó la mejora</label>
                                        <input type="text" class="form-control" id="auditor" name="auditor" value="{{ $hallazgo->auditor }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <!-- Campo para el ID del informe -->
                                   <div class="form-group">
                                    <label for="informe_id">Informe</label>
                                    <textarea class="form-control" id="informe_id" name="informe_id" rows="1">{{ $hallazgo->informe_id }}</textarea>
                                </div>

                                </div>
                            </div>
                            <hr>
                            <!-- Sección 2: Detalle de la Mejora -->
                            <div class="row">
                                <h5 class="text-left"><b>1.2. Detalle de la Mejora</b></h5>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="descripcion">Resumen SMP (Breve y concisa)</label>
                                        <textarea class="form-control" id="resumen" name="resumen" rows="3" required>{{ $hallazgo->resumen }}</textarea>
                                    </div>
                                </div>
                            </div>                         
                        
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Campo para la descripción -->
                                    <div class="form-group">
                                        <label for="descripcion">Descripción /Problemática</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="10" required>{{ $hallazgo->descripcion }}</textarea>
                                    </div>
                                     <!-- Campo para el criterio -->
                                     <div class="form-group">
                                        <label for="criterio">Criterio (Referencia)</label>
                                        <textarea class="form-control" id="criterio" name="criterio" rows="3">{{ $hallazgo->criterio }}</textarea>
                                    </div>
                                  
                                </div>
                                <div class="col-md-6">
                                    <!-- Campo para la evidencia -->
                                    <div class="form-group">
                                        <label for="evidencia">Evidencia</label>
                                        <textarea class="form-control" id="evidencia" name="evidencia" rows="10" required>{{ $hallazgo->evidencia }}</textarea>
                                    </div>
                                    <!-- Campo para la fecha de solicitud -->
                                    <div class="form-group">
                                        <label for="fecha_solicitud">Fecha de la solicitud</label>
                                        <input type="date" class="form-control" id="fecha_solicitud"
                                            name="fecha_solicitud" value="{{ $hallazgo->fecha_solicitud }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Actualizar') }}
                            </button>
                            <a href="{{route('smp.index', ['clasificacion' => $breadcrumb['codigo']]) }}" class="btn btn-secondary">
                                {{ __('Cancelar') }}
                            </a>

                        </div>
                    </form>
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
    </div>
@stop
@section('js')
    <script>
        var procesos = [];
        var objetivos = [];

        $(document).ready(function() {
            loadProcesos();


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

        });

        function loadProcesos() {
            $.ajax({
                url: '/listarprocesos', // Reemplaza por la URL correcta para obtener los procesos
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
        document.getElementById('myform').addEventListener('submit', function(event) {
            var procesoNombre = document.getElementById('proceso_nombre').value.trim();
            if (procesoNombre === '') {
                alert('Por favor, seleccione un proceso.');
                event.preventDefault(); // Evita que el formulario se envíe si el campo está vacío
            }
        });


        // Al hacer clic en "Guardar Cambios" en el modal de Proceso
    </script>
@stop

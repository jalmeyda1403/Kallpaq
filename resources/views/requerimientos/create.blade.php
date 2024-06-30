@extends('facilitador.layout.master')
@section('title', 'SIG')
@section('css')
<style>
    .list-group-item.active {
        background-color: #007bff;
        color: #fff;
    }
</style>
@stop
@section('content')
<div class="container-fluid">
        
  
    @if(session('success'))
    <div class="alert alert-success" id="success-alert">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger" id="error-alert">
    {{ session('error') }}
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <br>
            <div class="card">
                <div class="card-header">
                    
                    <h3 class="card-title">{{ __('Crear Requerimiento') }}</h3>
                
                </div>

                <div class="card-body">
                 <!-- Paso 1 -->
                 <div id="step1" class="step">
                    <h4>Paso 1: Generar Requerimiento</h4>
                    <form id="frmstep1" method="POST" action="{{ route('requerimientos.store') }}">
                        @csrf

                        <div class="form-group">
                            <div class="form-group">
                                <label for="proceso">Proceso</label>
                                <div class="input-group">
                                    <input type="hidden" name="proceso_id" id="proceso_id" >
                                    <input type="text" class="form-control" id="proceso_nombre" name="proceso_nombre" required readonly>
                                    <a href="#" class="btn btn-secondary" data-toggle="modal" data-target="#procesoModal"><i class="fas fa-search" ></i></a>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                        <div class="form-group">
                            <label for="justificacion">Justificación</label>
                            <textarea class="form-control" id="justificacion" name="justificacion" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="prioridad">Prioridad</label>
                            <select class="form-control" id="prioridad" name="prioridad" required>
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="muy alta">Muy Alta</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="complejidad">Complejidad</label>
                            <select class="form-control" id="complejidad" name="complejidad" required>
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="muy alta">Muy Alta</option>
                            </select>
                        </div>

                        <!-- Resto de los campos -->
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" onclick="nextStep()" class="btn btn-primary">Siguiente</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                                <a href="{{ route('requerimientos.index') }}" class="btn btn-secondary">
                                    {{ __('Cancelar') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="step2" class="step" style="display: none;">
                    <h4>Paso 2: Agregar Necesidad</h4>
                    <form id="frmstep2" action="" method="POST" id="formStep2">
                        @csrf
                        <!-- Campos para agregar la necesidad -->
                    <div class="form-group">
                        <label for="tipo_documento_id">Tipo de Documento</label>
                        <select name="tipo_documento_id" id="tipo_documento_id" class="form-control">
                            <option value="">Seleccionar Tipo</option>
                            <option value="1">Plan</option>
                            <option value="2">Guia</option>
                            <option value="3">Manual</option>
                            <option value="4">Procedimiento</option>
                            <option value="5">Otros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado" class="form-control" >
                            <option value="">Seleccionar Estado</option>
                            <option value="crear">Crear</option>
                            <option value="actualizar">Actualizar</option>
                            <option value="eliminar">Eliminar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nombre_documento">Nombre del Documento</label>
                        <input type="text" name="nombre_documento" id="nombre_documento" class="form-control" >
                    </div>
                    <!-- Botones de navegación -->
                    <button type="button" onclick="prevStep()" class="btn btn-secondary">Anterior</button>
                    <button type="button" onclick="agregarNecesidad()" class="btn btn-primary">[+] Necesidad</button>
                    <button type="button" onclick="generarRequerimiento()" class="btn btn-primary">Generar</button>
                    </form>
                    <!-- Grilla de necesidades -->
                   
                    <h6>Lista de Necesidades</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Id Td</th>
                                    <th>Tipo documento</th>
                                    <th>Estado</th>
                                    <th>Nombre documento</th>
                                </tr>
                            </thead>
                            <tbody id="necesidades">
                                <!-- Aquí se agregarán las filas de la tabla mediante JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
             </div> 
            </div>
        </div>
        <div class="col-md-4">
        <br>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pasos</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item active">Paso 1: Generar Requerimiento</li>
                        <li class="list-group-item">Paso 2: Agregar Necesidad</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para seleccionar Proceso -->
    <div class="modal fade" id="procesoModal" tabindex="-1" role="dialog" aria-labelledby="procesoModalLabel" aria-hidden="true">
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
   
    $(document).ready(function () {
        // Al hacer clic en "Guardar Cambios" en el modal de Proceso
        $('#procesoModal .btn-primary').click(function () {
            var selectedProceso = // Obtén el valor seleccionado del modal
            $('#proceso_id').val(selectedProceso);
            $('#procesoModal').modal('hide');
        });

       
    });

        $(document).ready(function () {
            loadProcesos();

            $('#procesoSearch').on('input', function () {
                var inputText = $(this).val().toLowerCase();
                var filteredProcesos = procesos.filter(function (proceso) {
                    return proceso.nombre.toLowerCase().includes(inputText);
                });
                displayProcesos(filteredProcesos);
            });

            $('#procesoResults').on('click', 'input[type=radio]', function () {
                var id = $(this).data('id');
                var nombre = $(this).data('nombre');
                console.log(id);
                $('#proceso_id').val(id);
                $('#proceso_nombre').val(nombre);
                $('#procesoModal').modal('hide');
            });
        });
      
        // Al hacer clic en "Guardar Cambios" en el modal de Proceso
        
        function loadProcesos() {
            $.ajax({
                url: '/buscarprocesos', // Reemplaza por la URL correcta para obtener los procesos
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    procesos = data;
                    displayProcesos(procesos);
                },
                error: function (error) {
                    console.log('Error al cargar los procesos:', error);
                }
            });
        }
        function displayProcesos(filteredProcesos) {
            var $procesoResults = $('#procesoResults');
            $procesoResults.empty();

            filteredProcesos.forEach(function (proceso) {
                var html = '<div class="form-check">';
                html += '<input class="form-check-input" type="radio" name="proceso_id" data-id="' + proceso.id + '" data-nombre="' + proceso.nombre + '">';
                html += '<label class="form-check-label" for="' + proceso.cod_proceso + '">' + proceso.cod_proceso + ' - ' + proceso.nombre + '</label>';
                html += '</div>';
                $procesoResults.append(html);
            });

        }
    
            function nextStep() {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            document.querySelector('.list-group-item.active').classList.remove('active');
            document.querySelectorAll('.list-group-item')[1].classList.add('active');
                }

            function prevStep() {
                document.getElementById('step2').style.display = 'none';
                document.getElementById('step1').style.display = 'block';
                document.querySelector('.list-group-item.active').classList.remove('active');
                document.querySelectorAll('.list-group-item')[0].classList.add('active');
            }

            // Validación para asegurarse de que al menos una necesidad se ha agregado antes de enviar el formulario
           

            // Función para agregar una necesidad a la grilla
            function agregarNecesidad() {
                // Obtener los valores de los campos del formulario
                var tipoDocumento = document.getElementById('tipo_documento_id');
                // Obtener el índice del tipo de documento seleccionado                
                var tipoDocumento_id = tipoDocumento.value;

                console.log(tipoDocumento.selectedIndex);
                var tipoDocumento_nombre = tipoDocumento.options[tipoDocumento.selectedIndex].text;
                var estado = document.getElementById('estado').value;
                var nombreDocumento = document.getElementById('nombre_documento').value;

                // Verificar si se han ingresado todos los campos
                if (tipoDocumento_id && estado && nombreDocumento) {
                    // Crear una nueva fila para la tabla
                    var newRow = "<tr>" +
                                    "<td>" + tipoDocumento_id + "</td>" +
                                    "<td>" + tipoDocumento_nombre + "</td>" +
                                    "<td>" + estado + "</td>" +
                                    "<td>" + nombreDocumento + "</td>" +
                                "</tr>";

                    // Agregar la nueva fila a la tabla
                    document.getElementById("necesidades").innerHTML += newRow;

                    // Limpiar los campos del formulario después de agregar la necesidad
                    document.getElementById('tipo_documento_id').value = '';
                    document.getElementById('estado').value = '';
                    document.getElementById('nombre_documento').value = '';
                } else {
                    // Mostrar un mensaje de error si no se han ingresado todos los campos
                    alert("Por favor ingrese todos los campos.");
                }
            }

            //Genera Requerimiento.

            function generarRequerimiento() {
                    // Obtener los datos del formulario del paso 1
                    /*
                    var formDataStep1 = new FormData(document.getElementById('frmstep1'));
                    var requerimientoId = "";

                    // Enviar los datos del primer formulario al controlador
                    fetch('/requerimientos', {
                        method: 'POST',
                        body: formDataStep1
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Verificar si la respuesta contiene el requerimiento_id
                        if ('requerimiento_id' in data) {
                            // Obtener el ID del requerimiento creado
                            var requerimientoId = data.requerimiento_id;
                            
                            }})

                            */
                  generarNecesidades(1);  
             }
                        
                        

            //Generar Necesidades.

            function generarNecesidades(requerimientoId) {
                    // Crear un array para almacenar los datos de la tabla
                    var necesidadesData = [];

                    // Obtener todas las filas de la tabla
                    var tableRows = document.querySelectorAll("#necesidades tr");

                    // Iterar sobre cada fila de la tabla
                    tableRows.forEach(function(row) {
                        // Obtener los datos de cada celda de la fila
                        var tipoDocumentoId = row.cells[0].textContent;
                        var estado = row.cells[2].textContent;
                        var nombreDocumento = row.cells[3].textContent;

                        // Crear un objeto con los datos de la fila y agregarlo al array
                        var necesidad = {
                            requerimiento_id: requerimientoId,
                            tipo_documento_id: tipoDocumentoId,
                            estado: estado,
                            nombre_documento: nombreDocumento
                        };
                        necesidadesData.push(necesidad);
                    });

                  // Enviar los datos al controlador NecesidadController
                  $.ajax({
                            url: '/necesidades',
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            contentType: 'application/json', // Establecer el tipo de contenido como JSON
                            data: JSON.stringify({ necesidades: necesidadesData }), // Enviar los datos como un objeto JSON
                            success: function(response) {
                                // Mostrar el mensaje de éxito en la vista
                                if (response.success) {
                                   
                                    setTimeout(function() {
                                        window.location.href = '{{ route("requerimientos.index") }}';
                                    }, 200)
                                }
                            },
                            error: function(xhr, status, error) {
                                // Manejar errores, si es necesario
                            }
                        });
                                 
                }       
            

</script>
@stop
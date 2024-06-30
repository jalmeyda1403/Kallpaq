@extends('facilitador.layout.master')
@section('title', 'SIG')
@section('css')
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

    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title">Listado de Requerimientos</h3>
                <div class="card-tools">
                 <a href="{{ route('requerimientos.create') }}"  class="btn btn-primary" data-toggle="tooltip" title="Nuevo Requerimiento">Nuevo Requerimiento</a>
                </div>
            </div>
            <div class="card-body">
                <table id="requerimientos" class="table table-striped table-bordered" style="width:100%">
                             <thead>
                                <tr>
                                    <th>ID</th>
                                    <th >Proceso</th>
                                    <th>Complejidad</th>
                                    <th>Estado</th>
                                    <th>Fecha Asignación</th>
                                    <th>Fecha Límite</th>
                                    <th style="width: 15%"></th>
                               
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requerimientos as $requerimiento)
                                <tr>
                                    <td>{{ $requerimiento->id }}</td>
                                    <td>{{ $requerimiento->proceso->nombre }}</td>
                                    <td>{{ $requerimiento->complejidad }}</td>
                                    <td>{{ $requerimiento->estado }}</td>
                                    <td>{{ $requerimiento->fecha_creacion }}</td>
                                    <td>{{ $requerimiento->fecha_limite }}</td>
                                    <td >
                                        <a href="#" class="btn btn-info btn-sm" title="Ver Trazabilidad" onclick="mostrarTrazabilidadModal({{ $requerimiento->id }})" data-toggle="modal" >
                                            <i class="fas fa-list"></i> </a>
                                            <a href="{{ route('requerimientos.show', $requerimiento->id) }}" class="btn btn-warning btn-sm" title="Ver Requerimiento">
                                                <i class="fas fa-pencil-alt"></i></a>
                                            <a href="{{ route('requerimientos.show', $requerimiento->id) }}" class="btn btn-dark btn-sm" title="Ver Requerimiento">
                                                <i class="fas fa-check"></i></a>
                                            <a href="{{ route('requerimientos.show', $requerimiento->id) }}" class="btn btn-primary btn-sm" title="Ver Requerimiento">
                                                <i class="fas fa-arrow-right"></i><i class="fas fa-user"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="trazabilidadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Trazabilidad del Requerimiento</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Aquí se mostrará la trazabilidad del requerimiento -->
          <div id="trazabilidadContenido"></div>
        </div>
      </div>
    </div>
  </div>


@stop
@section('js')

    <script>
        setTimeout(function() {
        $('#success-alert').fadeOut('slow');
    }, 2000); 

    $('#requerimientos').DataTable();

    function mostrarTrazabilidadModal(requerimientoId) {
    // Realizar una solicitud AJAX para obtener la trazabilidad del requerimiento
    $.ajax({
    url: '/requerimientos/' + requerimientoId + '/trazabilidad',
    type: 'GET',
    success: function(response) {
        console.log(response);
        // Procesar los datos recibidos en la respuesta
        var  trazabilidadHtml='<div class="container padding-bottom-3x mb-1">';
            trazabilidadHtml += '<div class="card mb-3">';

            trazabilidadHtml += '<div class="card-header bg-primary">';
            trazabilidadHtml += '<div class="row">';
            trazabilidadHtml += '<div class="col-8 text-left">';
            
            var requerimientoid = response.requerimiento.id +'';
            trazabilidadHtml += '<span class="text-small">Requerimiento n.°  '+ requerimientoid.padStart(4, '0') +'</span><p>';
            trazabilidadHtml += '</div>'; 
            trazabilidadHtml += '<div class="col-4 text-left">';
            trazabilidadHtml += '<span class="text-small"> Complejidad :' + response.requerimiento.complejidad + '</span>';
            trazabilidadHtml += '</div>'; 
            trazabilidadHtml += '</div>';
            trazabilidadHtml += '</div>';
            trazabilidadHtml += '</div>';
            trazabilidadHtml += '<div class="card-body">';
            trazabilidadHtml += '<div class="steps d-flex flex-wrap flex-sm-nowrap justify-content-between padding-top-2x padding-bottom-1x">';
            
        // Asumiendo que response.pasos contiene los datos de los pasos de la trazabilidad
        response.pasos.forEach(function(paso) {
            trazabilidadHtml += '<div class="step ' + (paso.completado ? 'completed' : '') + '">';
            trazabilidadHtml += '<div class="step-icon-wrap">';
            trazabilidadHtml += '<div class="step-icon">'+ paso.icono + '</div> </div>';  
            var fecha = paso.fecha;
            // Formatear la fecha usando moment.js
            var fechaFormateada = moment(fecha).format('DD/MM/YYYY HH:mm:ss');           
            trazabilidadHtml += '<h4 class="step-title">' + (paso.titulo ? paso.titulo : '') + '<p>'+ (paso.fecha ? fechaFormateada : '')+'</h4>';

            trazabilidadHtml += '</div>';
        });
        trazabilidadHtml += '</div>';
        trazabilidadHtml += '</div>';
        trazabilidadHtml += '</div>';
        trazabilidadHtml += '</div>';

        // Asignar el HTML generado al cuerpo del modal
        $('#trazabilidadModal .modal-content').html(trazabilidadHtml);

        // Mostrar el modal
        $('#trazabilidadModal').modal('show');
    },
    error: function(xhr, status, error) {
        // Manejar errores, si es necesario
    }
    });
    
}
var styleElement = document.createElement('style');
var cssContent = `body{margin-top:20px;}
.steps .step {
    display: block;
    width: 100%;
    margin-bottom: 35px;
    text-align: center
}

.steps .step .step-icon-wrap {
    display: block;
    position: relative;
    width: 100%;
    height: 100px;
    text-align: center
}

.steps .step .step-icon-wrap::before,
.steps .step .step-icon-wrap::after {
    display: block;
    position: absolute;
    top: 50%;
    width: 50%;
    height: 3px;
    margin-top: -1px;
    background-color: #e1e7ec;
    content: '';
    z-index: 1
}

.steps .step .step-icon-wrap::before {
    left: 0
}

.steps .step .step-icon-wrap::after {
    right: 0
}

.steps .step .step-icon {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
    border: 1px solid #e1e7ec;
    border-radius: 50%;
    background-color: #f5f5f5;
    color: #374250;
    font-size: 38px;
    line-height: 81px;
    z-index: 5
}

.steps .step .step-title {
    margin-top: 16px;
    margin-bottom: 0;
    color: #606975;
    font-size: 12px;
    font-weight: 500
}

.steps .step:first-child .step-icon-wrap::before {
    display: none
}

.steps .step:last-child .step-icon-wrap::after {
    display: none
}

.steps .step.completed .step-icon-wrap::before,
.steps .step.completed .step-icon-wrap::after {
    background-color: #0da9ef
}

.steps .step.completed .step-icon {
    border-color: #0da9ef;
    background-color: #0da9ef;
    color: #fff
}

@media (max-width: 576px) {
    .flex-sm-nowrap .step .step-icon-wrap::before,
    .flex-sm-nowrap .step .step-icon-wrap::after {
        display: none
    }
}

@media (max-width: 768px) {
    .flex-md-nowrap .step .step-icon-wrap::before,
    .flex-md-nowrap .step .step-icon-wrap::after {
        display: none
    }
}

@media (max-width: 991px) {
    .flex-lg-nowrap .step .step-icon-wrap::before,
    .flex-lg-nowrap .step .step-icon-wrap::after {
        display: none
    }
}

@media (max-width: 1200px) {
    .flex-xl-nowrap .step .step-icon-wrap::before,
    .flex-xl-nowrap .step .step-icon-wrap::after {
        display: none
    }
}

.bg-faded, .bg-secondary {
    background-color: #f5f5f5 !important;
}

.container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl {
   
    padding-right: 0px;
    padding-left: 0px;
    
}

`;

styleElement.innerHTML = cssContent;
document.head.appendChild(styleElement);

</script>;
@stop


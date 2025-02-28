<html>

<head>
    <style>
        /* Estilo general para el texto */
        .row h5 {
            font-size: 10pt;
        }

        .row {
            margin-bottom: 0;
            margin-top: 0;

            /* Reduce el margen inferior de la fila */
        }

        body,
        .form-control,
        .input-group-text,
        .custom-select,
        .table,
        .h5,
        .label,
        .card-header {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            padding: 5px;
            /* Tamaño de fuente ajustado a 10pt */
        }

        .card-header {
            background-color: #000000;
            color: #ffffff;

        }

        .card-header h5 {
            margin: 0;
            /* Eliminar márgenes para centrar el texto */
        }


        /* Campos de formulario */
        .form-control,
        .input-group-text,
        .custom-select {
            border: 1px solid #ced4da;
            /* Bordes estándar */
            border-radius: 4px;
            /* Bordes redondeados */
            padding: .375rem .75rem;
            /* Espaciado interno */
            color: #495057;
            /* Color del texto */
            background-color: #ffffff;
            /* Fondo blanco */
            margin-left: 15px;
            margin-right: 15px;
        }

        .form-control[readonly],
        .form-control[disabled] {
            background-color: #e9ecef;
            /* Fondo gris claro para campos de solo lectura */
            color: #212223;
            /* Texto gris */
            font-size: 10pt;
        }

        /* Estilo para las tablas */
        /* Estilo para las tablas */
        .table {
            width: 670px;
            margin-left: 15px;
            margin-right: 15px;
            margin-bottom: 0px;
            background-color: transparent;
            border-collapse: collapse;
            table-layout: fixed;
           
            /* Bordes colapsados para bordes simples */
        }

        table td label {
            margin-left: 15px;
        }

        

        .table th,
        .table td {
            padding: 8px;
            vertical-align:middle;
            border: 1px solid #313131;
            font-size: 8pt;
             
           
            /* Bordes simples y suaves */

        }          
       
        table {
            table-layout: fixed;
            width: 100%;
            border-collapse: collapse;
        }
        .table-identificacion tr td{
            font-size: 10pt;
        } 
        
        .table-acciones{
            table-layout: fixed;
        }

        .table-acciones th:nth-child(1), 
        .table-acciones td:nth-child(1)  {
            width: 40%;           
        }

        .logo {
            width: 4cm;
            /* Ancho de la imagen ajustado a 4cm */
            height: auto;
            /* Ajuste automático de la altura para mantener la proporción */
        }

        @page {
            margin: 100px 15x;
            counter-increment: page;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
            line-height: 30px;
        }

        footer {
            position: fixed;
            bottom: -70px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: right;
            font-size: 10px;
            line-height: 35px;
        }
        label{
            font-size: 10pt;
            font-weight: bold;
        }

        body {
            margin-top: 60px;
            /* Añadir un margen superior para el contenido principal */
        }
    </style>
</head>

<body>
    <header>
        <table class="table table-striped">
            <tr>
                <td style="width: 25%; text-align:center">
                    <img src="{{ $logoPath }}" class="logo" alt="Logo">
                </td>

                <td style="width: 55%;  text-align:center">
                    <h3>FORMATO SOLICITUD DE MEJORA DE PROCESOS<br>{{ $hallazgo->smp_cod }}</h3>
                </td>

                <td style="width: 20%;  text-align:center">
                    <h5>
                        <span class="page-number"></span>
                    </h5>
                </td>
            </tr>
        </table>

    </header>
    <footer>
        <div style="text-align: right">F02(PR-MODER-09) </div>
    </footer>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-right bg-navy">
                    <h5 class="card-title"> I. Solicitud de Mejora de Procesos </h5>
                </div>
                <div class="card-body">
                    <!-- Sección 1: Datos del Proceso -->
                    <h5 class="text-left"><b>1.1. Identificación de la Mejora</h5>
                    <table class="table table-identificacion">
                        <tr>
                            <td>
                                Origen
                            </td>
                            <td>
                                {{ $hallazgo->origen }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Proceso
                            </td>
                            <td>
                                {{ $hallazgo->proceso->proceso_nombre }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Clasificacion
                            </td>
                            <td>
                                {{ $hallazgo->clasificacion }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Sistema de Gestión Impactado
                            </td>
                            <td>
                                {{ $hallazgo->sig }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Auditor o colaborador que identificó la mejora
                            </td>
                            <td>
                                {{ $hallazgo->auditor }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Informe
                            </td>
                            <td>
                                {{ $hallazgo->informe_id }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Fecha de la solicitud
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($hallazgo->fecha_solicitud)->format('d/m/Y') }}
                            </td>
                        </tr>
                    </table>
                    <!-- Sección 2: Detalle de la Mejora -->
                    <h5 class="text-left"><b>1.2. Detalle de la Mejora</h5>
                    <table>
                        <tr>
                            <td>
                                <b><label>{{ $hallazgo->resumen }}</label></b><p>
                              
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Descripción /Problemática</label>
                                <p>
                                    <textarea class="form-control" id="descripcion" name="descripcion" style="height: auto; text-align: justify"readonly>{{ $hallazgo->descripcion }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Criterio (Referencia)</label>
                                <p>
                                    <textarea class="form-control" id="criterio" name="criterio" style="height: auto;" readonly>{{ $hallazgo->criterio }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>

                                <label>Evidencia</label>
                                <p>
                                    <textarea class="form-control" id="evidencia" name="evidencia" rows="5" style="height: auto; text-align: justify;" readonly>{{ $hallazgo->evidencia }}</textarea>
                            </td>

                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <p>

        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-right bg-navy">
                    <h5 class="card-title"> II. Acciones Correctivas</h5>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @if ($correctivas > 0)
                    <table class="table table-acciones">
                        <thead>
                            <tr>
                                <th>Acción</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Fin</th>
                                <th>Responsable</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($planesAccion as $plan)
                                <tr>
                                    <td>{{ $plan->accion }}</td>
                                    <td>  {{ \Carbon\Carbon::parse($plan->fecha_inicio)->format('d-m-Y') }}</td>
                                    <td>  {{ \Carbon\Carbon::parse($plan->fecha_fin)->format('d-m-Y') }}</td>
                                    <td>{{ $plan->responsable_id }}</td>
                                    <td>{{ $plan->estado }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    No se ha registrado acciones correctivas.
                    @endif

                </div>
            </div>
        </div>
        <p>
        <div class="col-md-10">
            @if ($hallazgo->clasificacion === 'NCM' || $hallazgo->clasificacion === 'Ncme')
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-right bg-navy">
                        <div class="mr-auto">
                            <h5 class="card-title"> III. Identificación de la causa raíz y plan de acción</h5>
                        </div>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <h5>3.1 Análisis de la Causa Raíz</h5>
                        @if ($hallazgo->causa?->metodo)
                            <table class="table table-striped">
                                <tr>
                                    <td><b>Método :</b> {{ $hallazgo->causa->metodo }}</td>
                                </tr>
                                @if ($hallazgo->causa->metodo === 'cinco_porques')
                                    <tr>
                                        <td>
                                            Porque 1:
                                            {{ $hallazgo->causa->por_que_1 }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Porque 2:
                                            {{ $hallazgo->causa->por_que_2 }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Porque 3:
                                            {{ $hallazgo->causa->por_que_3 }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Porque 4:
                                            {{ $hallazgo->causa->por_que_4 }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Porque 5:
                                            {{ $hallazgo->causa->por_que_5 }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>
                                            Mano de Obra:
                                            {{ $hallazgo->causa->mano_obra }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Maquinas:
                                            {{ $hallazgo->causa->maquinas }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Materiales:
                                            {{ $hallazgo->causa->materiales }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Medición:
                                            {{ $hallazgo->causa->medicion }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Metodologías:
                                            {{ $hallazgo->causa->metodologias }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Medio Ambiente:
                                            {{ $hallazgo->causa->medio_ambiente }}
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td><b>Resultado: </b>{{ $hallazgo->causa->resultado }}</td>
                                </tr>

                            </table>
                            @else
                           <label>No se ha realizado análisis de causa.</label>
                        @endif
                        <p>
                        <h5><b>3.2 Plan de Acción</b></h5>
                        @if ($preventivas > 0)
                        <table class="table table-acciones">
                            <thead>
                                <tr>
                                    <th >Acción</th>
                                    <th>Fecha de Inicio</th>
                                    <th>Fecha de Fin</th>
                                    <th>Responsable</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($planesAccion as $plan)
                                    <tr>
                                        <td>{{ $plan->accion }}</td>
                                        <td>  {{ \Carbon\Carbon::parse($plan->fecha_inicio)->format('d-m-Y') }}</td>
                                        <td>  {{ \Carbon\Carbon::parse($plan->fecha_fin)->format('d-m-Y') }}</td>
                                        <td>{{ $plan->responsable_id }}</td>
                                        <td>{{ $plan->estado }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        No se ha registrado planes de acción.
                        @endif

                    </div>
                </div>
            @endif
        </div>
        <p>
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5>IV. Firma del Propietario </h5>
                </div>
                <div class="card-body ">
                    <table >
                        <tr class="signature-row">
                            <td style="height: 100px; border: 1px solid rgba(15, 15, 15, 0.499);"></td>
                        </tr>

                    </table>
                </div>
            </div>

        </div>
</body>

</html>

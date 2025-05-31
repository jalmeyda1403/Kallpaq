@extends('layout.master')
@section('title', 'SIG')

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success" id="success-alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" id="error-alert">
                {{ session('error') }}
            </div>
        @endif
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('procesos.index') }}">Procesos</a></li>
            </ol>
        </nav>
        <div class="card">
            @php
                $tipoNormalizado = strtolower(
                    str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $proceso_padre->proceso_tipo),
                );
                $bgClass = 'bg-' . $tipoNormalizado;
            @endphp
            <div class="card-header {{ $bgClass }}">

                <h5 style="color: white">{{ $proceso_padre->cod_proceso }} {{ $proceso_padre->proceso_nombre }}</h5>
                <h7 style="color: white">Nivel: {{ $proceso_padre->proceso_nivel + 1 }}</h7>

            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    @foreach ($procesos as $proceso)
                        <div class="col-lg-2 col-md-2 col-sm-4 col-6" style="margin-top: 10px;">
                            <div class="small-box bg-proceso">
                                <div class="inner" style="text-align: left ">
                                    <h7>{{ $proceso->cod_proceso }} <br> {{ $proceso->proceso_nombre }}</h7>
                                </div>
                                <div class="small-box-footer">

                                    <a href="{{ route('procesos.caracterizacion', $proceso->id) }}"
                                        style="color: white; text-align: left; padding: 0 10px;">
                                        <i class="fas fa-file-alt"></i>
                                    </a>

                                    <a href="{{ route('procesos.nivel', $proceso->id) }}"
                                        style="color: white; text-align: right; padding: 0 10px 0 10px;">
                                        <i class="fas fa-arrow-circle-right"></i>
                                    </a>

                                </div>

                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="row justify-content-center" style="margin-top: 50px">
                    <table class="table table-bordered table-hover table-nivel">
                        <thead class="table-header">
                            <tr>
                                <th rowspan="2" style="vertical-align: middle">Procesos Nivel 1</th>
                                <th colspan="5" class="text-center">Sistema de Gestión</th>
                                <th colspan="4" class="text-center">Componentes</th>
                                <th colspan="2" class="text-center">Requerimientos</th>
                            </tr>
                            <tr>
                                <th>SGC</th>
                                <th>SGAS</th>
                                <th>SGCM</th>
                                <th>SGSI</th>
                                <th>SGCE</th>
                                <th class="text-nowrap"><i class="fas fa-file-alt"></i> Documentos</th>
                                <th class="text-nowrap"><i class="fas fa-clipboard-check"></i> Obligaciones</th>
                                <th class="text-nowrap"><i class="fas fa-exclamation-triangle"></i> Riesgos</th>
                                <th class="text-nowrap"><i class="fas fa-times-circle"></i> No Conformidades</th>
                                <th>Atendidos</th>
                                <th>Pendientes</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($procesos as $proceso)
                                <tr>
                                    <td class="text-left">{{ $proceso->cod_proceso }}-{{ $proceso->proceso_nombre }}</td>
                                    <td>{{ $proceso->sgc == 1 ? 'x' : '' }} </td>
                                    <td>{{ $proceso->sgas== 1 ? 'x' : ''}}</td>
                                    <td>{{ $proceso->sgcm== 1 ? 'x' : ''}}</td>
                                    <td>{{ $proceso->sgsi== 1 ? 'x' : ''}}</td>
                                    <td>{{ $proceso->sgce== 1 ? 'x' : ''}}</td>                                  
                                    <td>{{ $proceso->documentos->count() }}</td>
                                    <td>{{ $proceso->obligaciones->count() }}</td>
                                    <td>{{ $proceso->riesgos->count() }}</td>
                                    <td>{{ $proceso->hallazgos->count() }}</td>
                                    <td>ND</td>
                                    <td>ND</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.getElementById('success-alert');
            const errorAlert = document.getElementById('error-alert');

            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 2000); //  milisegundos = 1 segundo
            }
            
            if (errorAlert) {
                setTimeout(function() {
                    errorAlert.style.display = 'none';
                }, 1500); //  milisegundos = 1 segundo
            }
        });

    </script>
@endpush

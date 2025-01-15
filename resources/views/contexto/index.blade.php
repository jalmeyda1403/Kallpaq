@extends('facilitador.layout.master')
@section('title', 'Kallpaq')

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"> <a href="#">#</a>
                </li>
            </ol>
        </nav>
        <div id="successMessage"></div>

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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Contextos</h3>
                        <div class="card-tools">
                            <a href="#"
                                class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear
                            </a>
                        </div>

                    </div>
                    <div class="card-body">

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Año</th>
                                    <th>Versión</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contexto as $ct)
                                    <tr>
                                        <td>{{ $ct->id }}</td>
                                        <td>{{ $ct->year }}</td>
                                        <td>{{ $ct->version }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('smp.show', $hallazgo->id) }}"
                                                    class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                    title="Editar SMP">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a href="#" class="btn btn-primary btn-sm btnAsignar"
                                                    data-toggle="modal" data-target="#especialistaModal"
                                                    title="Asignar Especialista" data-id="{{ $hallazgo->id }}">
                                                    <i class="fas fa-user"></i>
                                                </a>
                                                <a href="{{ route('smp.plan', $hallazgo->id) }}"
                                                    class="btn btn-success btn-sm" title="Elaborar Plan de Acción">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <a href="{{ route('smp.acciones.seguimiento', $hallazgo->id) }}"
                                                    class="btn btn-dark btn-sm" title="Seguimiento Acciones">
                                                    <i class="fas fa-tasks"></i>
                                                </a>
                                            </div>

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
@endsection

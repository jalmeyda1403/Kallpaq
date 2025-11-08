@extends('layout.master')
@section('title', 'SIG')
@push('styles')
    <style>
        /* Estilo Profesional para Tarjetas y Controles */
        .card-documento {
            background-color: #fff;
            border: 1px solid #e4ebf3;
            border-radius: .4rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
            transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
        }

        .card-documento:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, .08);
        }

        .card-documento .card-title {
            color: #343a40;
            font-weight: 600;
        }

        /* Iconos de cambio de vista */
        .view-options a {
            color: #adb5bd;
            font-size: 1.2rem;
            transition: color .2s ease;
        }

        .view-options a:hover,
        .view-options a.active {
            color: #c82333;
            /* Rojo oscuro para estado activo/hover */
        }

        /* Botones de íconos cuadrados */
        .btn-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
        }

        #filter-form {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
    </style>
@endpush
@section('content')
    <div id="app">
        <pdf-modal></pdf-modal>
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
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-md-left">
                            <h3 class="card-title mb-0">Documentación del Proceso</h3>
                        </div>

                        <div class="col-md-6 text-md-right view-options">
                            <a href="#" id="verComoTarjetas" class="mx-2">
                                <i class="fas fa-th-large"></i>
                            </a> <span class="mx-2 text-muted">|</span>
                            <a href="#" id="verComoLista" class="mx-2">
                                <i class="fas fa-list"></i>
                            </a>
                        </div>
                    </div>

                    <hr>
                    <div id="filter-form">
                    <!-- Filtros del Proceso-->
                    <form action="{{ route('documento.buscar') }}" method="GET" >
                        <div class="row g-2 align-items-center" >
                            <div class="col-md">
                                <input type="text" name="buscar_documento" id="buscar_documento"
                                    value="{{ request('buscar_documento') }}" class="form-control"
                                    placeholder="Buscar por nombre documento">
                            </div>
                            <div class="col-md">
                                <input type="text" name="buscar_proceso" id="buscar_proceso"
                                    value="{{ request('buscar_proceso') }}" class="form-control"
                                    placeholder="Buscar por Proceso">
                            </div>
                            <div class="col-md">
                                <select name="fuente" id="fuente" class="form-control"
                                    data-placeholder="Buscar por fuente">
                                    <option value="">Buscar por fuente</option>
                                    <option value="interno" {{ request('fuente') == 'interno' ? 'selected' : '' }}>Fuente
                                        Interna
                                    </option>
                                    <option value="externo" {{ request('fuente') == 'externo' ? 'selected' : '' }}>Fuente
                                        Externa
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="tipo_documento[]" id="tipo_documento" class="form-control" multiple
                                    data-placeholder="Buscar por tipo documento">

                                    @foreach (config('opciones.tipos_documento') as $codigo => $nombre)
                                        <option value="{{ $codigo }}"
                                            {{ collect(request('tipo_documento'))->contains($codigo) ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn bg-black">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-danger" role="status" id="loader">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="row hidden-by-default" id="documentosContainer">
                        @forelse ($documentos as $documento)
                            @php
                                $version = $documento->usa_versiones ? $documento->ultimaVersion->version ?? 0 : 0;
                                $pdfPath = $documento->usa_versiones
                                    ? optional($documento->ultimaVersion)->archivo_path
                                    : optional($documento)->archivo_path;
                            @endphp

                            {{-- VISTA DE TARJETAS (Mantenemos tu estructura original para las tarjetas) --}}
                            {{-- Por defecto será visible (no d-none) --}}
                            <div class="col-md-6 col-lg-3 mb-2 document-card-view"> {{-- Clase para identificar la vista de tarjeta --}}
                                <div class="card h-100 card-documento">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0 me-2 ">{{ $documento->nombre_documento }}</h6>
                                            <span
                                                class="badge bg-danger text-white flex-shrink-0">{{ $documento->tipo_documento->nombre_tipodocumento }}</span>
                                        </div>

                                        <div class="text-muted small mb-2">
                                            <span class="fw-bold">{{ $documento->cod_documento }}</span>
                                            <span class="mx-1">|</span>
                                            <i class="fas fa-code-branch me-1"></i>
                                            v{{ str_pad($version, 2, '0', STR_PAD_LEFT) }}
                                        </div>

                                        <p class="card-text text-muted flex-grow-1" style="font-size: 13px;">
                                            {{ Str::limit($documento->resumen, 120) ?? 'Sin resumen disponible.' }}
                                        </p>

                                        <div class="mt-auto">
                                            <div
                                                class="d-flex justify-content-between align-items-center small text-muted mb-3">
                                                <div class="d-flex align-items-center" title="Área temática">
                                                    <i class="fas fa-layer-group me-1 text-secondary"></i>
                                                    <span>{{ Str::limit($documento->area_compliance?->area_compliance_nombre ?? 'N/A', 25) }}</span>
                                                </div>
                                                <div class="d-flex align-items-center" title="Fecha de Vigencia">
                                                    <i class="fas fa-clock me-1 text-secondary"></i>
                                                    <span> Vigencia:
                                                        {{ $documento->fecha_vigencia ? $documento->fecha_vigencia->format('d/m/Y') : 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                @if ($pdfPath)
                                                    <a href="#" class="btn btn-sm btn-outline-danger me-2"
                                                        onclick="document.dispatchEvent(new CustomEvent('open-pdf-modal', { detail: '{{ route('documento.mostrar', ['path' => $pdfPath]) }}' }))"
                                                        title="Abrir PDF">
                                                        <i class="fas fa-file-pdf"></i> PDF
                                                    </a>
                                                @endif
                                                <a href="#" class="btn btn-sm btn-outline-secondary"
                                                    data-toggle="modal" data-target="#documentoHijosModal"
                                                    title="Documentos Relacionados">
                                                    <i class="fas fa-sitemap"></i> Relacionados
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- VISTA DE LISTA --}}
                            <div class="col-12 mb-1 document-list-view d-none">
                                <div class="card card-list-item h-100"> {{-- Agregado h-100 para altura consistente si es posible --}}
                                    <div class="card-body p-2 d-flex flex-column"> {{-- Cambiado a flex-column para el resumen --}}
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            {{-- d-flex para título y badge --}}
                                            <div class="d-flex align-items-center flex-grow-1 me-3">

                                                <div class="d-flex flex-column flex-grow-1"> {{-- Asegura que el texto del título ocupe espacio --}}
                                                    <h6 class="mb-0 text-truncate-multiline text-dark">
                                                        {{-- Clase para truncar en múltiples líneas --}}
                                                        {{ $documento->nombre_documento }}
                                                    </h6>
                                                    <small class="badge-container"> {{-- Contenedor para el badge y otros datos pequeños --}}
                                                        <span
                                                            class="badge badge-pill badge-danger text-white">{{ $documento->tipo_documento->nombre_tipodocumento }}</span>
                                                        {{-- badge-pill para bordes redondeados --}}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end flex-shrink-0">
                                                {{-- Botones de acción (PDF, Relacionados) - Mantienen su posición --}}
                                                @if ($pdfPath)
                                                    <a href="#"
                                                        class="btn btn-sm btn-outline-danger me-20  btn-icon"
                                                        onclick="document.dispatchEvent(new CustomEvent('open-pdf-modal', { detail: '{{ route('documento.mostrar', ['path' => $pdfPath]) }}' }))"
                                                        title="Abrir PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                                <a href="#" class="btn btn-sm btn-outline-secondary  btn-icon"
                                                    data-toggle="modal" data-target="#documentoHijosModal"
                                                    title="Documentos Relacionados">
                                                    <i class="fas fa-sitemap"></i>
                                                </a>
                                            </div>
                                        </div>

                                        {{-- Nueva sección para la información secundaria y el resumen --}}
                                        <div class="d-flex justify-content-between align-items-end mt-1 mb-2">
                                            {{-- Alinea items al final (inferior) --}}
                                            <div class="text-muted small text-nowrap me-3"> {{-- text-nowrap para mantener estas partes en una línea --}}
                                                <span class="fw-bold">{{ $documento->cod_documento }}</span>
                                                <span class="mx-1">|</span>
                                                v{{ str_pad($version, 2, '0', STR_PAD_LEFT) }}
                                                <span class="mx-1">|</span>
                                                <i class="fas fa-layer-group me-1"></i> Área:
                                                {{ Str::limit($documento->area_compliance?->area_compliance_nombre ?? 'N/A', 15) }}
                                                {{-- Límite más corto para área en lista --}}
                                                <span class="mx-1">|</span>
                                                <i class="fas fa-clock me-1"></i> Vigencia:
                                                {{ $documento->fecha_vigencia ? $documento->fecha_vigencia->format('d/m/Y') : 'N/A' }}
                                            </div>
                                        </div>

                                        {{-- Resumen del documento con truncamiento de 2 líneas --}}
                                        <p class="card-text text-muted small resumen-truncado flex-grow-1 mb-0"
                                            title="{{ $documento->resumen }}">
                                            {{-- Aquí usaremos CSS para el truncamiento en múltiples líneas --}}
                                            {{ $documento->resumen ?? 'Sin resumen disponible.' }}
                                        </p>

                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted text-center">No se encontraron documentos.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $documentos->links() }}
                </div>
            </div>

        </div>

    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#tipo_documento').select2({
                placeHolder: "Buscar por tipo de proceso de ,,,,",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4'
            });

            $('#fuente').select2({
                allowClear: true,
                allowSearch: false,
                width: '100%',
                theme: 'bootstrap4'
            });

        // Hide the loader and show the filter form after Select2 is initialized
        $('#loader').hide();
        $('#filter-form').css('opacity', '1');

            // Delegar el clic en las filas para manejar selección
            $(document).on('click', '.clickable-row', function() {
                $('.clickable-row').removeClass('selected').find('td').css('opacity', 1);
                $(this).addClass('selected').find('td:not(:last-child)').css('opacity', 0.8);
                procesoId = $(this).data('id');
            });

            // Función para mostrar la vista de tarjetas
            function showCardView() {
                $('.document-list-view').addClass('d-none');
                $('.document-card-view').removeClass('d-none');
                $('#documentosContainer').addClass('row');
                $('#verComoTarjetas').addClass('active');
                $('#verComoLista').removeClass('active');
            }

            // Función para mostrar la vista de lista
            function showListView() {
                $('.document-card-view').addClass('d-none');
                $('.document-list-view').removeClass('d-none');
                $('#documentosContainer').addClass('row');
                $('#verComoLista').addClass('active');
                $('#verComoTarjetas').removeClass('active');
            }

            // Asignar evento click al botón "Ver como tarjetas"
            $('#verComoTarjetas').on('click', function(e) {
                e.preventDefault();
                showCardView();
                localStorage.setItem('documentViewMode', 'card');
            });

            // Asignar evento click al botón "Ver como lista"
            $('#verComoLista').on('click', function(e) {
                e.preventDefault();
                showListView();
                localStorage.setItem('documentViewMode', 'list');
            });

            // Al cargar la página, verificar la preferencia guardada o establecer la vista por defecto
            const savedViewMode = localStorage.getItem('documentViewMode');
            if (savedViewMode === 'list') {
                showListView();
            } else {
                showCardView();
            }
        });
    </script>
@endpush

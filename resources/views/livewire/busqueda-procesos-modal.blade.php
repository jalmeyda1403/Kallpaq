<div wire:ignore.self class="modal fade" tabindex="-1" role="dialog" id="busquedaModal"
    aria-labelledby="busquedaProcesosModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h6>{{ $modalTitle }}</h6>
                <button type="button" class="close" aria-label="Close" id="busquedaModalClose">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div wire:loading class="loading-spinner">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>

                <!-- La búsqueda se maneja completamente por DataTables -->

                @if (count($procesos))
                    <!-- Loading indicator para la tabla -->
                    <div id="table-loading" class="table-loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando tabla...</span>
                        </div>
                        <div class="mt-2">Preparando datos...</div>
                    </div>

                    <!-- Contenedor con wire:ignore para que Livewire no interfiera -->
                    <div wire:ignore>
                        <table id="table-procesos" class="table table-bordered table-hover table-sm table-procesos">
                            <thead class="table-header">
                                <tr>
                                    <th>Seleccionar</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($procesos as $proceso)
                                    <tr>
                                        <td class="text-center">
                                            <input type="radio" name="seleccion" value="{{ $proceso->id }}"
                                                onclick="selectProceso('{{ $proceso->id }}', '{{ $proceso->proceso_nombre }}')">
                                        </td>
                                        <td>{{ $proceso->cod_proceso }}</td>
                                        <td>{{ $proceso->proceso_nombre }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <div wire:loading.remove>
                            No se encontraron procesos.
                        </div>
                    </div>
                @endif
            </div>


        </div>
    </div>
</div>

@push('styles')
    <style>
        .table-procesos {
            font-size: 13px;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            font-size: 13px;
        }

        /* Mostrar el input de búsqueda de DataTables */
        .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_filter input {
            margin-left: 10px;
            padding: 5px 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        /* Ocultar tabla inicialmente para evitar lag visual */
        #table-procesos {
            display: none;
        }

        /* Mostrar tabla solo cuando DataTables esté inicializada */
        #table-procesos.dt-initialized {
            display: table !important;
        }

        /* Estilo para el loading de la tabla */
        .table-loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .table-loading .spinner-border {
            width: 2rem;
            height: 2rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let dataTable = null;
        let modalInitialized = false;

        // Función para seleccionar proceso
        function selectProceso(id, nombre) {
            @this.call('selectItem', id, nombre);
            $('#busquedaModal').modal('hide');
        }

        // Función para mostrar/ocultar loading
        function showTableLoading(show = true) {
            if (show) {
                $('#table-loading').show();
                $('#table-procesos').hide().removeClass('dt-initialized');
            } else {
                $('#table-loading').hide();
                $('#table-procesos').addClass('dt-initialized').show();
            }
        }

        // Función para inicializar DataTable
        function initDataTable() {
            try {
                // Verificar que la tabla existe y tiene datos
                if (!$('#table-procesos').length || $('#table-procesos tbody tr').length === 0) {
                    console.log('Tabla no encontrada o sin datos');
                    return;
                }

                // Mostrar loading
                showTableLoading(true);

                // Destruir instancia existente si hay una
                destroyDataTable();

                console.log('Inicializando DataTable con', $('#table-procesos tbody tr').length, 'filas');

                // Pequeño delay para suavizar la transición
                setTimeout(function() {
                    // Inicializar DataTable
                    dataTable = $('#table-procesos').DataTable({
                        pageLength: 10,
                        searching: true,
                        lengthChange: true,
                        ordering: true,
                        info: true,
                        autoWidth: false,
                        processing: false, // Deshabilitamos el processing de DataTables
                        language: {
                            search: "Buscar procesos:",
                            searchPlaceholder: "Escribe para filtrar...",
                            paginate: {
                                first: "Primero",
                                last: "Último",
                                next: "Siguiente",
                                previous: "Anterior"
                            },
                            lengthMenu: "Mostrar _MENU_ registros",
                            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                            infoEmpty: "Mostrando 0 a 0 de 0 registros",
                            infoFiltered: "(filtrado de _MAX_ registros totales)",
                            emptyTable: "No hay datos disponibles en la tabla",
                            zeroRecords: "No se encontraron registros coincidentes"
                        },
                        responsive: true,
                        order: [
                            [1, 'asc']
                        ], // Ordenar por código
                        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                        initComplete: function(settings, json) {
                            // Callback cuando DataTable está completamente inicializada
                            console.log('DataTable inicializada completamente');

                            // Ocultar loading y mostrar tabla con una pequeña animación
                            setTimeout(function() {
                                showTableLoading(false);
                            }, 100);
                        },
                        drawCallback: function(settings) {
                            // Callback después de cada redibujado
                            console.log('DataTable redibujada');
                        }
                    });

                    console.log('DataTable configurada correctamente');

                }, 50); // Pequeño delay para suavizar

            } catch (error) {
                console.error('Error al inicializar DataTable:', error);
                // En caso de error, mostrar la tabla sin DataTables
                showTableLoading(false);
            }
        }

        // Función para destruir DataTable
        function destroyDataTable() {
            try {
                if (dataTable && $.fn.DataTable.isDataTable('#table-procesos')) {
                    console.log('Destruyendo DataTable existente');
                    dataTable.destroy();
                    dataTable = null;
                }
                // Resetear el estado visual
                $('#table-procesos').removeClass('dt-initialized').hide();
                $('#table-loading').hide();
            } catch (error) {
                console.error('Error al destruir DataTable:', error);
            }
        }

        // Función para reinicializar DataTable
        function reinitDataTable() {
            setTimeout(function() {
                if ($('#busquedaModal').hasClass('show') && $('#table-procesos tbody tr').length > 0) {
                    initDataTable();
                }
            }, 200);
        }

        // Eventos del modal
        $(document).ready(function() {

            // Cerrar modal con botón X
            $(document).on('click', '#busquedaModalClose', function() {
                $('#busquedaModal').modal('hide');
            });

            // Evento cuando el modal se muestra completamente
            $('#busquedaModal').on('shown.bs.modal', function() {
                console.log('Modal mostrado, inicializando DataTable...');
                modalInitialized = true;

                // Delay para asegurar que el DOM esté completamente renderizado
                setTimeout(function() {
                    initDataTable();
                }, 150);
            });

            // Evento cuando el modal se oculta completamente
            $('#busquedaModal').on('hidden.bs.modal', function() {
                console.log('Modal ocultado, destruyendo DataTable...');
                modalInitialized = false;
                destroyDataTable();
                // Resetear estado visual
                showTableLoading(false);
            });

            // Evento cuando el modal está a punto de mostrarse
            $('#busquedaModal').on('show.bs.modal', function() {
                console.log('Modal a punto de mostrarse...');
                // Limpiar cualquier instancia previa y preparar loading
                destroyDataTable();
                showTableLoading(true);
            });
        });

        // Eventos de Livewire
        document.addEventListener('DOMContentLoaded', function() {

            // Para Livewire v2
            document.addEventListener('livewire:load', function() {

                // Escuchar evento para cerrar modal
                Livewire.on('cerrar-busqueda-modal', function() {
                    document.activeElement.blur();
                    $('#busquedaModal').modal('hide');
                });

                // Hook para cuando Livewire procesa mensajes
                Livewire.hook('message.processed', function(message, component) {
                    if (modalInitialized && $('#busquedaModal').hasClass('show')) {
                        console.log('Livewire mensaje procesado, reinicializando DataTable...');
                        reinitDataTable();
                    }
                });
            });

            // Para Livewire v3 (compatibilidad)
            if (typeof Livewire !== 'undefined' && Livewire.hook) {
                Livewire.hook('morph.updated', function({
                    component,
                    cleanup
                }) {
                    if (modalInitialized && $('#busquedaModal').hasClass('show')) {
                        console.log('Livewire DOM actualizado, reinicializando DataTable...');
                        reinitDataTable();
                    }
                });
            }
        });

        // Manejo de errores global para DataTables
        $.fn.dataTable.ext.errMode = 'none';

        $(document).on('error.dt', function(e, settings, techNote, message) {
            console.error('Error en DataTable:', message);
        });
    </script>
@endpush

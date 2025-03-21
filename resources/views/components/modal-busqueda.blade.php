<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-md-down" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: {{ $modalBgcolor }}; color: {{ $modalTxtcolor }};">
                <h5 class="modal-title" id="{{ $modalId }}Label">Seleccionar {{ $modalTitulo }}</h5>
                <button type="button" class="close" aria-label="Close" id="closeSearchModal-{{ $modalId }}">
                    <span aria-hidden="true" style="color: white">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <!-- Campo de búsqueda -->
                <input type="text" id="{{ $modalId }}-buscador" class="form-control mb-3"
                    placeholder="Buscar elementos..." onkeyup="filtrarItems('{{ $modalId }}')">
                <!-- Tabla de elementos -->
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>{{ $modalTitulo }}</th>
                            </tr>
                        </thead>
                        <tbody id="{{ $modalId }}-listaItems">
                            <!-- Los ítems se cargarán dinámicamente aquí -->
                        </tbody>
                    </table>
                </div>
                <!-- Indicador de carga -->
                <div id="{{ $modalId }}-loading" class="text-center" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btncloseModal-{{ $modalId }}">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        let campoId = '{{ $campoId }}';
        let campoNombre = '{{ $campoNombre }}';
        let modalId = '{{ $modalId }}';

        $('#{{ $modalId }}').on('show.bs.modal', function(event) {
            const loadingIndicator = document.getElementById(modalId + '-loading');
            loadingIndicator.style.display = 'block'; // Mostrar el indicador de carga
            cargarItems(campoId, campoNombre, modalId); // Cargar los elementos correspondientes
        });

        // Función para cargar los elementos según el tipo
        function cargarItems(tipo, campoNombre, modalId) {
            const listaItems = document.getElementById(modalId + '-listaItems');
            const loadingIndicator = document.getElementById(modalId + '-loading');
            loadingIndicator.style.display = 'block'; // Mostrar el indicador de carga

            // Guardar el ID del elemento seleccionado
            const selectedRadio = document.querySelector(`input[name="${tipo}"]:checked`);
            const selectedId = selectedRadio ? selectedRadio.value : null;

            fetch(`{{ url($ruta) }}`)
                .then(response => response.json())
                .then(data => {
                    listaItems.innerHTML = ''; // Limpiar la lista antes de agregar nuevos ítems
                    data.forEach(item => {
                        const row = document.createElement('tr');
                        const nombre = item[campoNombre] || ''; // Si no se encuentra, asigna vacío

                        row.innerHTML = `
                    <td class="text-center"><input class="form-check-input" type="radio" name="${tipo}" value="${item.id}" data-nombre="${nombre}"></td>
                    <td>${nombre}</td>
                `;
                        listaItems.appendChild(row);
                    });

                    // Restaurar la selección del elemento
                    if (selectedId) {
                        const radioToSelect = document.querySelector(
                            `input[name="${tipo}"][value="${selectedId}"]`);
                        if (radioToSelect) {
                            radioToSelect.checked = true;
                        }
                    }

                    loadingIndicator.style.display = 'none'; // Ocultar el indicador de carga
                })
                .catch(error => console.error('Error al cargar los elementos:', error));
        }


        // Función para filtrar los ítems según el texto ingresado
        window.filtrarItems = function(modalId) {
            const buscador = document.getElementById(modalId + '-buscador').value.toLowerCase();
            const items = document.querySelectorAll('#' + modalId + '-listaItems tr');
            items.forEach(item => {
                const nombre = item.querySelector('td:nth-child(2)').textContent.toLowerCase();
                item.style.display = nombre.includes(buscador) ? '' : 'none';
            });
        };

        // Evento para manejar la selección de un ítem
        document.addEventListener('change', function(event) {
            if (event.target.name === campoId) {
                const selectedRadio = event.target;
                const selectedId = selectedRadio.value;
                const selectedNombre = selectedRadio.getAttribute('data-nombre');
                document.getElementById(campoId).value = selectedId;
                document.getElementById(campoNombre).value = selectedNombre;
                // Cerrar el modal
                $('#{{ $modalId }}').modal('hide');
            }
        });

        $(`#closeSearchModal-${modalId}`).on('click', function() {
            $(`#${modalId}`).modal('hide');
        });
        $(`#btncloseModal-${modalId}`).on('click', function() {
            $(`#${modalId}`).modal('hide');
        });
    });
</script>

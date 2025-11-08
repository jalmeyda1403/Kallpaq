<template>
    <div class="container-fluid">
        <div v-if="successMessage" class="alert alert-success" id="success-alert">
            {{ successMessage }}
        </div>
        <div v-if="errorMessage" class="alert alert-danger" id="error-alert">
            {{ errorMessage }}
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Documentos</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Documentación del Proceso</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <a href="#" class="btn btn-primary btn-sm mr-1" @click.prevent="openNewDocumentModal"
                            title="Nuevo Documento">
                            <i class="fas fa-plus-circle"></i> Agregar
                        </a>
                        <button class="btn btn-warning btn-sm mr-1" :disabled="!selectedDocumento"
                            @click.prevent="editDocumento" title="Editar Documento">
                            <i class="fas fa-pencil-alt"></i> Editar
                        </button>
                        <button class="btn btn-danger btn-sm" :disabled="!selectedDocumento"
                            @click.prevent="deleteDocumento" title="Eliminar Documento">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                </div>
                <hr>

                <form @submit.prevent="fetchDocumentosWithFilters">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" v-model="filters.buscar_documento" class="form-control me-2"
                                placeholder="Buscar por nombre documento">
                        </div>
                        <div class="col-md-3">
                            <input type="text" v-model="filters.buscar_proceso" class="form-control me-2"
                                placeholder="Buscar por Proceso">
                        </div>
                        <div class="col-md-3">
                            <select v-model="filters.fuente" class="form-control me-2">
                                <option value="">Buscar por fuente</option>
                                <option value="1">Fuente Interna</option>
                                <option value="0">Fuente Externa</option>
                            </select>
                        </div>
                         <div class="col-md-3">
                            <div class="d-flex align-items-stretch gap-1">
                                <select ref="selectTipoDocumento" v-model="filters.tipo_documento" class="form-control" multiple></select>
                                <button type="submit" class="btn bg-dark btn-sm">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div v-if="isLoading" class="loading-spinner w-100 text-center my-5">
                    <div class="spinner-border text-danger" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
                <div v-else>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm" id="documentosTable">
                            <thead class="table-header">
                                <tr>
                                    <th class="text-nowrap">Código Documento</th>
                                    <th>Nombre Documento</th>
                                    <th class="text-nowrap">Tipo de Documento</th>
                                    <th class="text-center">Fuente</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Versión</th>
                                    <th class="text-center">Vigencia</th>
                                    <th>Proceso(s)</th>
                                    <th class="text-center"><i class="fas fa-info"></i></th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <DocumentoModal></DocumentoModal>
</template>
<script setup>
import { onMounted, onBeforeUnmount, ref, reactive, nextTick } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';

import DocumentoModal from '@/components/documentos/DocumentoModal.vue';
import { useDocumentoStore } from '@/stores/documentoStore'; // Importa la tienda

const documentos = ref([]);
const selectedDocumento = ref(null);
const successMessage = ref('');
const errorMessage = ref('');
const selectTipoDocumento = ref(null);
const documentoStore = useDocumentoStore(); // Instancia de la tienda
let dataTableInstance = null;
const filters = reactive({
    buscar_documento: '',
    buscar_proceso: '',
    fuente: '',
    tipo_documento: []
});
const isLoading = ref(true);

function format(rowData) {
    const procesosHtml = rowData.procesos.length > 0
        ? rowData.procesos.map(p => `<li>${p.cod_proceso} - ${p.proceso_nombre}</li>`).join('')
        : '<li>No hay procesos asociados.</li>';

    return `
        <div class="p-3 bg-light">
            <h6 class="font-weight-bold">Procesos Asociados</h6>
            <div class="row">
                <div class="col-md-12">
                  <ul class="list-unstyled">${procesosHtml}</ul>
                </div>
            </div>
        </div>
    `;
}

// Métodos
const fetchDocumentos = async () => {
    console.log('prueba');
    isLoading.value = true;
    try {
        const response = await axios.get(route('api.documentos'), {
            params: filters
        });
        documentos.value = response.data;
        console.log('Documentos fetched:', documentos.value);
    } catch (error) {
         errorMessage.value = 'Hubo un problema al cargar los documentos. Intente de nuevo más tarde.';
    } finally {
        isLoading.value = false;
        await nextTick();
        initializeDataTable();
    }
};

const initializeDataTable = () => {

    dataTableInstance = $('#documentosTable').DataTable({
        data: documentos.value,
        dom: "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'B>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Descargar',
                className: 'btn btn-success btn-sm'
            }
        ],
        pageLength: 25,
        searching: false,
        autoWidth: false,
        columns: [

            { data: 'cod_documento', title: 'Código', className: 'text-nowrap' },
            { data: 'nombre_documento', title: 'Nombre Documento' },
            {
                data: 'usa_versiones_documento',
                title: 'Versión',
                className: 'text-center',
                render: (data, type, row) => {
                    // Obtenemos el número de versión, si existe, o '0' por defecto
                    const version = data && row.ultima_version && row.ultima_version.dv_version ?
                        row.ultima_version.dv_version : '0';

                    // Formateamos el número para que siempre tenga 2 dígitos
                    return String(version).padStart(2, '0');
                }
            },
            {
                data: 'tipo_documento.nombre_tipodocumento',
                title: 'Tipo de Documento',
                defaultContent: ''
            },
            { data: 'fuente_documento', title: 'Fuente', className: 'text-center text-nowrap', render: (data) => data == 1 ? 'Interna' : 'Externa' },
            { data: 'estado_documento', title: 'Estado', className: 'text-center text-nowrap', render: (data) => data == 1 ? 'Vigente' : 'No vigente' },

            {
                data: 'usa_versiones_documento',
                title: 'Vigencia',
                className: 'text-center text-nowrap',
                render: (data, type, row) => {
                    let dateValue = '';

                    if (row.usa_versiones_documento === 1) {
                        // Si el documento usa versiones, obtenemos la fecha de la última versión
                        dateValue = row.ultima_version ? row.ultima_version.dv_fecha_vigencia : '';
                    } else {
                        // Si no usa versiones, obtenemos la fecha del documento
                        dateValue = row.fecha_vigencia_documento;
                    }

                    // Si no hay fecha, retornamos una cadena vacía
                    if (!dateValue) {
                        return '';
                    }

                    // Formateamos la fecha
                    const date = new Date(dateValue);
                    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };

                    return new Intl.DateTimeFormat('es-PE', options).format(date);
                }
            },
            {
                className: 'details-control text-center',
                orderable: false,
                data: null,
                defaultContent: '<i class="fas fa-plus-circle text-primary"></i>'
            },
            {
                data: null,
                title: '<i class="fas fa-info"></i>',
                orderable: false,
                className: 'text-center',
                render: (data, type, row) => {
                    const isValid = row.usa_versiones_documento ? (row.ultima_version?.dv_enlace_valido === 1) : (row.enlace_valido === 1);
                    const icon = isValid ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-warning';
                    const title = isValid ? 'Enlace válido' : 'Enlace no válido';
                    return `<i class="fas ${icon}" title="${title}"></i>`;
                }
            },
            {
                data: null,
                title: 'Ver',
                orderable: false,
                className: 'text-center text-nowrap',
                render: (data, type, row) => `
            <a href="#" class="px-1 btnVerDocumento" title="Abrir Pdf" data-id="${row.id}"><i class="fas fa-file-pdf fa-lg text-danger"></i></a>
            <a href="#" class="px-1 btnverHijos" title="Ver Documentos Relacionados" data-id="${row.id}"><i class="fas fa-copy"></i></a>
        `
            }
        ],
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
            info: "",
        },
        responsive: true,
        destroy: true,
        initComplete: function () {
            // Añadimos los manejadores de eventos de clic a las filas de la tabla
            $('#documentosTable tbody').off('click').on('click', 'tr', function () {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                    selectedDocumento.value = null;
                } else {
                    dataTableInstance.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                    const data = dataTableInstance.row(this).data();
                    selectedDocumento.value = data;

                }
            });
            $('#documentosTable tbody').on('click', 'td.details-control', function () {
                const tr = $(this).closest('tr');
                const row = dataTableInstance.row(tr);

                if (row.child.isShown()) {
                    // Esta fila ya está expandida, así que la colapsamos
                    row.child.hide();
                    tr.removeClass('details');
                } else {
                    // Expandimos la fila
                    row.child(format(row.data())).show();
                    tr.addClass('details');
                }
            });

            // Re-adjuntamos los manejadores de eventos para los botones de la tabla
            $('#documentosTable tbody').off('click', '.btnVerDocumento').on('click', '.btnVerDocumento', function (e) {
                e.preventDefault();
                const data = dataTableInstance.row($(this).closest('tr')).data();
                openPdfModal(data);
            });

            $('#documentosTable tbody').off('click', '.btnverHijos').on('click', '.btnverHijos', function (e) {
                e.preventDefault();
                const data = dataTableInstance.row($(this).closest('tr')).data();
                showRelatedDocs(data.id);
            });
        }
    });
};


const fetchDocumentosWithFilters = () => {
    if ($.fn.DataTable.isDataTable('#documentosTable')) {
        $('#documentosTable').DataTable().destroy();
    }
    fetchDocumentos();
};

const openNewDocumentModal = () => {
    documentoStore.openModal();
};

const editDocumento = () => {
    if (selectedDocumento.value) {
        documentoStore.openModal(selectedDocumento.value);
    }
};


const deleteDocumento = async () => {
    if (selectedDocumento.value) {
        if (confirm('¿Está seguro de que desea eliminar este documento?')) {
            try {
                await axios.delete(route('api.documentos.eliminar', { id: selectedDocumento.value.id }));
                successMessage.value = 'Documento eliminado con éxito.';
                fetchDocumentos();
            } catch (error) {
                console.error('Error al eliminar el documento:', error);
                errorMessage.value = 'Hubo un problema al eliminar el documento.';
            }
        }
    }
};

const openPdfModal = (documento) => {
    console.log('Abrir PDF de:', documento);
    // Lógica para abrir tu modal de PDF
};

const showRelatedDocs = (documentoId) => {
    console.log('Mostrar documentos relacionados para:', documentoId);
    // Lógica para mostrar docs relacionados
};

const initializeSelect2 = () => {
    if (selectTipoDocumento.value && !$(selectTipoDocumento.value).data('select2')) {
        $(selectTipoDocumento.value).select2({
            placeholder: 'Seleccione Tipo de Documento...',
            ajax: {
                url: route('tipoDocumento.buscar'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.map(tipoDocumento => ({ id: tipoDocumento.id, text: tipoDocumento.nombre_tipodocumento })) };
                },
                cache: true,
            },
            multiple: true // Asegura que el modo múltiple esté activado
        });

        // Conecta el evento de cambio de Select2 con tu variable de Vue
        $(selectTipoDocumento.value).on('change', function () {
            // Obtiene los valores seleccionados (un array de IDs) y actualiza la variable
            filters.tipo_documento = $(this).val();
        });
    }
};

// Lifecycle Hooks
onMounted(() => {
    fetchDocumentos();
    initializeSelect2();

});

onBeforeUnmount(() => {
    if ($.fn.DataTable.isDataTable('#documentosTable')) {
        dataTableInstance.destroy();
    }
    if (selectTipoDocumento.value && $(selectTipoDocumento.value).data('select2')) {
        $(selectTipoDocumento.value).select2('destroy');
    }
});
</script>


<style scoped>
/* Estilos del menú lateral */
.table {
    font-size: 12px;
}

.table thead th {
    vertical-align: top;
}

/* Estilos del contenedor del Select2 */
::v-deep(.select2-container--default .select2-selection--multiple) {
    font-size: 13px;
}

::v-deep(.select2-container--default .select2-results__option) {
    font-size: 13px;
}

::v-deep(.select2-container--default .select2-results__option--highlighted[aria-selected]) {
    background-color: #6c757d !important;
    color: white !important;
}

/* Estilos para los elementos seleccionados (las píldoras) */
::v-deep(.select2-container--default .select2-selection--multiple .select2-selection__choice) {
    background-color: #dc3545;
    /* Fondo rojo */
    color: white;
    /* Texto blanco */
    font-size: 12px;
    /* Tamaño de texto de 13px */
    border-color: #dc3545;
}


/* Estilos para la 'x' de eliminar en los elementos seleccionados */
::v-deep(.select2-container--default .select2-selection__choice__remove) {
    color: white;
    /* Color blanco para el icono de la 'x' */
}

/* Estilos para las opciones del menú desplegable */
::v-deep(.select2-results) {
    font-size: 12px;
    /* Tamaño de texto de 12px */
}

/* Estilos para la opción seleccionada/resaltada en el menú (hover) */

::v-deep(.select2-results__options .select2-results__option--highlighted) {
    background-color: #dc3545 !important;
    /* Fondo rojo al pasar el mouse */
    color: white !important;
    font-size: 12px;
}

/* Estilo para la fila seleccionada */
.clickable-row.selected {
    background-color: #f0f0f0;
    /* Color para la fila seleccionada */
}

.form-overlay-container {
    position: relative;
    min-height: 200px;
    /* Asegura que el contenedor tenga una altura mínima */
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.35);
    /* Fondo semi-transparente */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
    /* Asegura que esté por encima del formulario */
}
</style>
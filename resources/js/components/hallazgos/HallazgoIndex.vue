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
                <li class="breadcrumb-item active" aria-current="page">Solicitudes de Mejora</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Gestión de Solicitudes de Mejora</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <a href="#" class="btn btn-primary btn-sm mr-1" @click.prevent="openNewHallazgoModal"
                            title="Nuevo Hallazgo">
                            <i class="fas fa-plus-circle"></i> Agregar
                        </a>
                        <button class="btn btn-warning btn-sm mr-1" :disabled="!selectedHallazgo"
                            @click.prevent="editHallazgo" title="Editar Hallazgo">
                            <i class="fas fa-pencil-alt"></i> Editar
                        </button>
                        <button class="btn btn-danger btn-sm" :disabled="!selectedHallazgo"
                            @click.prevent="deleteHallazgo" title="Eliminar Hallazgo">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                </div>
                <hr>
                <form @submit.prevent="fetchHallazgosWithFilters">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" v-model="filters.descripcion" class="form-control"
                                placeholder="Buscar por descripción o resumen...">
                        </div>
                        <div class="col-md-4">
                            <input type="text" v-model="filters.proceso" class="form-control"
                                placeholder="Buscar por Proceso">
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex align-items-stretch gap-1">
                                <select v-model="filters.clasificacion" class="form-control">
                                    <option value="">Todas las Clasificaciones</option>
                                    <option value="NCM">No Conformidad Mayor</option>
                                    <option value="NCMe">No Conformidad Menor</option>
                                    <option value="OBS">Observación</option>
                                    <option value="OdM">Oportunidad de Mejora</option>
                                </select>
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
                        <table class="table table-bordered table-hover table-sm" id="hallazgosTable">
                            <thead class="table-header">
                                <tr>
                                    <th class="text-nowrap">Código</th>
                                    <th>Clasificación</th>
                                    <th class="text-nowrap">Resumen</th>
                                    <th class="text-center text-nowrap">Proceso</th>
                                    <th class="text-center text-nowrap">F. Identificación</th>
                                    <th class="text-center text-nowrap">F. Asignación</th>
                                    <th class="text-center text-nowrap">Estado</th>
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
    <HallazgoModal></HallazgoModal>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, reactive, nextTick } from 'vue'; // 1. Importa nextTick
import axios from 'axios';
import { route } from 'ziggy-js';

import HallazgoModal from '@/components/hallazgos/HallazgoModal.vue';
import { useHallazgoStore } from '@/stores/hallazgoStore'; // Importa la tienda
const hallazgos = ref([]);
const selectedHallazgo = ref(null);
const successMessage = ref('');
const errorMessage = ref('');
const hallazgoStore = useHallazgoStore();
let dataTableInstance = null;
const filters = reactive({
    descripcion: '',
    proceso: '',
    clasificacion: ''
});
const isLoading = ref(true);
const formatDate = (dateString) => {
    if (!dateString) {
        return '';
    }
    const date = new Date(dateString);
    // getMonth() es 0-indexado (Enero=0), por eso sumamos 1.
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
};
// Métodos
const fetchHallazgos = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('api.hallazgos'), {
            params: filters
        });

        hallazgos.value = response.data;


    } catch (error) {
        console.error('Error al obtener los hallazgos:', error);
        errorMessage.value = 'Hubo un problema al cargar los hallazgos.';
    } finally {
        isLoading.value = false;
        await nextTick();
        initializeDataTable();
    }
};

const initializeDataTable = () => {
    dataTableInstance = $('#hallazgosTable').DataTable({
        data: hallazgos.value,
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
        columns: [
            {
                data: 'hallazgo_cod',
                className: 'text-center text-nowrap'
            },
            {
                data: 'hallazgo_clasificacion',
                className: 'text-center text-nowrap'
            },
            { data: 'hallazgo_resumen' },
            {
                data: 'procesos',
                render: (data) => {
                    // Si el array existe y tiene elementos...
                    if (data && Array.isArray(data) && data.length > 0) {
                        // ...usamos map() para extraer el nombre de cada proceso...
                        return data.map(proceso => proceso.proceso_nombre).join(', ');
                    }
                    // Si no, mostramos el texto por defecto.
                    return '<em>No asignado</em>';
                }
            },
            {
                data: 'hallazgo_fecha_identificacion',
                render: data => formatDate(data),
                className: 'text-center'
            },
            {
                data: 'hallazgo_fecha_aprobacion', // Corregido para usar el nombre de tu modelo
                render: data => formatDate(data),
                className: 'text-center'
            },
            { data: 'hallazgo_estado' },
        ],

        // ... (el resto de tu configuración de DataTables: dom, language, etc.)

        pageLength: 10,
        searching: false,
        autoWidth: false,
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
    });

    $('#hallazgosTable tbody').off('click', 'tr').on('click', 'tr', function () {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            selectedHallazgo.value = null;
        } else {
            dataTableInstance.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            const data = dataTableInstance.row(this).data();
            selectedHallazgo.value = data;
            console.log('Hallazgo seleccionado:', selectedHallazgo.value);
        }
    });
};

const fetchHallazgosWithFilters = () => {
    if ($.fn.DataTable.isDataTable('#hallazgosTable')) {
        $('#hallazgosTable').DataTable().destroy();
    }
    fetchHallazgos();
};



const openNewHallazgoModal = () => {
    hallazgoStore.openModal();
};

const editHallazgo = () => {
    if (selectedHallazgo.value) {
        hallazgoStore.openModal(selectedHallazgo.value);
    }
};

const deleteHallazgo = async () => {
    // ...
};

// --- CICLO DE VIDA ---
onMounted(() => {
    fetchHallazgos();

});

onBeforeUnmount(() => {
    if ($.fn.DataTable.isDataTable('#hallazgosTable')) {
        dataTableInstance.destroy();
    }
    if (sigSelectElement.value && sigSelectElement.value.data('select2')) {
        sigSelectElement.value.select2('destroy');
    }
});

</script>

<style scoped>
/* Estilos similares a DocumentoIndex.vue para consistencia */
.table {
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
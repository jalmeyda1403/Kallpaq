<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Consolidado de Sugerencias</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Consolidado de Sugerencias</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-primary btn-sm ml-1" @click="openModal()">
                            <i class="fas fa-plus-circle"></i> Nueva Sugerencia
                        </button>
                        <button class="btn btn-danger btn-sm ml-1" :disabled="!selectedSugerenciaId"
                            @click="confirmDelete">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <form @submit.prevent="fetchSugerencias">
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" v-model="filters.buscar_sugerencia" class="form-control"
                                        placeholder="Buscar por ID, detalle...">
                                </div>
                                <div class="col">
                                    <select v-model="filters.estado" class="form-control">
                                        <option value="">Todos los Estados</option>
                                        <option value="abierta">Abierta</option>
                                        <option value="en progreso">En Progreso</option>
                                        <option value="cerrada">Cerrada</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select v-model="filters.proceso_id" class="form-control">
                                        <option value="">Todos los Procesos</option>
                                        <option v-for="proceso in procesos" :key="proceso.id" :value="proceso.id">
                                            {{ proceso.nombre }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn bg-dark">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <!-- Tabla -->
                <DataTable ref="dt" :value="sugerencias" v-model:filters="filtersPrimevue"
                    v-model:selection="selectedSugerenciaId" selectionMode="single" paginator :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                    :globalFilterFields="['id', 'sugerencia_detalle', 'sugerencia_clasificacion', 'sugerencia_estado', 'sugerencia_procedencia']"
                    class="p-datatable-sm p-datatable-striped p-datatable-hoverable-rows">
                    <template #header>
                        <div class="d-flex align-items-center">
                            <Button type="button" icon="pi pi-download" label="Descargar CSV" severity="secondary"
                                @click="exportCSV($event)" class="btn btn-secondary ml-auto" />
                        </div>
                    </template>

                    <Column field="id" header="ID" style="width:5%">
                    </Column>
                    <Column field="sugerencia_detalle" header="Detalle" style="width:30%">
                        <template #body="{ data }">
                            <span :title="data.sugerencia_detalle">
                                {{ truncateText(data.sugerencia_detalle, 60) }}
                            </span>
                        </template>
                    </Column>
                    <Column field="proceso.proceso_nombre" header="Proceso" style="width:15%">
                        <template #body="{ data }">
                            {{ data.proceso?.proceso_nombre || 'N/A' }}
                        </template>
                    </Column>
                    <Column field="sugerencia_procedencia" header="Procedencia" style="width:10%">
                    </Column>
                    <Column field="sugerencia_clasificacion" header="Clasificación" style="width:8%">
                    </Column>
                    <Column field="sugerencia_estado" header="Estado" style="width:10%; text-align: center;">
                        <template #body="{ data }">
                            <span :class="getStatusBadgeClass(data.sugerencia_estado)">
                                {{ data.sugerencia_estado }}
                            </span>
                        </template>
                    </Column>
                    <Column field="sugerencia_fecha_ingreso" header="F. Ingreso" style="width:10%">
                        <template #body="{ data }">
                            {{ formatDate(data.sugerencia_fecha_ingreso) }}
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:12%" headerStyle="width: 12%"
                        bodyStyle="width: 12%">
                        <template #body="{ data }">
                            <a href="#" title="Editar" class="mr-3 d-inline-block"
                                @click.prevent="editSugerencia(data)">
                                <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                            </a>
                            <a href="#" title="Tratamiento" class="mr-3 d-inline-block"
                                @click.prevent="openTratamiento(data)">
                                <i class="fas fa-tasks text-info fa-lg"></i>
                            </a>
                            <a href="#" title="Eliminar" class="mr-3 d-inline-block"
                                @click.prevent="deleteSugerencia(data.id)">
                                <i class="fas fa-trash text-danger fa-lg"></i>
                            </a>
                        </template>
                    </Column>
                </DataTable>
            </div>

            <!-- Modal Create/Edit -->
            <SugerenciaModal :show="showModal" :sugerencia-id="selectedSugerenciaId" :procesos="procesos"
                @close="closeModal" @saved="fetchSugerencias"></SugerenciaModal>

            <!-- Modal Tratamiento -->
            <SugerenciaTratamiento :show="showTratamientoModal" :sugerencia-id="selectedSugerenciaId"
                @close="closeTratamientoModal" @saved="fetchSugerencias"></SugerenciaTratamiento>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useSugerenciasStore } from '@/stores/sugerenciasStore';
import { storeToRefs } from 'pinia';
import SugerenciaModal from './SugerenciaModal.vue';
import SugerenciaTratamiento from './SugerenciaTratamiento.vue';
import Swal from 'sweetalert2';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { FilterMatchMode } from 'primevue/api';

// Usamos el store
const sugerenciasStore = useSugerenciasStore();
const { sugerencias, loading: storeLoading } = storeToRefs(sugerenciasStore);

const showModal = ref(false);
const showTratamientoModal = ref(false);
const selectedSugerenciaId = ref(null);
const dt = ref(null);

// Filtros
const filters = reactive({
    estado: '',
    proceso_id: '',
    buscar_sugerencia: ''
});

const filtersPrimevue = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    id: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    sugerencia_detalle: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    sugerencia_clasificacion: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    sugerencia_estado: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    sugerencia_procedencia: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
});

// Métodos
const fetchSugerencias = async () => {
    try {
        await sugerenciasStore.fetchSugerencias(filters);
    } catch (error) {
        console.error('Error fetching sugerencias:', error);
    }
};

const openModal = () => {
    selectedSugerenciaId.value = null;
    showModal.value = true;
};

const editSugerencia = async (sugerencia) => {
    try {
        // Cargamos la sugerencia específica en el store
        await sugerenciasStore.fetchSugerenciaById(sugerencia.id);
        selectedSugerenciaId.value = sugerencia.id;
        showModal.value = true;
    } catch (error) {
        console.error('Error al cargar datos de la sugerencia:', error);
    }
};

const closeModal = () => {
    showModal.value = false;
    selectedSugerenciaId.value = null;
};

const openTratamiento = (sugerencia) => {
    selectedSugerenciaId.value = sugerencia.id;
    showTratamientoModal.value = true;
};

const closeTratamientoModal = () => {
    showTratamientoModal.value = false;
    selectedSugerenciaId.value = null;
};

const confirmDelete = async () => {
    if (!selectedSugerenciaId.value) {
        await Swal.fire('Advertencia', 'Por favor selecciona una sugerencia para eliminar.', 'warning');
        return;
    }

    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar!'
    });

    if (result.isConfirmed) {
        try {
            await sugerenciasStore.deleteSugerencia(selectedSugerenciaId.value);
            await Swal.fire(
                'Eliminado!',
                'La sugerencia ha sido eliminada.',
                'success'
            );
            selectedSugerenciaId.value = null; // Limpiar la selección después de eliminar
        } catch (error) {
            await Swal.fire(
                'Error!',
                'Hubo un problema al eliminar la sugerencia.',
                'error'
            );
        }
    }
};

const deleteSugerencia = async (id) => {
    // Esta función se puede usar para eliminar desde las acciones directamente
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar!'
    });

    if (result.isConfirmed) {
        try {
            await sugerenciasStore.deleteSugerencia(id);
            await Swal.fire(
                'Eliminado!',
                'La sugerencia ha sido eliminada.',
                'success'
            );
            if (selectedSugerenciaId.value === id) {
                selectedSugerenciaId.value = null; // Limpiar la selección si se elimina la sugerencia seleccionada
            }
        } catch (error) {
            await Swal.fire(
                'Error!',
                'Hubo un problema al eliminar la sugerencia.',
                'error'
            );
        }
    }
};

const exportCSV = (event) => {
    dt.value.exportCSV();
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
};

const truncateText = (text, length) => {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'abierta': return 'badge badge-warning';
        case 'en progreso': return 'badge badge-primary';
        case 'cerrada': return 'badge badge-success';
        default: return 'badge badge-secondary';
    }
};

onMounted(() => {
    fetchSugerencias();
});
</script>

<style>
/* Custom loader styles - remove opacity and change color to red */
/* Remove the semi-transparent overlay that dims the table content during loading */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0) !important;
    /* Make background completely transparent */
}

/* Change the loader icon to red */
.p-datatable-loading-icon {
    color: red !important;
    font-size: 2rem !important;
}
</style>

<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link :to="{ name: 'requerimientos.index' }">Inicio</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Bandeja de Requerimientos</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">Lista de Requerimientos</h3>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="#" class="btn btn-primary btn-sm ml-1"
                                    @click.prevent="goToCreateRequerimiento">
                                    <i class="fas fa-plus-circle"></i> Nuevo Requerimiento
                                </a>

                                <button class="btn btn-danger btn-sm ml-1" id="btnEliminar"
                                    :disabled="!selectedRequerimientoId"
                                    @click="confirmDelete(selectedRequerimientoId)">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <form @submit.prevent="fetchRequerimientos">
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="text" name="buscar_requerimiento" id="buscar_requerimiento"
                                                class="form-control" placeholder="Buscar por Requerimiento"
                                                v-model="serverFilters.buscar_requerimiento">
                                        </div>
                                        <div class="col">
                                            <select name="especialista_id" id="especialista_id" class="form-control"
                                                v-model="serverFilters.especialista_id">
                                                <option value="">Todos los especialistas</option>
                                                <option v-for="especialista in especialistas" :key="especialista.id"
                                                    :value="especialista.id">
                                                    {{ especialista.user.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select name="estado" id="estado" class="form-control"
                                                v-model="serverFilters.estado">
                                                <option value="">Todos los estados</option>
                                                <option v-for="status in statuses" :key="status" :value="status">
                                                    {{ status }}
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
                        <!-- Loading State - Spinner circular rojo -->
                        <DataTable ref="dt" :value="requerimientos" v-model:filters="filters" paginator :rows="10"
                            :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                            :globalFilterFields="['id', 'proceso.proceso_nombre', 'asunto', 'complejidad', 'estado', 'especialista.name']"
                            :loading="loading">
                            <template #header>
                                <div class="d-flex align-items-center">
                                    <Button type="button" icon="pi pi-download" label="Descargar CSV"
                                        severity="secondary" @click="exportCSV($event)"
                                        class="btn btn-secondary ml-auto">
                                    </Button>
                                </div>
                            </template>
                            <Column field="id" header="ID" style="width:5%">
                            </Column>
                            <Column field="proceso.proceso_nombre" header="Proceso" sortable style="width:15%">
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                        class="p-column-filter" placeholder="Buscar por Proceso" />
                                </template>
                            </Column>
                            <Column field="asunto" header="Asunto" style="width:15%">
                            </Column>
                            <Column field="complejidad" header="Complejidad" style="width:8%">
                            </Column>
                            <Column field="estado" header="Estado" style="width:8%">
                            </Column>
                            <Column field="especialista.name" header="Especialista" sortable style="width:10%">
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                        class="p-column-filter" placeholder="Buscar por Especialista" />
                                </template>
                            </Column>
                            <Column field="fecha_asignacion" header="Fecha Asignación" sortable style="width:10%">
                                <template #body="{ data }">
                                    {{ formatDate(data.fecha_asignacion) }}
                                </template>
                            </Column>
                            <Column field="fecha_limite" header="Fecha Límite" sortable style="width:10%">
                                <template #body="{ data }">
                                    {{ formatDate(data.fecha_limite) }}
                                </template>
                            </Column>
                            <Column field="fecha_fin" header="Fecha Atención" sortable style="width:10%">
                                <template #body="{ data }">
                                    {{ formatDate(data.fecha_fin) }}
                                </template>
                            </Column>
                            <Column field="avance.updated_at" header="Ultimo Avance" sortable style="width:10%">
                                <template #body="{ data }">
                                    {{ formatDate(data.avance?.updated_at) }}
                                </template>
                            </Column>
                            <Column header="Avance" style="width:10%">
                                <template #body="{ data }">
                                    <template v-if="['creado', 'desestimado'].includes(data.estado)">
                                        <span class="small text-muted">Sin avance</span>
                                    </template>
                                    <template v-else-if="data.avance">
                                        <div class="small text-center">
                                            {{ parseInt(data.avance.avance_registrado) }}%
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-info"
                                                    :style="{ width: parseInt(data.avance.avance_registrado) + '%' }">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="small text-muted">Sin avance</span>
                                    </template>
                                </template>
                            </Column>
                            <Column header="Acciones" :exportable="false" style="width:15%">
                                <template #body="{ data }">
                                    <template v-if="!isMyRequerimientosView">
                                        <a href="#" title="Evaluar Requerimiento"
                                            class="mr-1 d-inline-block btn-modal-trigger"
                                            @click.prevent="openModal('mostrarEvaluacion', data)">
                                            <i class="fas fa-clipboard-check fa-lg text-primary"></i>
                                        </a>
                                        <a href="#" title="Asignar Requerimiento"
                                            class="mr-1 d-inline-block btn-modal-trigger"
                                            @click.prevent="openModal('mostrarAsignacion', data)">
                                            <i class="fas fa-user-check fa-lg text-dark"></i>
                                        </a>
                                        <a href="#" title="Registrar Avance Requerimiento"
                                            class="mr-1 d-inline-block btn-modal-trigger"
                                            @click.prevent="openModal('abrirAvanceRequerimientoModal', data)">

                                            <i class="fas fa-list-alt text-dark fa-lg"></i>
                                        </a>
                                    </template>
                                    <a href="#" title="Ver Avance Requerimiento"
                                        class="mr-1 d-inline-block btn-modal-trigger"
                                        @click.prevent="openModal('mostrarSeguimiento', data)">
                                        <i class="fas fa-stream text-success fa-lg"></i>
                                    </a>
                                    <template v-if="isMyRequerimientosView && data.estado === 'creado'">
                                        <a href="#" title="Eliminar Requerimiento"
                                            class="mr-1 d-inline-block btn-modal-trigger"
                                            @click.prevent="confirmDelete(data.id)">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </a>
                                    </template>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <!-- Existing Vue Modals (ensure they are imported and registered) -->
        <requerimiento-asignacion-modal :especialistas="especialistas"
            @asignacion-guardada="fetchRequerimientos"></requerimiento-asignacion-modal>
        <requerimiento-evaluacion-modal @evaluacion-guardada="fetchRequerimientos"></requerimiento-evaluacion-modal>
        <requerimiento-seguimiento-modal></requerimiento-seguimiento-modal>
        <requerimiento-avance-modal @avance-guardado="fetchRequerimientos"></requerimiento-avance-modal>
        <evidencias-modal></evidencias-modal>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown'; // Added Dropdown import
import { FilterMatchMode } from 'primevue/api';

// Components
import LoadingState from '@/components/generales/LoadingState.vue';

const router = useRouter();

const requerimientos = ref([]);
const especialistas = ref([]);
const statuses = ref([]);
const complejidadOptions = ref(['Baja', 'Media', 'Alta', 'Muy Alta']); // Capitalized for display
const dt = ref(null); // Reference to the DataTable component
const selectedRequerimientoId = ref(null); // Declare selectedRequerimientoId
const loading = ref(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    id: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    'proceso.proceso_nombre': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    asunto: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    complejidad: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    estado: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    'especialista.name': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
});

const serverFilters = ref({
    buscar_requerimiento: '',
    especialista_id: '',
    estado: '',
});

const isMyRequerimientosView = computed(() => router.currentRoute.value.name === 'requerimientos.mine');

const fetchRequerimientos = async () => {
    loading.value = true;
    console.log('fetchRequerimientos called'); // Log to confirm function call
    console.log('serverFilters.value:', serverFilters.value); // Log filter values
    try {
        const response = await axios.get(route('web.requerimientos.data'), {
            params: serverFilters.value
        });
        requerimientos.value = response.data.requerimientos;
        especialistas.value = response.data.especialistas;
        statuses.value = response.data.statuses;
        console.log('Fetched requerimientos:', requerimientos.value);
    } catch (error) {
        console.error('Error fetching requerimientos:', error);
    } finally {
        loading.value = false;
    }
};



const openModal = (eventName, requerimiento) => {
    document.dispatchEvent(new CustomEvent(eventName, {
        detail: requerimiento
    }));
};

const goToCreateRequerimiento = () => {
    router.push({ name: 'requerimientos.create' });
};



const confirmDelete = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este requerimiento?')) {
        // Implement delete logic here, e.g., axios.delete(`/api/requerimientos/${id}`)
        console.log('Deleting requerimiento:', id);
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const exportCSV = () => {
    dt.value.exportCSV();
};

onMounted(() => {
    // Check if 'mine=true' query parameter is present for 'My Requerimientos' view
    const route = useRouter().currentRoute.value;
    if (route.query.mine === 'true' && window.App && window.App.user && window.App.user.id) {
        filters.value.user_id = window.App.user.id;
    }
    fetchRequerimientos();
});
// Script logic will go here
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
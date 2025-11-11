<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link :to="{ name: 'requerimientos.index' }">Inicio</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Mis Requerimientos</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">Mis Requerimientos</h3>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="#" class="btn btn-primary btn-sm ml-1"
                                    @click.prevent="goToCreateRequerimiento">
                                    <i class="fas fa-plus-circle"></i> Nuevo Requerimiento
                                </a>

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
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <DataTable ref="dt" :value="requerimientos" v-model:filters="filters" paginator :rows="10"
                            :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                            :globalFilterFields="['id', 'proceso.proceso_nombre', 'asunto', 'complejidad', 'estado', 'especialista.name']">
                            <template #header>
                                <div class="d-flex align-items-center">
                                    <Button type="button" icon="pi pi-download" label="Descargar CSV"
                                        severity="secondary" @click="exportCSV($event)"  class="btn btn-secondary ml-auto" >
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
                            <Column header="Acciones" :exportable="false" style="width:15%" headerStyle="width: 15%"
                                bodyStyle="width: 15%">
                                <template #body="{ data }">
                                    <template v-if="!isMyRequerimientosView">
                                        <a href="#" title="Evaluar Requerimiento"
                                            class="mr-1 d-inline-block btn-modal-trigger"
                                            @click.prevent="openModal('mostrarEvaluacion', data)">
                                            <i class="fas fa-clipboard-check text-primary"></i>
                                        </a>
                                        <a href="#" title="Asignar Requerimiento"
                                            class="mr-1 d-inline-block btn-modal-trigger"
                                            @click.prevent="openModal('mostrarAsignacion', data)">
                                            <i class="fas fa-user-check text-dark"></i>
                                        </a>
                                        <a href="#" title="Registrar Avance Requerimiento"
                                            class="mr-1 d-inline-block btn-modal-trigger"
                                            @click.prevent="openModal('abrirAvanceRequerimientoModal', data)">

                                            <i class="fas fa-list-alt text-dark"></i>
                                        </a>
                                    </template>
                                    <a href="#" title="Ver Avance Requerimiento"
                                        class="mr-1 d-inline-block btn-modal-trigger"
                                        @click.prevent="openModal('mostrarSeguimiento', data)">
                                        <i class="fas fa-stream text-success"></i>
                                    </a>
                                    <a href="#" title="Editar Requerimiento"
                                        class="mr-1 d-inline-block btn-modal-trigger"
                                        @click.prevent="data.estado === 'creado' && editRequerimiento(data.id)"
                                        :class="{ 'disabled': data.estado !== 'creado' }">
                                        <i class="fas fa-pencil-alt"
                                            :class="{ 'text-warning': data.estado === 'creado', 'text-secondary': data.estado !== 'creado' }"></i>
                                    </a>
                                    <a href="#" title="Eliminar Requerimiento"
                                        class="mr-1 d-inline-block btn-modal-trigger"
                                        @click.prevent="data.estado === 'creado' && confirmDelete(data.id)"
                                        :class="{ 'disabled': data.estado !== 'creado' }">
                                        <i class="fas fa-trash-alt"
                                            :class="{ 'text-danger': data.estado === 'creado', 'text-secondary': data.estado !== 'creado' }"></i>
                                    </a>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <!-- Existing Vue Modals (ensure they are imported and registered) -->

        <requerimiento-seguimiento-modal></requerimiento-seguimiento-modal>

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
import Dropdown from 'primevue/dropdown';
import { FilterMatchMode } from 'primevue/api';

const router = useRouter();

const requerimientos = ref([]);
const especialistas = ref([]); // Keep this as it might be used in modals or future filters
const statuses = ref([]);
const complejidadOptions = ref(['Baja', 'Media', 'Alta', 'Muy Alta']);
const dt = ref(null); // Reference to the DataTable component
const selectedRequerimientoId = ref(null); // Declare selectedRequerimientoId

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
    especialista_id: '', // Keep this for consistency, even if not used in form
    estado: '',
});

const isMyRequerimientosView = computed(() => true); // Always true for this component

const fetchRequerimientos = async () => {
    console.log('fetchRequerimientos called');
    console.log('serverFilters.value:', serverFilters.value);
    try {
        const response = await axios.get(route('web.requerimientos.data'), {
            params: { ...serverFilters.value, mine: true }
        });
        requerimientos.value = response.data.requerimientos;
        // especialistas.value = response.data.especialistas; // This component doesn't use specialists in its form
        statuses.value = response.data.statuses;
        console.log('Fetched requerimientos:', requerimientos.value);
    } catch (error) {
        console.error('Error fetching requerimientos:', error);
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

const editRequerimiento = (id) => {
    router.push({ name: 'requerimientos.edit', params: { id: id } });
};



const confirmDelete = async (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este requerimiento?')) {
        try {
            await axios.delete(route('web.requerimientos.destroy', id));
            fetchRequerimientos(); // Refresh the list after deletion
        } catch (error) {
            console.error('Error deleting requerimiento:', error);
            alert('Error al eliminar el requerimiento.');
        }
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
    fetchRequerimientos();
});
</script>
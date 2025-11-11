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
                <form @submit.prevent="fetchHallazgos">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" v-model="serverFilters.descripcion" class="form-control"
                                placeholder="Buscar por descripción o resumen...">
                        </div>
                        <div class="col-md-4">
                            <input type="text" v-model="serverFilters.proceso" class="form-control"
                                placeholder="Buscar por Proceso">
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex align-items-stretch gap-1">
                                <select v-model="serverFilters.clasificacion" class="form-control">
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
                <DataTable ref="dt" :value="hallazgos" v-model:filters="filters" paginator :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                    :globalFilterFields="['hallazgo_cod', 'hallazgo_clasificacion', 'hallazgo_resumen', 'procesos.proceso_nombre', 'hallazgo_estado']">
                    <template #header>
                        <div class="d-flex align-items-center">
                            <Button type="button" icon="pi pi-download" label="Descargar CSV" severity="secondary"
                                @click="exportCSV($event)" class="btn btn-secondary ml-auto">
                            </Button>
                        </div>
                    </template>
                    <Column field="hallazgo_cod" header="Código" style="width:10%">
                    </Column>
                    <Column field="hallazgo_clasificacion" header="Clasificación" style="width:10%">
                    </Column>
                    <Column field="hallazgo_resumen" header="Resumen" style="width:25%" sortable>
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                class="p-column-filter" placeholder="Buscar por Resumen" />
                        </template>
                    </Column>
                    <Column field="procesos" header="Proceso" style="width:15%">
                        <template #body="{ data }">
                            <template v-if="data.procesos && Array.isArray(data.procesos) && data.procesos.length > 0">
                                {{data.procesos.map(proceso => proceso.proceso_nombre).join(', ')}}
                            </template>
                            <template v-else>
                                <em>No asignado</em>
                            </template>
                        </template>
                    </Column>
                    <Column field="hallazgo_fecha_identificacion" header="F. Identificación" style="width:10%" sortable>
                        <template #body="{ data }">
                            {{ formatDate(data.hallazgo_fecha_identificacion) }}
                        </template>
                    </Column>
                    <Column field="hallazgo_fecha_asignacion" header="F. Asignación" style="width:10%" sortable>
                        <template #body="{ data }">
                            {{ formatDate(data.hallazgo_fecha_asignacion) }}
                        </template>
                    </Column>
                    <Column field="hallazgo_estado" header="Estado" style="width:10%" sortable>
                        <template #filter="{ filterModel, filterCallback }">
                            <Dropdown v-model="filterModel.value"
                                :options="['creado', 'asignado', 'desestimado', 'en proceso', 'concluido', 'evaluado', 'cerrado']"
                                placeholder="Seleccionar" class="p-column-filter" :showClear="true"
                                @change="filterCallback()">
                                <template #option="slotProps">
                                    <span :class="'customer-badge status-' + slotProps.option">{{ slotProps.option
                                        }}</span>
                                </template>
                            </Dropdown>
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:10%" headerStyle="width: 10%"
                        bodyStyle="width: 10%">
                        <template #body="{ data }">
                            <a href="#" title="Editar Hallazgo" class="mr-3 d-inline-block"
                                @click.prevent="editHallazgo(data)">
                                <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                            </a>
                            <a href="#" title="Eliminar Hallazgo" class="mr-3 d-inline-block"
                                @click.prevent="deleteHallazgo(data.id)">
                                <i class="fas fa-trash-alt text-danger fa-lg"></i>
                            </a>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>
    <HallazgoModal></HallazgoModal>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, reactive } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import { FilterMatchMode } from 'primevue/api';

import HallazgoModal from '@/components/hallazgos/HallazgoModal.vue';
import { useHallazgoStore } from '@/stores/hallazgoStore'; // Importa la tienda

const hallazgos = ref([]);
const selectedHallazgo = ref(null);
const successMessage = ref('');
const errorMessage = ref('');
const hallazgoStore = useHallazgoStore();
const dt = ref(null); // Reference to the PrimeVue DataTable component

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    hallazgo_cod: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    hallazgo_clasificacion: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    hallazgo_resumen: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    'procesos.proceso_nombre': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    hallazgo_fecha_identificacion: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    hallazgo_fecha_asignacion: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    hallazgo_estado: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
});

const serverFilters = reactive({
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
            params: serverFilters
        });
        hallazgos.value = response.data;
    } catch (error) {
        console.error('Error al obtener los hallazgos:', error);
        errorMessage.value = 'Hubo un problema al cargar los hallazgos.';
    } finally {
        isLoading.value = false;
    }
};

const exportCSV = () => {
    dt.value.exportCSV();
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
    // No specific cleanup needed for PrimeVue DataTable beyond component unmount
});


</script>
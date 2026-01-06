<template>
    <div class="container-fluid">
        <div v-if="successMessage" class="alert alert-success" id="success-alert">
            {{ successMessage }}
        </div>
        <div v-if="errorMessage" class="alert alert-danger" id="error-alert">
            {{ errorMessage }}
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
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
                        <a href="#" class="btn btn-primary" @click.prevent="openNewHallazgoModal"
                            title="Nuevo Hallazgo">
                            <i class="fas fa-plus-circle"></i> Agregar Solicitud
                        </a>
                    </div>
                </div>
                <br></br>
                <form @submit.prevent="fetchHallazgos">
                    <div class="form-row">
                        <div class="col">
                            <input type="text" v-model="serverFilters.descripcion" class="form-control"
                                placeholder="Buscar por descripción o resumen...">
                        </div>
                        <div class="col">
                            <input type="text" v-model="serverFilters.proceso" class="form-control"
                                placeholder="Buscar por Proceso">
                        </div>

                        <div class="col">
                            <select v-model="serverFilters.clasificacion" class="form-control">
                                <option value="">Todas las Clasificaciones</option>
                                <option value="NCM">No Conformidad Mayor</option>
                                <option value="Ncme">No Conformidad Menor</option>
                                <option value="Obs">Observación</option>
                                <option value="Odm">Oportunidad de Mejora</option>
                            </select>
                        </div>
                        <div class="col">
                            <select v-model="serverFilters.estado" class="form-control">
                                <option value="">Todos los Estados</option>
                                <option value="creado">Creado</option>
                                <option value="asignado">Asignado</option>
                                <option value="desestimado">Desestimado</option>
                                <option value="en proceso">En Proceso</option>
                                <option value="concluido">Concluido</option>
                                <option value="evaluado">Evaluado</option>
                                <option value="cerrado">Cerrado</option>
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

            <div class="card-body">
                <!-- Loading State - Spinner circular rojo -->
                <DataTable ref="dt" :value="hallazgos" v-model:filters="filters" paginator :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                    :globalFilterFields="['hallazgo_cod', 'hallazgo_clasificacion', 'hallazgo_resumen', 'procesos.proceso_nombre', 'hallazgo_estado']"
                    :loading="isLoading">
                    <template #header>
                        <div class="d-flex align-items-center">
                            <Button type="button" icon="pi pi-download" label="Descargar CSV" severity="secondary"
                                @click="exportCSV($event)" class="btn btn-secondary ml-auto">
                            </Button>
                        </div>
                    </template>
                    <Column field="hallazgo_cod" header="Código" style="width:10%">
                    </Column>
                    <Column field="hallazgo_clasificacion" header="Clasificación" style="width:8%">
                    </Column>
                    <Column field="hallazgo_resumen" header="Hallazgo" style="width:35%">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                class="p-column-filter" placeholder="Buscar por Resumen" />
                        </template>
                    </Column>
                    <Column field="procesos" header="Proceso" style="width:20%">
                        <template #body="{ data }">
                            <template v-if="data.procesos && Array.isArray(data.procesos) && data.procesos.length > 0">
                                {{data.procesos.map(proceso => proceso.proceso_nombre).join(', ')}}
                            </template>
                            <template v-else>
                                <em>No asignado</em>
                            </template>
                        </template>
                    </Column>
                    <Column field="hallazgo_fecha_identificacion" header="F. Identificación" style="width:10%">
                        <template #body="{ data }">
                            {{ formatDate(data.hallazgo_fecha_identificacion) }}
                        </template>
                    </Column>
                    <Column field="hallazgo_fecha_conclusion" header="F. Conclusión" style="width:10%" sortable>
                        <template #body="{ data }">
                            {{ formatDate(data.hallazgo_fecha_conclusion) }}
                        </template>
                    </Column>
                    <Column field="hallazgo_estado" header="Estado" style="width:10%; text-align: center;">
                        <template #body="{ data }">
                            {{ data.hallazgo_estado }}
                        </template>
                    </Column>
                    <Column header="Total Acciones" style="width:10%; text-align: center;">
                        <template #body="{ data }">
                            {{ data.acciones ? data.acciones.length : 0 }}
                        </template>
                    </Column>
                    <Column header="Acciones Pendientes" style="width:10%; text-align: center;">
                        <template #body="{ data }">
                            {{ getAccionesPendientes(data.acciones) }}
                        </template>
                    </Column>


                    <Column field="hallazgo_avance" header="Avance" sortable>
                        <template #body="{ data }">
                            <template v-if="['creado', 'asignado', 'desestimado'].includes(data.hallazgo_estado)">
                                <span class="small text-muted">Sin avance</span>
                            </template>
                            <template v-else-if="data.hallazgo_avance">
                                <div class="small text-center">
                                    {{ parseInt(data.hallazgo_avance) }}%
                                    <div class="progress progress-xs">
                                        <div class="progress-bar bg-info"
                                            :style="{ width: parseInt(data.hallazgo_avance) + '%' }">
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <span class="small text-muted">Sin avance</span>
                            </template>
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:10%" headerStyle="width: 10%"
                        bodyStyle="width: 10%">
                        <template #body="{ data }">
                            <a href="#" title="Editar Hallazgo" class="mr-3 d-inline-block"
                                @click.prevent="editHallazgo(data)">
                                <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                            </a>
                            <a href="#" title="Planes de Acción" class="mr-3 d-inline-block"
                                @click.prevent="verPlanesDeAccion(data.id)">
                                <i class="fas fa-tasks text-info fa-lg"></i>
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
import { onMounted, onBeforeUnmount, ref, reactive, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { route } from 'ziggy-js';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

import { FilterMatchMode } from 'primevue/api';

import HallazgoModal from '@/components/hallazgos/HallazgoModal.vue';
import LoadingState from '@/components/generales/LoadingState.vue';
import { useHallazgoStore } from '@/stores/hallazgoStore'; // Importa la tienda

const router = useRouter();
const routeData = useRoute(); // Get current route
const hallazgos = ref([]);
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
    clasificacion: '',
    estado: ''
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

const getAccionesPendientes = (acciones) => {
    if (!acciones) return 0;
    return acciones.filter(accion => ['programada', 'en ejecucion'].includes(accion.accion_estado.toLowerCase())).length;
};

// Determinar si estamos en la bandeja basada en OUO
const isFromOuo = computed(() => {
    // Verificar si la ruta actual es 'smp.ouo.index' o si hay un parámetro especial
    return routeData.name === 'smp.ouo.index' || routeData.params.fromOuo === 'true';
});

// Métodos
const fetchHallazgos = async () => {
    isLoading.value = true;
    try {
        let response;
        if (isFromOuo.value) {
            // Usar la nueva ruta para obtener hallazgos basados en OUO del usuario
            response = await axios.get(route('smp.ouo'), {
                params: serverFilters
            });
        } else {
            // Usar la ruta original para obtener todos los hallazgos
            response = await axios.get(route('api.hallazgos'), {
                params: serverFilters
            });
        }
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

const editHallazgo = (hallazgo) => {
    hallazgoStore.openModal(hallazgo);
};

const verPlanesDeAccion = (hallazgoId) => {
    router.push({ name: 'acciones.index', params: { hallazgoId } });
};

const deleteHallazgo = async () => {
    // ...
};

// --- CICLO DE VIDA ---
onMounted(() => {
    fetchHallazgos();
    window.addEventListener('hallazgos-actualizados', fetchHallazgos); // Add event listener
});

onBeforeUnmount(() => {
    window.removeEventListener('hallazgos-actualizados', fetchHallazgos); // Remove event listener
    // No specific cleanup needed for PrimeVue DataTable beyond component unmount
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
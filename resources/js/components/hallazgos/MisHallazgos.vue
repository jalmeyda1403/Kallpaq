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
                <li class="breadcrumb-item active" aria-current="page">Mis Hallazgos</li>
            </ol>
        </nav>

        <!-- Modal para enviar plan de acción -->
        <EnviarPlanAccionModal
            :visible="modalEnviarPlanVisible"
            :hallazgoId="hallazgoIdSeleccionado"
            @cerrar="cerrarModalEnviarPlan"
            @plan-enviado="onPlanEnviado" />

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Mis Hallazgos</h3>
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
                            <select v-model="serverFilters.proceso_id" class="form-control select-truncate">
                                <option value="">Todos los Procesos</option>
                                <option v-for="proceso in procesos" :key="proceso.id" :value="proceso.id" :title="proceso.proceso_nombre">
                                    {{ proceso.proceso_nombre }}
                                </option>
                            </select>
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
                                <option value="aprobado">Aprobado</option>
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
                    <Column field="hallazgo_fecha_identificacion" header="F. Ident." style="width:10%">
                        <template #body="{ data }">
                            {{ formatDate(data.hallazgo_fecha_identificacion) }}
                        </template>
                    </Column>
                    <Column field="hallazgo_fecha_conclusion" header="F. Conc." style="width:10%">
                        <template #body="{ data }">
                            {{ formatDate(data.hallazgo_fecha_conclusion) }}
                        </template>
                    </Column>
                    <Column field="hallazgo_estado" header="Estado" style="width:10%; text-align: center;" >
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
                    <Column header="Acciones" :exportable="false" style="width:15%" headerStyle="width: 15%"
                        bodyStyle="width: 15%">
                        <template #body="{ data }">
                            
                            <a href="#" title="Planes de Acción" class="mr-2 d-inline-block"
                                @click.prevent="verPlanesDeAccion(data.id)">
                                <i class="fas fa-tasks text-info fa-lg"></i>
                            </a>
                            <a href="#" title="Enviar Plan de Acción" class="mr-2 d-inline-block"
                                @click.prevent="abrirModalEnviarPlan(data.id)"
                                v-if="data.hallazgo_estado === 'en proceso' || data.hallazgo_estado === 'creado'">
                                <i class="fas fa-paper-plane text-success fa-lg"></i>
                            </a>
                            <a href="#" title="Eliminar Hallazgo" class="mr-2 d-inline-block"
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

<style scoped>
.select-truncate option {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}

.select-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>

<script setup>
import { onMounted, onBeforeUnmount, ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { route } from 'ziggy-js';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

import { FilterMatchMode } from 'primevue/api';

import HallazgoModal from '@/components/hallazgos/HallazgoModal.vue';
import EnviarPlanAccionModal from '@/components/hallazgos/EnviarPlanAccionModal.vue';
import { useHallazgoStore } from '@/stores/hallazgoStore'; // Importa la tienda

const router = useRouter();
const hallazgos = ref([]);
const procesos = ref([]);
const successMessage = ref('');
const errorMessage = ref('');
const hallazgoStore = useHallazgoStore();
const dt = ref(null); // Reference to the PrimeVue DataTable component

// Variables para el modal de envío de plan
const modalEnviarPlanVisible = ref(false);
const hallazgoIdSeleccionado = ref(null);

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
    proceso_id: '',
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

// Métodos
const fetchHallazgos = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('hallazgos.mine'), {
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

const fetchProcesos = async () => {
    try {
        const response = await axios.get(route('procesos.listar'));
        procesos.value = response.data;
    } catch (error) {
        console.error('Error al obtener los procesos:', error);
    }
};

const exportCSV = () => {
    dt.value.exportCSV();
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

const abrirModalEnviarPlan = (hallazgoId) => {
    hallazgoIdSeleccionado.value = hallazgoId;
    modalEnviarPlanVisible.value = true;
};

const cerrarModalEnviarPlan = () => {
    modalEnviarPlanVisible.value = false;
    hallazgoIdSeleccionado.value = null;
};

const onPlanEnviado = () => {
    // Actualizar la lista de hallazgos para reflejar el cambio de estado
    fetchHallazgos();
    successMessage.value = 'Plan de acción enviado exitosamente. El estado del hallazgo ha sido actualizado a "Aprobado".';

    // Ocultar el mensaje después de unos segundos
    setTimeout(() => {
        successMessage.value = '';
    }, 5000);
};

// --- CICLO DE VIDA ---
onMounted(() => {
    fetchHallazgos();
    fetchProcesos();
    window.addEventListener('hallazgos-actualizados', fetchHallazgos); // Add event listener
});

onBeforeUnmount(() => {
    window.removeEventListener('hallazgos-actualizados', fetchHallazgos); // Remove event listener
    // No specific cleanup needed for PrimeVue DataTable beyond component unmount
});


</script>
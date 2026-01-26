<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/home"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Mis SMP Asignadas</li>
            </ol>
        </nav>
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title">Mis SMP Asignadas</h3>
                    </div>
                </div>

                <form @submit.prevent="fetchHallazgos">
                    <div class="form-row">
                        <div class="col">
                            <input type="text" v-model="serverFilters.descripcion" class="form-control"
                                placeholder="Buscar por descripción o resumen...">
                        </div>
                        <div class="col">
                            <select v-model="serverFilters.proceso_id" class="form-control select-truncate">
                                <option value="">Todos los Procesos</option>
                                <option v-for="proceso in procesos" :key="proceso.id" :value="proceso.id"
                                    :title="proceso.proceso_nombre">
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
                <br></br>
            </div>
            <div class="card-body">


                <div class="h-1 mb-2">
                    <ProgressBar v-if="loading" mode="indeterminate" style="height: 4px;" />
                </div>

                <DataTable :value="hallazgos" :paginator="true" :rows="10" :filters="filters"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 25, 50]"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} hallazgos"
                    responsiveLayout="scroll" :class="{ 'opacity-50 pointer-events-none': loading }">

                    <Column field="hallazgo_cod" header="Código" sortable></Column>
                    <Column field="hallazgo_resumen" header="Resumen" sortable></Column>
                    <Column field="procesos" header="Proceso">
                        <template #body="slotProps">
                            <span v-for="(proceso, index) in slotProps.data.procesos" :key="index">
                                {{ proceso.proceso_nombre }}<span v-if="index < slotProps.data.procesos.length - 1">,
                                </span>
                            </span>
                        </template>
                    </Column>
                    <Column field="hallazgo_fecha_conclusion" header="F. Conclusión" sortable>
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.hallazgo_fecha_conclusion) }}
                        </template>
                    </Column>
                    <Column field="hallazgo_estado" header="Estado" sortable>
                        <template #body="{ data }">
                            {{ data.hallazgo_estado }}
                        </template>
                    </Column>
                    <Column field="especialista.name" header="Especialista Asignado" sortable></Column>
                    <Column header="Avance Acciones" style="width:12%; text-align: center;">
                        <template #body="{ data }">
                            <span v-if="data.acciones && data.acciones.length > 0">
                                {{ data.acciones.length - getAccionesPendientes(data.acciones) }}/{{
                                    data.acciones.length }}
                            </span>
                            <span v-else class="text-muted small">-</span>
                            <span v-if="hasPendingReprogramming(data.acciones)" class="badge badge-warning ml-2"
                                title="Solicitudes de reprogramación pendientes">
                                <i class="fas fa-clock"></i>
                            </span>
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
                    <Column header="Acciones" style="width: 200px">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-around align-items-center">
                                <!-- Visualizar SMP -->
                                <button class="btn btn-link p-0 text-dark" title="Visualizar SMP"
                                    @click.prevent="verHallazgo(slotProps.data)">
                                    <i class="fas fa-eye fa-lg"></i>
                                </button>

                                <!-- Ver Plan de Acción -->
                                <a href="#" title="Ver Plan de Acción" class="text-danger"
                                    @click.prevent="verPlanAccion(slotProps.data.id)">
                                    <i class="fas fa-tasks fa-lg"></i>
                                </a>

                                <!-- Verificar Eficacia -->
                                <button class="btn btn-link p-0"
                                    :class="slotProps.data.hallazgo_estado === 'concluido' ? 'text-success' : 'text-muted'"
                                    :title="slotProps.data.hallazgo_estado === 'concluido' ? 'Verificar Eficacia' : 'La eficacia solo se puede verificar cuando el plan está concluido'"
                                    :disabled="slotProps.data.hallazgo_estado !== 'concluido'"
                                    @click.prevent="verificarEficacia(slotProps.data)">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </button>
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        No se encontraron hallazgos pendientes de verificación.
                    </template>
                </DataTable>
            </div>
        </div>

        <VerificarEficaciaModal :visible="modalVisible" :hallazgo="selectedHallazgo" :initialTab="initialTab"
            @cerrar="modalVisible = false" @guardado="onEvaluacionGuardada" @archivos-subidos="onArchivosSubidos" />

        <HallazgoModal />
    </div>
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
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ProgressBar from 'primevue/progressbar';
import { FilterMatchMode } from 'primevue/api';
import VerificarEficaciaModal from './VerificarEficaciaModal.vue';
import HallazgoModal from './HallazgoModal.vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';

const router = useRouter();
const hallazgoStore = useHallazgoStore();

const hallazgos = ref([]);
const loading = ref(true);
const modalVisible = ref(false);
const selectedHallazgo = ref(null);
const initialTab = ref('evidencias'); // New state for tab control

const procesos = ref([]);

const filters = reactive({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const serverFilters = reactive({
    descripcion: '',
    proceso_id: '',
    clasificacion: '',
    estado: ''
});

const fetchHallazgos = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('hallazgo.eficacia.listar'), {
            params: serverFilters
        });
        hallazgos.value = response.data;
    } catch (error) {
        console.error('Error al cargar hallazgos:', error);
    } finally {
        loading.value = false;
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

const verificarEficacia = (hallazgo) => {
    selectedHallazgo.value = hallazgo;
    initialTab.value = 'evaluacion';
    modalVisible.value = true;
};

const verHallazgo = (hallazgo) => {
    hallazgoStore.openViewOnlyModal(hallazgo);
};



const verPlanAccion = (id) => {
    router.push({ name: 'acciones.ejecucion', params: { hallazgoId: id }, query: { from: 'hallazgos.eficacia' } });
};

const onEvaluacionGuardada = () => {
    fetchHallazgos();
};

const onArchivosSubidos = () => {
    // Opcional: Recargar si fuera necesario
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('es-ES');
};

const getAccionesPendientes = (acciones) => {
    if (!acciones) return 0;
    return acciones.filter(accion => ['programada', 'en ejecucion'].includes(accion.accion_estado.toLowerCase())).length;
};

const hasPendingReprogramming = (acciones) => {
    if (!acciones) return false;
    return acciones.some(accion => accion.reprogramaciones && accion.reprogramaciones.some(r => r.ar_estado === 'pendiente'));
};

onMounted(() => {
    fetchHallazgos();
    fetchProcesos();
});
</script>
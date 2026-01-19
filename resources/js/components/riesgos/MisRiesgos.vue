<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Mis Riesgos Asignados</li>
            </ol>
        </nav>

        <div class="card border-top-danger shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h5 class="card-title text-danger mb-0 font-weight-bold">
                            <i class="fas fa-user-shield mr-2"></i>Mis Riesgos (Especialista)
                        </h5>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-dark btn-sm ml-1 shadow-sm" @click="showMapaCalor = true">
                            <i class="fas fa-th mr-1"></i> Mapa de Calor
                        </button>
                        <button class="btn btn-danger btn-sm ml-1 shadow-sm" @click="store.openModal(null)">
                            <i class="fas fa-plus-circle mr-1"></i> Nuevo Riesgo
                        </button>
                    </div>
                </div>
                <!-- Buscador -->
                <div class="mt-3 p-3 bg-light rounded border">
                    <form @submit.prevent="search">
                        <div class="form-row">
                            <div class="col-md-2 mb-2">
                                <input type="text" v-model="localFilters.codigo" class="form-control border-0"
                                    placeholder="Buscar código/proc...">
                            </div>
                            <div class="col-md-3 mb-2">
                                <input type="text" v-model="localFilters.nombre" class="form-control border-0"
                                    placeholder="Buscar por nombre...">
                            </div>
                            <div class="col-md-2 mb-2">
                                <select v-model="localFilters.factor" class="form-control border-0">
                                    <option value="">Factor: Todos</option>
                                    <option value="1">Estratégico</option>
                                    <option value="2">Operacional</option>
                                    <option value="3">Corrupción</option>
                                    <option value="4">Cumplimiento</option>
                                    <option value="5">Reputacional</option>
                                    <option value="6">Ambiental</option>
                                    <option value="7">Seguridad</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <select v-model="localFilters.nivel" class="form-control border-0">
                                    <option value="">Nivel: Todos</option>
                                    <option value="Muy Alto">Muy Alto</option>
                                    <option value="Alto">Alto</option>
                                    <option value="Medio">Medio</option>
                                    <option value="Bajo">Bajo</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2 text-right">
                                <button type="submit" class="btn btn-dark btn-sm btn-block">
                                    <i class="fas fa-search mr-1"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body p-0">
                <LoadingState :loading="loading" />

                <div v-if="!loading" class="animate-fade-in">
                    <DataTable ref="dt" :value="riesgos" :paginator="true" :rows="10" :loading="false"
                        :rowsPerPageOptions="[5, 10, 20, 50]"
                        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} riesgos"
                        responsiveLayout="scroll" class="p-datatable-sm table-hover" stripedRows>
        
                        <template #header>
                            <div class="d-flex align-items-center justify-content-end pb-2">
                                <Button type="button" icon="pi pi-download" label="Exportar" severity="secondary"
                                    @click="exportCSV($event)" class="p-button-outlined p-button-secondary p-button-sm">
                                </Button>
                            </div>
                        </template>

                        <Column field="id" header="#" sortable style="width:5%">
                             <template #body="{ index }">
                                <span class="text-muted small">{{ index + 1 }}</span>
                            </template>
                        </Column>
                        <Column field="proceso.proceso_nombre" header="Proceso" sortable style="width:20%">
                            <template #body="{ data }">
                                <span class="font-weight-500 text-dark">{{ data.proceso?.proceso_nombre || 'N/A' }}</span>
                            </template>
                        </Column>
                        <Column field="riesgo_nombre" header="Descripción del Riesgo" sortable style="width:30%">
                             <template #body="{ data }">
                                <div class="d-flex flex-column">
                                    <strong class="text-dark small">{{ data.riesgo_cod }}</strong>
                                    <span class="text-secondary small text-justify">{{ data.riesgo_nombre }}</span>
                                </div>
                            </template>
                        </Column>
                        <Column field="factor.nombre" header="Factor" sortable style="width:10%">
                            <template #body="{ data }">
                                <span class="badge badge-light border text-muted">{{ data.factor?.nombre || 'General' }}</span>
                            </template>
                        </Column>
                        <Column field="riesgo_nivel" header="Nivel" sortable style="width:10%">
                            <template #body="{ data }">
                                <span :class="['badge', 'p-2', getBadgeClass(data.riesgo_nivel)]">
                                    {{ data.riesgo_nivel }}
                                </span>
                            </template>
                        </Column>
                        <Column header="Avance Acciones" style="width:10%" class="text-center">
                            <template #body="{ data }">
                                <a href="#" @click.prevent="store.openAccionesModal(data)"
                                    class="font-weight-bold text-dark" title="Gestionar Avance">
                                    {{ data.acciones_completadas_count }} / {{ data.acciones_total_count }}
                                </a>
                                <i v-if="data.reprogramaciones_pendientes_count > 0"
                                    class="fas fa-exclamation-triangle text-warning ml-1"
                                    title="Tiene reprogramaciones pendientes de aprobación"></i>
                            </template>
                        </Column>
                        <Column header="% Avance" style="width:10%">
                            <template #body="{ data }">
                                <div class="d-flex align-items-center flex-column">
                                    <span class="small font-weight-bold mb-1">{{ calculateProgress(data) }}%</span>
                                    <div class="progress progress-xs w-100 rounded-pill" style="height: 4px;">
                                        <div class="progress-bar rounded-pill"
                                            :class="calculateProgress(data) >= 100 ? 'bg-success' : 'bg-primary'"
                                            :style="{ width: calculateProgress(data) + '%' }">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Column>
                        <Column field="riesgo_estado" header="Estado" sortable style="width:5%">
                             <template #body="{ data }">
                                <span class="badge badge-pill badge-light border">{{ data.riesgo_estado }}</span>
                            </template>
                        </Column>
                        <Column header="Acciones" :exportable="false" style="width:10%" class="text-center">
                            <template #body="{ data }">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-light text-warning" @click.prevent="store.openModal(data)" title="Editar">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button class="btn btn-light text-info" @click.prevent="openAccionesModal(data)" title="Plan de Acción">
                                        <i class="fas fa-list"></i>
                                    </button>
                                    <button class="btn btn-light text-danger" @click.prevent="confirmDelete(data)" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <RiesgoModal />

    <!-- Modal for Heat Map -->
    <Teleport to="body">
        <div v-if="showMapaCalor" class="modal fade show" tabindex="-1" style="display: block; overflow-y: auto; background-color: rgba(0,0,0,0.5);"
            aria-labelledby="mapaCalorModalLabel" aria-hidden="false">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="mapaCalorModalLabel">
                            <i class="fas fa-th mr-2"></i>Mapa de Calor de Riesgos
                        </h5>
                        <button type="button" class="close text-white" @click="showMapaCalor = false">
                            <span class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <MapaCalor :riesgos="riesgos" />
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary btn-sm" @click="showMapaCalor = false">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>

</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import MapaCalor from './MapaCalor.vue';
import RiesgoModal from './RiesgoModal.vue';
import LoadingState from '@/components/generales/LoadingState.vue';

// PrimeVue
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import ProgressBar from 'primevue/progressbar';

const store = useRiesgoStore();
const riesgos = computed(() => store.riesgos);
const loading = computed(() => store.loading);
const dt = ref(null);
const showMapaCalor = ref(false);
const selectedRiesgo = ref(null);

const calculateProgress = (data) => {
    const total = data.acciones_total_count || 0;
    const completed = data.acciones_completadas_count || 0;
    if (total === 0) return 0;
    return Math.round((completed / total) * 100);
};

const localFilters = ref({
    codigo: store.filters.codigo,
    nombre: store.filters.nombre,
    factor: store.filters.factor,
    tipo: store.filters.tipo,
    nivel: store.filters.nivel,
    matriz: store.filters.matriz
});

const search = () => {
    store.filters = { ...localFilters.value };
    store.fetchMisRiesgos({ scope: 'specialist' });
};

onMounted(() => {
    store.fetchMisRiesgos({ scope: 'specialist' });
});

const refreshList = () => {
    store.fetchMisRiesgos({ scope: 'specialist' });
};

const openAccionesModal = (riesgo) => {
    store.openModal(riesgo);
    store.setCurrentTab('RiesgoAcciones');
};

const confirmDelete = async (riesgo) => {
    const result = await Swal.fire({
        title: '¿Eliminar Riesgo?',
        text: `Se eliminará el riesgo ${riesgo.riesgo_cod}. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await store.deleteRiesgo(riesgo.id);
            Swal.fire('Eliminado', 'El riesgo ha sido eliminado.', 'success');
        } catch (error) {
            Swal.fire('Error', 'No se pudo eliminar el riesgo.', 'error');
        }
    }
};

const exportCSV = () => {
    dt.value.exportCSV();
};

const getBadgeClass = (nivel) => {
    if (!nivel) return 'badge-secondary';
    const n = nivel.toLowerCase();
    if (n === 'muy alto') return 'badge-danger';
    if (n === 'alto') return 'badge-warning text-dark';
    if (n === 'medio') return 'badge-info text-white';
    return 'badge-success';
};
</script>

<style scoped>
.border-top-danger {
    border-top: 3px solid #dc3545 !important;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.badge-lg {
    font-size: 0.85em;
    font-weight: 600;
}
</style>

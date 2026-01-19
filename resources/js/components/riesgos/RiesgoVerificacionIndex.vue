<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Verificación de Eficacia (Mis Asignaciones)</li>
            </ol>
        </nav>

        <div class="card border-top-danger shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h5 class="card-title text-danger mb-0 font-weight-bold">
                            <i class="fas fa-check-double mr-2"></i>Riesgos Asignados para Verificación
                        </h5>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <!-- No "New Risk" button here as this is for verification of existing risks -->
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
                    <DataTable ref="dt" :value="filteredRiesgos" :paginator="true" :rows="10" :loading="false"
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
                                <span class="font-weight-bold text-dark">
                                    {{ data.acciones_completadas_count }} / {{ data.acciones_total_count }}
                                </span>
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
                                        <div class="progress-bar rounded-pill bg-success"
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
                                <!-- Edit button removed as per requirements -->

                                <!-- Task/Verification Button -->
                                <button class="btn btn-sm btn-light text-info border shadow-xs" @click.prevent="openVerificacionModal(data)"
                                    title="Verificar Eficacia">
                                    <i class="fas fa-check-double mr-1"></i> Verificar
                                </button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <RiesgoModal />

</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
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

const calculateProgress = (data) => {
    const total = data.acciones_total_count || 0;
    const completed = data.acciones_completadas_count || 0;
    if (total === 0) return 0;
    return Math.round((completed / total) * 100);
};

const filteredRiesgos = computed(() => {
    return store.riesgos.filter(riesgo => calculateProgress(riesgo) === 100);
});

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

const openVerificacionModal = (riesgo) => {
    store.openModal(riesgo);
    store.setCurrentTab('RiesgoVerificacionForm'); // Open directly to Verification tab
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

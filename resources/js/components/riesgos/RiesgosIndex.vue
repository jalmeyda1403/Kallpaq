<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Gestión de Riesgos</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Listado de Riesgos</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-info btn-sm ml-1" @click="showMapaCalor = true">
                            <i class="fas fa-th"></i> Mapa de Calor
                        </button>
                        <button class="btn btn-primary btn-sm ml-1" @click="store.openModal(null)">
                            <i class="fas fa-plus-circle"></i> Nuevo Riesgo
                        </button>
                    </div>
                </div>
                <hr>
                <form @submit.prevent="search">
                    <div class="form-row">
                        <div class="col">
                            <input type="text" v-model="localFilters.codigo" class="form-control"
                                placeholder="Buscar por código o proceso...">
                        </div>
                        <div class="col">
                            <input type="text" v-model="localFilters.nombre" class="form-control"
                                placeholder="Buscar por nombre...">
                        </div>
                        <div class="col">
                            <select v-model="localFilters.factor" class="form-control">
                                <option value="">Todos los Factores</option>
                                <option value="1">Estratégico</option>
                                <option value="2">Operacional</option>
                                <option value="3">Corrupción</option>
                                <option value="4">Cumplimiento</option>
                                <option value="5">Reputacional</option>
                                <option value="6">Ambiental</option>
                                <option value="7">Seguridad</option>
                            </select>
                        </div>
                        <div class="col">
                            <select v-model="localFilters.tipo" class="form-control">
                                <option value="">Todos los Tipos</option>
                                <option value="Riesgo">Riesgo</option>
                                <option value="Oportunidad">Oportunidad</option>
                            </select>
                        </div>
                        <div class="col">
                            <select v-model="localFilters.nivel" class="form-control">
                                <option value="">Todos los Niveles</option>
                                <option value="Muy Alto">Muy Alto</option>
                                <option value="Alto">Alto</option>
                                <option value="Medio">Medio</option>
                                <option value="Bajo">Bajo</option>
                            </select>
                        </div>
                        <div class="col">
                            <select v-model="localFilters.matriz" class="form-control">
                                <option value="">Todas las Matrices</option>
                                <option value="Estratégica">Estratégica</option>
                                <option value="Táctica">Táctica</option>
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
                <div class="tab-content" id="riesgosTabsContent">
                    <!-- Tab Listado -->
                    <div class="tab-pane fade show active" id="listado" role="tabpanel" aria-labelledby="listado-tab">
                        <DataTable ref="dt" :value="riesgos" :paginator="true" :rows="10" :loading="loading"
                            :rowsPerPageOptions="[5, 10, 20, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} riesgos"
                            responsiveLayout="scroll">

                            <template #header>
                                <div class="d-flex align-items-center">
                                    <Button type="button" icon="pi pi-download" label="Descargar CSV"
                                        severity="secondary" @click="exportCSV($event)"
                                        class="btn btn-secondary ml-auto">
                                    </Button>
                                </div>
                            </template>

                            <Column field="id" header="ID" sortable style="width:5%"></Column>
                            <Column field="proceso.proceso_nombre" header="Proceso" sortable style="width:20%"></Column>
                            <Column field="riesgo_nombre" header="Descripción del Riesgo" sortable style="width:30%">
                            </Column>
                            <Column field="factor.nombre" header="Factor" sortable style="width:15%"></Column>
                            <Column field="riesgo_nivel" header="Nivel" sortable style="width:15%">
                                <template #body="{ data }">
                                    <span :class="['badge', 'badge-lg', getBadgeClass(data.riesgo_nivel)]">{{
                                        data.riesgo_nivel }}</span>
                                </template>
                            </Column>
                            <Column field="riesgo_estado" header="Estado" sortable style="width:10%"></Column>
                            <Column header="Acciones" :exportable="false" style="width:12%" headerStyle="width: 12%"
                                bodyStyle="width: 12%">
                                <template #body="{ data }">
                                    <a href="#" class="mr-3 d-inline-block" @click.prevent="store.openModal(data)"
                                        title="Editar Riesgo">
                                        <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                                    </a>
                                    <a href="#" class="mr-3 d-inline-block" @click.prevent="openAccionesModal(data)"
                                        title="Plan de Acción">
                                        <i class="fas fa-list text-info fa-lg"></i>
                                    </a>
                                    <a href="#" class="mr-3 d-inline-block" @click.prevent="confirmDelete(data)"
                                        title="Eliminar">
                                        <i class="fas fa-trash-alt text-danger fa-lg"></i>
                                    </a>
                                </template>
                            </Column>
                        </DataTable>
                    </div>

                    <!-- Tab Mapa de Calor -->
                    <div class="tab-pane fade" id="mapa" role="tabpanel" aria-labelledby="mapa-tab">
                        <MapaCalor :riesgos="riesgos" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <RiesgoModal />

    <!-- Modal for Heat Map -->
    <Teleport to="body">
        <div v-if="showMapaCalor" class="modal fade show" tabindex="-1" style="display: block; overflow-y: auto;"
            aria-labelledby="mapaCalorModalLabel" aria-hidden="false">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="mapaCalorModalLabel">
                            <i class="fas fa-th mr-2"></i>Mapa de Calor de Riesgos
                        </h5>
                        <button type="button" class="close text-white" @click="showMapaCalor = false">
                            <span class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <MapaCalor :riesgos="riesgos" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showMapaCalor = false">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showMapaCalor" class="modal-backdrop fade show" style="display: block;"></div>
    </Teleport>

</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import MapaCalor from './MapaCalor.vue';
import RiesgoModal from './RiesgoModal.vue';

// PrimeVue
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const store = useRiesgoStore();
const riesgos = computed(() => store.riesgos);
const loading = computed(() => store.loading);
const dt = ref(null);
const showMapaCalor = ref(false);
const selectedRiesgo = ref(null);

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
    store.fetchMisRiesgos();
};

onMounted(() => {
    store.fetchMisRiesgos();
});

const refreshList = () => {
    store.fetchMisRiesgos();
};

const openAccionesModal = (riesgo) => {
    store.openModal(riesgo);
    store.setCurrentTab('RiesgoAcciones');
};

const confirmDelete = async (riesgo) => {
    if (confirm(`¿Eliminar riesgo ${riesgo.riesgo_cod}?`)) {
        try {
            await store.deleteRiesgo(riesgo.id);
        } catch (error) {
            console.error(error);
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
    if (n === 'alto') return 'badge-orange';
    if (n === 'medio') return 'badge-warning';
    return 'badge-success';
};
</script>

<style scoped>
/* Orange badge for risk level - AdminLTE/Bootstrap doesn't have badge-orange by default */
.badge-orange {
    background-color: #fd7e14;
    color: white;
}

/* Larger badge styles for level indicators */
.badge-lg {
    font-size: 0.9em;
    padding: 0.4em 0.8em;
    font-weight: 600;
}

/* Custom loader styles */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0) !important;
}

.p-datatable-loading-icon {
    color: #dc3545 !important;
    /* Use hex for consistency */
    font-size: 2rem !important;
}
</style>

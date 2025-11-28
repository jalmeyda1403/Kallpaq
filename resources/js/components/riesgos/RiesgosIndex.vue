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
                        <button class="btn btn-primary btn-sm ml-1" @click="openRiesgoModal(null)">
                            <i class="fas fa-plus-circle"></i> Nuevo Riesgo
                        </button>
                    </div>
                </div>
                <hr>
                <form @submit.prevent="store.fetchMisRiesgos">
                    <div class="form-row">
                        <div class="col">
                            <input type="text" v-model="store.filters.codigo" class="form-control"
                                placeholder="Buscar por código...">
                        </div>
                        <div class="col">
                            <input type="text" v-model="store.filters.nombre" class="form-control"
                                placeholder="Buscar por nombre...">
                        </div>
                        <div class="col">
                            <select v-model="store.filters.valoracion" class="form-control">
                                <option value="">Todas las Valoraciones</option>
                                <option value="Muy Alto">Muy Alto</option>
                                <option value="Alto">Alto</option>
                                <option value="Medio">Medio</option>
                                <option value="Bajo">Bajo</option>
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
                    <div class="tab-pane fade show active" id="listado" role="tabpanel"
                        aria-labelledby="listado-tab">
                        <DataTable ref="dt" :value="riesgos" :paginator="true" :rows="10" :loading="loading"
                            :rowsPerPageOptions="[5, 10, 20, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} riesgos"
                            responsiveLayout="scroll">

                            <template #header>
                                <div class="d-flex align-items-center">
                                    <Button type="button" icon="pi pi-download" label="Descargar CSV" severity="secondary"
                                        @click="exportCSV($event)" class="btn btn-secondary ml-auto">
                                    </Button>
                                </div>
                            </template>

                            <Column field="id" header="ID" sortable style="width:5%"></Column>
                            <Column field="riesgo_cod" header="Código" sortable style="width:10%"></Column>
                            <Column field="riesgo_nombre" header="Nombre" sortable style="width:20%"></Column>
                            <Column field="proceso.proceso_nombre" header="Proceso" sortable style="width:20%"></Column>
                            <Column field="riesgo_valoracion" header="Nivel" sortable style="width:15%">
                                <template #body="{ data }">
                                    <span :class="['badge', 'badge-lg', getBadgeClass(data.riesgo_valoracion)]">{{
                                        data.riesgo_valoracion }}</span>
                                </template>
                            </Column>
                            <Column field="estado" header="Estado" sortable style="width:10%"></Column>
                            <Column header="Acciones" :exportable="false" style="width:12%" headerStyle="width: 12%" bodyStyle="width: 12%">
                                <template #body="{ data }">
                                    <a href="#" class="mr-3 d-inline-block" @click.prevent="openRiesgoModal(data)"
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
    <RiesgoModal v-model:show="showRiesgoModal" :riesgo="selectedRiesgo" @saved="refreshList" />
    <RiesgoAccionesModal v-model:show="showAccionesModal" :riesgo="selectedRiesgo" />

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
import RiesgoAccionesModal from './RiesgoAccionesModal.vue';

// PrimeVue
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const store = useRiesgoStore();
const riesgos = computed(() => store.riesgos);
const loading = computed(() => store.loading);
const dt = ref(null);
const showMapaCalor = ref(false);

const showRiesgoModal = ref(false);
const showAccionesModal = ref(false);
const selectedRiesgo = ref(null);

onMounted(() => {
    store.fetchMisRiesgos();
});

const refreshList = () => {
    store.fetchMisRiesgos();
};

const openRiesgoModal = (riesgo) => {
    selectedRiesgo.value = riesgo;
    showRiesgoModal.value = true;
};

const openAccionesModal = (riesgo) => {
    selectedRiesgo.value = riesgo;
    showAccionesModal.value = true;
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

const getBadgeClass = (valoracion) => {
    if (valoracion === 'Muy Alto') return 'badge-danger';
    if (valoracion === 'Alto') return 'badge-orange';
    if (valoracion === 'Medio') return 'badge-warning';
    return 'badge-success';
};
</script>

<style scoped>
.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: black;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

/* Form row styling */
.form-row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -5px;
}

.form-row .col,
.form-row .col-auto {
    padding: 0 5px;
    margin-bottom: 10px;
}

/* Form control styling */
.form-control {
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    border-radius: 0.375rem;
}

.form-control:focus {
    color: #495057;
    background-color: #fff;
    border-color: #dc3545;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Button styling */
.btn {
    border-radius: 0.375rem;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    transition: all 0.15s ease-in-out;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(220, 53, 69, 0.3);
}

.btn-danger:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.5);
}

.btn-danger:not(:disabled):not(.disabled):active,
.btn-danger:not(:disabled):not(.disabled).active {
    background-color: #bd2130;
    border-color: #b21f2d;
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
    color: #212529;
}

.btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0069d9;
    border-color: #0062cc;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

.btn-dark {
    background-color: #454d55;
    border-color: #454d55;
}

.btn-dark:hover {
    background-color: #343a40;
    border-color: #2e343a;
}

.bg-dark {
    background-color: #454d55 !important;
}

/* Input group text */
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}

/* Orange badge for risk level */
.badge-orange {
    background-color: #fd7e14;
    color: white;
}

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

/* Larger badge styles for level indicators */
.badge-lg {
    font-size: 1em;
    padding: 0.5em 1em;
    font-weight: bold;
}
</style>

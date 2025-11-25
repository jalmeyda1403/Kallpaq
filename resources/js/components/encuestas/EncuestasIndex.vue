<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Encuestas de Satisfacción</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Encuestas de Satisfacción</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-primary btn-sm ml-1" @click="openModal()">
                            <i class="fas fa-plus-circle"></i> Nuevo Resultado
                        </button>
                        <button class="btn btn-danger btn-sm ml-1" :disabled="!selectedEncuesta"
                            @click="confirmDelete">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <form @submit.prevent="submitSearch">
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" class="form-control" v-model="filters.proceso_nombre"
                                           placeholder="Filtrar por proceso...">
                                </div>
                                <div class="col">
                                    <select v-model="filters.es_anio" class="form-control">
                                        <option value="">Todos los Años</option>
                                        <option v-for="year in getYears()" :key="year" :value="year">{{ year }}</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select v-model="filters.es_periodo" class="form-control">
                                        <option value="">Todos los Periodos</option>
                                        <option value="Bimestral">Bimestral</option>
                                        <option value="Trimestral">Trimestral</option>
                                        <option value="Semestral">Semestral</option>
                                        <option value="Anual">Anual</option>
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
                <!-- Tabla PrimeVue -->
                <DataTable ref="dt" :value="encuestas" v-model:filters="filtersPrimevue"
                    v-model:selection="selectedEncuesta" selectionMode="single" paginator :rows="10"
                    :loading="loading" :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                    :globalFilterFields="['id', 'proceso.proceso_nombre', 'es_nps_score', 'es_score']"
                    class="p-datatable-sm p-datatable-striped p-datatable-hoverable-rows" responsiveLayout="scroll"
                    loadingIcon="pi pi-spinner pi-spin" :loadingIconProps="{ class: 'p-datatable-loading-icon' }">

                    <template #header>
                        <div class="d-flex align-items-center">
                            <Button type="button" icon="pi pi-download" label="Descargar CSV" severity="secondary"
                                @click="exportCSV($event)" class="btn btn-secondary ml-auto" />
                        </div>
                    </template>

                    <Column field="id" header="ID" style="width: 5%"></Column>
                    <Column field="proceso.proceso_nombre" header="Proceso" sortable style="width: 30%">
                        <template #body="{ data }">
                            {{ data.proceso?.proceso_nombre || data.proceso?.nombre || 'N/A' }}
                        </template>
                    </Column>
                    <Column header="Informe" field="informe" style="width: 20%">
                        <template #body="{ data }">
                            <span class="text-muted">
                                {{ String(data.proceso_id).padStart(3, '0') }}-{{ data.es_anio }}-{{ data.es_numero_periodo }}
                            </span>
                        </template>
                    </Column>
                    <Column field="es_periodo" header="Periodo" style="width: 8%"></Column>
                    <Column field="es_nps_score" header="NPS" sortable style="width: 8%">
                        <template #body="{ data }">
                            <span :class="getNpsClass(data.es_nps_score)" class="badge p-2" style="font-size: 0.9em;">
                                {{ data.es_nps_score }}
                            </span>
                        </template>
                    </Column>
                    <Column field="es_score" header="Score" sortable style="width: 8%">
                        <template #body="{ data }">
                            <span :class="getScoreClass(data.es_score)" class="badge p-2" style="font-size: 0.9em;">
                                {{ data.es_score }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Acciones" style="width: 11%">
                        <template #body="{ data }">
                            <a v-if="getInformePath(data.es_informe_path)" :href="`/storage/${getInformePath(data.es_informe_path)}`" target="_blank"
                                class="mr-2" title="Ver Informe">
                                <i class="fas fa-file-pdf text-info fa-lg"></i>
                            </a>
                            <a v-else class="mr-2 disabled" title="No hay informe">
                                <i class="fas fa-file-pdf text-muted fa-lg"></i>
                            </a>
                            <a href="#" class="mr-2" @click.prevent="editEncuesta(data)" title="Editar">
                                <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                            </a>
                        </template>
                    </Column>

                    <template #empty>
                        <div class="text-center p-3">No se encontraron registros.</div>
                    </template>
                </DataTable>
            </div>
        </div>

        <EncuestaModal :show="showModal" :encuestaId="selectedEncuesta ? selectedEncuesta.id : null" @close="closeModal" @saved="fetchData" />

        <ModalHijo ref="modalHijoRef" fetchUrl="/procesos/listar" targetId="proceso_id" targetDesc="proceso_nombre"
            @update-target="handleProcessSelected" />
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useEncuestasStore } from '@/stores/encuestasStore';
import { storeToRefs } from 'pinia';
import EncuestaModal from './EncuestaModal.vue';
import ModalHijo from '@/components/generales/ModalHijo.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { FilterMatchMode } from 'primevue/api';
import Swal from 'sweetalert2';

const encuestasStore = useEncuestasStore();
const { encuestas, loading } = storeToRefs(encuestasStore);

const showModal = ref(false);
const selectedEncuesta = ref(null);
const modalHijoRef = ref(null);
const processName = ref('');
const dt = ref(null);

const filters = reactive({
    es_periodo: '',
    es_anio: '',
    proceso_nombre: ''
});

const filtersPrimevue = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'proceso.proceso_nombre': { value: null, matchMode: FilterMatchMode.CONTAINS },
    es_nps_score: { value: null, matchMode: FilterMatchMode.EQUALS },
    es_score: { value: null, matchMode: FilterMatchMode.EQUALS },
});

const fetchData = () => {
    encuestasStore.fetchEncuestas(filters);
};

const openModal = () => {
    selectedEncuesta.value = null;
    showModal.value = true;
};

const editEncuesta = (data) => {
    selectedEncuesta.value = data;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedEncuesta.value = null;
};

const confirmDelete = async () => {
    if (!selectedEncuesta.value) {
        await Swal.fire('Advertencia', 'Por favor selecciona un registro para eliminar.', 'warning');
        return;
    }

    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar!'
    });

    if (result.isConfirmed) {
        try {
            await encuestasStore.deleteEncuesta(selectedEncuesta.value.id);
            Swal.fire('Eliminado!', 'El registro ha sido eliminado.', 'success');
            selectedEncuesta.value = null;
            fetchData(); // Refresh the data after deletion
        } catch (error) {
            Swal.fire('Error!', 'Hubo un problema al eliminar.', 'error');
        }
    }
};


const exportCSV = (event) => {
    dt.value.exportCSV();
};

// Process Filter Logic
const openProcessModal = () => {
    modalHijoRef.value.open();
};

const handleProcessSelected = (data) => {
    filters.proceso_id = data.idValue;
    processName.value = data.descValue;
};

const clearProcessFilter = () => {
    filters.proceso_id = '';
    processName.value = '';
};

const resetFilters = () => {
    filters.periodo = '';
    filters.anio = '';
    filters.proceso_id = '';
    processName.value = '';
};

const getNpsClass = (score) => {
    if (score >= 50) return 'badge-success'; // Excellent
    if (score >= 0) return 'badge-warning';  // Good/Neutral
    return 'badge-danger';                   // Poor
};

const getInformePath = (informePath) => {
    if (!informePath) return null;

    try {
        // Try to parse as JSON (new format)
        const fileData = JSON.parse(informePath);
        if (typeof fileData === 'object' && fileData.path) {
            // New format: {path: "...", name: "..."}
            return fileData.path;
        }
    } catch (e) {
        // If parsing fails, it might be just the path string (old format)
        return informePath;
    }

    return null;
};

const getScoreClass = (score) => {
    if (score === null || score === undefined) return 'badge-secondary';
    if (score >= 4.2) return 'badge-info';      // Muy bueno (4.2 ≤ ISC < 5)
    if (score >= 3.4) return 'badge-success';   // Bueno (3.4 ≤ ISC < 4.2)
    if (score >= 2.5) return 'badge-warning';   // Regular (2.5 ≤ ISC < 3.4)
    if (score >= 1.8) return 'badge-danger';    // Malo (1.8 ≤ ISC < 2.5)
    if (score >= 1) return 'badge-danger';      // Muy malo (1 ≤ ISC < 1.8)
    return 'badge-secondary';                   // Default for invalid values
};

const getYears = () => {
    const currentYear = new Date().getFullYear();
    const startYear = 2023;
    const years = [];
    for (let year = currentYear; year >= startYear; year--) {
        years.push(year);
    }
    return years;
};


const submitSearch = () => {
    fetchData();
};

onMounted(() => {
    fetchData();
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

<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Bandeja de Eficacia</li>
            </ol>
        </nav>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bandeja de Eficacia de Hallazgos</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" v-model="filters.global.value" class="form-control"
                                placeholder="Buscar...">
                        </div>
                    </div>
                </div>

                <DataTable :value="hallazgos" :paginator="true" :rows="10" :filters="filters"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 25, 50]"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} hallazgos"
                    responsiveLayout="scroll" :loading="loading">

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
                    <Column field="especialista.name" header="Especialista Asignado" sortable></Column>
                    <Column header="Acciones" style="width: 150px">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-around">
                                <!-- Ver Plan de Acción -->
                                <a href="#" title="Ver Plan de Acción" class="text-info"
                                    @click.prevent="verPlanAccion(slotProps.data.id)">
                                    <i class="fas fa-tasks fa-lg"></i>
                                </a>
                                <!-- Subir Documentos -->
                                <a href="#" title="Subir Documentos" class="text-secondary"
                                    @click.prevent="subirDocumentos(slotProps.data)">
                                    <i class="fas fa-file-upload fa-lg"></i>
                                </a>
                                <!-- Verificar Eficacia -->
                                <a href="#" title="Verificar Eficacia" class="text-success"
                                    @click.prevent="verificarEficacia(slotProps.data)">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </a>
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        No se encontraron hallazgos pendientes de verificación.
                    </template>
                </DataTable>
            </div>
        </div>

        <HallazgoEvaluacionModal :visible="modalVisible" :hallazgo="selectedHallazgo" @cerrar="modalVisible = false"
            @guardado="onEvaluacionGuardada" />

        <EvaluacionArchivosModal :visible="modalArchivosVisible" :hallazgo="selectedHallazgo"
            @cerrar="modalArchivosVisible = false" @archivos-subidos="onArchivosSubidos" />
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode } from 'primevue/api';
import HallazgoEvaluacionModal from './HallazgoEvaluacionModal.vue';
import EvaluacionArchivosModal from './EvaluacionArchivosModal.vue';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';

const router = useRouter();

const hallazgos = ref([]);
const loading = ref(true);
const modalVisible = ref(false);
const modalArchivosVisible = ref(false);
const selectedHallazgo = ref(null);

const filters = reactive({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const fetchHallazgos = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('hallazgo.eficacia.listar'));
        hallazgos.value = response.data;
    } catch (error) {
        console.error('Error al cargar hallazgos:', error);
    } finally {
        loading.value = false;
    }
};

const verificarEficacia = (hallazgo) => {
    selectedHallazgo.value = hallazgo;
    modalVisible.value = true;
};

const subirDocumentos = (hallazgo) => {
    selectedHallazgo.value = hallazgo;
    modalArchivosVisible.value = true;
};

const verPlanAccion = (id) => {
    window.location.href = `/vue/hallazgos/${id}/acciones`;
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

onMounted(() => {
    fetchHallazgos();
});
</script>
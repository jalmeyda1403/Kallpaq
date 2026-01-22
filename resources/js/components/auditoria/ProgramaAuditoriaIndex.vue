<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Programa de Auditoría</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Lista de Programas de Auditorías</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-primary btn-sm ml-1" @click="nuevoPrograma">
                            <i class="fas fa-plus-circle"></i> Nuevo Programa
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="col-md-3">
                                <select class="form-control" v-model="filterYear" @change="fetchProgramas">
                                    <option v-for="year in availableYears" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn bg-dark" @click="fetchProgramas">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="h-1 mb-2">
                    <ProgressBar v-if="loading" mode="indeterminate" style="height: 4px;" />
                </div>
                <DataTable :value="programas" :paginator="true" :rows="10"
                    :class="{ 'opacity-50 pointer-events-none': loading }" tableStyle="min-width: 50rem"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 25, 50]"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registros"
                    class="p-datatable-sm p-datatable-striped p-datatable-hoverable-rows">

                    <Column field="id" header="Id" sortable style="width:5%"></Column>
                    <Column field="pa_anio" header="Año" sortable style="width:10%"></Column>
                    <Column field="pa_version" header="Versión" sortable style="width:10%"></Column>
                    <Column field="pa_recursos" header="Presupuesto" sortable style="width:20%">
                        <template #body="slotProps">
                            {{ slotProps.data.pa_recursos }}
                        </template>
                    </Column>
                    <Column field="pa_fecha_aprobacion" header="F. Aprobación" sortable style="width:15%">
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.pa_fecha_aprobacion) }}
                        </template>
                    </Column>
                    <Column field="pa_estado" header="Estado" sortable style="width:12%; text-align: center;">
                        <template #body="slotProps">
                            <div :class="['status-badge', getEstadoClass(slotProps.data.pa_estado)]">
                                <i :class="['fas mr-1', getEstadoIcon(slotProps.data.pa_estado)]"></i>
                                {{ slotProps.data.pa_estado }}
                            </div>
                        </template>
                    </Column>
                    <Column header="PDF" class="text-center" style="width:8%">
                        <template #body="slotProps">
                            <button v-if="slotProps.data.archivo_pdf" class="btn btn-link text-danger p-0"
                                @click="descargarPDF(slotProps.data)" title="Descargar PDF">
                                <i class="fas fa-file-pdf fa-lg"></i>
                            </button>
                            <span v-else class="text-muted small">No cargado</span>
                        </template>
                    </Column>
                    <Column header="Acciones" class="text-center" style="width:20%">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-center action-buttons">
                                <button class="btn btn-outline-warning btn-xs mr-1"
                                    @click="editarPrograma(slotProps.data)" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-xs mr-1"
                                    @click="subirDocumento(slotProps.data)" title="Cargar">
                                    <i class="fas fa-upload"></i>
                                </button>
                                <button class="btn btn-outline-primary btn-xs mr-1" @click="verGantt(slotProps.data)"
                                    title="Ver">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                                <button class="btn btn-outline-info btn-xs" @click="verHistorial(slotProps.data)"
                                    title="Historial">
                                    <i class="fas fa-history"></i>
                                </button>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>
    <!-- Form Modal -->
    <ProgramaAuditoriaForm v-if="formVisible" :visible="formVisible" :programa="selectedPrograma"
        @update:visible="formVisible = $event" @saved="fetchProgramas" />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useProgramaAuditoriaStore } from '@/stores/programaAuditoriaStore';
import { useToast } from 'primevue/usetoast';
import { useRouter } from 'vue-router';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ProgressBar from 'primevue/progressbar';
import ProgramaAuditoriaForm from './ProgramaAuditoriaForm.vue';

const toast = useToast();
const router = useRouter();
const store = useProgramaAuditoriaStore();

// Refs needed for UI state that are not in store
const formVisible = ref(false);
const selectedPrograma = ref(null);
const filterYear = ref(new Date().getFullYear());

// Computed properties mapped from store
const programas = computed(() => store.programas);
const loading = computed(() => store.loading);

const availableYears = computed(() => {
    const currentYear = new Date().getFullYear();
    const startYear = 2022;
    const years = [];
    for (let year = currentYear; year >= startYear; year--) {
        years.push(year);
    }
    return years;
});

const fetchProgramas = async () => {
    await store.fetchProgramas(filterYear.value);
};

const formatDate = (date) => {
    if (!date) return '-';
    // Adjust logic if timezone issue persists, but standard locale string usually works
    return new Date(date).toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const getEstadoClass = (estado) => {
    switch (estado) {
        case 'Aprobada': return 'status-success';
        case 'Borrador': return 'status-warning';
        case 'Cerrado': return 'status-secondary';
        case 'Programada': return 'status-info';
        case 'Ejecutada': return 'status-success-dark';
        default: return 'status-primary';
    }
};

const getEstadoIcon = (estado) => {
    switch (estado) {
        case 'Aprobada': return 'fa-check-circle';
        case 'Borrador': return 'fa-file-alt';
        case 'Cerrado': return 'fa-lock';
        case 'Programada': return 'fa-clock';
        case 'Ejecutada': return 'fa-tasks';
        default: return 'fa-info-circle';
    }
};

const subirDocumento = (programa) => {
    toast.add({ severity: 'info', summary: 'Info', detail: 'Módulo de carga en desarrollo', life: 3000 });
};

const verGantt = (programa) => {
    store.setPrograma(programa);
    router.push({ name: 'auditoria.gantt', params: { id: programa.id } });
};

const nuevoPrograma = () => {
    selectedPrograma.value = null;
    formVisible.value = true;
};

const verHistorial = (programa) => {
    toast.add({ severity: 'info', summary: 'Info', detail: 'Historial en desarrollo', life: 3000 });
};

const descargarPDF = (programa) => {
    toast.add({ severity: 'info', summary: 'Info', detail: 'Descarga en desarrollo', life: 3000 });
};

const editarPrograma = (programa) => {
    selectedPrograma.value = programa;
    formVisible.value = true;
};

const reprogramarPrograma = (programa) => {
    toast.add({ severity: 'info', summary: 'Info', detail: 'Funcionalidad en desarrollo', life: 3000 });
};

onMounted(() => {
    fetchProgramas();
});
</script>

<style scoped>
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.status-success {
    background-color: #e6fcf5;
    color: #0ca678;
    border: 1px solid #c3fae8;
}

.status-warning {
    background-color: #fff9db;
    color: #f08c00;
    border: 1px solid #fff3bf;
}

.status-secondary {
    background-color: #f8f9fa;
    color: #495057;
    border: 1px solid #dee2e6;
}

.status-info {
    background-color: #e7f5ff;
    color: #1c7ed6;
    border: 1px solid #d0ebff;
}

.status-success-dark {
    background-color: #2b8a3e;
    color: #ffffff;
    border: 1px solid #2f9e44;
}

.status-primary {
    background-color: #f3f0ff;
    color: #7048e8;
    border: 1px solid #e5dbff;
}

.action-buttons .btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.action-buttons .btn-xs:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Custom loader styles - remove opacity and change color to red */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0.7) !important;
}

.p-datatable-loading-icon {
    color: #dc3545 !important;
    font-size: 2.5rem !important;
}
</style>

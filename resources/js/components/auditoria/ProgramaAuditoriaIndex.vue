<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
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

            <div class="card-body">
                <DataTable :value="programas" :paginator="true" :rows="10" :loading="loading"
                    tableStyle="min-width: 50rem"
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
                    <Column field="pa_estado" header="Estado" sortable style="width:15%; text-align: center;">
                        <template #body="slotProps">
                            <span :class="['badge', getEstadoBadge(slotProps.data.pa_estado)]">{{
                                slotProps.data.pa_estado }}</span>
                        </template>
                    </Column>
                    <Column header="Acciones" class="text-center" style="width:25%">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-center">
                                <a href="#" class="mr-3 d-inline-block" @click.prevent="verGantt(slotProps.data)"
                                    title="Ver Cronograma">
                                    <i class="fas fa-calendar-alt text-primary fa-lg"></i>
                                </a>
                                <a href="#" class="mr-3 d-inline-block" @click.prevent="editarPrograma(slotProps.data)"
                                    title="Editar">
                                    <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                                </a>
                                <a href="#" class="mr-3 d-inline-block" @click.prevent="verHistorial(slotProps.data)"
                                    title="Historial">
                                    <i class="fas fa-list text-info fa-lg"></i>
                                </a>
                                <a href="#" class="mr-3 d-inline-block" @click.prevent="descargarPDF(slotProps.data)"
                                    title="Descargar PDF">
                                    <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                </a>
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

const getEstadoBadge = (estado) => {
    switch (estado) {
        case 'Aprobada': return 'badge-success';
        case 'Borrador': return 'badge-warning';
        case 'Cerrado': return 'badge-secondary';
        case 'Programada': return 'badge-info';
        default: return 'badge-primary';
    }
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
    toast.add({ severity: 'info', summary: 'Info', detail: 'Funcionalidad en desarrollo', life: 3000 });
};

const descargarPDF = (programa) => {
    toast.add({ severity: 'info', summary: 'Info', detail: 'Funcionalidad en desarrollo', life: 3000 });
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
.badge {
    font-size: 0.85rem;
    padding: 0.35em 0.65em;
}

/* Custom loader styles - remove opacity and change color to red */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0) !important;
}

.p-datatable-loading-icon {
    color: red !important;
    font-size: 2rem !important;
}
</style>

<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Reportes de Satisfacción</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">Reportes Trimestrales de Satisfacción</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <router-link :to="{ name: 'reportes-satisfaccion.wizard' }" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle"></i> Nuevo Reporte
                        </router-link>
                    </div>
                </div>
                <br />
                <div id="filter-form">
                    <form @submit.prevent="fetchReportes">
                        <div class="row g-2 align-items-center">
                            <div class="col-md">
                                <input type="number" v-model="filters.anio" class="form-control" placeholder="Año (ej: 2025)">
                            </div>
                            <div class="col-md">
                                <select v-model="filters.trimestre" class="form-control">
                                    <option :value="null">Todos los trimestres</option>
                                    <option :value="1">I Trimestre</option>
                                    <option :value="2">II Trimestre</option>
                                    <option :value="3">III Trimestre</option>
                                    <option :value="4">IV Trimestre</option>
                                </select>
                            </div>
                            <div class="col-md">
                                <select v-model="filters.estado" class="form-control">
                                    <option :value="null">Todos los estados</option>
                                    <option value="borrador">Borrador</option>
                                    <option value="generado">Generado</option>
                                </select>
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn bg-dark btn-sm">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <DataTable :value="reportes" :paginator="true" :rows="25" stripedRows :loading="loading"
                    tableStyle="min-width: 50rem"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 25, 50]"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registros">

                    <Column field="anio" header="Periodo" sortable>
                        <template #body="slotProps">
                            {{ slotProps.data.anio }} - T{{ slotProps.data.trimestre }}
                        </template>
                    </Column>
                    <Column field="proceso.proceso_nombre" header="Proceso" sortable style="width: 25%"></Column>
                    <Column field="fecha_generacion" header="Fecha Generación" sortable>
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.fecha_generacion) }}
                        </template>
                    </Column>
                    <Column field="estado" header="Estado" sortable>
                        <template #body="slotProps">
                            <span class="badge" :class="getBadgeClass(slotProps.data.estado)">
                                {{ slotProps.data.estado.toUpperCase() }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Acciones" class="text-center">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-center gap-2">
                                <!-- Ver/Editar consolidado -->
                                <Button 
                                    v-if="slotProps.data.estado === 'firmado'"
                                    icon="pi pi-eye"
                                    class="p-button-rounded p-button-info p-button-text p-button-sm"
                                    @click="viewReporte(slotProps.data)" 
                                    v-tooltip.top="'Ver (Solo lectura)'" 
                                />
                                <Button 
                                    v-else
                                    icon="pi pi-pencil"
                                    class="p-button-rounded p-button-warning p-button-text p-button-sm"
                                    @click="editReporte(slotProps.data)" 
                                    v-tooltip.top="'Editar'" 
                                />
                                
                                <!-- Enviar Firmado (solo si está generado) -->
                                <Button 
                                    v-if="slotProps.data.estado === 'generado'"
                                    icon="pi pi-upload"
                                    class="p-button-rounded p-button-success p-button-text p-button-sm"
                                    @click="openEnviarModal(slotProps.data)" 
                                    v-tooltip.top="'Enviar Firmado'" 
                                />
                                
                                <!-- Ver PDF Firmado (solo si está firmado) -->
                                <Button 
                                    v-if="slotProps.data.estado === 'firmado' && slotProps.data.archivo_path"
                                    icon="pi pi-file-pdf"
                                    class="p-button-rounded p-button-danger p-button-text p-button-sm"
                                    @click="viewPDF(slotProps.data)" 
                                    v-tooltip.top="'Ver PDF Firmado'" 
                                />
                                
                                <!-- Eliminar -->
                                <Button 
                                    icon="pi pi-trash"
                                    class="p-button-rounded p-button-danger p-button-text p-button-sm"
                                    @click="deleteReporte(slotProps.data)" 
                                    v-tooltip.top="'Eliminar'" 
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
        
        <!-- Modal de Enviar Firmado -->
        <ReporteSatisfaccionEnviar 
            ref="enviarModal" 
            :reporte-id="selectedReporte?.id"
            @success="handleEnviarSuccess"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useReporteSatisfaccionStore } from '../../stores/reporteSatisfaccionStore';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

// Components
import ReporteSatisfaccionEnviar from './ReporteSatisfaccionEnviar.vue';

const store = useReporteSatisfaccionStore();
const router = useRouter();

const reportes = ref([]);
const loading = ref(false);
const enviarModal = ref(null);
const selectedReporte = ref(null);
const filters = reactive({
    anio: new Date().getFullYear(),
    trimestre: null,
    estado: null
});

const fetchReportes = async () => {
    loading.value = true;
    try {
        await store.fetchReportes(filters);
        reportes.value = store.reportes;
    } catch (error) {
        console.error('Error fetching reportes:', error);
        Swal.fire('Error', 'No se pudieron cargar los reportes', 'error');
    } finally {
        loading.value = false;
    }
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-PE');
};

const getBadgeClass = (estado) => {
    if (estado === 'firmado') return 'badge-success';
    if (estado === 'generado') return 'badge-primary';
    return 'badge-secondary';
};

const viewReporte = (reporte) => {
    router.push({ name: 'reportes-satisfaccion.wizard', params: { id: reporte.id } });
};

const editReporte = (reporte) => {
    router.push({ name: 'reportes-satisfaccion.wizard', params: { id: reporte.id } });
};

const openEnviarModal = (reporte) => {
    selectedReporte.value = reporte;
    enviarModal.value.open();
};

const handleEnviarSuccess = async () => {
    await fetchReportes(); // Reload data
};

const viewPDF = (reporte) => {
    if (reporte.archivo_path_url) {
        window.open(reporte.archivo_path_url, '_blank');
    }
};

const deleteReporte = (reporte) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo!',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await store.deleteReporte(reporte.id);
                Swal.fire('Eliminado!', 'El reporte ha sido eliminado.', 'success');
                fetchReportes();
            } catch (error) {
                console.error('Error deleting:', error);
                Swal.fire('Error!', 'Hubo un problema al eliminar el reporte.', 'error');
            }
        }
    });
};

onMounted(() => {
    fetchReportes();
});
</script>

<style scoped>
.p-button-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
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

<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item"><router-link to="/obligaciones">Obligaciones</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Radar Normativo (IA)</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-satellite-dish mr-2"></i> Radar de Obligaciones
                    </h3>
                    <div>
                        <Button label="Escanear Nuevas Normas (IA)" icon="pi pi-search" class="p-button-info"
                            @click="scanNormas" :loading="radarStore.loading" />
                    </div>
                </div>
                <hr>
                <form @submit.prevent="searchNormas">
                    <div class="form-row">
                        <div class="col">
                            <input type="date" class="form-control" v-model="filters.fecha"
                                placeholder="Filtrar por fecha...">
                        </div>
                        <div class="col">
                            <select v-model="filters.relevancia" class="form-control">
                                <option value="">Todas las Relevancias</option>
                                <option value="Alta">Alta</option>
                                <option value="Media">Media</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>
                        <div class="col">
                            <select v-model="filters.estado" class="form-control">
                                <option value="">Todos los Estados</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="En Análisis">En Análisis</option>
                                <option value="Aplicable">Aplicable</option>
                                <option value="No Aplicable">No Aplicable</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn bg-dark">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger" @click="clearFilters">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <DataTable :value="radarStore.normas" :loading="radarStore.loading" paginator :rows="10"
                    responsiveLayout="scroll" dataKey="id">
                    <template #empty>
                        No se han detectado nuevas normas. Haga clic en "Escanear".
                    </template>

                    <Column field="titulo" header="Norma / Título" sortable>
                        <template #body="{ data }">
                            <strong>{{ data.numero_norma }}</strong>
                            <a v-if="data.url_fuente" :href="data.url_fuente" target="_blank" class="ml-2 text-primary"
                                title="Ver fuente oficial">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <br>
                            {{ data.titulo }}
                        </template>
                    </Column>
                    <Column field="organismo_emisor" header="Emisor" sortable></Column>
                    <Column field="fecha_publicacion" header="Fecha" sortable>
                        <template #body="{ data }">
                            {{ formatDate(data.fecha_publicacion) }}
                        </template>
                    </Column>
                    <Column field="nivel_relevancia" header="Relevancia" sortable>
                        <template #body="{ data }">
                            <span :class="getRelevanciaClass(data.nivel_relevancia)">
                                {{ data.nivel_relevancia }}
                            </span>
                        </template>
                    </Column>
                    <Column field="estado" header="Estado" sortable>
                        <template #body="{ data }">
                            <span :class="getEstadoClass(data.estado)">
                                {{ data.estado }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Acciones">
                        <template #body="{ data }">
                            <div v-if="data.estado === 'Pendiente' || data.estado === 'En Análisis'">
                                <Button icon="pi pi-check" class="p-button-rounded p-button-success mr-2"
                                    title="Aprobar / Crear Obligación" @click="openModal(data, 'approve')" />
                                <Button icon="pi pi-times" class="p-button-rounded p-button-danger"
                                    title="Descartar / No Aplica" @click="openModal(data, 'reject')" />
                            </div>
                            <div v-else>
                                <small class="text-muted">{{ data.analisis_humano }}</small>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <AnalisisNormativoModal :show="showModal" :norma="selectedNorma" :mode="modalMode" @close="closeModal"
            @confirm="handleConfirm" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRadarStore } from '@/stores/radarStore';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import AnalisisNormativoModal from './AnalisisNormativoModal.vue';
import Swal from 'sweetalert2';

const radarStore = useRadarStore();
const showModal = ref(false);
const selectedNorma = ref(null);
const modalMode = ref('approve');
const filters = ref({
    fecha: '',
    relevancia: '',
    estado: ''
});

onMounted(() => {
    radarStore.fetchNormas();
});

const searchNormas = async () => {
    // Apply filters to the store
    radarStore.filters.fecha = filters.value.fecha;
    radarStore.filters.relevancia = filters.value.relevancia;
    radarStore.filters.estado = filters.value.estado;

    // Fetch normas with filters
    await radarStore.fetchNormas();
};

const clearFilters = async () => {
    filters.value.fecha = '';
    filters.value.relevancia = '';
    filters.value.estado = '';

    // Clear filters in the store
    radarStore.filters.fecha = '';
    radarStore.filters.relevancia = '';
    radarStore.filters.estado = '';

    // Fetch normas without filters
    await radarStore.fetchNormas();
};

const scanNormas = async () => {
    try {
        await radarStore.scanNormas();
        Swal.fire('Escaneo Completado', 'Se han detectado nuevas normas potenciales.', 'success');
    } catch (error) {
        const errorMessage = radarStore.error || 'No se pudo completar el escaneo. Verifique la conexión con El Peruano.';
        Swal.fire('Error', errorMessage, 'error');
    }
};

const openModal = (norma, mode) => {
    selectedNorma.value = norma;
    modalMode.value = mode;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedNorma.value = null;
};

const handleConfirm = async (data) => {
    try {
        if (modalMode.value === 'approve') {
            await radarStore.approveNorma(data.id, data);
            Swal.fire('Aprobado', 'La obligación ha sido creada exitosamente.', 'success');
        } else {
            await radarStore.rejectNorma(data.id, data.analisis_humano);
            Swal.fire('Rechazado', 'La norma ha sido marcada como No Aplicable.', 'info');
        }
    } catch (error) {
        Swal.fire('Error', 'Hubo un problema al procesar la solicitud.', 'error');
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString();
};

const getRelevanciaClass = (relevancia) => {
    switch (relevancia) {
        case 'Alta': return 'badge badge-danger';
        case 'Media': return 'badge badge-warning';
        case 'Baja': return 'badge badge-info';
        default: return 'badge badge-secondary';
    }
};

const getEstadoClass = (estado) => {
    switch (estado) {
        case 'Aplicable': return 'badge badge-success';
        case 'No Aplicable': return 'badge badge-secondary';
        case 'Pendiente': return 'badge badge-primary';
        default: return 'badge badge-light';
    }
};
</script>

<style scoped>
.badge {
    font-size: 0.9em;
    padding: 5px 10px;
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
</style>

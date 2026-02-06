<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link :to="{ name: 'home' }"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Salidas No Conformes</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Salidas No Conformes</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-primary btn-sm ml-1" @click="showCreateModal = true">
                            <i class="fas fa-plus-circle"></i> Nueva Salida NC
                        </button>
                        <button class="btn btn-danger btn-sm ml-1" :disabled="!selectedSNCId" @click="confirmDelete">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <form @submit.prevent="fetchSalidasNC">
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" v-model="serverFilters.buscar_snc" class="form-control"
                                        placeholder="Buscar por ID, descripción...">
                                </div>
                                <div class="col">
                                    <select v-model="serverFilters.estado" class="form-control">
                                        <option value="">Todos los Estados</option>
                                        <option value="identificada">Identificada</option>
                                        <option value="en tratamiento">En Tratamiento</option>
                                        <option value="tratada">Tratada</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select v-model="serverFilters.origen" class="form-control">
                                        <option value="">Todos los Orígenes</option>
                                        <option value="cliente">Cliente</option>
                                        <option value="auditoría interna">Auditoría Interna</option>
                                        <option value="auditoría externa">Auditoría Externa</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select v-model="serverFilters.clasificacion" class="form-control">
                                        <option value="">Todas las Clasificaciones</option>
                                        <option value="crítica">Crítica</option>
                                        <option value="mayor">Mayor</option>
                                        <option value="menor">Menor</option>
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
                <!-- Loading State - Barra de progreso -->
                <div class="h-1 mb-2">
                    <ProgressBar v-if="loading" mode="indeterminate" style="height: 4px;" />
                </div>
                <DataTable ref="dt" :value="salidasNC" v-model:filters="filters" paginator :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                    :globalFilterFields="['id', 'snc_descripcion', 'snc_clasificacion', 'snc_estado', 'snc_origen']"
                    :class="{ 'opacity-50 pointer-events-none': loading }"
                    class="p-datatable-sm p-datatable-striped p-datatable-hoverable-rows">
                    <template #header>
                        <div class="d-flex align-items-center">
                            <Button type="button" icon="pi pi-download" label="Descargar CSV" severity="secondary"
                                @click="exportCSV($event)" class="btn btn-secondary ml-auto">
                            </Button>
                        </div>
                    </template>

                    <Column field="id" header="ID" style="width:5%">
                    </Column>
                    <Column field="proceso.proceso_nombre" header="Proceso" style="width:15%">
                        <template #body="{ data }">
                            {{ data.proceso?.proceso_nombre || 'N/A' }}
                        </template>
                    </Column>
                    <Column field="snc_descripcion" header="Salida No Conforme" style="width:30%">
                        <template #body="{ data }">
                            <span :title="data.snc_descripcion">
                                {{ truncateText(data.snc_descripcion, 60) }}
                            </span>
                        </template>
                    </Column>
                    <Column field="snc_origen" header="Origen" style="width:10%">
                    </Column>
                    <Column field="snc_clasificacion" header="Clasificación" style="width:8%">
                    </Column>
                    <Column field="snc_estado" header="Estado" style="width:10%; text-align: center;">
                        <template #body="{ data }">
                            <span :class="getEstadoBadge(data.snc_estado)" class="badge-text">
                                {{ data.snc_estado }}
                            </span>
                        </template>
                    </Column>
                    <Column field="snc_fecha_deteccion" header="F. Detección" style="width:10%">
                        <template #body="{ data }">
                            {{ formatDate(data.snc_fecha_deteccion) }}
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:15%" headerStyle="width: 15%"
                        bodyStyle="width: 15%">
                        <template #body="{ data }">
                            <a href="#" title="Editar" class="mr-2 d-inline-block"
                                :class="{ 'disabled-link': data.snc_estado !== 'identificada' }"
                                @click.prevent="data.snc_estado === 'identificada' ? openEditModal(data) : null">
                                <i class="fas fa-pencil-alt fa-lg"
                                    :class="data.snc_estado === 'identificada' ? 'text-warning' : 'text-secondary opacity-50'"></i>
                            </a>
                            <a href="#" title="Tratamiento" class="mr-2 d-inline-block"
                                @click.prevent="openTratamientoModal(data)">
                                <i class="fas fa-tasks text-info fa-lg"></i>
                            </a>
                            <a v-if="authStore.hasRole('admin') || authStore.hasRole('especialista')" href="#"
                                title="Eliminar" class="d-inline-block" @click.prevent="confirmDelete(data.id)">
                                <i class="fas fa-trash-alt text-danger fa-lg"></i>
                            </a>
                        </template>
                    </Column>
                </DataTable>
                <!-- Modals -->
                <SalidaNCModal :show="showCreateModal" :snc="selectedSNC" @update:show="showCreateModal = $event"
                    @saved="onSNCSaved">
                </SalidaNCModal>
                <SNCtratamientoModal :show="showTratamientoModal" :snc="selectedSNCForTratamiento"
                    @update:show="showTratamientoModal = $event" @saved="onSNCSaved"></SNCtratamientoModal>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useSalidasNCStore } from '@/stores/salidasNCStore';
import { storeToRefs } from 'pinia';
import { route } from 'ziggy-js';
import SalidaNCModal from '@/components/salidas-nc/SalidaNCModal.vue';
import SNCtratamientoModal from '@/components/salidas-nc/SNCtratamientoModal.vue';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import ProgressBar from 'primevue/progressbar';
import { FilterMatchMode } from 'primevue/api';

// Usamos el store
import { useAuthStore } from '@/stores/authStore';
const salidasNCStore = useSalidasNCStore();
const authStore = useAuthStore();
const { salidas: salidasNC, loading } = storeToRefs(salidasNCStore);

const dt = ref(null);
const selectedSNCId = ref(null);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    snc_codigo: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    snc_descripcion: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    snc_producto_servicio: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    snc_clasificacion: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    snc_estado: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
});

const serverFilters = reactive({
    buscar_snc: '',
    estado: '',
    origen: '',
    clasificacion: '',
});

const fetchSalidasNC = async () => {
    try {
        await salidasNCStore.fetchSalidasNC(serverFilters);
    } catch (error) {
        console.error('Error al obtener las salidas no conformes:', error);
    }
};

const showCreateModal = ref(false);
const selectedSNC = ref(null);

const openCreateModal = () => {
    console.log('openCreateModal called');
    selectedSNC.value = null;
    showCreateModal.value = true;
};

const openEditModal = async (snc) => {
    try {
        // Cargamos la SNC específica en el store
        await salidasNCStore.fetchSNCById(snc.id);
        selectedSNC.value = salidasNCStore.getCurrentSNC;
        showCreateModal.value = true;
    } catch (error) {
        console.error('Error al cargar datos de la SNC:', error);
    }
};

const showTratamientoModal = ref(false);
const selectedSNCForTratamiento = ref(null);

const openTratamientoModal = async (snc) => {
    try {
        await salidasNCStore.fetchSNCById(snc.id);
        selectedSNCForTratamiento.value = salidasNCStore.getCurrentSNC;
        showTratamientoModal.value = true;
    } catch (error) {
        console.error('Error al cargar datos de la SNC para tratamiento:', error);
    }
};

const confirmDelete = async (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta Salida No Conforme?')) {
        try {
            await salidasNCStore.deleteSNC(id);
        } catch (error) {
            console.error('Error al eliminar la SNC:', error);
        }
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const getClasificacionBadge = (clasificacion) => {
    const badges = {
        'crítica': 'badge badge-danger',
        'mayor': 'badge badge-warning',
        'menor': 'badge badge-info'
    };
    return badges[clasificacion] || 'badge badge-secondary';
};

const getEstadoBadge = (estado) => {
    switch (estado) {
        case 'identificada': return 'badge badge-secondary';
        case 'en análisis': return 'badge badge-info';
        case 'en tratamiento': return 'badge badge-primary';
        case 'tratada': return 'badge badge-success';
        case 'cerrada': return 'badge badge-purple'; // Changed to purple to match Sugerencias
        case 'observada': return 'badge badge-warning';
        default: return 'badge badge-secondary';
    }
};

const exportCSV = () => {
    dt.value.exportCSV();
};

const onSNCSaved = () => {
    // Recargar la lista para reflejar los cambios (especialmente estado y fecha de cierre)
    fetchSalidasNC();
};

const truncateText = (text, maxLength) => {
    if (!text) return 'N/A';
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};

onMounted(() => {
    fetchSalidasNC();
});
</script>

<style>
/* Improved badge styling */
.badge-text {
    font-size: 0.9em !important;
    font-weight: 500 !important;
    padding: 0.4em 0.8em !important;
    border-radius: 0.375rem !important;
    text-transform: capitalize;
}

.badge-purple {
    background-color: #605ca8;
    color: white;
}

.disabled-link {
    cursor: not-allowed;
    pointer-events: none;
}
</style>

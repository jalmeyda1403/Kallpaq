<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/home"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Listado Obligaciones</li>
            </ol>
        </nav>

        <div class="card border-top-danger shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h5 class="card-title text-danger mb-0 font-weight-bold">
                            <i class="fas fa-gavel mr-2"></i>Lista de Obligaciones
                        </h5>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <a href="#" class="btn btn-danger btn-sm shadow-sm" @click.prevent="openNewObligacion"
                            title="Nueva Obligación">
                            <i class="fas fa-plus-circle mr-1"></i> Agregar
                        </a>
                        <button class="btn btn-dark btn-sm ml-1 shadow-sm" @click="router.push({ name: 'radar.index' })"
                            title="Radar Normativo (IA)">
                            <i class="fas fa-satellite-dish mr-1"></i> Radar IA
                        </button>
                    </div>
                </div>
                <hr>
                <!-- Filters remain same -->
                <div class="row">
                    <div class="col-md-12">
                        <form @submit.prevent="obligacionStore.fetchObligaciones">
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" v-model="obligacionStore.filters.documento" class="form-control"
                                        placeholder="Buscar por documento...">
                                </div>
                                <div class="col">
                                    <input type="text" v-model="obligacionStore.filters.proceso" class="form-control"
                                        placeholder="Buscar por proceso...">
                                </div>
                                <div class="col">
                                    <select v-model="obligacionStore.filters.fuente" class="form-control">
                                        <option value="">Todas las fuentes</option>
                                        <option value="interno">Fuente Interna</option>
                                        <option value="externo">Fuente Externa</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn bg-dark text-white shadow-sm">
                                        <i class="fas fa-search mr-1"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="h-1 mb-2">
                    <ProgressBar v-if="obligacionStore.loading" mode="indeterminate" style="height: 4px;" />
                </div>

                <DataTable ref="dt" :value="obligacionStore.obligaciones" v-model:filters="filters" paginator :rows="10"
                    :class="{ 'opacity-50 pointer-events-none': obligacionStore.loading }"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                    :globalFilterFields="['obligacion_documento', 'obligacion_principal', 'obligacion_consecuencia', 'obligacion_estado', 'procesos.proceso_nombre']"
                    class="p-datatable-sm table-hover" stripedRows responsiveLayout="scroll">
                    <!-- Columns remain mostly same -->
                    <Column field="id" header="#" style="width:3%">
                        <template #body="{ index }">
                            <span class="text-muted small">{{ index + 1 }}</span>
                        </template>
                    </Column>
                    <Column field="procesos" header="Procesos" sortable style="width:20%">
                        <template #body="{ data }">
                            <div v-if="data.procesos && data.procesos.length" class="d-flex flex-wrap">
                                <span v-for="p in data.procesos" :key="p.id"
                                    class="badge badge-light border text-danger mr-1 mb-1 small px-2 py-1">
                                    <i class="fas fa-network-wired mr-1 opacity-50"></i>{{ p.proceso_nombre }}
                                </span>
                            </div>
                            <span v-else class="text-muted small">N/A</span>
                        </template>
                    </Column>
                    <Column field="documento.nombre_documento" header="Documento" style="width:25%">
                        <template #body="{ data }">
                            <div class="d-flex align-items-center">
                                <i class="far fa-file-pdf text-danger mr-2 fa-lg" v-if="data.documento"></i>
                                <span v-if="data.documento" class="text-break small">{{ data.documento.nombre_documento
                                    }}</span>
                                <span v-else class="text-break small text-muted">{{ data.obligacion_documento ||
                                    'Sin documento' }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column field="obligacion_principal" header="Obligación Principal" style="width:25%">
                        <template #body="{ data }">
                            <span class="small d-block text-justify text-secondary">{{ data.obligacion_principal
                                }}</span>
                        </template>
                    </Column>
                    <Column field="obligacion_estado" header="Estado" style="width:10%" sortable>
                        <template #body="{ data }">
                            <span :class="['badge p-2', getEstadoClass(data.obligacion_estado)]">
                                {{ ucfirst(data.obligacion_estado) }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:10%" class="text-center">
                        <template #body="{ data }">
                            <button type="button" class="btn btn-light text-primary btn-sm" title="Gestionar Obligación"
                                @click.prevent="editObligacion(data)">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button type="button" class="btn btn-light text-danger btn-sm ml-1" title="Eliminar"
                                @click.prevent="confirmDelete(data.id)">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>

    <!-- Nueva Obligacion Form Modal (Bootstrap HTML based) -->
    <ObligacionesModal :show="showModal" :obligacion="selectedObligacion" @close="showModal = false"
        @saved="onObligacionSaved" />

</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useObligacionStore } from '@/stores/obligacionStore';
import { FilterMatchMode } from 'primevue/api';
import ProgressBar from 'primevue/progressbar';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button'; // Still used for export icon if needed
import ObligacionesModal from './ObligacionesModal.vue';
import Swal from 'sweetalert2';

const router = useRouter();
const obligacionStore = useObligacionStore();
const dt = ref(null);

const showModal = ref(false);
const selectedObligacion = ref(null);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'procesos.proceso_nombre': { value: null, matchMode: FilterMatchMode.CONTAINS },
    obligacion_documento: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    obligacion_principal: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    obligacion_consecuencia: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    obligacion_estado: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
});

const getEstadoClass = (estado) => {
    switch (estado) {
        case 'pendiente': return 'badge-secondary';
        case 'mitigada': return 'badge-warning';
        case 'controlada': return 'badge-primary';
        case 'inactiva':
        case 'suspendida': return 'badge-dark';
        case 'vencida': return 'badge-danger';
        default: return '';
    }
};

const ucfirst = (string) => string ? string.charAt(0).toUpperCase() + string.slice(1) : '';

const openNewObligacion = () => {
    selectedObligacion.value = null; // New mode
    showModal.value = true;
};

const editObligacion = (obligacion) => {
    selectedObligacion.value = obligacion; // Edit mode
    showModal.value = true;
};

const onObligacionSaved = () => {
    // Refresh list
    obligacionStore.fetchObligaciones();
};

const confirmDelete = async (id) => {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esta acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await obligacionStore.deleteObligacion(id);
            Swal.fire('Eliminado', 'La obligación ha sido eliminada.', 'success');
        } catch (error) {
            Swal.fire('Error', 'Hubo un problema al eliminar la obligación.', 'error');
        }
    }
};

onMounted(() => {
    obligacionStore.fetchObligaciones();
});
</script>

<style scoped>
.border-top-danger {
    border-top: 3px solid #dc3545 !important;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.valoracion-circle {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 5px;
}

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-primary {
    background-color: #007bff;
}

.badge-secondary {
    background-color: #6c757d;
}

.badge-dark {
    background-color: #343a40;
}

.opacity-50 {
    opacity: 0.5;
}

.pointer-events-none {
    pointer-events: none;
}
</style>

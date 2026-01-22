<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link :to="{ name: 'requerimientos.index' }"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Mis Requerimientos Asignados</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-user-cog text-danger mr-2"></i>
                                    Mis Requerimientos Asignados
                                </h3>
                            </div>
                            <div class="col-md-6 text-md-right">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <form @submit.prevent="fetchRequerimientos">
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="text" name="buscar_requerimiento" id="buscar_requerimiento"
                                                class="form-control" placeholder="Buscar por ID, asunto o proceso"
                                                v-model="serverFilters.buscar_requerimiento">
                                        </div>
                                        <div class="col">
                                            <select name="estado" id="estado" class="form-control"
                                                v-model="serverFilters.estado">
                                                <option value="">Todos los estados</option>
                                                <option v-for="status in statuses" :key="status" :value="status">
                                                    {{ status }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn bg-dark text-white shadow-sm">
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
                        <DataTable ref="dt" :value="requerimientos" v-model:filters="filters" paginator :rows="10"
                            :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                            :class="{ 'opacity-50 pointer-events-none': loading }"
                            :globalFilterFields="['id', 'proceso.proceso_nombre', 'asunto', 'complejidad', 'estado']">
                            <template #header>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-info shadow-sm px-3 py-2 rounded-pill mr-auto"
                                        style="font-size: 0.9rem;">
                                        <i class="fas fa-tasks mr-1"></i> {{ requerimientos.length }} Requerimientos
                                    </span>
                                    <Button type="button" icon="pi pi-download" label="Descargar CSV"
                                        severity="secondary" @click="exportCSV($event)"
                                        class="btn btn-secondary shadow-sm btn-sm">
                                    </Button>
                                </div>
                            </template>
                            <Column field="id" header="ID" style="width:5%">
                            </Column>
                            <Column field="proceso.proceso_nombre" header="Proceso" sortable style="width:15%">
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                        class="p-column-filter" placeholder="Buscar por Proceso" />
                                </template>
                            </Column>
                            <Column field="asunto" header="Asunto" style="width:20%">
                            </Column>
                            <Column field="complejidad" header="Complejidad" style="width:8%">
                            </Column>
                            <Column field="estado" header="Estado" style="width:8%">
                                <template #body="{ data }">
                                    <span class="badge badge-pill text-uppercase px-2"
                                        :class="getEstadoBadgeClass(data.estado)">
                                        {{ data.estado }}
                                    </span>
                                </template>
                            </Column>
                            <Column field="fecha_asignacion" header="Fecha Asignación" sortable style="width:10%">
                                <template #body="{ data }">
                                    {{ formatDate(data.fecha_asignacion) }}
                                </template>
                            </Column>
                            <Column header="Fin Prog./Real" sortable style="width:12%">
                                <template #body="{ data }">
                                    <div class="small">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="fas fa-calendar-alt mr-1"
                                                :class="isOverdue(data) ? 'text-danger' : 'text-primary'"
                                                title="Fecha Programada"></i>
                                            <span
                                                :class="isOverdue(data) ? 'text-danger font-weight-bold' : 'text-primary font-weight-bold'">{{
                                                    formatDate(data.fecha_limite) || '-' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center" v-if="data.fecha_fin">
                                            <i class="fas fa-calendar-check text-success mr-1" title="Fecha Real"></i>
                                            <span class="text-success">{{ formatDate(data.fecha_fin) }}</span>
                                        </div>
                                        <span v-else class="text-muted small"><i
                                                class="fas fa-clock mr-1"></i>Pendiente</span>
                                    </div>
                                </template>
                            </Column>
                            <Column header="Avance" style="width:10%">
                                <template #body="{ data }">
                                    <template v-if="['creado', 'desestimado'].includes(data.estado)">
                                        <span class="small text-muted">Sin avance</span>
                                    </template>
                                    <template v-else-if="data.avance">
                                        <div class="small text-center">
                                            {{ parseInt(data.avance.avance_registrado) }}%
                                            <div class="progress progress-xs">
                                                <div class="progress-bar"
                                                    :class="getProgressBarClass(data.avance.avance_registrado)"
                                                    :style="{ width: parseInt(data.avance.avance_registrado) + '%' }">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span class="small text-muted">Sin avance</span>
                                    </template>
                                </template>
                            </Column>
                            <Column header="Acciones" :exportable="false" style="width:14%">
                                <template #body="{ data }">
                                    <!-- Registrar Avance - activo solo si está asignado -->
                                    <a href="#" title="Registrar Avance"
                                        class="mr-2 d-inline-block shadow-sm rounded-circle p-2 bg-light"
                                        @click.prevent="data.estado === 'asignado' && openModal('abrirAvanceRequerimientoModal', data)"
                                        :class="{ 'disabled-action': data.estado !== 'asignado' }">
                                        <i class="fas fa-edit"
                                            :class="data.estado === 'asignado' ? 'text-primary' : 'text-secondary'"></i>
                                    </a>
                                    <!-- Ver Seguimiento - siempre activo -->
                                    <a href="#" title="Ver Seguimiento"
                                        class="mr-2 d-inline-block shadow-sm rounded-circle p-2 bg-light"
                                        @click.prevent="openModal('mostrarSeguimiento', data)">
                                        <i class="fas fa-stream text-success"></i>
                                    </a>
                                    <!-- Finalizar - activo solo si asignado y avance >= 100 -->
                                    <a href="#" title="Finalizar Requerimiento"
                                        class="d-inline-block shadow-sm rounded-circle p-2 bg-light"
                                        @click.prevent="canFinalize(data) && finalizarRequerimiento(data)"
                                        :class="{ 'disabled-action': !canFinalize(data) }">
                                        <i class="fas fa-check-circle"
                                            :class="canFinalize(data) ? 'text-success' : 'text-secondary'"></i>
                                    </a>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modals -->
        <requerimiento-seguimiento-modal></requerimiento-seguimiento-modal>
        <requerimiento-avance-modal @avance-guardado="fetchRequerimientos"></requerimiento-avance-modal>
        <evidencias-modal></evidencias-modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { Ziggy } from '../../ziggy';
import Swal from 'sweetalert2';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import ProgressBar from 'primevue/progressbar';
import { FilterMatchMode } from 'primevue/api';

const requerimientos = ref([]);
const statuses = ref(['asignado', 'finalizado', 'desestimado']);
const dt = ref(null);
const loading = ref(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const serverFilters = ref({
    buscar_requerimiento: '',
    estado: '',
});

const fetchRequerimientos = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('web.requerimientos.especialista', {}, false, Ziggy), {
            params: serverFilters.value
        });
        requerimientos.value = response.data.requerimientos;
        if (response.data.statuses) {
            statuses.value = response.data.statuses;
        }
    } catch (error) {
        console.error('Error fetching requerimientos:', error);
    } finally {
        loading.value = false;
    }
};

const openModal = (eventName, requerimiento) => {
    document.dispatchEvent(new CustomEvent(eventName, {
        detail: requerimiento
    }));
};

const finalizarRequerimiento = async (requerimiento) => {
    Swal.fire({
        title: '¿Finalizar Requerimiento?',
        text: "¿Estás seguro de que deseas finalizar este requerimiento?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.post(route('requerimientos.finalizar', { id: requerimiento.id }, false, Ziggy));
                Swal.fire('¡Finalizado!', 'Requerimiento finalizado correctamente.', 'success');
                fetchRequerimientos();
            } catch (error) {
                console.error('Error al finalizar requerimiento:', error);
                Swal.fire('Error', 'Error al finalizar el requerimiento.', 'error');
            }
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const isOverdue = (data) => {
    if (!data.fecha_limite || data.estado === 'finalizado') return false;
    return new Date(data.fecha_limite) < new Date();
};

const getEstadoBadgeClass = (estado) => {
    switch (estado) {
        case 'atendido':
        case 'finalizado': return 'badge-success';
        case 'desestimado':
        case 'vencido': return 'badge-danger';
        case 'aprobado': return 'badge-info';
        case 'asignado': return 'badge-primary';
        case 'evaluado': return 'badge-dark';
        case 'creado': return 'badge-secondary';
        case 'en_proceso': return 'badge-info';
        default: return 'badge-light';
    }
};

const getProgressBarClass = (value) => {
    if (value >= 80) return 'bg-success';
    if (value >= 50) return 'bg-warning';
    return 'bg-info';
};

const canFinalize = (data) => {
    return data.estado === 'asignado' && data.avance && data.avance.avance_registrado >= 100;
};

const exportCSV = () => {
    dt.value.exportCSV();
};

onMounted(() => {
    fetchRequerimientos();
});
</script>

<style>
/* Custom loader styles */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0) !important;
}

.p-datatable-loading-icon {
    color: red !important;
    font-size: 2rem !important;
}

.disabled-action {
    cursor: not-allowed;
    opacity: 0.6;
}

.breadcrumb {
    font-size: 0.85rem;
}

.breadcrumb-item+.breadcrumb-item::before {
    content: "\f105";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    color: #adb5bd;
}

.rounded-lg {
    border-radius: 0.75rem !important;
}
</style>

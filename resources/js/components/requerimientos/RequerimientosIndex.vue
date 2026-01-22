<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link :to="{ name: 'home' }"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Bandeja de Requerimientos</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">Lista de Requerimientos</h3>
                            </div>
                            <div class="col-md-6 text-md-right d-flex justify-content-end align-items-center">
                                <a href="#" class="btn btn-primary btn-sm ml-1"
                                    @click.prevent="goToCreateRequerimiento">
                                    <i class="fas fa-plus-circle"></i> Nuevo Requerimiento
                                </a>

                                <button class="btn btn-danger btn-sm ml-1" id="btnEliminar"
                                    :disabled="!selectedRequerimientoId"
                                    @click="confirmDelete(selectedRequerimientoId)">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <form @submit.prevent="fetchRequerimientos">
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="text" name="buscar_requerimiento" id="buscar_requerimiento"
                                                class="form-control" placeholder="Buscar por Requerimiento"
                                                v-model="serverFilters.buscar_requerimiento">
                                        </div>
                                        <div class="col">
                                            <select name="especialista_id" id="especialista_id" class="form-control"
                                                v-model="serverFilters.especialista_id">
                                                <option value="">Todos los especialistas</option>
                                                <option v-for="especialista in especialistas" :key="especialista.id"
                                                    :value="especialista.id">
                                                    {{ especialista.user.name }}
                                                </option>
                                            </select>
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
                                            <button type="submit" class="btn bg-dark text-white">
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
                            :globalFilterFields="['id', 'proceso.proceso_nombre', 'asunto', 'complejidad', 'estado', 'especialista.name']">
                            <template #header>
                                <div class="d-flex align-items-center">
                                    <div class="custom-control custom-switch mr-3 ml-auto">
                                        <input type="checkbox" class="custom-control-input" id="includeAllSwitch"
                                            v-model="serverFilters.include_all" @change="fetchRequerimientos">
                                        <label class="custom-control-label" for="includeAllSwitch">Ver
                                            Borradores</label>
                                    </div>
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
                            <Column field="asunto" header="Asunto" style="width:15%">
                            </Column>
                            <Column field="complejidad" header="Complejidad" style="width:8%">
                            </Column>
                            <Column field="estado" header="Estado" style="width:8%">
                                <template #body="{ data }">
                                    <span class="badge badge-pill text-uppercase px-2"
                                        :class="getStatusBadgeClass(data.estado)">
                                        {{ data.estado }}
                                    </span>
                                </template>
                            </Column>
                            <Column field="especialista.name" header="Especialista" sortable style="width:10%">
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                        class="p-column-filter" placeholder="Buscar por Especialista" />
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
                                            <i class="fas fa-calendar-alt text-primary mr-1"
                                                title="Fecha Programada"></i>
                                            <span class="text-primary font-weight-bold">{{ formatDate(data.fecha_limite)
                                                || '-' }}</span>
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
                                                <div class="progress-bar bg-info"
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
                            <Column header="Acciones" :exportable="false" style="width:15%">
                                <template #body="{ data }">
                                    <template v-if="!isMyRequerimientosView">
                                        <a href="#" title="Evaluar Requerimiento"
                                            class="mr-2 d-inline-block shadow-sm rounded-circle p-2 bg-light"
                                            @click.prevent="openModal('mostrarEvaluacion', data)">
                                            <i class="fas fa-clipboard-check text-primary"></i>
                                        </a>
                                        <a href="#" title="Asignar Requerimiento"
                                            class="mr-2 d-inline-block shadow-sm rounded-circle p-2 bg-light"
                                            :class="{ 'disabled-action': !['evaluado', 'asignado'].includes(data.estado) }"
                                            @click.prevent="['evaluado', 'asignado'].includes(data.estado) && openModal('mostrarAsignacion', data)">
                                            <i class="fas fa-user-check"
                                                :class="['evaluado', 'asignado'].includes(data.estado) ? 'text-dark' : 'text-secondary'"></i>
                                        </a>
                                    </template>
                                    <a href="#" title="Ver Avance Requerimiento"
                                        class="mr-2 d-inline-block shadow-sm rounded-circle p-2 bg-light"
                                        @click.prevent="openModal('mostrarSeguimiento', data)">
                                        <i class="fas fa-stream text-success"></i>
                                    </a>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <!-- Existing Vue Modals -->
        <requerimiento-asignacion-modal :especialistas="especialistas"
            @asignacion-guardada="fetchRequerimientos"></requerimiento-asignacion-modal>
        <requerimiento-evaluacion-modal @evaluacion-guardada="fetchRequerimientos"></requerimiento-evaluacion-modal>
        <requerimiento-seguimiento-modal></requerimiento-seguimiento-modal>
        <requerimiento-avance-modal @avance-guardado="fetchRequerimientos"></requerimiento-avance-modal>
        <evidencias-modal></evidencias-modal>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';

import Button from 'primevue/button';
import ProgressBar from 'primevue/progressbar';
import { FilterMatchMode } from 'primevue/api';

// Components
import Swal from 'sweetalert2';

const router = useRouter();

const requerimientos = ref([]);
const especialistas = ref([]);
const statuses = ref([]);
const dt = ref(null);
const selectedRequerimientoId = ref(null);
const loading = ref(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const serverFilters = ref({
    buscar_requerimiento: '',
    especialista_id: '',
    estado: '',
    include_all: false,
});

const isMyRequerimientosView = computed(() => router.currentRoute.value.name === 'requerimientos.mine');

const fetchRequerimientos = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('web.requerimientos.data'), {
            params: serverFilters.value
        });
        requerimientos.value = response.data.requerimientos;
        especialistas.value = response.data.especialistas;
        statuses.value = response.data.statuses;
    } catch (error) {
        console.error('Error fetching requerimientos:', error);
    } finally {
        loading.value = false;
    }
};

const getStatusBadgeClass = (estado) => {
    switch (estado) {
        case 'atendido':
        case 'finalizado': return 'badge-success';
        case 'desestimado':
        case 'vencido': return 'badge-danger';
        case 'aprobado': return 'badge-info';
        case 'asignado': return 'badge-primary';
        case 'evaluado': return 'badge-dark'; // Darker than primary
        case 'creado': return 'badge-secondary';
        case 'en_proceso': return 'badge-info';
        default: return 'badge-light';
    }
};

const openModal = (eventName, requerimiento) => {
    document.dispatchEvent(new CustomEvent(eventName, {
        detail: requerimiento
    }));
};

const goToCreateRequerimiento = () => {
    router.push({ name: 'requerimientos.create' });
};

const confirmDelete = (id) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(route('web.requerimientos.destroy', id));
                fetchRequerimientos();
                Swal.fire('¡Eliminado!', 'El requerimiento ha sido eliminado.', 'success');
            } catch (error) {
                Swal.fire('Error', 'Hubo un problema al eliminar.', 'error');
            }
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const exportCSV = () => { dt.value.exportCSV(); };

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
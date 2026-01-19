<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link :to="{ name: 'requerimientos.index' }" class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Mis Requerimientos</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">Mis Requerimientos</h3>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="#" class="btn btn-primary btn-sm ml-1"
                                    @click.prevent="goToCreateRequerimiento">
                                    <i class="fas fa-plus-circle"></i> Nuevo Requerimiento
                                </a>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <form @submit.prevent="fetchRequerimientos">
                                    <div class="form-row">
                                        <div class="col-md-3">
                                            <input type="text" name="buscar_requerimiento" id="buscar_requerimiento"
                                                class="form-control" placeholder="Buscar por Requerimiento"
                                                v-model="serverFilters.buscar_requerimiento">
                                        </div>
                                        <div class="col-md-3">
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
                        <!-- Loading State - Spinner circular rojo -->
                        <DataTable ref="dt" :value="requerimientos" v-model:filters="filters" paginator :rows="10"
                            :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                            :globalFilterFields="['id', 'proceso.proceso_nombre', 'asunto', 'complejidad', 'estado', 'especialista.name']"
                            :loading="loading">
                            <template #header>
                                <div class="d-flex align-items-center">
                                    <Button type="button" icon="pi pi-download" label="Descargar CSV"
                                        severity="secondary" @click="exportCSV($event)"
                                        class="btn btn-secondary ml-auto shadow-sm btn-sm">
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
                            <Column field="complejidad" header="Complejidad" style="width:10%">
                            </Column>
                            <Column field="estado" header="Estado" style="width:10%">
                                <template #body="{ data }">
                                    <span class="badge badge-pill text-uppercase px-2" :class="getStatusBadgeClass(data.estado)">
                                        {{ data.estado }}
                                    </span>
                                </template>
                            </Column>
                            <Column field="especialista.name" header="Especialista" sortable style="width:15%">
                                <template #body="{ data }">
                                    {{ data.especialista ? data.especialista.name : 'No asignado' }}
                                </template>
                            </Column>
                            <Column header="Acciones" :exportable="false" style="width:15%">
                                <template #body="{ data }">
                                    <a href="#" title="Ver Avance Requerimiento"
                                        class="mr-2 d-inline-block shadow-sm rounded-circle p-2 bg-light"
                                        @click.prevent="openModal('mostrarSeguimiento', data)">
                                        <i class="fas fa-stream text-success"></i>
                                    </a>
                                    <template v-if="data.estado === 'creado'">
                                        <router-link :to="{ name: 'requerimientos.edit', params: { id: data.id } }" 
                                            class="mr-2 d-inline-block shadow-sm rounded-circle p-2 bg-light" title="Editar">
                                            <i class="fas fa-edit text-primary"></i>
                                        </router-link>
                                        <a href="#" title="Eliminar Requerimiento"
                                            class="mr-2 d-inline-block btn-modal-trigger shadow-sm rounded-circle p-2 bg-light"
                                            @click.prevent="confirmDelete(data.id)">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </a>
                                    </template>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <!-- Existing Vue Modals -->
        <requerimiento-seguimiento-modal></requerimiento-seguimiento-modal>
        <requerimiento-avance-modal @avance-guardado="fetchRequerimientos"></requerimiento-avance-modal>
        <evidencias-modal></evidencias-modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import { FilterMatchMode } from 'primevue/api';

// Components
import Swal from 'sweetalert2';

const router = useRouter();

const requerimientos = ref([]);
const statuses = ref([]);
const dt = ref(null);
const loading = ref(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const serverFilters = ref({
    buscar_requerimiento: '',
    estado: '',
    mine: true
});

const fetchRequerimientos = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('web.requerimientos.data'), {
            params: serverFilters.value
        });
        requerimientos.value = response.data.requerimientos;
        statuses.value = response.data.statuses;
    } catch (error) {
        console.error('Error fetching requerimientos:', error);
    } finally {
        loading.value = false;
    }
};

const getStatusBadgeClass = (estado) => {
    switch (estado) {
        case 'finalizado': return 'badge-success';
        case 'vencido': return 'badge-danger';
        case 'en_proceso': return 'badge-info';
        case 'creado': return 'badge-secondary';
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

.breadcrumb {
    font-size: 0.85rem;
}
.breadcrumb-item + .breadcrumb-item::before {
    content: "\f105";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    color: #adb5bd;
}
.rounded-lg { border-radius: 0.75rem !important; }
</style>
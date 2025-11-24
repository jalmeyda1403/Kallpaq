<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link :to="{ name: 'salidas-nc.index' }">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Salidas No Conformes</li>
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
                                        placeholder="Buscar por código, descripción o producto...">
                                </div>
                                <div class="col">
                                    <select v-model="serverFilters.estado" class="form-control">
                                        <option value="">Todos los Estados</option>
                                        <option value="registrada">Registrada</option>
                                        <option value="en análisis">En Análisis</option>
                                        <option value="en tratamiento">En Tratamiento</option>
                                        <option value="tratada">Tratada</option>
                                        <option value="cerrada">Cerrada</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select v-model="serverFilters.tipo" class="form-control">
                                        <option value="">Todos los Tipos</option>
                                        <option value="producto">Producto</option>
                                        <option value="servicio">Servicio</option>
                                        <option value="proceso">Proceso</option>
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
                <DataTable ref="dt" :value="salidasNC" v-model:filters="filters" paginator :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                    :globalFilterFields="['snc_codigo', 'snc_descripcion', 'snc_producto_servicio', 'snc_tipo', 'snc_clasificacion', 'snc_estado']">
                    <template #header>
                        <div class="d-flex align-items-center">
                            <Button type="button" icon="pi pi-download" label="Descargar CSV" severity="secondary"
                                @click="exportCSV($event)" class="btn btn-secondary ml-auto">
                            </Button>
                        </div>
                    </template>
                    
                    <Column field="snc_codigo" header="Código" style="width:10%"></Column>
                    <Column field="snc_producto_servicio" header="Producto/Servicio" style="width:15%"></Column>
                    <Column field="snc_tipo" header="Tipo" style="width:10%">
                        <template #body="{ data }">
                            <span class="badge badge-secondary">{{ data.snc_tipo }}</span>
                        </template>
                    </Column>
                    <Column field="snc_clasificacion" header="Clasificación" style="width:10%">
                        <template #body="{ data }">
                            <span :class="getClasificacionBadge(data.snc_clasificacion)">
                                {{ data.snc_clasificacion }}
                            </span>
                        </template>
                    </Column>
                    <Column field="snc_estado" header="Estado" style="width:12%">
                        <template #body="{ data }">
                            <span :class="getEstadoBadge(data.snc_estado)">
                                {{ data.snc_estado }}
                            </span>
                        </template>
                    </Column>
                    <Column field="snc_fecha_deteccion" header="F. Detección" style="width:10%">
                        <template #body="{ data }">
                            {{ formatDate(data.snc_fecha_deteccion) }}
                        </template>
                    </Column>
                    <Column field="responsable.name" header="Responsable" style="width:15%">
                        <template #body="{ data }">
                            {{ data.responsable?.name || 'Sin asignar' }}
                        </template>
                    </Column>
                    <Column header="Acciones" style="width:18%">
                        <template #body="{ data }">
                            <a href="#" title="Ver Acciones" class="mr-2 d-inline-block"
                                @click.prevent="openAccionesModal(data)">
                                <i class="fas fa-tasks text-info fa-lg"></i>
                            </a>
                            <a href="#" title="Editar" class="mr-2 d-inline-block"
                                @click.prevent="openEditModal(data)">
                                <i class="fas fa-edit text-primary fa-lg"></i>
                            </a>
                            <a href="#" title="Eliminar" class="mr-2 d-inline-block"
                                @click.prevent="confirmDelete(data.id)">
                                <i class="fas fa-trash-alt text-danger fa-lg"></i>
                            </a>
                        </template>
                    </Column>
                </DataTable>
                <!-- Modals -->
                <SalidaNCModal :show="showCreateModal" :snc="selectedSNC" @update:show="showCreateModal = $event" @saved="fetchSalidasNC"></SalidaNCModal>
                <SNCAccionesModal :show="showAccionesModal" :snc-id="selectedSNCForAcciones?.id" @update:show="showAccionesModal = $event" @saved="fetchSalidasNC"></SNCAccionesModal>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import SalidaNCModal from '@/components/salidas-nc/SalidaNCModal.vue';
import SNCAccionesModal from '@/components/salidas-nc/SNCAccionesModal.vue';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { FilterMatchMode } from 'primevue/api';

const salidasNC = ref([]);
const dt = ref(null);
const selectedSNCId = ref(null);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    snc_codigo: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    snc_descripcion: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    snc_producto_servicio: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    snc_tipo: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    snc_clasificacion: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    snc_estado: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
});

const serverFilters = reactive({
    buscar_snc: '',
    estado: '',
    tipo: '',
    clasificacion: '',
});

const fetchSalidasNC = async () => {
    try {
        const response = await axios.get(route('api.salidas-nc.index'), {
            params: serverFilters
        });
        salidasNC.value = response.data;
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

const openEditModal = (snc) => {
    selectedSNC.value = snc;
    showCreateModal.value = true;
};

const showAccionesModal = ref(false);
const selectedSNCForAcciones = ref(null);

const openAccionesModal = (snc) => {
    selectedSNCForAcciones.value = snc;
    showAccionesModal.value = true;
};

const confirmDelete = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta Salida No Conforme?')) {
        deleteSNC(id);
    }
};

const deleteSNC = async (id) => {
    try {
        await axios.delete(route('api.salidas-nc.destroy', id));
        fetchSalidasNC();
    } catch (error) {
        console.error('Error al eliminar la SNC:', error);
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
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
    const badges = {
        'registrada': 'badge badge-secondary',
        'en análisis': 'badge badge-info',
        'en tratamiento': 'badge badge-warning',
        'tratada': 'badge badge-primary',
        'cerrada': 'badge badge-success'
    };
    return badges[estado] || 'badge badge-secondary';
};

const exportCSV = () => {
    dt.value.exportCSV();
};

onMounted(() => {
    fetchSalidasNC();
});
</script>

<style scoped>
.badge {
    font-size: 0.85rem;
    padding: 0.35em 0.65em;
}
</style>

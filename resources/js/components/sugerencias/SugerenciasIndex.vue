<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link :to="{ name: 'home' }"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Consolidado de Sugerencias</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Consolidado de Sugerencias</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-primary btn-sm ml-1" @click="openModal()"
                            v-if="!authStore.hasRole('facilitador')">
                            <i class="fa fa-plus-circle"></i> Nueva Sugerencia
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <form @submit.prevent="fetchSugerencias">
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" v-model="filters.proceso_nombre" class="form-control"
                                        placeholder="Buscar por nombre de proceso...">
                                </div>
                                <div class="col">
                                    <select v-model="filters.estado" class="form-control">
                                        <option value="">Todos los Estados</option>
                                        <option value="abierta">Abierta</option>
                                        <option value="en progreso">En Progreso</option>
                                        <option value="concluida">Concluida</option>
                                        <option value="observada">Observada</option>
                                        <option value="cerrada">Cerrada</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select v-model="filters.clasificacion" class="form-control">
                                        <option value="">Todas las Clasificaciones</option>
                                        <option value="MP">Mejora de procesos y servicios</option>
                                        <option value="MT">Mejora tecnológica</option>
                                        <option value="AC">Atención al cliente y trato del personal</option>
                                        <option value="MF">Mejora de infraestructura física</option>
                                        <option value="CF">Capacitación y formación</option>
                                        <option value="CT">Comunicación y transparencia</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn bg-dark">
                                        <i class="fa fa-search"></i> Buscar
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
                    <ProgressBar v-if="storeLoading" mode="indeterminate" style="height: 4px;" />
                </div>
                <!-- Tabla -->
                <DataTable ref="dt" :value="sugerencias" v-model:filters="filtersPrimevue"
                    v-model:selection="selectedSugerencia" selectionMode="single" paginator :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                    :globalFilterFields="['id', 'sugerencia_clasificacion', 'sugerencia_estado', 'sugerencia_procedencia', 'proceso.proceso_nombre']"
                    :class="{ 'opacity-50 pointer-events-none': storeLoading }"
                    class="p-datatable-sm p-datatable-striped p-datatable-hoverable-rows">
                    <template #header>
                        <div class="d-flex align-items-center">
                            <Button type="button" icon="pi pi-download" label="Descargar CSV" severity="secondary"
                                @click="exportCSV($event)" class="btn btn-secondary ml-auto" />
                        </div>
                    </template>

                    <Column field="id" header="ID" style="width:5%">
                    </Column>
                    <Column field="proceso.proceso_nombre" header="Proceso" style="width:15%">
                        <template #body="{ data }">
                            {{ data.proceso?.proceso_nombre || 'N/A' }}
                        </template>
                    </Column>
                    <Column field="sugerencia_detalle" header="Sugerencia" style="width:30%">
                        <template #body="{ data }">
                            <span :title="data.sugerencia_detalle">
                                {{ truncateText(data.sugerencia_detalle, 60) }}
                            </span>
                        </template>
                    </Column>
                    <Column field="sugerencia_procedencia" header="Procedencia" style="width:10%">
                    </Column>
                    <Column field="sugerencia_clasificacion" header="Clasificación" style="width:20%">
                        <template #body="{ data }">
                            {{ getClasificacionFullName(data.sugerencia_clasificacion) }}
                        </template>
                    </Column>
                    <Column field="sugerencia_estado" header="Estado" style="width:10%; text-align: center;">
                        <template #body="{ data }">
                            <span :class="getStatusBadgeClass(data.sugerencia_estado)" class="badge-text">
                                {{ data.sugerencia_estado }}
                            </span>
                        </template>
                    </Column>
                    <Column field="sugerencia_fecha_ingreso" header="F. Ingreso" style="width:10%">
                        <template #body="{ data }">
                            {{ formatDate(data.sugerencia_fecha_ingreso) }}
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:15%" headerStyle="width: 15%"
                        bodyStyle="width: 15%">
                        <template #body="{ data }">
                            <a href="#" title="Editar" class="mr-2 d-inline-block" @click.prevent="editSugerencia(data)"
                                v-if="!authStore.hasRole('facilitador')">
                                <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                            </a>
                            <a href="#" title="Tratamiento" class="mr-2 d-inline-block"
                                @click.prevent="openTratamiento(data)">
                                <i class="fas fa-tasks text-info fa-lg"></i>
                            </a>
                            <a v-if="data.sugerencia_estado === 'concluida'" href="#" title="Validar"
                                class="mr-2 d-inline-block" @click.prevent="validateSugerencia(data)">
                                <i class="fas fa-check text-success fa-lg"></i>
                            </a>
                            <a href="#" title="Eliminar" class="d-inline-block"
                                v-if="authStore.hasRole('admin') || authStore.hasRole('especialista')"
                                @click.prevent="deleteSugerencia(data.id)">
                                <i class="fas fa-trash-alt text-danger fa-lg"></i>
                            </a>
                        </template>
                    </Column>
                </DataTable>
            </div>

            <!-- Modal Create/Edit -->
            <SugerenciaModal :show="showModal" :sugerencia-id="selectedSugerencia?.id" @close="closeModal"
                @saved="fetchSugerencias">
            </SugerenciaModal>

            <!-- Modal Tratamiento -->
            <SugerenciaTratamiento :show="showTratamientoModal" :sugerencia-id="selectedSugerencia?.id"
                @close="closeTratamientoModal" @saved="fetchSugerencias"
                @open-evaluation="openEvaluacionFromTratamiento">
            </SugerenciaTratamiento>

            <!-- Modal Evaluación -->
            <SugerenciaEvaluacionModal :show="showEvaluacionModal" :sugerencia-id="selectedSugerenciaForEvaluation?.id"
                @close="closeEvaluacionModal" @validated="handleEvaluacionValidated"></SugerenciaEvaluacionModal>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useSugerenciasStore } from '@/stores/sugerenciasStore';
import { useAuthStore } from '@/stores/authStore';
import { storeToRefs } from 'pinia';
import SugerenciaModal from './SugerenciaModal.vue';
import SugerenciaTratamiento from './SugerenciaTratamiento.vue';
import SugerenciaEvaluacionModal from './SugerenciaEvaluacionModal.vue';
import Swal from 'sweetalert2';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ProgressBar from 'primevue/progressbar';
import Button from 'primevue/button';
import { FilterMatchMode } from 'primevue/api';

// Usamos el store
const sugerenciasStore = useSugerenciasStore();
const authStore = useAuthStore();
const { sugerencias, loading: storeLoading } = storeToRefs(sugerenciasStore);

const showModal = ref(false);
const showTratamientoModal = ref(false);
const showEvaluacionModal = ref(false);
const selectedSugerencia = ref(null);
const selectedSugerenciaForEvaluation = ref(null);
const dt = ref(null);

// Filtros
const filters = reactive({
    proceso_nombre: '',
    estado: '',
    proceso_id: '',
    clasificacion: ''
});

const filtersPrimevue = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    id: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    sugerencia_clasificacion: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    sugerencia_estado: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    sugerencia_procedencia: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    'proceso.proceso_nombre': { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Métodos
const fetchSugerencias = async () => {
    try {
        await sugerenciasStore.fetchSugerencias(filters);
    } catch (error) {
        console.error('Error fetching sugerencias:', error);
    }
};

const openModal = () => {
    selectedSugerencia.value = null;
    showModal.value = true;
};

const editSugerencia = async (sugerencia) => {
    try {
        // Cargamos la sugerencia específica en el store
        await sugerenciasStore.fetchSugerenciaById(sugerencia.id);
        selectedSugerencia.value = sugerencia;
        showModal.value = true;
    } catch (error) {
        console.error('Error al cargar datos de la sugerencia:', error);
    }
};

const closeModal = () => {
    showModal.value = false;
    selectedSugerencia.value = null;
};

const openTratamiento = (sugerencia) => {
    selectedSugerencia.value = sugerencia;
    showTratamientoModal.value = true;
};

const closeTratamientoModal = () => {
    showTratamientoModal.value = false;
    selectedSugerencia.value = null;
};

const validateSugerencia = (sugerencia) => {
    selectedSugerenciaForEvaluation.value = sugerencia;
    showEvaluacionModal.value = true;
};

const closeEvaluacionModal = () => {
    showEvaluacionModal.value = false;
    selectedSugerenciaForEvaluation.value = null;
};

const openEvaluacionFromTratamiento = (sugerenciaId) => {
    // NO cerramos el modal de tratamiento para que quede de fondo (overlay)
    // showTratamientoModal.value = false; 

    // Abrimos el modal de evaluación
    selectedSugerenciaForEvaluation.value = { id: sugerenciaId };
    showEvaluacionModal.value = true;
};

const handleEvaluacionValidated = () => {
    // Primero, refrescamos la lista
    fetchSugerencias();

    // Cerramos modal de evaluación
    closeEvaluacionModal();

    // Y ahora sí, cerramos el modal de tratamiento porque el flujo terminó
    closeTratamientoModal();

    Swal.fire({
        icon: 'success',
        title: 'Proceso completado',
        text: 'La sugerencia ha sido validada y cerrada correctamente.',
        timer: 2000,
        showConfirmButton: false
    });
};

const confirmDelete = async () => {
    if (!selectedSugerencia.value) {
        await Swal.fire('Advertencia', 'Por favor selecciona una sugerencia para eliminar.', 'warning');
        return;
    }

    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar!'
    });

    if (result.isConfirmed) {
        try {
            await sugerenciasStore.deleteSugerencia(selectedSugerencia.value.id);
            await Swal.fire(
                'Eliminado!',
                'La sugerencia ha sido eliminada.',
                'success'
            );
            selectedSugerencia.value = null; // Limpiar la selección después de eliminar
        } catch (error) {
            await Swal.fire(
                'Error!',
                'Hubo un problema al eliminar la sugerencia.',
                'error'
            );
        }
    }
};

const deleteSugerencia = async (id) => {
    // Esta función se puede usar para eliminar desde las acciones directamente
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar!'
    });

    if (result.isConfirmed) {
        try {
            await sugerenciasStore.deleteSugerencia(id);
            await Swal.fire(
                'Eliminado!',
                'La sugerencia ha sido eliminada.',
                'success'
            );
            if (selectedSugerencia.value?.id === id) {
                selectedSugerencia.value = null; // Limpiar la selección si se elimina la sugerencia seleccionada
            }
        } catch (error) {
            await Swal.fire(
                'Error!',
                'Hubo un problema al eliminar la sugerencia.',
                'error'
            );
        }
    }
};

const exportCSV = (event) => {
    dt.value.exportCSV();
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
};

const truncateText = (text, length) => {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
};

const getClasificacionFullName = (code) => {
    const map = {
        'MP': 'Mejora de procesos y servicios',
        'MT': 'Mejora tecnológica',
        'AC': 'Atención al cliente y trato del personal',
        'MF': 'Mejora de infraestructura física',
        'CF': 'Capacitación y formación',
        'CT': 'Comunicación y transparencia'
    };
    return map[code] || code;
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'abierta': return 'badge badge-warning';
        case 'en progreso': return 'badge badge-primary';
        case 'implementada': return 'badge badge-success';
        case 'concluida': return 'badge badge-info';
        case 'observada': return 'badge badge-warning';
        case 'cerrada': return 'badge badge-purple';
        default: return 'badge badge-secondary';
    }
};

onMounted(() => {
    fetchSugerencias();
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
</style>

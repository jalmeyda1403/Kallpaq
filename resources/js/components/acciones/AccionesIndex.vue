<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/home">Home</a></li>
                <li class="breadcrumb-item"><router-link :to="{ name: 'hallazgos.mine.vue' }">Mis Hallazgos</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Planes de Acción</li>
            </ol>
        </nav>

        <!-- Detalles del Hallazgo (Solo Lectura) -->
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Detalles del Hallazgo: {{ hallazgo.hallazgo_cod }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <strong>Resumen:</strong> {{ hallazgo.hallazgo_resumen }}
                    </div>
                    <div class="col-md-12 mb-2">
                        <strong>Descripción:</strong> 
                        <p class="text-muted">{{ hallazgo.hallazgo_descripcion }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Clasificación:</strong> <span class="badge badge-secondary">{{ hallazgo.hallazgo_clasificacion }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Estado:</strong> <span class="badge badge-primary">{{ hallazgo.hallazgo_estado }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Origen:</strong> {{ hallazgo.hallazgo_origen }}
                    </div>
                    <div class="col-md-3">
                        <strong>Fecha Identificación:</strong> {{ formatDate(hallazgo.hallazgo_fecha_identificacion) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Análisis de Causa Raíz -->
        <CausaRaiz :hallazgoId="hallazgoId" />

        <!-- Planes de Acción -->
        <div class="card mt-3">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Planes de Acción</h5>
            </div>
            <div class="card-body">
                <!-- Botón Nueva Acción en el body, alineado a la derecha -->
                <div class="text-right mb-3">
                    <button class="btn btn-primary btn-sm" @click="openCreateModal">
                        <i class="fas fa-plus"></i> Nueva Acción
                    </button>
                </div>

                <DataTable :value="acciones" responsiveLayout="scroll" :loading="isLoading">
                    <Column field="accion_cod" header="Código" style="width: 8%;"></Column>
                    <Column field="Proceso" header="Proceso" style="width: 15%;">
                        <template #body="{ data }">
                            {{ data.hallazgo_proceso?.proceso?.proceso_nombre }}
                        </template>
                    </Column>
                    <Column field="accion_descripcion" header="Descripción" style="width: 30%;"></Column>
                    <Column field="accion_responsable" header="Responsable" style="width: 15%;"></Column>
                    <Column field="accion_fecha_inicio" header="F. Inicio">
                        <template #body="{ data }">
                            {{ formatDate(data.accion_fecha_inicio) }}
                        </template>
                    </Column>
                    <Column field="accion_fecha_fin_planificada" header="F. Fin Prog.">
                        <template #body="{ data }">
                            {{ formatDate(data.accion_fecha_fin_planificada) }}
                        </template>
                    </Column>
                    <Column field="accion_fecha_fin_reprogramada" header="F. Fin Reprog.">
                        <template #body="{ data }">
                            {{ formatDate(data.accion_fecha_fin_reprogramada) }}
                        </template>
                    </Column>
                    <Column field="accion_estado" header="Estado">
                        <template #body="{ data }">
                            <span :class="getEstadoBadgeClass(data.accion_estado)">{{ data.accion_estado }}</span>
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false">
                        <template #body="{ data }">
                            <a href="#" @click.prevent="openReprogramarModal(data)" :class="['mr-2', { 'disabled': isAccionTerminada(data) }]" title="Gestionar Acción">
                                <i class="fas fa-calendar-alt fa-lg text-warning"></i>
                            </a>
                            <a href="#" @click.prevent="openConcluirModal(data)" :class="{ 'disabled': isAccionTerminada(data) }" title="Concluir Acción">
                                <i class="fas fa-check-circle fa-lg text-success"></i>
                            </a>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <!-- Modals -->
        <ReprogramarAccionModal @accion-gestionada="fetchAcciones" />
        <ConcluirAccionModal @accion-concluida="fetchAcciones" />
        <AccionCreateModal :hallazgoId="hallazgoId" :procesos="hallazgo.procesos" @accion-creada="fetchAcciones" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import axios from 'axios';
import { route } from 'ziggy-js';

// Import modals and components
import ReprogramarAccionModal from './ReprogramarAccionModal.vue';
import ConcluirAccionModal from './ConcluirAccionModal.vue';
import AccionCreateModal from './AccionCreateModal.vue';
import CausaRaiz from './CausaRaiz.vue';

const props = defineProps({
    hallazgoId: {
        type: [Number, String],
        required: true
    }
});

const acciones = ref([]);
const hallazgo = ref({});
const isLoading = ref(true);

const isAccionTerminada = (accion) => {
    return ['desestimada', 'finalizada'].includes(accion.accion_estado);
};

const getEstadoBadgeClass = (estado) => {
    switch (estado) {
        case 'programada': return 'badge badge-primary';
        case 'en ejecucion': return 'badge badge-info';
        case 'finalizada': return 'badge badge-success';
        case 'desestimada': return 'badge badge-secondary';
        case 'reprogramada': return 'badge badge-warning';
        default: return 'badge badge-light';
    }
};

const openReprogramarModal = (accion) => {
    document.dispatchEvent(new CustomEvent('open-reprogramar-modal', { detail: accion }));
};

const openConcluirModal = (accion) => {
    document.dispatchEvent(new CustomEvent('open-concluir-modal', { detail: accion }));
};

const openCreateModal = () => {
    document.dispatchEvent(new CustomEvent('open-create-accion-modal'));
};

const fetchAcciones = async () => {
    try {
        const response = await axios.get(route('api.acciones.por-hallazgo', { hallazgo: props.hallazgoId }));
        acciones.value = response.data;
    } catch (error) {
        console.error('Error al obtener las acciones:', error);
    }
};

const fetchHallazgoData = async () => {
    try {
        const response = await axios.get(route('smp.show', { smp: props.hallazgoId }));
        hallazgo.value = response.data;
    } catch (error) {
        console.error('Error al obtener los datos del hallazgo:', error);
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
};

onMounted(async () => {
    isLoading.value = true;
    await Promise.all([
        fetchHallazgoData(),
        fetchAcciones()
    ]);
    isLoading.value = false;
});
</script>

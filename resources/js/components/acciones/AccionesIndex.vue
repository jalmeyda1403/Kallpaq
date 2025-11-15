<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/home">Home</a></li>
                <li class="breadcrumb-item"><router-link :to="{ name: 'hallazgos.index' }">Solicitudes de Mejora</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Planes de Acción</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    Planes de Acción del Hallazgo: {{ hallazgo.hallazgo_cod || 'Cargando...' }}
                </h3>
            </div>
            <div class="card-body">
                <DataTable :value="acciones" responsiveLayout="scroll">
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
                    <Column field="accion_estado" header="Estado"></Column>
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
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import axios from 'axios';
import { route } from 'ziggy-js';

// Import modals
import ReprogramarAccionModal from './ReprogramarAccionModal.vue';
import ConcluirAccionModal from './ConcluirAccionModal.vue';

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

const openReprogramarModal = (accion) => {
    document.dispatchEvent(new CustomEvent('open-reprogramar-modal', { detail: accion }));
};

const openConcluirModal = (accion) => {
    document.dispatchEvent(new CustomEvent('open-concluir-modal', { detail: accion }));
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
        const response = await axios.get(route('hallazgo.show', { hallazgo: props.hallazgoId }));
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

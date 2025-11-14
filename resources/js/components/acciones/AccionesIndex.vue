<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Solicitudes de Mejora</li>
                <li class="breadcrumb-item active" aria-current="page">Planes de Acción</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">Planes de Acción</h3>
            </div>
            <div class="card-body">
                <DataTable :value="acciones" responsiveLayout="scroll">
                    <Column field="accion_cod" header="Código" style="width: 10%"></Column>
                    <Column field="Proceso" header="Proceso" style="width: 20%">
                        <template #body="{ data }">
                            {{ data.hallazgo_proceso?.proceso?.proceso_nombre }}
                        </template>
                    </Column>
                    <Column field="accion_descripcion" header="Acción inmediata o correctiva" style="width: 35%;"></Column>
                    <Column field="accion_responsable" header="Responsable" style="width: 20%"></Column>
                    <Column field="accion_fecha_inicio" header="Inicio">
                        <template #body="{ data }">
                            {{ formatDate(data.accion_fecha_inicio) }}
                        </template>
                    </Column>
                    <Column field="accion_fecha_fin_planificada" header="Fin Prog" style="width: 10%;">
                        <template #body="{ data }">
                            {{ formatDate(data.accion_fecha_fin_planificada) }}
                        </template>
                    </Column>
                    <Column field="accion_fecha_fin_reprogramada" header="Fin Rep" style="width: 10%;">
                        <template #body="{ data }">
                            {{ formatDate(data.accion_fecha_fin_reprogramada) }}
                        </template>
                    </Column>
                    <Column field="accion_estado" header="Estado" style="width: 10%;"></Column>
                    <Column header="Acciones" style="width:10%">
                        <template #body="{ data }">
                            <a href="#" class="text-warning mr-3" title="Reprogramar"
                                :class="{ 'disabled-link': isAccionTerminada(data) }"
                                @click.prevent="!isAccionTerminada(data) && openReprogramarModal(data)">
                                <i class="fas fa-clock fa-lg" :class="{ 'text-secondary': isAccionTerminada(data) }"></i>
                            </a>

                            <a href="#" class="text-success" title="Concluir"
                                :class="{ 'disabled-link': isAccionTerminada(data) }"
                                @click.prevent="!isAccionTerminada(data) && openConcluirModal(data)">
                                <i class="fas fa-check-circle fa-lg"  :class="{ 'text-secondary': isAccionTerminada(data) }"></i>
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
    isLoading.value = true;
    try {
        const response = await axios.get(route('api.acciones.por-hallazgo', { hallazgo: props.hallazgoId }));
        acciones.value = response.data;
    } catch (error) {
        console.error('Error al obtener las acciones:', error);
    } finally {
        isLoading.value = false;
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

onMounted(() => {
    fetchAcciones();
});
</script>

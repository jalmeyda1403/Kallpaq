<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Obligaciones</li>
                <li class="breadcrumb-item active" aria-current="page">Radar Normativo (IA)</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-satellite-dish mr-2"></i> Radar de Obligaciones
                    </h3>
                    <div>
                        <Button label="Escanear Nuevas Normas (IA)" icon="pi pi-search" class="p-button-info"
                            @click="scanNormas" :loading="radarStore.loading" />
                    </div>
                </div>
            </div>

            <div class="card-body">
                <DataTable :value="radarStore.normas" :loading="radarStore.loading" paginator :rows="10"
                    responsiveLayout="scroll" dataKey="id">
                    <template #empty>
                        No se han detectado nuevas normas. Haga clic en "Escanear".
                    </template>

                    <Column field="titulo" header="Norma / Título" sortable>
                        <template #body="{ data }">
                            <strong>{{ data.numero_norma }}</strong>
                            <a v-if="data.url_fuente" :href="data.url_fuente" target="_blank" class="ml-2 text-primary"
                                title="Ver fuente oficial">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <br>
                            {{ data.titulo }}
                        </template>
                    </Column>
                    <Column field="organismo_emisor" header="Emisor" sortable></Column>
                    <Column field="fecha_publicacion" header="Fecha" sortable>
                        <template #body="{ data }">
                            {{ formatDate(data.fecha_publicacion) }}
                        </template>
                    </Column>
                    <Column field="nivel_relevancia" header="Relevancia" sortable>
                        <template #body="{ data }">
                            <span :class="getRelevanciaClass(data.nivel_relevancia)">
                                {{ data.nivel_relevancia }}
                            </span>
                        </template>
                    </Column>
                    <Column field="estado" header="Estado" sortable>
                        <template #body="{ data }">
                            <span :class="getEstadoClass(data.estado)">
                                {{ data.estado }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Acciones">
                        <template #body="{ data }">
                            <div v-if="data.estado === 'Pendiente' || data.estado === 'En Análisis'">
                                <Button icon="pi pi-check" class="p-button-rounded p-button-success mr-2"
                                    title="Aprobar / Crear Obligación" @click="openModal(data, 'approve')" />
                                <Button icon="pi pi-times" class="p-button-rounded p-button-danger"
                                    title="Descartar / No Aplica" @click="openModal(data, 'reject')" />
                            </div>
                            <div v-else>
                                <small class="text-muted">{{ data.analisis_humano }}</small>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <AnalisisNormativoModal :show="showModal" :norma="selectedNorma" :mode="modalMode" @close="closeModal"
            @confirm="handleConfirm" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRadarStore } from '@/stores/radarStore';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import AnalisisNormativoModal from './AnalisisNormativoModal.vue';
import Swal from 'sweetalert2';

const radarStore = useRadarStore();
const showModal = ref(false);
const selectedNorma = ref(null);
const modalMode = ref('approve');

onMounted(() => {
    radarStore.fetchNormas();
});

const scanNormas = async () => {
    try {
        await radarStore.scanNormas();
        Swal.fire('Escaneo Completado', 'Se han detectado nuevas normas potenciales.', 'success');
    } catch (error) {
        const errorMessage = radarStore.error || 'No se pudo completar el escaneo. Verifique la conexión con El Peruano.';
        Swal.fire('Error', errorMessage, 'error');
    }
};

const openModal = (norma, mode) => {
    selectedNorma.value = norma;
    modalMode.value = mode;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedNorma.value = null;
};

const handleConfirm = async (data) => {
    try {
        if (modalMode.value === 'approve') {
            await radarStore.approveNorma(data.id, data);
            Swal.fire('Aprobado', 'La obligación ha sido creada exitosamente.', 'success');
        } else {
            await radarStore.rejectNorma(data.id, data.analisis_humano);
            Swal.fire('Rechazado', 'La norma ha sido marcada como No Aplicable.', 'info');
        }
    } catch (error) {
        Swal.fire('Error', 'Hubo un problema al procesar la solicitud.', 'error');
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString();
};

const getRelevanciaClass = (relevancia) => {
    switch (relevancia) {
        case 'Alta': return 'badge badge-danger';
        case 'Media': return 'badge badge-warning';
        case 'Baja': return 'badge badge-info';
        default: return 'badge badge-secondary';
    }
};

const getEstadoClass = (estado) => {
    switch (estado) {
        case 'Aplicable': return 'badge badge-success';
        case 'No Aplicable': return 'badge badge-secondary';
        case 'Pendiente': return 'badge badge-primary';
        default: return 'badge badge-light';
    }
};
</script>

<style scoped>
.badge {
    font-size: 0.9em;
    padding: 5px 10px;
}
</style>

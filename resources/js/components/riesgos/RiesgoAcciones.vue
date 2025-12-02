<template>
    <div class="container-fluid">
        <!-- Encabezado -->
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ formatBreadcrumbId(store.riesgoActual ? store.riesgoActual.id : null)
                    }}</span>
                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                <span class="text-dark">Planes de Tratamiento</span>
            </h6>
        </div>
        <div class="text-left mb-4">
            <h6 class="mb-1" style="font-weight: bold;">PLANES DE TRATAMIENTO</h6>
            <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                Gestione las acciones necesarias para mitigar el riesgo identificado.
            </p>
        </div>

        <div class="row mb-3">
            <div class="col-12 text-right">
                <button class="btn btn-primary btn-sm" @click="openModal()">
                    <i class="fas fa-plus"></i> Añadir Acciones
                </button>
            </div>
        </div>

        <DataTable :value="store.acciones" :paginator="true" :rows="5" :loading="store.loadingAcciones"
            responsiveLayout="scroll" class="p-datatable-sm">
            <template #empty>
                No hay planes de tratamiento registrados.
            </template>
            <Column field="ra_descripcion" header="Descripción"></Column>
            <Column field="ra_responsable" header="Responsable"></Column>
            <Column field="ra_ciclo" header="Ciclo" headerStyle="width: 80px; text-align: center"
                bodyStyle="text-align: center">
            </Column>
            <Column header="Fecha Inicio">
                <template #body="slotProps">
                    {{ formatDate(slotProps.data.ra_fecha_inicio) }}
                </template>
            </Column>
            <Column header="Fecha Fin">
                <template #body="slotProps">
                    <div v-if="slotProps.data.ra_fecha_fin_reprogramada">
                        <span class="text-danger" style="text-decoration: line-through;">
                            {{ formatDate(slotProps.data.ra_fecha_fin_planificada) }}
                        </span>
                        <br>
                        <span class="text-success font-weight-bold">
                            {{ formatDate(slotProps.data.ra_fecha_fin_reprogramada) }}
                        </span>
                    </div>
                    <div v-else>
                        {{ formatDate(slotProps.data.ra_fecha_fin_planificada) }}
                    </div>
                    <!-- Indicator for pending reprogramming -->
                    <div v-if="hasPendingReprogramming(slotProps.data)" class="mt-1">
                        <span class="badge badge-warning">
                            <i class="fas fa-clock"></i> Solicitud Pendiente
                        </span>
                    </div>
                </template>
            </Column>
            <Column header="Estado">
                <template #body="slotProps">
                    <span class="badge" :class="getEstadoBadgeClass(slotProps.data.ra_estado)">
                        {{ slotProps.data.ra_estado }}
                    </span>
                </template>
            </Column>
            <Column header="Acciones" headerStyle="width: 150px; text-align: center" bodyStyle="text-align: center">
                <template #body="slotProps">
                    <a href="#" class="mr-2 d-inline-block" @click.prevent="openModal(slotProps.data)" title="Editar">
                        <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                    </a>
                    <a href="#" class="mr-2 d-inline-block" @click.prevent="openReprogramarModal(slotProps.data)"
                        title="Reprogramar">
                        <i class="fas fa-clock text-info fa-lg"></i>
                    </a>
                    <a href="#" class="d-inline-block" @click.prevent="confirmDelete(slotProps.data)" title="Eliminar">
                        <i class="fas fa-trash-alt text-danger fa-lg"></i>
                    </a>
                </template>
            </Column>
        </DataTable>

        <RiesgoAccionesForm :show="showActionModal" :actionData="selectedAction" @close="closeModal"
            @saved="onActionSaved" />

        <RiesgoAccionesReproForm :show="showReproModal" :actionData="selectedReproAction" @close="closeReprogramarModal"
            @updated="onReproUpdated" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import Swal from 'sweetalert2';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import RiesgoAccionesForm from './RiesgoAccionesForm.vue';
import RiesgoAccionesReproForm from './RiesgoAccionesReproForm.vue';

const store = useRiesgoStore();

const formatBreadcrumbId = (id) => {
    if (!id) return 'Riesgo';
    return `R${id.toString().padStart(6, '0')}`;
};

const showActionModal = ref(false);
const showReproModal = ref(false);
const selectedAction = ref(null);
const selectedReproAction = ref(null);

onMounted(() => {
    if (store.riesgoActual && store.riesgoActual.id) {
        store.fetchAcciones(store.riesgoActual.id);
    }
});

const openModal = (accion = null) => {
    selectedAction.value = accion;
    showActionModal.value = true;
};

const closeModal = () => {
    showActionModal.value = false;
    selectedAction.value = null;
};

const onActionSaved = () => {
    // Refresh list or handle update if needed (store updates automatically usually if we push to it)
    // But fetchAcciones is safer to ensure sync
    if (store.riesgoActual && store.riesgoActual.id) {
        store.fetchAcciones(store.riesgoActual.id);
    }
};

const openReprogramarModal = (action) => {
    selectedReproAction.value = action;
    showReproModal.value = true;
};

const closeReprogramarModal = () => {
    showReproModal.value = false;
    selectedReproAction.value = null;
};

const onReproUpdated = (updatedAction) => {
    // Update the action in the list locally to reflect changes immediately
    const index = store.acciones.findIndex(a => a.id === updatedAction.id);
    if (index !== -1) {
        store.acciones[index] = updatedAction;
    }
    // Also update the selected action passed to the modal if it's still open (for history update)
    selectedReproAction.value = updatedAction;
};

const confirmDelete = (accion) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await store.deleteAccion(accion.id);
                Swal.fire('Eliminado!', 'El plan ha sido eliminado.', 'success');
            } catch (error) {
                Swal.fire('Error', 'No se pudo eliminar el plan.', 'error');
            }
        }
    });
};

const hasPendingReprogramming = (action) => {
    if (!action.reprogramaciones) return false;
    return action.reprogramaciones.some(r => r.rar_estado === 'pendiente');
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString();
};

const getEstadoBadgeClass = (status) => {
    const badges = {
        'programada': 'badge badge-secondary',
        'en proceso': 'badge badge-primary',
        'implementada': 'badge badge-success',
        'desestimada': 'badge badge-danger'
    };
    return badges[status] || 'badge badge-secondary';
};
</script>

<style scoped>
.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #dc3545;
    display: flex;
    align-items: center;
}
</style>
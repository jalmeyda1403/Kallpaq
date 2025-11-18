<template>
    <div class="container-fluid">
        <!-- Header -->
        <div class="text-left mb-4">
            <div class="header-container">
                <h6 class="mb-0 d-flex align-items-center">
                    <span class="text-dark">{{ inventarioStore.modalTitle }}</span>
                    <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                    <span class="text-dark">{{ inventarioStore.currentInventario?.nombre || 'Nuevo Inventario' }}</span>
                </h6>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div v-if="inventarioStore.localLoading" class="loading-spinner w-100 text-center my-5 p-5">
            <div class="spinner-border spinner-lg text-danger" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>

        <!-- DataTable -->
        <div v-show="!inventarioStore.localLoading" style="min-height: 300px;">
            <DataTable :value="inventarioStore.allProcesos" :paginator="true" :rows="10" responsiveLayout="scroll" class="h-100">
                <template #header>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-dark btn-sm mr-2" @click="inventarioStore.openNewProcessModal">
                            <i class="fas fa-plus"></i> Agregar Proceso
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" @click="inventarioStore.openAssignProcessModal">
                            <i class="fas fa-plus-circle"></i> Asignar Proceso
                        </button>
                    </div>
                </template>

                <Column field="cod_proceso" header="Código"></Column>
                <Column field="proceso_nombre" header="Nombre"></Column>
                <Column field="nombre_ouo_propietario" header="Propietario"></Column>
                <Column header="Estado">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.estado === 1 ? 'Vigente' : 'Inactivo'" 
                             :severity="slotProps.data.estado === 1 ? 'success' : 'danger'" />
                    </template>
                </Column>
                <Column header="Acciones">
                    <template #body="slotProps">
                        <button type="button" class="btn btn-sm btn-warning mr-2" @click="inventarioStore.openModifyOwnerModal(slotProps.data)">
                            <i class="pi pi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" @click="inventarioStore.disassociateProcess(slotProps.data)">
                            <i class="pi pi-trash"></i>
                        </button>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Modals -->
        <div class="modal fade" id="newProcessModal" tabindex="-1" aria-labelledby="newProcessModalLabel" aria-hidden="true" ref="newProcessModalRef">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="newProcessModalLabel">Agregar Nuevo Proceso</h5>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ProcesoForm @proceso-creado="inventarioStore.handleProcesoCreado" />
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="assignProcessModal" tabindex="-1" aria-labelledby="assignProcessModalLabel" aria-hidden="true" ref="assignProcessModalRef">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="assignProcessModalLabel">Asignar Proceso Existente</h5>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <AsignarProcesoModal />
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modifyOwnerModal" tabindex="-1" aria-labelledby="modifyOwnerModalLabel" aria-hidden="true" ref="modifyOwnerModalRef">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modifyOwnerModalLabel">Modificar Propietario</h5>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ModificarPropietarioModal />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import { useInventarioStore } from '@/stores/inventarioStore';

// PrimeVue components
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';

// Custom components
import ProcesoForm from '../procesos/ProcesoForm.vue';
import AsignarProcesoModal from './AsignarProcesoModal.vue';
import ModificarPropietarioModal from './ModificarPropietarioModal.vue';

const inventarioStore = useInventarioStore();

// Watch for modal state changes to show/hide Bootstrap modals
watch(() => inventarioStore.displayNewProcessModal, (newValue) => {
    const modal = document.getElementById('newProcessModal');
    if (newValue) {
        $(modal).modal('show');
    } else {
        $(modal).modal('hide');
    }
});

watch(() => inventarioStore.displayAssignProcessModal, (newValue) => {
    const modal = document.getElementById('assignProcessModal');
    if (newValue) {
        $(modal).modal('show');
    } else {
        $(modal).modal('hide');
    }
});

watch(() => inventarioStore.displayModifyOwnerModal, (newValue) => {
    const modal = document.getElementById('modifyOwnerModal');
    if (newValue) {
        $(modal).modal('show');
    } else {
        $(modal).modal('hide');
    }
});

// Lifecycle hook
onMounted(() => {
    inventarioStore.loadProcesos();
});
</script>

<style scoped>
.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}
</style>

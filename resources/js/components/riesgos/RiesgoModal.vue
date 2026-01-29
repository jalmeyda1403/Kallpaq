<template>
    <div class="modal fade" tabindex="-1" aria-labelledby="riesgoModalLabel" aria-hidden="true" ref="modalRef"
        id="riesgoModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="riesgoModalLabel">
                        <i class="fas fa-shield-alt mr-2"></i>
                        {{ store.modalTitle }}
                    </h5>
                    <button type="button" class="close text-white" @click="store.closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-body-scrollable p-0">
                    <div class="d-flex h-100">
                        <!-- Sidebar de Navegación -->
                        <div class="nav flex-column nav-pills bg-light border-right p-3" style="width: 250px;"
                            role="tablist" aria-orientation="vertical">
                            <a class="nav-link"
                                :class="{ 'text-danger active': store.currentTab === 'RiesgoForm', 'd-none': store.isActionPlanMode }"
                                @click="store.setCurrentTab('RiesgoForm')" role="tab">
                                <i class="fas fa-file-alt"></i> Identificar Riesgo
                            </a>

                            <div :class="{ 'disabled-links': !store.isEditing && !store.isActionPlanMode }">
                                <a class="nav-link"
                                    :class="{ 'text-danger active': store.currentTab === 'RiesgoEvaluacionForm', 'd-none': store.isActionPlanMode }"
                                    @click="store.setCurrentTab('RiesgoEvaluacionForm')" role="tab">
                                    <i class="fas fa-chart-bar"></i> Evaluar Riesgo
                                </a>
                                <a class="nav-link"
                                    :class="{ 'text-danger active': store.currentTab === 'RiesgoAcciones' }"
                                    @click="store.setCurrentTab('RiesgoAcciones')" role="tab">
                                    <i class="fas fa-tasks"></i> Tratar Riesgo
                                </a>
                                <a class="nav-link"
                                    :class="{ 'text-danger active': store.currentTab === 'RiesgoAsignarEspecialista', 'd-none': store.isActionPlanMode }"
                                    @click="store.setCurrentTab('RiesgoAsignarEspecialista'); store.fetchAsignaciones()"
                                    role="tab">
                                    <i class="fas fa-user-tie"></i> Asignar Especialista
                                </a>
                                <a class="nav-link"
                                    :class="{ 'text-danger active': store.currentTab === 'RiesgoVerificacionForm', 'd-none': store.isActionPlanMode }"
                                    @click="store.setCurrentTab('RiesgoVerificacionForm')" role="tab">
                                    <i class="fas fa-check-circle"></i> Verificar Eficacia
                                </a>
                            </div>
                        </div>

                        <!-- Contenido del Tab -->
                        <div class="flex-grow-1 p-4">
                            <component :is="tabs[store.currentTab]" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, shallowRef } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import { Modal } from 'bootstrap';

// Importar componentes de los tabs
import RiesgoForm from './RiesgoForm.vue';
import RiesgoEvaluacionForm from './RiesgoEvaluacionForm.vue';
import RiesgoAcciones from './RiesgoAcciones.vue';
import RiesgoAsignarEspecialista from './RiesgoAsignarEspecialista.vue';
import RiesgoVerificacionForm from './RiesgoVerificacionForm.vue';

const store = useRiesgoStore();
const modalRef = ref(null);
let modalInstance = null;

const tabs = shallowRef({
    RiesgoForm,
    RiesgoEvaluacionForm,
    RiesgoAcciones,
    RiesgoAsignarEspecialista,
    RiesgoVerificacionForm
});

onMounted(() => {
    // Initialize modal manually
    modalInstance = new Modal(modalRef.value, {
        backdrop: 'static',
        keyboard: false
    });

    // Subscribe to store changes
    store.$subscribe((mutation, state) => {
        if (state.isModalOpen) {
            modalInstance.show();
        } else {
            modalInstance.hide();
        }
    });

    // Handle hidden event
    modalRef.value.addEventListener('hidden.bs.modal', (event) => {
        // Prevent closing store modal if the event came from a nested modal/dialog
        if (event.target !== modalRef.value) return;

        if (store.isModalOpen) {
            store.fetchMisRiesgos(); // Refresh list on close
            store.closeModal();
        }
    });
});
</script>

<style scoped>
.nav-pills .nav-link {
    color: #495057;
    cursor: pointer;
    margin-bottom: 0.5rem;
    border-radius: 0.25rem;
    transition: all 0.2s;
}

.nav-pills .nav-link:hover {
    background-color: #e9ecef;
}

.nav-pills .nav-link.active {
    background-color: #fff;
    border-left: 4px solid #dc3545;
    color: #dc3545;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.nav-pills .nav-link i {
    width: 20px;
    margin-right: 10px;
    text-align: center;
}

.disabled-links {
    pointer-events: none;
    opacity: 0.6;
}

.modal-body-scrollable {
    height: 90vh;
    overflow-y: auto;
}
</style>

<template>
    <div class="modal fade" tabindex="-1" aria-labelledby="riesgoModalLabel" ref="modal" id="riesgoModal"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ store.modalTitle }}</h5>
                    <button type="button" class="close text-white" aria-label="Close" @click="store.closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-body-scrollable p-0">
                    <div class="row m-0" style="min-height: 100%;">
                        <div class="col-md-3 border-right p-0">
                            <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                                <h6 class="text-secondary mx-3 mt-3">GENERAL</h6>
                                <a class="nav-link" :class="{ 'text-danger active': store.currentTab === 'RiesgoForm' }"
                                    @click="store.setCurrentTab('RiesgoForm')" role="tab">
                                    <i class="fas fa-file-alt"></i> Identificar Riesgo
                                </a>
                                <!-- Future tabs can be added here -->
                                <div :class="{ 'disabled-links': !store.isEditing }">
                                    <a class="nav-link"
                                        :class="{ 'text-danger active': store.currentTab === 'RiesgoEvaluacionForm' }"
                                        @click="store.setCurrentTab('RiesgoEvaluacionForm')" role="tab">
                                        <i class="fas fa-chart-bar"></i> Evaluar Riesgo
                                    </a>
                                    <a class="nav-link"
                                        :class="{ 'text-danger active': store.currentTab === 'RiesgoAcciones' }"
                                        @click="store.setCurrentTab('RiesgoAcciones')" role="tab">
                                        <i class="fas fa-tasks"></i> Tratar Riesgo
                                    </a>
                                    <a class="nav-link"
                                        :class="{ 'text-danger active': store.currentTab === 'RiesgoAsignarEspecialista' }"
                                        @click="store.setCurrentTab('RiesgoAsignarEspecialista')" role="tab">
                                        <i class="fas fa-user-tie"></i> Asignar Especialista
                                    </a>
                                    <a class="nav-link"
                                        :class="{ 'text-danger active': store.currentTab === 'RiesgoVerificacionForm' }"
                                        @click="store.setCurrentTab('RiesgoVerificacionForm')" role="tab">
                                        <i class="fas fa-check-circle"></i> Verificar Eficacia
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-9 p-4">
                            <component :is="tabs[store.currentTab]" :key="store.currentTab"></component>
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

// Import tab components
// Import tab components
import RiesgoForm from './RiesgoForm.vue';
import RiesgoEvaluacionForm from './RiesgoEvaluacionForm.vue';
import RiesgoAcciones from './RiesgoAcciones.vue';
import RiesgoAsignarEspecialista from './RiesgoAsignarEspecialista.vue';
import RiesgoVerificacionForm from './RiesgoVerificacionForm.vue';

const store = useRiesgoStore();
const modal = ref(null);

const tabs = shallowRef({
    RiesgoForm,
    RiesgoEvaluacionForm,
    RiesgoAcciones,
    RiesgoAsignarEspecialista,
    RiesgoVerificacionForm
});

onMounted(() => {
    // Initialize Bootstrap modal
    $(modal.value).modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });

    // Subscribe to store state changes
    store.$subscribe((mutation, state) => {
        if (state.isModalOpen) {
            $(modal.value).modal('show');
        } else {
            if (document.activeElement instanceof HTMLElement) {
                document.activeElement.blur();
            }
            $(modal.value).modal('hide');
        }
    });

    // Handle Bootstrap modal events
    $(modal.value).on('hidden.bs.modal', (e) => {
        // Only react if the event was triggered by this modal, not a child modal
        if (e.target !== modal.value) return;

        // Ensure store state is synced if modal is closed via other means (e.g. ESC)
        if (store.isModalOpen) {
            store.closeModal();
        }
    });
});
</script>

<style scoped>
.nav-pills .nav-link {
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
    border-radius: 0.25rem;
    text-align: left !important;
    transition: background-color 0.2s ease-in-out;
    cursor: pointer;
}

.nav-pills .nav-link:not(.active):hover {
    background-color: #f8f9fa;
    color: #000;
}

.nav-pills .nav-link.active {
    background-color: #fff;
    font-weight: bold;
    color: #dc3545 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.nav-link i {
    width: 1.5rem;
    text-align: left !important;
}

.nav-pills h6 {
    text-transform: uppercase;
    font-size: 0.7rem;
    margin-bottom: 0.5rem;
    letter-spacing: 0.05rem;
    text-align: left !important;
}

.disabled-links {
    pointer-events: none;
    opacity: 0.5;
}

.modal-body-scrollable {
    height: 90vh;
    overflow-y: auto;
}
</style>

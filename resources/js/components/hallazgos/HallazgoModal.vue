<template>
    <div class="modal fade" tabindex="-1" aria-labelledby="hallazgoModalLabel" aria-hidden="true" ref="modal"
        id="hallazgoModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ hallazgoStore.modalTitle }}</h5>
                    <button type="button" class="close text-white" aria-label="Close" @click="hallazgoStore.closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-body-scrollable d-flex">

                    <div class="col-md-3 border-right p-0">
                        <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                            <h6 class="text-secondary mx-3 mt-2">GENERAL</h6>
                            <a class="nav-link"
                                :class="{ 'text-danger active': hallazgoStore.currentTab === 'HallazgoForm' }"
                                @click="hallazgoStore.setCurrentTab('HallazgoForm')" role="tab">
                                <i class="fas fa-file-alt"></i> Información del Hallazgo
                            </a>

                            <hr class="my-2">

                            <h6 class="text-secondary mx-3 mt-3">ASOCIACIONES</h6>
                            <div :class="{ 'disabled-links': !hallazgoStore.isEditing }">
                                <a class="nav-link"
                                    :class="{ 'text-danger active': hallazgoStore.currentTab === 'AsignarProcesos' }"
                                    @click="hallazgoStore.setCurrentTab('AsignarProcesos')" role="tab">
                                    <i class="fas fa-project-diagram"></i> Asociar a Procesos
                                </a>
                                <a class="nav-link"
                                    :class="{ 'text-danger active': hallazgoStore.currentTab === 'AsignarEspecialistas' }"
                                    @click="hallazgoStore.setCurrentTab('AsignarEspecialistas')" role="tab">
                                    <i class="fas fa-user-tie"></i> Asignar Especialista
                                </a>

                                <a class="nav-link"
                                    :class="{ 'text-danger active': hallazgoStore.currentTab === 'GestionarPlan' }"
                                    @click="hallazgoStore.setCurrentTab('GestionarPlan')" role="tab">
                                    <i class="fas fa-tasks"></i> Planes de Acción
                                </a>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-9 px-4">
                        <component :is="tabs[hallazgoStore.currentTab]" :key="hallazgoStore.currentTab"
                            :ref="el => { if (hallazgoStore.currentTab === 'HallazgoForm') hallazgoFormRef = el }">
                        </component>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, shallowRef } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import { Modal } from 'bootstrap';

// Importa todos tus componentes de las pestañas
import HallazgoForm from './HallazgoForm.vue';
import AsignarProcesos from './AsignarProcesos.vue';
import AsignarEspecialistas from './AsignarEspecialistas.vue';
import GestionarPlan from './GestionarPlan.vue';

// ... (otros componentes)

const hallazgoStore = useHallazgoStore();
const modal = ref(null); // Ref para el elemento modal de Bootstrap
let modalInstance = null;
const hallazgoFormRef = ref(null); // <-- NUEVO ref para el componente HallazgoForm

const tabs = shallowRef({
    HallazgoForm,
    AsignarProcesos,
    AsignarEspecialistas,
    GestionarPlan,
    // ... (otros componentes)
});

onMounted(() => {
    // Inicializa el modal de Bootstrap manualmente para controlar su ciclo de vida
    modalInstance = new Modal(modal.value, {
        backdrop: 'static',
        keyboard: false
    });

    // Observa el estado isModalOpen del store para mostrar/ocultar el modal
    hallazgoStore.$subscribe((mutation, state) => {
        if (state.isModalOpen) {
            modalInstance.show();
            // Asegúrate de que el tab por defecto sea HallazgoForm al abrir el modal
            if (!hallazgoStore.currentTab) {
                hallazgoStore.setCurrentTab('HallazgoForm');
            }
        } else {
            modalInstance.hide();
        }
    });

    // --- CAMBIO CLAVE: Usa el evento shown.bs.modal ---
    modal.value.addEventListener('shown.bs.modal', () => {

        if (hallazgoStore.currentTab === 'HallazgoForm' && hallazgoFormRef.value) {
           
            hallazgoFormRef.value.reInitializeSelect2(); // Llama a un método expuesto por HallazgoForm
        }
    });

    // Manejar el evento 'hidden.bs.modal' para limpiar el store
    modal.value.addEventListener('hidden.bs.modal', (event) => {
        if (event.target === modal.value) {
            hallazgoStore.resetForm();
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

/* Estilos de los campos de formulario */
.form-group small {
    font-size: 0.75rem;
}

.form-label.text-danger {
    font-weight: bold;
}

.disabled-links {
    pointer-events: none;
    /* Deshabilita el click */
    opacity: 0.5;
    /* Atenúa visualmente los enlaces */
}

.modal-body-scrollable {
    height: 90vh;
    /* Ajusta este valor según lo necesites */
    overflow-y: auto;
}
</style>
<template>
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" ref="modalRef" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 shadow-lg">
                <!-- HEADER -->
                <div class="modal-header bg-danger text-white d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-link text-white mr-3 p-0"
                            @click="sidebarVisible = !sidebarVisible" title="Alternar Menú">
                            <i class="fas" :class="sidebarVisible ? 'fa-indent' : 'fa-outdent'"
                                style="font-size: 1.2rem;"></i>
                        </button>
                        <h5 class="modal-title font-weight-bold">{{ modalTitle }}</h5>
                        <i v-if="loadingAudit" class="fas fa-spinner fa-spin ml-3 text-white-50"></i>
                    </div>
                    <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body p-0 d-flex overflow-hidden" style="height: 100vh;">
                    <!-- SIDEBAR -->
                    <div v-if="sidebarVisible" class="col-md-3 border-right p-0 bg-white overflow-auto"
                        style="z-index: 2;">
                        <div class="nav flex-column nav-pills py-3" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <h6 class="text-uppercase text-secondary mx-4 mt-2 mb-3 font-weight-bold small">
                                GENERAL
                            </h6>
                            <a class="nav-link mx-2" :class="{ 'active': activeTab === 'general' }"
                                @click="activeTab = 'general'" href="javascript:void(0)">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-alt mr-3"></i>
                                    <span>Información del Plan</span>
                                </div>
                            </a>

                            <hr class="my-3 mx-4">

                            <h6 class="text-uppercase text-secondary mx-4 mt-3 mb-3 font-weight-bold small">
                                PLANIFICACIÓN ESPECÍFICA
                            </h6>
                            <div :class="{ 'disabled-links': !auditId }">
                                <a class="nav-link mx-2" :class="{ 'active': activeTab === 'procesos' }"
                                    @click="changeTab('procesos')" href="javascript:void(0)">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-project-diagram mr-3"></i>
                                        <span>Procesos Auditados</span>
                                    </div>
                                </a>
                                <a class="nav-link mx-2" :class="{ 'active': activeTab === 'equipo' }"
                                    @click="changeTab('equipo')" href="javascript:void(0)">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users-cog mr-3"></i>
                                        <span>Equipo Auditor</span>
                                    </div>
                                </a>
                                <a class="nav-link mx-2" :class="{ 'active': activeTab === 'planificacion' }"
                                    @click="changeTab('planificacion')" href="javascript:void(0)">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-check mr-3"></i>
                                        <span>Cronograma de Actividades</span>
                                    </div>
                                </a>

                                <hr class="my-3 mx-4">
                                <h6 class="text-uppercase text-secondary mx-4 mt-3 mb-3 font-weight-bold small">
                                    EJECUCIÓN
                                </h6>
                                <a class="nav-link mx-2" :class="{ 'active': activeTab === 'ejecucion' }"
                                    @click="changeTab('ejecucion')" href="javascript:void(0)">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-tasks mr-3"></i>
                                        <span>Ejecución de Auditoría</span>
                                    </div>
                                </a>

                                <a class="nav-link mx-2" :class="{ active: activeTab === 'ejecucion_gabinete' }"
                                    @click="changeTab('ejecucion_gabinete')" href="javascript:void(0)">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-search-plus mr-3"></i>
                                        <span>Revisión de Gabinete</span>
                                    </div>
                                </a>

                                <hr class="my-3 mx-4">
                                <h6 class="text-uppercase text-secondary mx-4 mt-3 mb-3 font-weight-bold small">
                                    INFORMES
                                </h6>
                                <a class="nav-link mx-2" :class="{ 'active': activeTab === 'informes' }"
                                    @click="changeTab('informes')" href="javascript:void(0)">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf mr-3"></i>
                                        <span>Informes Emitidos</span>
                                    </div>
                                </a>
                                <hr class="my-3 mx-4">
                                <h6 class="text-uppercase text-secondary mx-4 mt-3 mb-3 font-weight-bold small">
                                    EVALUACIÓN
                                </h6>

                                <a class="nav-link mx-2"
                                    :class="{ 'active': activeTab === 'evaluacion', 'disabled-links': !canEvaluate }"
                                    @click="changeTab('evaluacion')" href="javascript:void(0)">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle mr-3"></i>
                                        <span class="font-weight-bold">Evaluación Auditores</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- CONTENT -->
                    <div :class="sidebarVisible ? 'col-md-9' : 'col-md-12'"
                        class="px-0 bg-light border-left modal-body-scrollable transition-all">
                        <div class="p-4">
                            <KeepAlive>
                                <component :is="currentComponent" v-bind="commonProps" @saved="onAuditSaved"
                                    @close="closeModal" />
                            </KeepAlive>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits, watch, onMounted } from 'vue';
import axios from 'axios';
import { Modal } from 'bootstrap';

import PlanEspecifico from './partials/PlanEspecifico.vue';
import ProcesosAuditados from './partials/ProcesosAuditados.vue';
import PlanCronograma from './partials/PlanCronograma.vue';
import EquipoAuditor from './partials/EquipoAuditor.vue';
import InformesEmitidos from './partials/InformesEmitidos.vue';
import EvaluacionAuditores from './partials/EvaluacionAuditores.vue';
import EjecucionAuditoria from './partials/EjecucionAuditoria.vue';
import InformeAuditoria from './partials/InformeAuditoria.vue';
import RevisionGabinete from './partials/RevisionGabinete.vue';

const props = defineProps({
    visible: Boolean,
    auditId: { type: Number, default: null },
    programaId: { type: Number, required: true },
    auditStatus: { type: String, default: null }
});

const emit = defineEmits(['update:visible', 'refresh']);

const modalRef = ref(null);
let modalInstance = null;
const activeTab = ref('general');
const auditId = ref(props.auditId);
const sidebarVisible = ref(true);
const fullAuditData = ref(null);
const loadingAudit = ref(false);

const loadFullAuditData = async (id) => {
    if (!id) {
        fullAuditData.value = null;
        return;
    }
    loadingAudit.value = true;
    try {
        const response = await axios.get(`/api/auditorias/${id}`);
        fullAuditData.value = response.data;
    } catch (e) {
        console.error("Error fetching full audit data", e);
    } finally {
        loadingAudit.value = false;
    }
};

const commonProps = computed(() => ({
    auditId: auditId.value,
    programaId: props.programaId,
    auditData: fullAuditData.value,
    auditStatus: props.auditStatus,
    loading: loadingAudit.value
}));

onMounted(() => {
    // Pre-fetch auditors list early to avoid delays in the Team tab
    axios.get('/api/auditores').catch(e => console.error("Error pre-fetching auditors", e));

    modalInstance = new Modal(modalRef.value, {
        backdrop: 'static',
        keyboard: false
    });

    // Cleanup backdrop and scroll lock when Bootstrap hides the modal
    modalRef.value.addEventListener('hidden.bs.modal', (event) => {
        // Only act if the event target is the main modal itself, not a child modal (event bubbling)
        if (event.target !== modalRef.value) return;

        emit('update:visible', false);
        // Force cleanup to prevent view blocking
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    });

    if (props.visible) {
        modalInstance?.show();
        if (props.auditId) loadFullAuditData(props.auditId);
    }
});

watch(() => props.visible, (newVal) => {
    if (newVal) {
        modalInstance?.show();
        if (props.auditId) loadFullAuditData(props.auditId);
    } else {
        modalInstance?.hide();
    }
});

watch(() => props.auditId, (newVal) => {
    auditId.value = newVal;
    activeTab.value = 'general';
    if (newVal) loadFullAuditData(newVal);
});

const modalTitle = computed(() => {
    return auditId.value ? `Editar Auditoría Específica` : 'Agendar Nueva Auditoría';
});

const currentComponent = computed(() => {
    switch (activeTab.value) {
        case 'general': return PlanEspecifico;
        case 'procesos': return ProcesosAuditados;
        case 'planificacion': return PlanCronograma;
        case 'equipo': return EquipoAuditor;
        case 'ejecucion': return EjecucionAuditoria;
        case 'ejecucion_gabinete': return RevisionGabinete;
        case 'informes': return InformesEmitidos;
        case 'evaluacion': return EvaluacionAuditores;
        default: return PlanEspecifico;
    }
});

const canEvaluate = computed(() => {
    return auditId.value && ['Ejecutada', 'Cerrada', 'Cancelada'].includes(props.auditStatus);
});

const changeTab = (tab) => {
    if (!auditId.value) return;
    if (tab === 'evaluacion' && !canEvaluate.value) return;
    activeTab.value = tab;
};

const onAuditSaved = (newAudit) => {
    if (!auditId.value && newAudit && newAudit.id) {
        auditId.value = newAudit.id;
    }
    // Refresh local data so other tabs (Cronograma, etc.) see the changes immediately
    if (auditId.value) {
        loadFullAuditData(auditId.value);
    }
    emit('refresh');
};

const closeModal = () => {
    modalInstance?.hide();
};
</script>

<style scoped>
.modal-xl {
    max-width: 95% !important;
}

.modal-content {
    border-radius: 0.5rem;
}

/* SIDEBAR STYLING */
.nav-pills .nav-link {
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
    border-radius: 4px;
    text-align: left !important;
    color: #495057;
    transition: all 0.2s ease;
    border-left: 4px solid transparent;
    margin-bottom: 2px;
}

.nav-pills .nav-link:hover {
    background-color: #f8f9fa;
    color: #000;
}

.nav-pills .nav-link.active {
    background-color: #fff !important;
    font-weight: bold;
    color: #dc3545 !important;
    border-radius: 0;
    border-left: 4px solid #dc3545;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.modal-body-scrollable {
    overflow-y: auto;
}

.disabled-links {
    pointer-events: none;
    opacity: 0.5;
}

.border-right {
    border-right: 1px solid #dee2e6 !important;
}

.border-left {
    border-left: 1px solid #dee2e6 !important;
}

.modal-header .close {
    outline: none;
    opacity: 0.8;
}

.modal-header .close:hover {
    opacity: 1;
}

.transition-all {
    transition: all 0.3s ease;
}

.btn-link:hover {
    text-decoration: none;
    transform: scale(1.1);
}
</style>

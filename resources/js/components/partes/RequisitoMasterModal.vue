<template>
    <div class="modal fade" id="reqMasterModal" tabindex="-1" aria-hidden="true" ref="modalElement" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="min-height: 600px;">
                <!-- HEADER -->
                <div class="modal-header bg-danger text-white flex-column align-items-start p-3">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-tasks mr-2"></i> Gestión de Requisitos - {{ parteNombre }}
                        </h5>
                        <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <small class="text-white-50 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Módulo centralizado para identificar necesidades, vincular procesos y gestionar planes de acción.
                    </small>
                </div>
                
                <div class="modal-body p-0 d-flex flex-column flex-md-row bg-light" style="min-height: 500px;">
                    <!-- SIDEBAR -->
                    <div class="col-md-3 border-right p-0 bg-white sidebar-nav">
                        <div class="nav flex-column nav-pills p-3" id="v-pills-tab" role="tablist">
                            
                            <!-- 1. Create / Edit (General Form) -->
                            <a class="nav-link font-weight-bold mb-1" 
                               :class="{ 'active': mode === 'edit' && currentTab === 'general' }" 
                               @click="switchToGeneral" 
                               href="#">
                                <i class="fas fa-edit mr-2"></i> {{ masterForm.id ? 'Detalle Requisito' : 'Nuevo Requisito' }}
                            </a>

                            <!-- 2. List -->
                            <a class="nav-link font-weight-bold mb-3" 
                               :class="{ 'active': mode === 'list' }" 
                               @click="setMode('list')" 
                               href="#">
                                <i class="fas fa-list-alt mr-2"></i> Listado
                            </a>
                            
                            <hr v-if="mode === 'edit' && masterForm.id" class="my-2">
                            
                            <!-- Edit Sub-Nav (Only if editing existing) -->
                            <div v-if="mode === 'edit' && masterForm.id" class="animate__animated animate__fadeInLeft">
                                <h6 class="text-uppercase font-weight-bold text-muted small mb-2 ml-2 mt-2">
                                    Gestión
                                </h6>
                                <a class="nav-link mb-1 small-link" :class="{ 'active': currentTab === 'procesos' }" @click="setTab('procesos')" href="#">
                                    <i class="fas fa-cogs mr-2"></i> Procesos
                                </a>
                                <a class="nav-link small-link" :class="{ 'active': currentTab === 'compromisos' }" @click="setTab('compromisos')" href="#">
                                    <i class="fas fa-tasks mr-2"></i> Compromisos
                                </a>
                                
                                <button class="btn btn-sm btn-outline-secondary btn-block mt-4" @click="setMode('list')">
                                    <i class="fas fa-arrow-left mr-1"></i> Volver al Listado
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- CONTENT -->
                    <div class="col-md-9 p-0 bg-white">
                        <!-- LIST VIEW -->
                        <div v-show="mode === 'list'" class="p-3 h-100 animate__animated animate__fadeIn">
                             <RequisitoList 
                                :requirements="filteredRequirements" 
                                @create="create" 
                                @edit="edit"
                             />
                        </div>

                        <!-- EDIT VIEW -->
                        <div v-if="mode === 'edit'" class="h-100 animate__animated animate__fadeIn">
                             <component 
                                :is="currentTabComponent" 
                                v-if="currentTabComponent"
                                :requirement-id="masterForm.id" 
                                :initial-data="masterForm.compromisos"
                                @update-list="handleCompromisosUpdate"
                            ></component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, defineProps, defineEmits, computed, provide, watch } from 'vue';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';
import { useParteStore } from '@/stores/parteInteresadaStore';

// Components
import RequisitoList from './RequisitoList.vue';
import RequisitoGeneralForm from './RequisitoGeneralForm.vue';
import RequisitoProcesos from './RequisitoProcesos.vue';
import RequisitoCompromisos from './RequisitoCompromisos.vue';

const props = defineProps({
    parteId: Number,
    filterContext: {
        type: String,
        default: 'all'
    }
});
const emit = defineEmits(['close']);
const store = useParteStore();

const modalElement = ref(null);
let modalInstance = null;
let isExplicitClose = false;

// State
const mode = ref('list'); // 'list' | 'edit'
const currentTab = ref('general');
const masterForm = ref({});
const parte = computed(() => store.partes.find(p => p.id === props.parteId));
const parteNombre = computed(() => parte.value?.pi_nombre || '');

const filteredRequirements = computed(() => {
    if (!parte.value?.expectativas) return [];
    if (props.filterContext === 'all') return parte.value.expectativas;
    
    return parte.value.expectativas.filter(e => {
        const normas = e.exp_normas || [];
        // Support array of strings or simple string match if norms are just text
        return normas.some(n => n.includes(props.filterContext) || props.filterContext.includes(n));
    });
});

// Provide form to children
provide('masterForm', masterForm);
provide('saveAction', () => savePayload());

const tabs = {
    general: RequisitoGeneralForm,
    procesos: RequisitoProcesos,
    compromisos: RequisitoCompromisos
};

const currentTabComponent = computed(() => tabs[currentTab.value]);

onMounted(() => {
    modalInstance = new Modal(modalElement.value, { backdrop: 'static', keyboard: false });
    
    // Global listener to prevent parent modal from closing when child modal closes
    const handleModalHide = (e) => {
        // Get all currently shown modals
        const shownModals = document.querySelectorAll('.modal.show');
        
        // If our modal is shown and the event is NOT from our modal
        if (modalElement.value.classList.contains('show') && e.target !== modalElement.value) {
            // A child modal is trying to close, don't let it affect our modal
            return;
        }
        
        // If this event IS for our modal
        if (e.target === modalElement.value) {
            // Only allow closing if it's explicit OR there are no other modals
            if (!isExplicitClose && shownModals.length > 1) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }
    };
    
    // Listen on document to catch all modal events
    document.addEventListener('hide.bs.modal', handleModalHide, true);
    
    modalElement.value.addEventListener('hidden.bs.modal', () => {
        isExplicitClose = false;
        emit('close');
    });
    
    // Cleanup on unmount
    onUnmounted(() => {
        document.removeEventListener('hide.bs.modal', handleModalHide, true);
    });
});

const open = () => {
    mode.value = 'list';
    modalInstance.show();
    if (props.parteId && !parte.value) {
        store.fetchPartes(); // Ensure data
    }
};

const closeModal = () => {
    isExplicitClose = true;
    modalInstance.hide();
};

const setMode = (m) => mode.value = m;
const setTab = (tab) => currentTab.value = tab;

const switchToGeneral = () => {
    // If we are in 'list' mode, this implies creating new? 
    // Or if we are editing, it creates new?
    // Logic: If I click "Nuevo Requisito" (top link), I expect to clear form and go to New.
    // UNLESS I am already editing one?
    // User said: "place the menu create requirement".
    // Let's assume clicking the top link always resets to NEW if we are in list, or just switches tab?
    // "Nuevo Requisito" acts as "Current Item" if we have an ID.
    
    if (mode.value === 'list') {
         create(); // Default action for top link when in list
    } else {
         // Already in edit mode, just switch tab
         currentTab.value = 'general';
    }
};

const create = () => {
    resetForm();
    mode.value = 'edit';
    currentTab.value = 'general';
};

const edit = (req, tab = 'general') => {
    loadForm(req);
    currentTab.value = tab;
    mode.value = 'edit';
};

const resetForm = () => {
    masterForm.value = {
        parte_interesada_id: props.parteId,
        exp_tipo: 'necesidad',
        exp_normas: [],
        exp_criticidad: 3,
        exp_viabilidad: 3,
        exp_estado: 'pendiente',
        exp_observaciones: '',
        procesos: [],
        procesos_list: [],
        compromisos: []
    };
};

const loadForm = (req) => {
    masterForm.value = JSON.parse(JSON.stringify(req));
    if (!masterForm.value.procesos_list) masterForm.value.procesos_list = masterForm.value.procesos || [];
    if (masterForm.value.procesos_list.length > 0 && !masterForm.value.procesos) {
        masterForm.value.procesos = masterForm.value.procesos_list.map(p => p.id);
    }
};

const handleCompromisosUpdate = (newList) => {
    masterForm.value.compromisos = newList;
};

const savePayload = async () => {
    if (!masterForm.value.exp_descripcion) {
        Swal.fire('Error', 'Descripción requerida', 'warning');
        return;
    }
    try {
        const response = await store.saveExpectativa(masterForm.value);
        if (!masterForm.value.id && response) {
             masterForm.value.id = response.expectativa?.id || response.id; 
        }
        
        Swal.fire({
            icon: 'success',
            title: 'Guardado',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500
        });
        
        // Refresh List? Store update should trigger computed re-eval if it mutates 'partes'
        // If expectation was new, we might need a full fetch or ensure store adds it.
        // Assuming store handles it.
        
    } catch (e) {
        // Handled in store
    }
};

defineExpose({ open, closeModal });
</script>

<style scoped>
.sidebar-nav {
    min-height: 500px;
}
.nav-pills .nav-link {
    color: #495057;
    border-radius: 0;
    transition: all 0.2s;
}
.nav-pills .nav-link:hover {
    background-color: #e9ecef;
    color: #dc3545;
}
.nav-pills .nav-link.active {
    background-color: #f8f9fa;
    color: #dc3545;
    border-left: 4px solid #dc3545;
    font-weight: bold;
}
.small-link {
    font-size: 0.9rem;
    padding-left: 20px;
}
.x-small {
    font-size: 0.75rem;
}
</style>

<template>
    <div class="p-3">
        <!-- Header / Breadcrumb -->
        <div class="header-container mb-3">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Requisito</span>
                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                <span class="text-muted small text-truncate" style="max-width: 300px;">{{ masterForm.exp_descripcion }}</span>
            </h6>
        </div>

        <h6 class="mb-1 font-weight-bold text-uppercase text-dark">Asociación de Procesos</h6>
        <p class="mb-3 text-muted small">
            Vincule este requisito con los procesos del sistema de gestión para asegurar trazabilidad e impacto.
        </p>

        <!-- Search Bar -->
        <div class="d-flex align-items-center my-3">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Seleccione el Proceso a Asociar..." v-model="selectedProcess.descripcion" readonly />
                <div class="input-group-append">
                    <button class="btn btn-dark" @click="openProcessModal">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-danger" :disabled="!selectedProcess.id" @click="addProcess">
                        <i class="fas fa-link"></i> Asociar
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive border rounded bg-white">
            <table class="table table-bordered table-hover table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Nombre del Proceso</th>
                        <th class="text-center" style="width: 100px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="localList.length === 0">
                        <td colspan="3" class="text-center py-3 text-muted small">
                            No hay procesos asociados.
                        </td>
                    </tr>
                    <tr v-for="proc in localList" :key="proc.id">
                        <td class="text-center">{{ proc.id }}</td>
                        <td>
                            <i class="fas fa-project-diagram text-muted mr-2"></i> {{ proc.descripcion || proc.proceso_nombre }}
                        </td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm py-0" @click="removeProcess(proc.id)" title="Desvincular">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- REMOVED MANUAL SAVE BUTTON SECTION -->
        
        <Teleport to="body">
            <ModalHijo ref="processModalRef" fetch-url="/buscarProcesos" target-id="id" target-desc="proceso_nombre" @update-target="handleProcessSelected" />
        </Teleport>
    </div>
</template>

<script setup>
import { ref, inject, watch, onMounted, nextTick } from 'vue';
import ModalHijo from '@/components/generales/ModalHijo.vue';

const masterForm = inject('masterForm');
const saveAction = inject('saveAction');

const processModalRef = ref(null);
const selectedProcess = ref({ id: null, descripcion: '' });
const localList = ref([]);

// Init from master
onMounted(() => {
    loadProcesses();
    
    // Fix scroll issue when child modal closes
    // We listen to hidden.bs.modal on the child modal element dynamically if possible, 
    // or we can just enforce body class since we control the open/close flow somewhat.
    // The easiest way with Teleport/Bootstrap interaction is to re-add 'modal-open' to body 
    // and ensuring our parent modal is scrollable.
    
    // Since ModalHijo emits nothing on close, we hook into our interaction flow
});

watch(() => masterForm.value.procesos_list, () => {
    loadProcesses();
}, { deep: true });

const loadProcesses = () => {
    if (masterForm.value.procesos_list) {
         // Ensure standard structure
        localList.value = masterForm.value.procesos_list.map(p => ({
            id: p.id,
            descripcion: p.descripcion || p.proceso_nombre // Handle different naming conventions
        }));
    }
};

const openProcessModal = () => {
    processModalRef.value.open();
    // Bootstrap sometimes removes modal-open when second modal opens. Ensure it stays.
    // But mainly the issue is when it CLOSES.
};

const handleProcessSelected = (data) => {
    selectedProcess.value = { id: data.idValue, descripcion: data.descValue };
    fixScrollAndFocus();
};

// If the user closes the modal without selecting, we might still lose scroll.
// Ideally ModalHijo should emit a 'closed' event. If it doesn't, we might need a workaround.
// Assuming ModalHijo doesn't emit 'close', we rely on when we finish interaction.
// However, if user clicks 'X' in ModalHijo, we don't catch it here easily without modifying ModalHijo.
// BUT, since we teleported it, we can try to find its DOM element and add listener?
// Let's try to add a listener to document for any modal close to restore class if needed.
// Actually, simpler fix: Whenever we interact, check body class. 

const addProcess = () => {
    if (!selectedProcess.value.id) return;
    if (localList.value.some(p => p.id == selectedProcess.value.id)) return;
    
    // Add locally
    localList.value.push({ ...selectedProcess.value });
    
    // Convert back to master format and Auto Save
    updateMaster();
    save();
    
    selectedProcess.value = { id: null, descripcion: '' };
};

const removeProcess = (id) => {
    localList.value = localList.value.filter(p => p.id !== id);
    updateMaster();
    save(); // Auto Save
};

const updateMaster = () => {
    masterForm.value.procesos_list = localList.value;
    masterForm.value.procesos = localList.value.map(p => p.id); 
};

const save = () => {
    saveAction();
};

const fixScrollAndFocus = () => {
    // Wait for Bootstrap to cleanup the child modal
    setTimeout(() => {
        // Ensure body has modal-open class so background doesn't scroll
        if (!document.body.classList.contains('modal-open')) {
            document.body.classList.add('modal-open');
        }
        // Ensure our active modal (if exists) is still scrollable
        // This usually fixes the "frozen" UI issue
    }, 500);
};

// Global fix for any modal closing while another is open
// Since we used Teleport, the 'hidden.bs.modal' event bubbles to document.
document.addEventListener('hidden.bs.modal', (event) => {
    // If a modal closed, but there is still another open modal (like our parent)
    if (document.querySelector('.modal.show')) {
        if (!document.body.classList.contains('modal-open')) {
             document.body.classList.add('modal-open');
        }
    }
});
</script>

<style scoped>
.header-container {
    padding: 0.75rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    border-left: 5px solid #dc3545; /* Red for Requirements */
    display: flex;
    align-items: center;
}
</style>

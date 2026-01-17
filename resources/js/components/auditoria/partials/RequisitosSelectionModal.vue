<template>
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" ref="modalRef" data-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-tasks mr-2"></i>Seleccionar Requisitos
                    </h5>
                    <button type="button" class="close text-white" @click="close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3 bg-light border-bottom">
                        <input type="text" v-model="searchInput" class="form-control shadow-sm"
                            placeholder="Buscar requisito por numeral o descripción..." autofocus>
                    </div>

                    <div v-if="loading" class="text-center py-5">
                        <div class="spinner-border text-danger" role="status"></div>
                    </div>

                    <div v-else-if="normas.length === 0" class="text-center py-5 text-muted">
                        <i class="fas fa-exclamation-circle fa-2x mb-3 text-secondary"></i>
                        <p>No se encontraron requisitos disponibles para los sistemas seleccionados.</p>
                    </div>

                    <div v-else class="list-group list-group-flush" style="max-height: 50vh; overflow-y: auto;">
                        <template v-for="norma in filteredNormas" :key="norma.id">
                            <div
                                class="list-group-item bg-light font-weight-bold text-uppercase py-2 small d-flex justify-content-between align-items-center">
                                <div class="cursor-pointer" @click="toggleCollapse(norma.id)">
                                    <i class="fas mr-2"
                                        :class="isCollapsed(norma.id) ? 'fa-chevron-right' : 'fa-chevron-down'"></i>
                                    <i class="fas fa-book mr-2"></i> {{ norma.nombre }}
                                </div>
                                <div class="custom-control custom-checkbox" title="Seleccionar Visible">
                                    <input type="checkbox" class="custom-control-input" :id="'sa-' + norma.id"
                                        :checked="isAllSelected(norma)" @click="toggleSelectAll($event, norma)">
                                    <label class="custom-control-label small" :for="'sa-' + norma.id">Todos</label>
                                </div>
                            </div>

                            <div v-if="!isCollapsed(norma.id)">
                                <div v-for="req in norma.filteredRequirements" :key="req.nr_id"
                                    class="list-group-item list-group-item-action cursor-pointer"
                                    :class="{ 'bg-soft-danger': isSelected(req, norma) }"
                                    @click.stop="toggleSelection(req, norma)">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                :checked="isSelected(req, norma)" readonly>
                                            <label class="custom-control-label fw-bold" style="cursor: pointer;">
                                                {{ req.nr_numeral }} - {{ req.nr_denominacion }}
                                            </label>
                                        </div>
                                    </div>
                                    <small v-if="req.nr_detalle" class="text-muted d-block mt-1 pl-4">
                                        {{ truncate(req.nr_detalle, 100) }}
                                    </small>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="modal-footer bg-white justify-content-between">
                    <div>
                        <span class="font-weight-bold text-danger">{{ selectedItems.length }}</span> requisitos
                        seleccionados
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary mr-2" @click="close">Cancelar</button>
                        <button type="button" class="btn btn-danger shadow-sm px-4" @click="confirm">
                            <i class="fas fa-check mr-1"></i> Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { Modal } from 'bootstrap';

const props = defineProps({
    auditId: { type: Number, required: true },
    initialSelection: { type: Array, default: () => [] } // IDs or Objects? Let's assume Objects for display or formatted string. But here we deal with objects.
});

const emit = defineEmits(['selected', 'close']);

const modalRef = ref(null);
let modalInstance = null;
const loading = ref(false);
const normas = ref([]);
const searchInput = ref('');
const selectedItems = ref([]);
const collapsedNormas = ref(new Set());

const loadRequisitos = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/auditoria/especifica/${props.auditId}/requisitos-disponibles`);
        normas.value = response.data;
        // parseInitialSelection() handled in open()
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

// We need to handle how initial selection is passed. 
// If it's a string "ISO 9001: 4.1, 4.2", it's hard to map back.
// Ideally, `PlanCronograma` should store specific IDs or structure. 
// However, the DB field is just a text/json field. 
// Let's assume we start empty or try to match by numeral if possible, 
// but for now let's keep it simple: Selection is a fresh action or we try to match by string.
// Initial selection parsed in open()

const filteredNormas = computed(() => {
    if (!searchInput.value) {
        return normas.value.map(n => ({ ...n, filteredRequirements: n.requisitos }));
    }

    const term = searchInput.value.toLowerCase();
    return normas.value.map(n => {
        const reqs = n.requisitos.filter(r =>
            (r.nr_numeral && r.nr_numeral.toLowerCase().includes(term)) ||
            (r.nr_denominacion && r.nr_denominacion.toLowerCase().includes(term)) ||
            (r.nr_detalle && r.nr_detalle.toLowerCase().includes(term))
        );
        return { ...n, filteredRequirements: reqs };
    }).filter(n => n.filteredRequirements.length > 0);
});

const isSelected = (req, norma) => {
    if (!norma) return false;
    return selectedItems.value.some(i => i.id === req.nr_id && i.norma_id === norma.id);
};

const toggleSelection = (req, norma) => {
    const idx = selectedItems.value.findIndex(i => i.id === req.nr_id && i.norma_id === norma.id);
    if (idx > -1) {
        selectedItems.value.splice(idx, 1);
    } else {
        selectedItems.value.push({
            id: req.nr_id,
            norma_id: norma.id,
            numeral: req.nr_numeral,
            nombre_norma: norma.nombre,
            full_label: `${req.nr_numeral} ${req.nr_denominacion}`
        });
    }
};

const isCollapsed = (id) => collapsedNormas.value.has(id);

const toggleCollapse = (id) => {
    if (collapsedNormas.value.has(id)) collapsedNormas.value.delete(id);
    else collapsedNormas.value.add(id);
};

const isAllSelected = (norma) => {
    if (!norma.filteredRequirements || norma.filteredRequirements.length === 0) return false;
    return norma.filteredRequirements.every(req => isSelected(req, norma));
};

const toggleSelectAll = (event, norma) => {
    if (!norma || !norma.filteredRequirements) return;

    const shouldSelect = event.target.checked;

    // Identify target IDs from this norma's visible requirements
    const targetReqs = norma.filteredRequirements;

    if (shouldSelect) {
        // Add items that are NOT currently selected
        targetReqs.forEach(req => {
            if (!isSelected(req, norma)) {
                selectedItems.value.push({
                    id: req.nr_id,
                    norma_id: norma.id,
                    numeral: req.nr_numeral,
                    nombre_norma: norma.nombre,
                    full_label: `${req.nr_numeral} ${req.nr_denominacion}`
                });
            }
        });
    } else {
        // Remove items that ARE currently selected belonging to THIS norma
        const targetIds = new Set(targetReqs.map(r => r.nr_id));
        selectedItems.value = selectedItems.value.filter(item => {
            if (item.norma_id !== norma.id) return true;
            return !targetIds.has(item.id);
        });
    }
};

const truncate = (text, length) => {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
};

// Parse initial selection, handling legacy data (missing IDs or norma_id)
const parseInitialSelection = (initial) => {
    selectedItems.value = [];
    if (!initial || !Array.isArray(initial)) return;

    initial.forEach(i => {
        // Case 1: Has valid IDs (New standard)
        if (i.id && i.norma_id) {
            selectedItems.value.push({ ...i });
            return;
        }

        // Case 2: Legacy (missing norma_id or old bad ID). Try to match by norma string and numeral.
        if (normas.value.length > 0) {
            const foundNorma = normas.value.find(n => n.nombre === i.norma || n.nombre === i.nombre_norma);
            if (foundNorma) {
                const foundReq = foundNorma.requisitos.find(r => r.nr_numeral === i.numeral);
                if (foundReq) {
                    selectedItems.value.push({
                        id: foundReq.nr_id,
                        norma_id: foundNorma.id,
                        numeral: foundReq.nr_numeral,
                        nombre_norma: foundNorma.nombre,
                        full_label: `${foundReq.nr_numeral} ${foundReq.nr_denominacion}`
                    });
                }
            }
        }
    });
};

const open = async (initialSelection = null) => {
    modalInstance = new Modal(modalRef.value, { keyboard: false, backdrop: 'static' });
    modalInstance.show();

    // Load requisites first so we can match legacy data
    await loadRequisitos();

    // Set initial selection if provided
    if (initialSelection) {
        parseInitialSelection(initialSelection);
    } else {
        selectedItems.value = [];
    }
};

const close = () => {
    modalInstance?.hide();
    emit('close');
};

const confirm = () => {
    emit('selected', selectedItems.value);
    close();
};

defineExpose({ open });
</script>

<style scoped>
.bg-soft-danger {
    background-color: #ffeef0;
}

.cursor-pointer {
    cursor: pointer;
}
</style>

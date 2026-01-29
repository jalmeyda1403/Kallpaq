<template>
    <div class="control-selector">
        <label v-if="label" class="font-weight-bold small text-uppercase text-muted">{{ label }}</label>

        <!-- Selection View -->
        <div class="card border-light shadow-sm mb-3">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <input type="text" class="form-control form-control-sm w-75" v-model="search"
                        placeholder="Buscar controles..." @input="filterControls" @keydown.enter.prevent>
                    <button type="button" class="btn btn-sm btn-outline-danger ml-2" @click="showCreateModal = true">
                        <i class="fas fa-plus"></i> Nuevo
                    </button>
                </div>

                <div class="list-group control-list" style="max-height: 200px; overflow-y: auto;">
                    <div v-if="loading" class="text-center py-2 text-muted">Cargando...</div>
                    <div v-else-if="filteredControls.length === 0" class="text-center py-2 text-muted small">
                        No se encontraron controles.
                    </div>
                    <button v-for="control in filteredControls" :key="control.id" type="button"
                        class="list-group-item list-group-item-action py-2 px-3 small d-flex justify-content-between align-items-center"
                        :class="{ 'active': isSelected(control) }" @click="toggleSelection(control)">
                        <div>
                            <strong class="d-block">{{ control.nombre }}</strong>
                            <small class="text-muted">{{ control.tipo }}</small>
                        </div>
                        <i v-if="isSelected(control)" class="fas fa-check"></i>
                    </button>
                </div>
            </div>
            <div class="card-footer bg-light p-2 small text-muted">
                {{ selectedControls.length }} controles seleccionados
            </div>
        </div>

        <!-- Quick Create Modal (Inline) -->
        <div v-if="showCreateModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center">
            <div class="card shadow-lg" style="width: 400px; z-index: 1060;">
                <div class="card-header bg-danger text-white py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Nuevo Control</h6>
                    <button type="button" class="close text-white" @click="closeCreateModal">&times;</button>
                </div>
                <div class="card-body p-3">
                    <div class="form-group mb-2">
                        <label class="small text-muted">Nombre/Código</label>
                        <input v-model="newControl.nombre" class="form-control form-control-sm"
                            placeholder="Ej. C-01 Control de Acceso">
                    </div>
                    <div class="form-group mb-2">
                        <label class="small text-muted">Tipo</label>
                        <select v-model="newControl.tipo" class="form-control form-control-sm">
                            <option value="Preventivo">Preventivo</option>
                            <option value="Detectivo">Detectivo</option>
                            <option value="Correctivo">Correctivo</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label class="small text-muted">Descripción</label>
                        <textarea v-model="newControl.descripcion" class="form-control form-control-sm"
                            rows="2"></textarea>
                    </div>
                </div>
                <div class="card-footer p-2 text-right">
                    <button type="button" class="btn btn-sm btn-secondary mr-2"
                        @click="closeCreateModal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-danger" @click="createControl" :disabled="creating">
                        {{ creating ? 'Guardando...' : 'Crear' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    modelValue: { type: Array, default: () => [] }, // Array of Control IDs or Objects
    label: { type: String, default: 'Asignar Controles' }
});

const emit = defineEmits(['update:modelValue']);

const controls = ref([]);
const filteredControls = ref([]);
const search = ref('');
const loading = ref(false);
const showCreateModal = ref(false);
const creating = ref(false);

const newControl = ref({
    nombre: '',
    tipo: 'Preventivo',
    descripcion: '',
    estado: 'Activo'
});

// Helper: Determine if we work with IDs or Objects. Assuming IDs for v-model.
const selectedControls = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
});

const isSelected = (control) => selectedControls.value.includes(control.id);

const toggleSelection = (control) => {
    const id = control.id;
    const newSelection = [...selectedControls.value];
    if (newSelection.includes(id)) {
        const index = newSelection.indexOf(id);
        newSelection.splice(index, 1);
    } else {
        newSelection.push(id);
    }
    emit('update:modelValue', newSelection);
};

const fetchControls = async () => {
    loading.value = true;
    try {
        const res = await axios.get('/api/controles');
        controls.value = res.data;
        filterControls();
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const filterControls = () => {
    if (!search.value) {
        filteredControls.value = controls.value;
    } else {
        const term = search.value.toLowerCase();
        filteredControls.value = controls.value.filter(c =>
            c.nombre.toLowerCase().includes(term) ||
            (c.descripcion && c.descripcion.toLowerCase().includes(term))
        );
    }
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    newControl.value = { nombre: '', tipo: 'Preventivo', descripcion: '', estado: 'Activo' };
};

const createControl = async () => {
    if (!newControl.value.nombre) return;
    creating.value = true;
    try {
        const res = await axios.post('/api/controles', newControl.value);
        controls.value.push(res.data.control);
        filterControls();
        // Auto select
        toggleSelection(res.data.control);
        closeCreateModal();
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Control creado', timer: 1500, showConfirmButton: false });
    } catch (e) {
        Swal.fire('Error', 'No se pudo crear el control', 'error');
    } finally {
        creating.value = false;
    }
};

onMounted(() => {
    fetchControls();
});
</script>

<style scoped>
.modal-backdrop-custom {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1055;
}

.control-list::-webkit-scrollbar {
    width: 6px;
}

.control-list::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 4px;
}
</style>

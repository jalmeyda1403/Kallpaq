<template>
    <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ isEdit ? 'Editar' : 'Crear' }} Rol</h5>
                    <button type="button" class="close text-white" aria-label="Close" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="save">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold custom-label text-muted small">Nombre del Rol
                                <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0 text-danger">
                                        <i :class="getRoleIcon(form.name)"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control border-left-0" id="name" v-model="form.name"
                                    required :class="{ 'is-invalid': errors.name }"
                                    placeholder="Ej: Administrador, Auditor...">
                                <div class="invalid-feedback" v-if="errors.name">
                                    {{ errors.name[0] }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description" class="font-weight-bold custom-label">Descripción</label>
                            <textarea class="form-control" id="description" v-model="form.description" rows="3"
                                :class="{ 'is-invalid': errors.description }"
                                placeholder="Ingrese una descripción opcional"></textarea>
                            <div class="invalid-feedback" v-if="errors.description">
                                {{ errors.description[0] }}
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="close">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger ml-2" @click="save" :disabled="saving">
                        <span v-if="saving" class="spinner-border spinner-border-sm mr-1" role="status"
                            aria-hidden="true"></span>
                        <i class="fas fa-save mr-1" v-if="!saving"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, computed } from 'vue';
import { Modal } from 'bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    role: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['saved', 'close']);

const modalEl = ref(null);
let modalInstance = null;
const saving = ref(false);
const errors = ref({});

const form = reactive({
    name: '',
    description: ''
});

const isEdit = computed(() => !!props.role);

onMounted(() => {
    if (modalEl.value) {
        modalInstance = new Modal(modalEl.value, {
            backdrop: 'static',
            keyboard: false
        });
    }
});

watch(() => props.role, (newRole) => {
    if (newRole) {
        form.name = newRole.name;
        form.description = newRole.description || '';
    } else {
        form.name = '';
        form.description = '';
    }
    errors.value = {};
});

const open = () => {
    modalInstance.show();
};

const close = () => {
    modalInstance.hide();
    emit('close');
};

const save = async () => {
    saving.value = true;
    errors.value = {};
    try {
        if (isEdit.value) {
            await axios.put(`/api/roles/${props.role.id}`, form);
        } else {
            await axios.post('/api/roles', form);
        }
        // Removed success alert, parent handles it
        emit('saved');
        close();
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error('Error saving role:', error);
            Swal.fire('Error', 'Error al guardar el rol.', 'error');
        }
    } finally {
        saving.value = false;
    }
};

const getRoleIcon = (role) => {
    if (!role) return 'fas fa-user-tag';
    const roleLower = role.toLowerCase();
    if (roleLower.includes('admin')) return 'fas fa-user-shield';
    if (roleLower.includes('auditor')) return 'fas fa-clipboard-check';
    if (roleLower.includes('especialista')) return 'fas fa-user-cog';
    if (roleLower.includes('propietario')) return 'fas fa-user-tie';
    if (roleLower.includes('facilitador')) return 'fas fa-chalkboard-teacher';
    return 'fas fa-user-tag';
};

defineExpose({
    open,
    close
});
</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-weight: 600 !important;
    color: #495057 !important;
}
</style>

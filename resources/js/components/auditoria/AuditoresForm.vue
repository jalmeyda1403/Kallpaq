<template>
    <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">
                        {{ isEdit ? 'Editar Auditor' : 'Nuevo Auditor' }}
                    </h5>
                    <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="saveAuditor">
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Seleccionar Usuario</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <select v-model="form.user_id" class="form-control" required>
                                    <option value="">Seleccione un usuario...</option>
                                    <option v-for="user in availableUsers" :key="user.id" :value="user.id">
                                        {{ user.name }} ({{ user.email }})
                                    </option>
                                </select>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Solo usuarios que no están registrados como auditores aparecen en la lista.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-secondary" @click="close">Cancelar</button>
                        <button type="submit" class="btn btn-danger px-4 shadow-sm" :disabled="loading">
                            <i class="fas fa-save mr-1" v-if="!loading"></i>
                            <span v-else class="spinner-border spinner-border-sm mr-1"></span>
                            {{ isEdit ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const modalRef = ref(null);
const bootstrapModal = ref(null);
const isEdit = ref(false);
const loading = ref(false);
const availableUsers = ref([]);

const form = reactive({
    id: null,
    user_id: ''
});

const emit = defineEmits(['saved']);

const open = async (auditor = null) => {
    isEdit.value = !!auditor;
    if (auditor) {
        form.id = auditor.id;
        form.user_id = auditor.user_id;
    } else {
        form.id = null;
        form.user_id = '';
    }

    await fetchAvailableUsers();

    if (!bootstrapModal.value) {
        bootstrapModal.value = new bootstrap.Modal(modalRef.value);
    }
    bootstrapModal.value.show();
};

const close = () => {
    if (bootstrapModal.value) {
        bootstrapModal.value.hide();
    }
};

const fetchAvailableUsers = async () => {
    try {
        const params = {};
        if (isEdit.value) {
            params.exclude_auditor_id = form.id;
        }
        const response = await axios.get('/api/auditores-disponibles', { params });
        availableUsers.value = response.data;
    } catch (e) {
        console.error("Error fetching users", e);
    }
};

const saveAuditor = async () => {
    loading.value = true;
    try {
        if (isEdit.value) {
            await axios.put(`/api/auditores/${form.id}`, form);
            toast.add({ severity: 'success', summary: 'Actualizado', detail: 'Auditor actualizado correctamente', life: 3000 });
        } else {
            await axios.post('/api/auditores', form);
            toast.add({ severity: 'success', summary: 'Registrado', detail: 'Auditor registrado correctamente', life: 3000 });
        }
        emit('saved');
        close();
    } catch (e) {
        const msg = e.response?.data?.message || 'Error al procesar la solicitud';
        toast.add({ severity: 'error', summary: 'Error', detail: msg, life: 3000 });
    } finally {
        loading.value = false;
    }
};

defineExpose({ open });

onMounted(() => {
    // Initial fetch not strictly needed here as we fetch on open
});
</script>

<style scoped>
.modal-header.bg-danger {
    background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%) !important;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}

.form-control {
    border-left: none;
}

.form-control:focus {
    box-shadow: none;
    border-color: #ced4da;
}
</style>

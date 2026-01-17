<template>
    <!-- Removed tabindex="-1" to avoid Bootstrap focus trap interfering with PrimeVue inputs if needed, though overflow visible is the main fix for appendTo="self" -->
    <div ref="modalRef" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg" style="overflow: visible !important;">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">
                        {{ isEdit ? 'Editar Auditor' : 'Nuevo Auditor' }}
                    </h5>
                    <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="saveAuditor">
                    <div class="modal-body p-4" style="overflow: visible !important;">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark mb-2">
                                <i class="fas fa-user-circle mr-1 text-danger"></i> Seleccionar Usuario
                            </label>
                            
                            <Dropdown
                                v-model="form.user_id"
                                :options="availableUsers"
                                optionLabel="name"
                                optionValue="id"
                                filter
                                :filterBy="['name', 'email']"
                                placeholder="Busque y seleccione un usuario..."
                                class="w-100 custom-dropdown"
                                :class="{'p-invalid': !form.user_id && submitted}"
                                emptyFilterMessage="No se encontraron usuarios"
                                emptyMessage="No hay usuarios disponibles"
                                panelClass="custom-dropdown-panel"
                            >
                                <template #value="slotProps">
                                    <div v-if="slotProps.value">
                                        {{ availableUsers.find(u => u.id === slotProps.value)?.name }} 
                                        <span class="text-muted small ml-1" v-if="availableUsers.find(u => u.id === slotProps.value)?.email">
                                            ({{ availableUsers.find(u => u.id === slotProps.value)?.email }})
                                        </span>
                                    </div>
                                    <span v-else>
                                        {{ slotProps.placeholder }}
                                    </span>
                                </template>
                                <template #option="slotProps">
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2 text-dark" style="font-size: 0.95rem;">{{ slotProps.option.name }}</span>
                                        <small class="text-muted">({{ slotProps.option.email }})</small>
                                    </div>
                                </template>
                            </Dropdown>
                            
                            <div v-if="!form.user_id && submitted" class="text-danger small mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i> Debe seleccionar un usuario.
                            </div>

                            <div class="alert alert-light border mt-3 mb-0 d-flex align-items-start">
                                <i class="fas fa-info-circle text-info mt-1 mr-2"></i>
                                <small class="text-muted">
                                    La lista solo muestra usuarios que aún no están registrados como auditores.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 px-4 pb-4">
                        <button type="button" class="btn btn-outline-secondary px-4 transition-button" @click="close">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger px-4 shadow-sm transition-button" :disabled="loading">
                            <i class="fas fa-save mr-1" v-if="!loading"></i>
                            <span v-else class="spinner-border spinner-border-sm mr-1"></span>
                            {{ isEdit ? 'Guardar Cambios' : 'Registrar Auditor' }}
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
import Dropdown from 'primevue/dropdown';

const toast = useToast();
const modalRef = ref(null);
const bootstrapModal = ref(null);
const isEdit = ref(false);
const loading = ref(false);
const availableUsers = ref([]);
const submitted = ref(false);

const form = reactive({
    id: null,
    user_id: ''
});

const emit = defineEmits(['saved']);

const open = async (auditor = null) => {
    isEdit.value = !!auditor;
    submitted.value = false;
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
    submitted.value = true;
    if (!form.user_id) {
         toast.add({ severity: 'warn', summary: 'Atención', detail: 'Debe seleccionar un usuario', life: 3000 });
         return;
    }

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
    //
});
</script>

<style scoped>
.modal-header.bg-danger {
    background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%) !important;
}

/* Custom styling for PrimeVue Dropdown */
:deep(.custom-dropdown) {
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
    transition: all 0.2s;
}

:deep(.custom-dropdown:hover) {
    border-color: #b71c1c;
}

:deep(.custom-dropdown.p-focus) {
    border-color: #d32f2f;
    box-shadow: 0 0 0 0.2rem rgba(211, 47, 47, 0.25);
}

:deep(.p-dropdown-label) {
    padding: 0.6rem 0.75rem; 
}

/* Panel Styling */
:deep(.custom-dropdown-panel .p-dropdown-items .p-dropdown-item) {
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
}

:deep(.custom-dropdown-panel .p-dropdown-items .p-dropdown-item.p-highlight) {
    background-color: #ffebee;
    color: #b71c1c;
}

.transition-button {
    transition: transform 0.1s ease, box-shadow 0.2s ease;
}

.transition-button:active {
    transform: translateY(1px);
}
</style>

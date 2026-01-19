<template>
    <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box mr-3 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-user-tag text-danger"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold">Asignar Rol</h5>
                            <small class="text-white-50">Defina el nivel de acceso del usuario</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white outline-none" aria-label="Close" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- User Info Header -->
                <div class="bg-light p-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div
                            class="avatar-circle mr-3 bg-danger text-white d-flex align-items-center justify-content-center font-weight-bold">
                            {{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold">{{ user?.name }}</h6>
                            <small class="text-muted">{{ user?.email }}</small>
                        </div>
                    </div>
                </div>

                <div class="modal-body p-4 bg-light">
                    <div v-if="loading" class="text-center py-5">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted small">Cargando roles...</p>
                    </div>

                    <div v-else-if="availableRoles.length === 0" class="text-center text-muted py-5">
                        <i class="fas fa-exclamation-circle fa-2x mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0">No se encontraron roles disponibles.</p>
                    </div>

                    <div v-else class="row">
                        <div class="col-md-4 mb-3" v-for="role in availableRoles" :key="role.id">
                            <div class="role-card h-100 p-3 rounded bg-white border shadow-sm cursor-pointer transition-all d-flex flex-column align-items-center justify-content-center text-center position-relative"
                                :class="{ 'border-danger role-selected': selectedRole === role.name }"
                                @click="selectedRole = role.name">

                                <div class="role-icon mb-2 rounded-circle d-flex align-items-center justify-content-center"
                                    :class="selectedRole === role.name ? 'bg-danger text-white' : 'bg-light text-secondary'">
                                    <i :class="getRoleIcon(role.name)" class="fa-2x"></i>
                                </div>
                                <h6 class="mb-1 font-weight-bold" :class="{ 'text-danger': selectedRole === role.name }">{{
                                    role.name }}</h6>

                                <div class="selection-check text-danger position-absolute"
                                    style="top: 10px; right: 10px;" v-if="selectedRole === role.name">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="close">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger btn-sm px-4 shadow-sm" @click="saveRoles"
                        :disabled="saving">
                        <span v-if="saving" class="spinner-border spinner-border-sm mr-1" role="status"
                            aria-hidden="true"></span>
                        <i class="fas fa-save mr-1" v-if="!saving"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Modal } from 'bootstrap';
import { useUserStore } from '@/stores/userStore';
import Swal from 'sweetalert2';

const props = defineProps({
    user: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['roles-updated', 'close']);
const store = useUserStore();

const modalEl = ref(null);
let modalInstance = null;
const availableRoles = ref([]);
const selectedRole = ref(null);
const loading = ref(false);
const saving = ref(false);

const initModal = () => {
    if (modalEl.value && !modalInstance) {
        modalInstance = new Modal(modalEl.value, {
            backdrop: 'static',
            keyboard: false
        });
    }
};

onMounted(() => {
    initModal();
    loadRoles();
});

const loadRoles = async () => {
    loading.value = true;
    try {
        availableRoles.value = await store.fetchRoles();
    } catch (error) {
        console.error('Error fetching roles:', error);
        Swal.fire('Error', 'No se pudieron cargar los roles', 'error');
    } finally {
        loading.value = false;
    }
};

const getRoleIcon = (roleName) => {
    const icons = {
        'admin': 'fas fa-user-shield',
        'administrador': 'fas fa-user-shield',
        'super-admin': 'fas fa-crown',
        'auditor': 'fas fa-clipboard-check',
        'auditor_lider': 'fas fa-user-tie',
        'facilitador': 'fas fa-chalkboard-teacher',
        'propietario': 'fas fa-user-tag',
        'especialista': 'fas fa-user-cog',
        'usuario': 'fas fa-user',
        'editor': 'fas fa-edit',
        'visualizador': 'fas fa-eye'
    };
    return icons[roleName.toLowerCase()] || 'fas fa-id-badge';
};

watch(() => props.user, (newUser) => {
    if (newUser) {
        // Handle if roles is array of strings or objects
        if (newUser.roles && newUser.roles.length > 0) {
             const firstRole = newUser.roles[0];
             selectedRole.value = typeof firstRole === 'object' ? firstRole.name : firstRole;
        } else {
            selectedRole.value = null;
        }
    } else {
        selectedRole.value = null;
    }
});

const open = async () => {
    initModal();
    if (modalInstance) {
        modalInstance.show();
        if (availableRoles.value.length === 0) {
            await loadRoles();
        }
    }
};

const close = () => {
    modalInstance.hide();
    emit('close');
};

const saveRoles = async () => {
    if (!props.user) return;

    saving.value = true;
    try {
        const rolesToSend = selectedRole.value ? [selectedRole.value] : [];
        await store.assignRoles(props.user.id, rolesToSend);
        emit('roles-updated');
        close();
        Swal.fire({
            title: 'Rol Asignado',
            text: 'El rol se ha actualizado correctamente.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    } catch (error) {
        console.error('Error assigning roles:', error);
        Swal.fire('Error', 'Error al asignar el rol.', 'error');
    } finally {
        saving.value = false;
    }
};

defineExpose({
    open,
    close
});
</script>

<style scoped>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 1.2rem;
}

.role-card {
    transition: all 0.2s ease-in-out;
    border: 2px solid transparent;
}

.role-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
}

.role-selected {
    background-color: #fff5f5 !important;
    border-color: #dc3545 !important;
}

.role-icon {
    width: 60px;
    height: 60px;
}

.cursor-pointer {
    cursor: pointer;
}
</style>

<template>
    <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="height: 650px;">
                <!-- Header -->
                <div class="modal-header bg-danger text-white border-bottom py-3">
                    <div class="d-flex align-items-center w-100 justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-box mr-3 bg-white text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-shield-alt fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="modal-title font-weight-bold mb-0">Gestionar Permisos</h5>
                                <p class="small mb-0 text-white-50">Rol: <span class="font-weight-bold text-white">{{
                                    role?.name }}</span></p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">

                            <button type="button" class="close text-white outline-none" @click="close"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Body (Master-Detail Layout) -->
                <div class="modal-body p-0 d-flex overflow-hidden bg-light">
                    <!-- Loading State -->
                    <div v-if="loading" class="w-100 d-flex flex-column align-items-center justify-content-center">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-3 text-muted">Cargando definición de permisos...</p>
                    </div>

                    <template v-else>
                        <!-- Sidebar (Modules List) -->
                        <div class="module-sidebar bg-white border-right overflow-auto custom-scrollbar"
                            style="width: 300px; min-width: 300px;">
                            <div class="p-3 border-bottom bg-light sticky-top">
                                <h6 class="text-uppercase text-muted font-weight-bold small mb-0 ls-1">Módulos del Sistema
                                </h6>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="#" v-for="module in groupedPermissions" :key="module.name"
                                    class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 border-bottom-light"
                                    :class="{ 'active-module': selectedModule?.name === module.name }"
                                    @click.prevent="selectModule(module)">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        <div class="status-indicator mr-3 rounded-circle"
                                            :class="isModuleFullyActive(module) ? 'bg-success' : (isModulePartiallyActive(module) ? 'bg-warning' : 'bg-gray-200')"
                                            style="width: 10px; height: 10px; min-width: 10px;"></div>
                                        <div class="text-truncate">
                                            <span class="font-weight-600 d-block">{{ formatModuleName(module.name)
                                                }}</span>
                                            <span class="small text-muted">{{ getActiveCount(module) }}/{{
                                                module.permissions.length }} permisos</span>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch custom-switch-sm ml-2" @click.stop>
                                        <input type="checkbox" class="custom-control-input" :id="'mod-' + module.name"
                                            :checked="isModuleFullyActive(module)"
                                            @change="toggleModule(module, $event.target.checked)">
                                        <label class="custom-control-label" :for="'mod-' + module.name"></label>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Content (Permissions Details) -->
                        <div class="flex-fill overflow-auto custom-scrollbar p-0 bg-light">
                            <div v-if="selectedModule">
                                <div class="bg-white p-4 border-bottom shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-1 font-weight-bold text-dark">{{
                                                formatModuleName(selectedModule.name) }}</h4>
                                            <p class="text-muted mb-0">Configura el acceso específico para este módulo.
                                            </p>
                                        </div>
                                        <div class="custom-control custom-switch custom-switch-lg custom-switch-danger">
                                            <input type="checkbox" class="custom-control-input"
                                                :id="'main-' + selectedModule.name"
                                                :checked="isModuleFullyActive(selectedModule)"
                                                @change="toggleModule(selectedModule, $event.target.checked)">
                                            <label class="custom-control-label font-weight-bold text-dark"
                                                :for="'main-' + selectedModule.name">
                                                Habilitar Módulo Completo
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-12 mb-3" v-for="perm in selectedModule.permissions"
                                            :key="perm.id">
                                            <div class="card border border-light shadow-xs permission-card hover-lift"
                                                :class="{ 'border-danger active-card': isPermissionActive(perm.name) }"
                                                @click="togglePermission(perm.name)">
                                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                                    <div class="flex-fill">
                                                        <h6 class="mb-1 font-weight-bold text-dark">{{
                                                            cleanPermissionName(perm.name) }}</h6>
                                                        <small class="text-muted d-block">{{ perm.description ||
                                                            'Sin descripción' }}</small>
                                                        <small class="text-monospace text-xs text-muted mt-1 d-block">{{
                                                            perm.name }}</small>
                                                    </div>
                                                    <div class="custom-control custom-switch custom-switch-danger ml-3">
                                                        <input type="checkbox" class="custom-control-input"
                                                            :id="'perm-' + perm.id"
                                                            :checked="isPermissionActive(perm.name)"
                                                            @change="togglePermission(perm.name)">
                                                        <label class="custom-control-label" :for="'perm-' + perm.id"
                                                            @click.stop></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="fas fa-hand-pointer fa-3x mb-3 text-gray-300"></i>
                                <p class="h5">Selecciona un módulo para configurar sus permisos</p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-secondary px-4 font-weight-500" @click="close"
                        :disabled="saving">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger px-5 shadow-sm font-weight-bold"
                        @click="savePermissions" :disabled="saving">
                        <span v-if="saving" class="spinner-border spinner-border-sm mr-2" role="status"
                            aria-hidden="true"></span>
                        <span v-else><i class="fas fa-save mr-2"></i> Guardar Cambios</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { Modal } from 'bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useAuthStore } from '@/stores/authStore';

const props = defineProps({
    role: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['saved', 'close']);
const authStore = useAuthStore();

const modalEl = ref(null);
let modalInstance = null;
const loading = ref(false);
const saving = ref(false);

const allPermissions = ref([]);
const selectedPermissions = ref([]);
const selectedModule = ref(null);

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
        loadData();
    }
});

// Agrupa permisos por módulo (menu.MODULO.accion)
const groupedPermissions = computed(() => {
    const groups = {};

    allPermissions.value.forEach(perm => {
        // Asumimos formato menu.modulo.accion o modulo.accion
        const parts = perm.name.split('.');
        // Si empieza con "menu", el modulo es el segundo segmento, si no, es el primero
        let moduleName = 'General';

        if (parts[0] === 'menu' && parts.length > 1) {
            moduleName = parts[1];
        } else if (parts.length > 1) {
            moduleName = parts[0];
        }

        if (!groups[moduleName]) {
            groups[moduleName] = {
                name: moduleName,
                permissions: []
            };
        }
        groups[moduleName].permissions.push(perm);
    });

    // Ordenar módulos alfabéticamente
    return Object.values(groups).sort((a, b) => a.name.localeCompare(b.name));
});

const loadData = async () => {
    loading.value = true;
    try {
        const [allRes, roleRes] = await Promise.all([
            axios.get('/api/roles/permissions/all'),
            axios.get(`/api/roles/${props.role.id}/permissions`)
        ]);
        allPermissions.value = allRes.data;
        selectedPermissions.value = roleRes.data;

        // Seleccionar primer módulo por defecto
        if (groupedPermissions.value.length > 0) {
            selectedModule.value = groupedPermissions.value[0];
        }
    } catch (error) {
        console.error('Error loading permissions:', error);
        Swal.fire('Error', 'No se pudieron cargar los permisos.', 'error');
    } finally {
        loading.value = false;
    }
};

// Utils
const formatModuleName = (name) => {
    return name.charAt(0).toUpperCase() + name.slice(1).replace(/_/g, ' ');
};

const cleanPermissionName = (name) => {
    const parts = name.split('.');
    return formatModuleName(parts[parts.length - 1]);
};

// Selection Logic
const isPermissionActive = (permName) => {
    return selectedPermissions.value.includes(permName);
};

const togglePermission = (permName) => {
    const index = selectedPermissions.value.indexOf(permName);
    if (index === -1) {
        selectedPermissions.value.push(permName);
    } else {
        selectedPermissions.value.splice(index, 1);
    }
};

const isModuleFullyActive = (module) => {
    if (!module || !module.permissions) return false;
    return module.permissions.every(p => selectedPermissions.value.includes(p.name));
};

const isModulePartiallyActive = (module) => {
    if (!module || !module.permissions) return false;
    const count = getActiveCount(module);
    return count > 0 && count < module.permissions.length;
};

const getActiveCount = (module) => {
    if (!module || !module.permissions) return 0;
    return module.permissions.filter(p => selectedPermissions.value.includes(p.name)).length;
};

const toggleModule = (module, isActive) => {
    const modulePermNames = module.permissions.map(p => p.name);

    if (isActive) {
        // Agregar los que faltan
        modulePermNames.forEach(name => {
            if (!selectedPermissions.value.includes(name)) {
                selectedPermissions.value.push(name);
            }
        });
    } else {
        // Remover todos
        selectedPermissions.value = selectedPermissions.value.filter(
            name => !modulePermNames.includes(name)
        );
    }
};

const toggleAllGlobal = (isActive) => {
    if (isActive) {
        selectedPermissions.value = allPermissions.value.map(p => p.name);
    } else {
        selectedPermissions.value = [];
    }
};

const selectModule = (module) => {
    selectedModule.value = module;
};

// Modal Actions
const open = () => {
    modalInstance.show();
};

const close = () => {
    modalInstance.hide();
    emit('close');
};

const savePermissions = async () => {
    saving.value = true;
    try {
        await axios.post(`/api/roles/${props.role.id}/permissions`, {
            permissions: selectedPermissions.value
        });

        const isOwnRole = authStore.roles.includes(props.role.name);

        Swal.fire({
            icon: 'success',
            title: '¡Guardado!',
            text: isOwnRole 
                ? 'Configuración actualizada. La página se recargará para aplicar los cambios a tu usuario.' 
                : 'Configuración actualizada correctamente.',
            timer: isOwnRole ? 2500 : 1500,
            showConfirmButton: false
        }).then(() => {
            if (isOwnRole) {
                window.location.reload();
            }
        });
        
        emit('saved');
        close();
    } catch (error) {
        console.error('Error saving permissions:', error);
        Swal.fire('Error', 'No se pudieron guardar los permisos.', 'error');
    } finally {
        saving.value = false;
    }
};

defineExpose({ open, close });
</script>

<style scoped>
.bg-danger-light {
    background-color: #fce8e8;
}

.bg-gray-200 {
    background-color: #e2e8f0;
}

.text-xs {
    font-size: 0.75rem;
}

.list-group-item {
    border: none;
    border-left: 4px solid transparent;
    transition: all 0.2s;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.active-module {
    background-color: #fff5f5 !important;
    border-left-color: #dc3545;
    color: #dc3545;
}

.active-module .text-muted {
    color: #e57373 !important;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.permission-card {
    cursor: pointer;
    transition: all 0.2s;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05) !important;
}

.active-card {
    background-color: #fff5f5;
}

.custom-switch-lg {
    padding-left: 3.2rem !important;
    min-height: 1.75rem;
}

.custom-switch-lg .custom-control-label {
    font-size: 0.9rem;
    padding-top: 2px;
}

/* Track */
.custom-switch-lg .custom-control-label::before {
    left: -3.2rem !important;
    width: 3rem !important;
    height: 1.5rem !important;
    border-radius: 2rem !important;
    top: 0 !important;
    background-color: #e9ecef;
    border: 1px solid #adb5bd;
}

/* Knob */
.custom-switch-lg .custom-control-label::after {
    top: 2px !important;
    left: calc(-3.2rem + 2px) !important;
    width: calc(1.5rem - 4px) !important;
    height: calc(1.5rem - 4px) !important;
    border-radius: 50% !important;
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

/* Checked State - Slide Knob */
.custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after {
    transform: translateX(1.5rem) !important;
}

.custom-switch-danger .custom-control-input:checked~.custom-control-label::before {
    background-color: #dc3545;
    border-color: #dc3545;
}

.border-bottom-light {
    border-bottom: 1px solid #f0f0f0;
}
</style>

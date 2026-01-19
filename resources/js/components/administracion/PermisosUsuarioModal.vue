<template>
    <div class="modal fade" ref="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="height: 650px;">
                <!-- Header -->
                <div class="modal-header bg-danger text-white border-bottom py-3">
                    <div class="d-flex align-items-center w-100 justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-box mr-3 bg-white text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 48px; height: 48px;">
                                <i class="fas fa-user-lock fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="modal-title font-weight-bold mb-0">Permisos por Usuario</h5>
                                <p class="small mb-0 text-white-50">Usuario: <span class="font-weight-bold text-white">{{
                                    user?.name }}</span></p>
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

                <!-- Body -->
                <div class="modal-body p-0 d-flex overflow-hidden bg-light">
                    <!-- Sidebar: Modules List -->
                    <div class="module-sidebar bg-white border-right overflow-auto custom-scrollbar" style="width: 300px; min-width: 300px;">
                        <div class="p-3 border-bottom bg-light sticky-top">
                            <h6 class="text-uppercase font-weight-bold text-muted small mb-0 spacing-1">Módulos del Sistema</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="#" v-for="module in modules" :key="module.name"
                                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 border-bottom-light"
                                :class="{ 'active-module': selectedStep === module.name }"
                                @click.prevent="selectedStep = module.name">
                                <div class="d-flex align-items-center overflow-hidden">
                                    <div class="status-indicator mr-3 rounded-circle" 
                                        :class="isModuleFullyActive(module) ? 'bg-success' : (getActiveCount(module) > 0 ? 'bg-warning' : 'bg-gray-200')"
                                        style="width: 10px; height: 10px; min-width: 10px;"></div>
                                    <div class="text-truncate">
                                        <h6 class="mb-0 font-weight-600">{{ formatModuleName(module.name) }}</h6>
                                        <small class="text-muted">{{ getActiveCount(module) }}/{{ module.permissions.length }} permisos</small>
                                    </div>
                                </div>
                                
                                <!-- Module Toggle Switch -->
                                <div class="custom-control custom-switch custom-switch-sm ml-2" @click.stop>
                                    <input type="checkbox" class="custom-control-input" 
                                        :id="'switch-' + module.name"
                                        :checked="isModuleFullyActive(module)"
                                        @change="toggleModule(module, $event.target.checked)">
                                    <label class="custom-control-label" :for="'switch-' + module.name"></label>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Content: Permissions List -->
                    <div class="flex-fill overflow-auto custom-scrollbar p-0 bg-light">
                        <div v-if="currentModule">
                            <div class="bg-white p-4 border-bottom shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="font-weight-bold text-dark mb-1">{{ formatModuleName(selectedStep) }}</h4>
                                        <p class="text-muted mb-0">Configura el acceso específico (Excepciones y Permisos Directos).</p>
                                    </div>
                                    
                                <div class="custom-control custom-switch custom-switch-lg custom-switch-danger">
                                        <input type="checkbox" class="custom-control-input" 
                                            :id="'head-switch-' + selectedStep"
                                            :checked="isModuleFullyActive(currentModule)"
                                            @change="toggleModule(currentModule, $event.target.checked)">
                                        <label class="custom-control-label font-weight-bold text-dark" :for="'head-switch-' + selectedStep">
                                            Habilitar Módulo Completo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="row">
                                    <div v-for="permission in currentPermissions" :key="permission.id" class="col-md-12 mb-3">
                                        <div class="card border border-light shadow-xs permission-card hover-lift" 
                                            :class="{ 'border-danger active-card': hasPermission(permission.name) }"
                                            @click="togglePermission(permission.name)">
                                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                                <div class="flex-fill">
                                                    <h6 class="mb-1 font-weight-bold text-dark">
                                                        {{ permission.description || cleanPermissionName(permission.name) }}
                                                    </h6>
                                                    <code class="text-xs text-muted d-block mb-1">{{ permission.name }}</code>
                                                    
                                                    <!-- Permission Source Badge -->
                                                    <div>
                                                        <span v-if="isDenied(permission.id)" class="badge badge-danger">
                                                            <i class="fas fa-ban mr-1"></i> Bloqueado
                                                        </span>
                                                        <span v-else-if="isDirect(permission.id)" class="badge badge-info text-white">
                                                            <i class="fas fa-user mr-1"></i> Directo
                                                        </span>
                                                        <span v-else-if="isByRole(permission.id) && hasPermission(permission.name)" class="badge badge-secondary">
                                                            <i class="fas fa-users mr-1"></i> Por Rol
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="custom-control custom-switch custom-switch-danger ml-3">
                                                    <input type="checkbox" class="custom-control-input"
                                                        :id="'perm-' + permission.id" 
                                                        :checked="hasPermission(permission.name)"
                                                        @change="togglePermission(permission.name)">
                                                    <label class="custom-control-label" :for="'perm-' + permission.id" @click.stop></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light border-top py-3">
                    <button type="button" class="btn btn-secondary font-weight-bold" @click="close">Cancelar</button>
                    <button type="button" class="btn btn-dark font-weight-bold px-4" @click="save">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Modal } from 'bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useAuthStore } from '@/stores/authStore';

const props = defineProps({
    user: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['saved', 'close']);
const authStore = useAuthStore();

const modal = ref(null);
let modalInstance = null;

// Data
const allPermissions = ref([]);       // List of all Permission objects
const userRolePermissionIds = ref([]); // IDs provided by roles
const userDirectPermissionIds = ref([]);// IDs provided directly
const userDeniedPermissionIds = ref([]); // IDs in blacklist

// Permissions State (The simplified boolean view: Is Active?)
const selectedPermissions = ref([]); // List of permission NAMES that are currently active

const modules = ref([]);
const selectedStep = ref('');

// Computed
const currentModule = computed(() => modules.value.find(m => m.name === selectedStep.value));
const currentPermissions = computed(() => currentModule.value ? currentModule.value.permissions : []);

// Initialization
onMounted(async () => {
    modalInstance = new Modal(modal.value, {
        backdrop: 'static',
        keyboard: false
    });
    modalInstance.show();
    await fetchPermissions();
});

const fetchPermissions = async () => {
    try {
        // Fetch User Analysis (Roles, Direct, Denied)
        const response = await axios.get(`/api/users/${props.user.id}/permissions`);
        
        allPermissions.value = response.data.all_permissions;
        userRolePermissionIds.value = response.data.role_permissions;
        userDirectPermissionIds.value = response.data.direct_permissions;
        userDeniedPermissionIds.value = response.data.denied_permissions;

        // FILTER: Only show permissions that are granted by the Role.
        // The user cannot "add" new permissions here, only "block" existing ones.
        allPermissions.value = allPermissions.value.filter(p => userRolePermissionIds.value.includes(p.id));

        // Calculate Initial Active State
        // Active if: In Role AND NOT Denied
        const activeNames = [];
        
        allPermissions.value.forEach(p => {
            const isDenied = userDeniedPermissionIds.value.includes(p.id);
            // Since we only list Role permissions, we just check if it's NOT denied.
            if (!isDenied) {
                activeNames.push(p.name);
            }
        });
        
        selectedPermissions.value = activeNames;

        groupPermissionsByModule();
    } catch (error) {
        console.error("Error fetching permissions", error);
        Swal.fire('Error', 'No se pudieron cargar los permisos', 'error');
    }
};

const groupPermissionsByModule = () => {
    const groups = {};
    
    // 1. Group by prefix 'menu.module'
    allPermissions.value.forEach(p => {
        const parts = p.name.split('.');
        const moduleName = parts.length > 1 ? parts[1] : 'General'; // menu.[module]...
        
        if (!groups[moduleName]) {
            groups[moduleName] = [];
        }
        groups[moduleName].push(p);
    });

    // 2. Convert to array and sort
    modules.value = Object.keys(groups).map(key => ({
        name: key,
        permissions: groups[key]
    })).sort((a, b) => a.name.localeCompare(b.name));

    if (modules.value.length > 0) {
        selectedStep.value = modules.value[0].name;
    }
};

// Checkers
const hasPermission = (name) => selectedPermissions.value.includes(name);

const isDenied = (id) => userDeniedPermissionIds.value.includes(id);
const isDirect = (id) => userDirectPermissionIds.value.includes(id);
const isByRole = (id) => userRolePermissionIds.value.includes(id);

// UI Helpers
const formatModuleName = (name) => {
    return name ? name.charAt(0).toUpperCase() + name.slice(1).replace(/_/g, ' ') : '';
};

const cleanPermissionName = (name) => {
    const parts = name.split('.');
    return formatModuleName(parts[parts.length - 1]);
};

const getActiveCount = (module) => {
    return module.permissions.filter(p => hasPermission(p.name)).length;
};

const getModuleStatusColor = (module) => {
    const active = getActiveCount(module);
    const total = module.permissions.length;
    
    if (active === 0) return 'bg-gray-300';
    if (active === total) return 'bg-success';
    return 'bg-warning';
};

const isModuleFullyActive = (module) => {
    return module && getActiveCount(module) === module.permissions.length;
};


// Actions
const togglePermission = (name) => {
    if (selectedPermissions.value.includes(name)) {
        selectedPermissions.value = selectedPermissions.value.filter(p => p !== name);
    } else {
        selectedPermissions.value.push(name);
    }
};

const toggleModule = (module, isActive) => {
    module.permissions.forEach(p => {
        if (isActive) {
            if (!selectedPermissions.value.includes(p.name)) {
                selectedPermissions.value.push(p.name);
            }
        } else {
            selectedPermissions.value = selectedPermissions.value.filter(name => name !== p.name);
        }
    });
};

const toggleAllGlobal = (isActive) => {
    if (isActive) {
        const allNames = allPermissions.value.map(p => p.name);
        selectedPermissions.value = [...new Set([...selectedPermissions.value, ...allNames])];
    } else {
        selectedPermissions.value = [];
    }
};

const save = async () => {
    try {
        await axios.post(`/api/users/${props.user.id}/permissions`, {
            permissions: selectedPermissions.value
        });

        const isOwnUser = authStore.user.id === props.user.id;

        Swal.fire({
            icon: 'success',
            title: '¡Guardado!',
            text: isOwnUser 
                ? 'Permisos actualizados. Recargando para aplicar cambios...' 
                : 'Permisos de usuario actualizados correctamente.',
            timer: isOwnUser ? 2500 : 1500,
            showConfirmButton: false
        }).then(() => {
             if (isOwnUser) {
                window.location.reload();
            }
        });
        
        emit('saved');
        close();
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'No se pudieron guardar los cambios', 'error');
    }
};

const close = () => {
    modalInstance.hide();
    modal.value.addEventListener('hidden.bs.modal', () => {
        emit('close');
    }, { once: true });
};
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

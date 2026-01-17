<template>
    <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box mr-3 bg-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-shield-alt text-danger"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold">Gestionar Permisos</h5>
                            <small class="text-white-50">Rol: {{ role?.name }}</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white outline-none" @click="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light p-4">
                    <div v-if="loading" class="text-center py-5">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando permisos...</p>
                    </div>

                    <div v-else>
                        <!-- Buscador y Acciones Globales -->
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-7">
                                <div class="input-group bg-white shadow-sm rounded border">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-0 px-3 text-muted">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="text" v-model="searchQuery"
                                        class="form-control border-0 shadow-none px-2"
                                        placeholder="Buscar permiso por nombre...">
                                </div>
                            </div>
                            <div class="col-md-5 text-md-right mt-3 mt-md-0">
                                <button class="btn btn-outline-secondary btn-sm mr-2" @click="toggleAll(true)">
                                    <i class="fas fa-check-double mr-1"></i> Todos
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" @click="toggleAll(false)">
                                    <i class="fas fa-times mr-1"></i> Ninguno
                                </button>
                            </div>
                        </div>

                        <!-- Lista de Permisos -->
                        <div class="card border-0 shadow-sm overflow-hidden">
                            <div class="table-responsive" style="max-height: 50vh;">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-white border-bottom">
                                        <tr>
                                            <th
                                                class="border-0 text-muted small text-uppercase font-weight-bold py-3 pl-4">
                                                Permiso</th>
                                            <th class="border-0 text-muted small text-uppercase font-weight-bold py-3 text-center"
                                                style="width: 100px;">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="permission in filteredPermissions" :key="permission.id"
                                            class="permission-row">
                                            <td class="py-3 pl-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-key text-muted mr-3"
                                                        style="font-size: 0.8rem;"></i>
                                                    <div>
                                                        <span class="d-block font-weight-500 text-dark">{{
                                                            permission.name }}</span>
                                                        <small class="text-muted d-block">{{ permission.description
                                                            }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 text-center align-middle">
                                                <div class="custom-control custom-switch custom-switch-danger">
                                                    <input type="checkbox" class="custom-control-input cursor-pointer"
                                                        :id="'perm-' + permission.id" :value="permission.name"
                                                        v-model="selectedPermissions">
                                                    <label class="custom-control-label cursor-pointer"
                                                        :for="'perm-' + permission.id"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="filteredPermissions.length === 0">
                                            <td colspan="2" class="text-center py-5 text-muted">
                                                <i class="fas fa-search-minus fa-2x mb-3 d-block opacity-25"></i>
                                                No se encontraron permisos.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-secondary px-4" @click="close" :disabled="saving">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger px-4" @click="savePermissions" :disabled="saving">
                        <span v-if="saving">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Guardando...
                        </span>
                        <span v-else>
                            <i class="fas fa-save mr-1"></i> Guardar Permisos
                        </span>
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

const props = defineProps({
    role: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['saved', 'close']);

const modalEl = ref(null);
let modalInstance = null;
const loading = ref(false);
const saving = ref(false);
const searchQuery = ref('');

const allPermissions = ref([]);
const selectedPermissions = ref([]);

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

const loadData = async () => {
    loading.value = true;
    try {
        const [allRes, roleRes] = await Promise.all([
            axios.get('/api/roles/permissions/all'),
            axios.get(`/api/roles/${props.role.id}/permissions`)
        ]);
        allPermissions.value = allRes.data;
        selectedPermissions.value = roleRes.data;
    } catch (error) {
        console.error('Error loading permissions:', error);
        Swal.fire('Error', 'No se pudieron cargar los permisos.', 'error');
    } finally {
        loading.value = false;
    }
};

const filteredPermissions = computed(() => {
    if (!searchQuery.value) return allPermissions.value;
    const query = searchQuery.value.toLowerCase();
    return allPermissions.value.filter(item =>
        item.name.toLowerCase().includes(query) ||
        (item.description && item.description.toLowerCase().includes(query))
    );
});

const open = () => {
    modalInstance.show();
};

const close = () => {
    modalInstance.hide();
    emit('close');
};

const toggleAll = (select) => {
    if (select) {
        selectedPermissions.value = allPermissions.value.map(p => p.name);
    } else {
        selectedPermissions.value = [];
    }
};

const savePermissions = async () => {
    saving.value = true;
    try {
        await axios.post(`/api/roles/${props.role.id}/permissions`, {
            permissions: selectedPermissions.value
        });
        Swal.fire({
            icon: 'success',
            title: '¡Guardado!',
            text: 'Permisos actualizados correctamente.',
            timer: 1500,
            showConfirmButton: false
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
.modal-content {
    border-radius: 8px;
}

.font-weight-500 {
    font-weight: 500;
}

.cursor-pointer {
    cursor: pointer;
}

.permission-row {
    transition: background 0.2s;
}

.permission-row:hover {
    background-color: rgba(220, 53, 69, 0.02);
}

/* Custom switch danger */
.custom-switch-danger .custom-control-input:checked~.custom-control-label::before {
    background-color: #dc3545;
    border-color: #dc3545;
}

.custom-switch .custom-control-label::before {
    height: 1.25rem;
    width: 2.25rem;
    border-radius: 1rem;
    cursor: pointer;
}

.custom-switch .custom-control-label::after {
    width: calc(1.25rem - 4px);
    height: calc(1.25rem - 4px);
    border-radius: 50%;
    cursor: pointer;
}

.custom-control-input:focus~.custom-control-label::before {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.table-responsive::-webkit-scrollbar {
    width: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #ddd;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #ccc;
}
</style>

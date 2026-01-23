<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link :to="{ name: 'home' }"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Gestión de Usuarios</li>
            </ol>
        </nav>
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Gestión de Usuarios</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-secondary btn-sm ml-1" @click="downloadTemplate">
                            <i class="fas fa-file-download"></i> Plantilla
                        </button>
                        <button class="btn btn-success btn-sm ml-1" @click="triggerFileUpload">
                            <i class="fas fa-file-excel"></i> Importar Excel
                        </button>
                        <button class="btn btn-info btn-sm ml-1" @click="syncSpecialists">
                            <i class="fas fa-sync"></i> Sincronizar Especialistas
                        </button>
                        <input type="file" ref="fileInput" class="d-none" accept=".xlsx, .xls, .csv"
                            @change="handleFileUpload">
                        <button class="btn btn-primary btn-sm ml-1" @click="openCreateUserModal">
                            <i class="fas fa-plus-circle"></i> Nuevo Usuario
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <form @submit.prevent="applyFilters">
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" name="search" id="search" class="form-control"
                                        placeholder="Buscar por Nombre o Email" v-model="store.filters.search">
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" v-model="store.filters.role" @change="applyFilters">
                                        <option value="">Todos los Roles</option>
                                        <option v-for="role in store.roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn bg-dark">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="h-1 mb-2">
                    <ProgressBar v-if="store.loading" mode="indeterminate" style="height: 4px;" />
                </div>
                <DataTable ref="dt" :value="store.users" :lazy="true" :paginator="true" :rows="20"
                    :class="{ 'opacity-50 pointer-events-none': store.loading }" :totalRecords="store.pagination.total"
                    :first="(store.pagination.currentPage - 1) * store.pagination.perPage" @page="onPage" @sort="onSort"
                    :sortField="store.sorting.field" :sortOrder="store.sorting.order"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" :globalFilterFields="['id', 'name', 'email']"
                    class="p-datatable-striped p-datatable-hoverable-rows">

                    <Column field="id" header="ID" sortable style="width:5%"></Column>
                    <Column field="name" header="Usuario" sortable style="width:30%">
                        <template #body="{ data }">
                            <div class="d-flex align-items-center">
                                <div v-if="data.user_foto_url"
                                    class="mr-3 shadow-sm border rounded-circle overflow-hidden"
                                    style="width: 35px; height: 35px;">
                                    <img :src="data.user_foto_url" alt="User Photo"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div v-else class="avatar-circle mr-3 shadow-sm text-white"
                                    :class="getAvatarColorClass(data.roles)">
                                    <i :class="getAvatarIcon(data.roles)"></i>
                                </div>
                                <div>
                                    <span class="font-weight-bold d-block text-dark">{{ data.name }}</span>
                                    <small class="text-muted">{{ data.email }}</small>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="user_cod_personal" header="Código" sortable style="width:10%">
                        <template #body="{ data }">
                            <span class="text-muted font-weight-bold">{{ data.user_cod_personal || 'N/A' }}</span>
                        </template>
                    </Column>
                    <Column field="user_iniciales" header="Iniciales" sortable style="width:10%" class="d-none">
                    </Column>
                    <Column header="Rol" style="width:20%">
                        <template #body="{ data }">
                            <div v-if="data.roles && data.roles.length > 0">
                                <span v-for="role in data.roles" :key="role"
                                    class="badge p-2 mb-1 mr-1 shadow-sm role-badge" :class="getRoleBadgeClass(role)">
                                    <i :class="getRoleIcon(role)" class="mr-1"></i> {{ role }}
                                </span>
                            </div>
                            <span v-else class="text-muted small font-italic">Sin roles</span>
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:15%">
                        <template #body="{ data }">
                            <!-- 1. Editar -->
                            <a href="#" title="Editar Usuario" class="mr-2 d-inline-block"
                                @click.prevent="editUser(data)">
                                <i class="fas fa-pencil-alt text-primary fa-lg"></i>
                            </a>
                            <!-- 2. Asignar Rol -->
                            <a href="#" title="Asignar Roles" class="mr-2 d-inline-block"
                                @click.prevent="assignRoles(data)">
                                <i class="fas fa-user-tag text-info fa-lg"></i>
                            </a>
                            <!-- 3. Asignar Permisos -->
                            <a href="#" title="Gestionar Permisos" class="mr-2 d-inline-block"
                                @click.prevent="openPermissionsModal(data)">
                                <i class="fas fa-shield-alt text-warning fa-lg"></i>
                            </a>
                            <!-- 4. Contraseña -->
                            <a href="#" title="Restablecer Contraseña" class="mr-2 d-inline-block"
                                @click.prevent="resetPassword(data)">
                                <i class="fas fa-key text-dark fa-lg"></i>
                            </a>
                            <!-- 5. Eliminar -->
                            <a href="#" title="Eliminar Usuario" class="mr-2 d-inline-block"
                                @click.prevent="deleteUser(data)">
                                <i class="fas fa-trash-alt text-danger fa-lg"></i>
                            </a>
                        </template>
                    </Column>
                    <template #empty>
                        No se encontraron usuarios.
                    </template>
                </DataTable>
            </div>
        </div>
    </div>

    <!-- Modals for Roles, Permissions, Create/Edit User -->
    <teleport to="body">
        <!-- RolModal -->
        <RolModal ref="rolModal" :user="currentUser" @roles-updated="onRolesUpdated" @close="onModalClosed" />

        <!-- PermisosUsuarioModal -->
        <PermisosUsuarioModal v-if="showPermissionsModal" :user="selectedUser" @saved="onPermissionsSaved"
            @close="showPermissionsModal = false" />

        <!-- Create/Edit User Modal -->
        <UsuariosForm ref="usuariosForm" :user="currentUser" @saved="onUserSaved" @close="closeUserModal" />
    </teleport>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from '@/stores/userStore';
import { useToast } from 'primevue/usetoast';
import Swal from 'sweetalert2';
import axios from 'axios';
import { route } from 'ziggy-js';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ProgressBar from 'primevue/progressbar';

// Custom Components
import RolModal from './RolModal.vue';
import PermisosUsuarioModal from './PermisosUsuarioModal.vue';
import UsuariosForm from './UsuariosForm.vue';

const router = useRouter();
const store = useUserStore();
const toast = useToast();

const dt = ref(null);

const rolModal = ref(null);
const currentUser = ref(null);

const usuariosForm = ref(null);
const fileInput = ref(null);

const selectedUser = ref(null); // Used for modals that use v-if
const showPermissionsModal = ref(false); // State for PermisosUsuarioModal

onMounted(async () => {
    try {
        await Promise.all([
            store.fetchUsers(),
            store.fetchRoles()
        ]);
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la lista de usuarios', life: 3000 });
    }
});

// Helper to determine primary role (first one or null)
const getPrimaryRole = (roles) => {
    return (roles && roles.length > 0) ? roles[0] : null;
};

const getAvatarColorClass = (roles) => {
    const role = getPrimaryRole(roles);
    if (!role) return 'bg-secondary';

    // Reuse specific badge colors for avatar background
    const roleLower = role.toLowerCase();
    if (roleLower.includes('admin')) return 'bg-danger';
    if (roleLower.includes('auditor')) return 'bg-success';
    if (roleLower.includes('especialista')) return 'bg-info';
    if (roleLower.includes('propietario')) return 'bg-primary';
    if (roleLower.includes('facilitador')) return 'bg-warning text-dark';
    return 'bg-secondary';
};

const getAvatarIcon = (roles) => {
    const role = getPrimaryRole(roles);
    return getRoleIcon(role || '');
};



const getUserColorClass = (id) => {
    // Deprecated in favor of role colors, but kept if needed for fallback
    const colors = ['bg-primary', 'bg-secondary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-dark'];
    return colors[id % colors.length] + ' text-white';
};

const getRoleBadgeClass = (role) => {
    const roleLower = role.toLowerCase();
    if (roleLower.includes('admin')) return 'badge-danger';
    if (roleLower.includes('auditor')) return 'badge-success';
    if (roleLower.includes('especialista')) return 'badge-info';
    if (roleLower.includes('propietario')) return 'badge-primary';
    if (roleLower.includes('facilitador')) return 'badge-warning text-dark';
    return 'badge-secondary';
};

const getRoleIcon = (role) => {
    if (!role) return 'fas fa-user'; // Default user icon
    const roleLower = role.toLowerCase();
    if (roleLower.includes('admin')) return 'fas fa-user-shield';
    if (roleLower.includes('auditor')) return 'fas fa-clipboard-check';
    if (roleLower.includes('especialista')) return 'fas fa-user-cog';
    if (roleLower.includes('propietario')) return 'fas fa-user-tie';
    if (roleLower.includes('facilitador')) return 'fas fa-chalkboard-teacher';
    return 'fas fa-user-tag';
};

const onPage = (event) => {
    if (event.rows !== store.pagination.perPage) {
        store.setPerPage(event.rows);
    } else {
        store.setPage(event.page + 1);
    }
};

const onSort = (event) => {
    store.setSort(event.sortField, event.sortOrder);
};

const applyFilters = () => {
    store.pagination.currentPage = 1;
    store.fetchUsers();
};

const resetFilters = () => {
    store.resetFilters();
    store.fetchUsers();
};

const openCreateUserModal = () => {
    currentUser.value = null;
    usuariosForm.value.open();
};

const closeUserModal = () => {
    currentUser.value = null;
};

const onUserSaved = () => {
    store.fetchUsers();
    Swal.fire('Guardado', 'La operación se realizó con éxito.', 'success');
};

const editUser = (user) => {
    currentUser.value = user;
    usuariosForm.value.open();
};

const deleteUser = (user) => {
    Swal.fire({
        title: '¿Eliminar Usuario?',
        text: `Se eliminará a ${user.name} del sistema.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await store.deleteUser(user.id);
                Swal.fire('Eliminado', 'El usuario ha sido eliminado correctamente.', 'success');
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudo eliminar el usuario.', 'error');
            }
        }
    });
};

const resetPassword = (user) => {
    Swal.fire({
        title: '¿Restablecer Contraseña?',
        text: `La contraseña de ${user.name} se restablecerá a su Código Personal (${user.user_cod_personal || 'No registrado'}). El usuario deberá cambiarla en su próximo ingreso.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, restablecer',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            if (!user.user_cod_personal) {
                Swal.fire('Error', 'El usuario no tiene un código personal registrado.', 'error');
                return;
            }
            try {
                await store.resetPassword(user.email);
                Swal.fire('Restablecida', 'La contraseña ha sido reseteada al código personal.', 'success');
            } catch (error) {
                console.error('Error resetting password:', error);
                const msg = error.response?.data?.message || 'Error al restablecer la contraseña.';
                Swal.fire('Error', msg, 'error');
            }
        }
    });
};

const triggerFileUpload = () => {
    fileInput.value.click();
};

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // SweetAlert2 doesn't support async pre-confirm nicely with file inputs inside standard flow easily in one go
    // But we can just use normal confirmation dialog logic

    Swal.fire({
        title: '¿Importar Usuarios?',
        text: 'Se importarán los usuarios desde el archivo seleccionado.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Sí, importar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('file', file);

            try {
                await store.importUsers(formData);
                Swal.fire('Importado', 'Usuarios importados correctamente.', 'success');
            } catch (error) {
                console.error('Error importing:', error);
                Swal.fire('Error', 'Error al importar usuarios.', 'error');
            } finally {
                event.target.value = null;
            }
        } else {
            event.target.value = null;
        }
    });
};

const downloadTemplate = () => {
    store.downloadTemplate();
};

const syncSpecialists = () => {
    Swal.fire({
        title: '¿Sincronizar Especialistas?',
        text: 'Se actualizará la lista de especialistas basada en los roles de usuario.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, sincronizar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                // Assuming we can use axios directly or add a method to the store. 
                // Direct axios call for simplicity based on current context imports.
                const response = await axios.post(route('api.admin.usuarios.sync-specialists'));
                Swal.fire('Sincronizado', response.data.message, 'success');
            } catch (error) {
                console.error('Error syncing specialists:', error);
                Swal.fire('Error', 'No se pudo sincronizar la lista de especialistas.', 'error');
            }
        }
    });
};

const assignRoles = (user) => {
    currentUser.value = user;
    rolModal.value.open();
};

// Handlers for Modal Events
const onRolesUpdated = () => {
    store.fetchUsers();
};

const onModalClosed = () => {
    currentUser.value = null;
    // Do NOT call modal.close() here as it causes infinite recursion if triggered by the modal's close event
};

const openPermissionsModal = (user) => {
    selectedUser.value = user;
    showPermissionsModal.value = true;
};

const onPermissionsSaved = () => {
    store.fetchUsers();
};
</script>

<style scoped>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

.role-badge {
    font-size: 0.8rem;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
}



:deep(.p-datatable .p-datatable-tbody > tr > td),
:deep(.p-datatable .p-datatable-thead > tr > th) {
    padding: 0.8rem !important;
    vertical-align: middle !important;
}
</style>

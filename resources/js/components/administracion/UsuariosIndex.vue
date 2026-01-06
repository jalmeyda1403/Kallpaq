<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link :to="{ name: 'documentos.index' }">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Gestión de Usuarios</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">Gestión de Usuarios</h3>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <button class="btn btn-success" @click="openCreateUserModal">
                                    <i class="fas fa-plus"></i> Nuevo Usuario
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
                        <DataTable ref="dt" :value="store.users" :lazy="true" :paginator="true" :rows="20"
                            :totalRecords="store.pagination.total"
                            :first="(store.pagination.currentPage - 1) * store.pagination.perPage" @page="onPage"
                            :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id"
                            :globalFilterFields="['id', 'name', 'email']"
                            :loading="store.loading">
                            <Column field="id" header="ID" style="width:5%"></Column>
                            <Column field="name" header="Nombre" sortable style="width:30%"></Column>
                            <Column field="email" header="Email" sortable style="width:30%"></Column>
                            <Column header="Acciones" :exportable="false" style="width:10%">
                                <template #body="{ data }">
                                    <a href="#" title="Asignar Roles"
                                        class="mr-1 d-inline-block btn-modal-trigger p-2"
                                        @click.prevent="assignRoles(data)">
                                        <i class="fas fa-user-tag fa-lg text-info"></i>
                                    </a>
                                    <a href="#" title="Asignar Permisos"
                                        class="mr-1 d-inline-block btn-modal-trigger p-2"
                                        @click.prevent="assignPermissions(data)">
                                        <i class="fas fa-shield-alt fa-lg text-warning"></i>
                                    </a>
                                    <a href="#" title="Editar Usuario"
                                        class="mr-1 d-inline-block btn-modal-trigger p-2"
                                        @click.prevent="editUser(data)">
                                        <i class="fas fa-edit fa-lg text-primary"></i>
                                    </a>
                                    <a href="#" title="Eliminar Usuario"
                                        class="mr-1 d-inline-block btn-modal-trigger p-2"
                                        @click.prevent="deleteUser(data)">
                                        <i class="fas fa-trash fa-lg text-danger"></i>
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
        </div>
    </div>

    <!-- Modals for Roles, Permissions, Create/Edit User -->
    <teleport to="body">
        <!-- RolModal -->
        <RolModal ref="rolModal" :user="currentUser" @roles-updated="closeRolModal" @close="closeRolModal" />

        <!-- PermisosModal -->
        <PermisosModal ref="permisosModal" :user="currentUser" @permissions-updated="closePermisosModal" @close="closePermisosModal" />

        <!-- Create/Edit User Modal -->
        <div v-if="showCreateEditUserModal" class="modal fade show d-block" tabindex="-1" role="dialog"
            aria-labelledby="createEditUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="createEditUserModalLabel">{{ isEditingUser ? 'Editar' : 'Crear' }} Usuario</h5>
                        <button type="button" class="close" @click="closeCreateEditUserModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- CreateEditUserModal Component will go here -->
                        <p>Contenido del modal de creación/edición de usuario</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeCreateEditUserModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showCreateEditUserModal" class="modal-backdrop fade show"></div>
    </teleport>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from '@/stores/userStore'; // Placeholder for new user store

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// Custom Components (will be created later)
import RolModal from './RolModal.vue';
import PermisosModal from './PermisosModal.vue';
// import CreateEditUserModal from './CreateEditUserModal.vue';

const router = useRouter();
const store = useUserStore(); // Placeholder for new user store

const dt = ref(null); // Reference to the DataTable component

const rolModal = ref(null); // Ref for RolModal
const permisosModal = ref(null); // Ref for PermisosModal
const showCreateEditUserModal = ref(false);
const isEditingUser = ref(false);
const currentUser = ref(null); // To store the user being edited/assigned roles/permissions

onMounted(() => {
    store.fetchUsers();
});

const onPage = (event) => {
    store.setPage(event.page + 1);
    store.setPerPage(event.rows);
};

const applyFilters = () => {
    store.pagination.currentPage = 1; // Reset to first page on filter application
    store.fetchUsers();
};

const resetFilters = () => {
    store.resetFilters();
    store.fetchUsers();
};

const openCreateUserModal = () => {
    isEditingUser.value = false;
    currentUser.value = null;
    showCreateEditUserModal.value = true;
};

const closeCreateEditUserModal = () => {
    showCreateEditUserModal.value = false;
    currentUser.value = null;
    store.fetchUsers(); // Refresh list after create/edit
};

const editUser = (user) => {
    isEditingUser.value = true;
    currentUser.value = user;
    showCreateEditUserModal.value = true;
};

const deleteUser = (user) => {
    if (confirm(`¿Está seguro de que desea eliminar al usuario ${user.name}?`)) {
        console.log('Deleting user:', user.id);
        // Implement actual delete logic here
        // store.deleteUser(user.id);
        store.fetchUsers(); // Refresh list after delete
    }
};

const assignRoles = (user) => {
    currentUser.value = user;
    rolModal.value.open(); // Open RolModal
};

const closeRolModal = () => {
    currentUser.value = null;
    rolModal.value.close(); // Close RolModal
    store.fetchUsers(); // Refresh list after role assignment
};

const assignPermissions = (user) => {
    currentUser.value = user;
    permisosModal.value.open(); // Open PermisosModal
};

const closePermisosModal = () => {
    currentUser.value = null;
    permisosModal.value.close(); // Close PermisosModal
    store.fetchUsers(); // Refresh list after permission assignment
};
</script>

<style scoped>
/* Estilos específicos del componente aquí */

/* Custom loader styles - remove opacity and change color to red */
/* Remove the semi-transparent overlay that dims the table content during loading */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0) !important;
    /* Make background completely transparent */
}

/* Change the loader icon to red */
.p-datatable-loading-icon {
    color: red !important;
    font-size: 2rem !important;
}
</style>

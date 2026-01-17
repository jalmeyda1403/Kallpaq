<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link :to="{ name: 'documentos.index' }">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Gestión de Roles</li>
            </ol>
        </nav>
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Gestión de Roles</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-primary btn-sm ml-1" @click="openCreateRoleModal">
                            <i class="fas fa-plus-circle"></i> Nuevo Rol
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
                                        placeholder="Buscar por Nombre" v-model="filters.search">
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
                <DataTable ref="dt" :value="roles" :lazy="true" :paginator="true" :rows="10"
                    :totalRecords="totalRecords" :loading="loading" @page="onPage" @sort="onSort"
                    :rowsPerPageOptions="[5, 10, 20, 50]" class="p-datatable-striped p-datatable-hoverable-rows">

                    <Column field="id" header="ID" sortable style="width:7%"></Column>
                    <Column field="name" header="Nombre del Rol" sortable style="width:20%">
                        <template #body="{ data }">
                            <div class="d-flex align-items-center">
                                <div class="role-icon-circle mr-3 shadow-sm text-white"
                                    :class="getRoleColorClass(data.name)">
                                    <i :class="getRoleIcon(data.name)"></i>
                                </div>
                                <span class="font-weight-bold text-dark">{{ data.name }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column field="description" header="Descripción" sortable style="width:48%">
                        <template #body="{ data }">
                            <span class="text-secondary" style="font-size: 0.75rem;">{{ data.description ||
                                'Sin descripción' }}</span>
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:15%">
                        <template #body="{ data }">
                            <div class="d-flex align-items-center">
                                <a href="#" title="Editar Rol" class="mr-3 btn-action text-decoration-none"
                                    @click.prevent="editRole(data)">
                                    <i class="fas fa-pencil-alt text-primary fa-lg"></i>
                                </a>
                                <a href="#" title="Gestionar Permisos" class="mr-3 btn-action text-decoration-none"
                                    @click.prevent="managePermissions(data)">
                                    <i class="fas fa-shield-alt text-success fa-lg"></i>
                                </a>
                                <a href="#" title="Eliminar Rol" class="btn-action text-decoration-none"
                                    @click.prevent="deleteRole(data)">
                                    <i class="fas fa-trash-alt text-danger fa-lg"></i>
                                </a>
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        No se encontraron roles.
                    </template>
                </DataTable>
            </div>
        </div>
    </div>

    <!-- Create/Edit Role Modal -->
    <teleport to="body">
        <RolesForm ref="rolesForm" :role="currentRole" @saved="onRoleSaved" @close="closeRoleModal" />
        <PermisosRolModal ref="permisosModal" :role="currentRole" @saved="fetchRoles" @close="closeRoleModal" />
    </teleport>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import RolesForm from './RolesForm.vue';
import PermisosRolModal from './PermisosRolModal.vue';
import Swal from 'sweetalert2';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const roles = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const currentRole = ref(null);
const rolesForm = ref(null);
const permisosModal = ref(null);

const pagination = reactive({
    page: 1,
    perPage: 10
});

const filters = reactive({
    search: ''
});

const sorting = reactive({
    field: 'id',
    order: 1 // 1 for asc, -1 for desc (PrimeVue order)
});

const fetchRoles = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/roles', {
            params: {
                page: pagination.page,
                per_page: pagination.perPage,
                search: filters.search,
                sort_field: sorting.field,
                sort_order: sorting.order === 1 ? 'asc' : 'desc'
            }
        });
        roles.value = response.data.data;
        totalRecords.value = response.data.total;
    } catch (error) {
        console.error('Error fetching roles:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la lista de roles', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const getRoleColorClass = (role) => {
    if (!role) return 'bg-secondary';
    const roleLower = role.toLowerCase();
    if (roleLower.includes('admin')) return 'bg-danger';
    if (roleLower.includes('auditor')) return 'bg-success';
    if (roleLower.includes('especialista')) return 'bg-info';
    if (roleLower.includes('propietario')) return 'bg-primary';
    if (roleLower.includes('facilitador')) return 'bg-warning text-dark';
    return 'bg-secondary';
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

onMounted(() => {
    fetchRoles();
});

const onPage = (event) => {
    pagination.page = event.page + 1;
    pagination.perPage = event.rows;
    fetchRoles();
};

const onSort = (event) => {
    sorting.field = event.sortField;
    sorting.order = event.sortOrder;
    fetchRoles();
};

const applyFilters = () => {
    pagination.page = 1;
    fetchRoles();
};

const openCreateRoleModal = () => {
    currentRole.value = null;
    rolesForm.value.open();
};

const editRole = (role) => {
    currentRole.value = role;
    rolesForm.value.open();
};

const managePermissions = (role) => {
    currentRole.value = role;
    permisosModal.value.open();
};

const onRoleSaved = () => {
    fetchRoles();
    Swal.fire('Guardado', 'La operación se realizó con éxito.', 'success');
};

const deleteRole = (role) => {
    Swal.fire({
        title: '¿Eliminar Rol?',
        text: `Se eliminará el rol ${role.name} del sistema.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/roles/${role.id}`);
                fetchRoles();
                Swal.fire('Eliminado', 'El rol ha sido eliminado correctamente.', 'success');
            } catch (error) {
                console.error('Error deleting role:', error);
                Swal.fire('Error', 'No se pudo eliminar el rol.', 'error');
            }
        }
    });
};

const closeRoleModal = () => {
    currentRole.value = null;
};
</script>

<style scoped>
.role-icon-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.btn-action {
    transition: transform 0.2s ease;
}

.btn-action:hover {
    transform: scale(1.2);
}

/* Custom loader styles - remove opacity and change color to red */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0.1) !important;
}

.p-datatable-loading-icon {
    color: #dc3545 !important;
    font-size: 2.5rem !important;
}

:deep(.p-datatable .p-datatable-tbody > tr > td),
:deep(.p-datatable .p-datatable-thead > tr > th) {
    padding: 0.9rem 0.8rem !important;
    vertical-align: middle !important;
}

:deep(.p-datatable.p-datatable-striped .p-datatable-tbody > tr:nth-child(even)) {
    background-color: #fafafa !important;
}
</style>

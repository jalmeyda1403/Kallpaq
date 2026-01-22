<template>
    <div>
        <div class="d-flex align-items-center justify-content-between my-4">
            <div class="d-flex align-items-center flex-grow-1">
                <div class="input-group mr-3" style="max-width: 400px;">
                    <input type="text" class="form-control" placeholder="Buscar usuario para asignar..."
                        v-model="selectedUser.name" readonly @click="openUserModal" />
                    <div class="input-group-append">
                        <button type="button" class="btn btn-dark shadow-sm" @click="openUserModal"
                            title="Buscar Usuario">
                            <i class="fas fa-search"></i> Seleccionar
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-info shadow-sm" @click="toggleShowDeleted">
                    <i class="fas fa-history"></i> {{ showDeleted ? 'Ver Activos' : 'Ver Eliminados' }}
                </button>
            </div>

            <button type="button" class="btn btn-success shadow px-4 font-weight-bold"
                :disabled="saving || assignedUsers.length === 0" @click="saveAllChanges">
                <i class="fas" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i> Guardar Cambios
            </button>
        </div>

        <!-- PrimeVue DataTable for Assigned Users -->
        <DataTable :value="assignedUsers" dataKey="id" responsiveLayout="scroll"
            class="p-datatable-sm shadow-sm rounded border" :loading="loading"
            :rowClass="data => data.deleted_at ? 'table-danger' : (data.isNew ? 'table-success' : '')">
            <template #empty>
                <div class="text-center p-4">
                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay usuarios asignados a esta unidad orgánica.</p>
                </div>
            </template>
            <template #loading>
                <div class="d-flex justify-content-center align-items-center p-5">
                    <div class="spinner-grow text-danger" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </template>
            <Column field="id" header="ID" style="width:5%"></Column>
            <Column field="name" header="Nombre" style="width:30%">
                <template #body="{ data }">
                    <div class="font-weight-bold">{{ data.name }}</div>
                    <small class="text-muted">{{ data.email }}</small>
                </template>
            </Column>
            <Column header="Rol en OUO" style="width:25%; text-align: center;" headerStyle="text-align: center;">
                <template #body="{ data }">
                    <select class="form-control form-control-sm shadow-sm border-0" v-model="data.role_in_ouo"
                        :disabled="!!data.deleted_at">
                        <option v-for="role in ouoRoles" :key="role.value" :value="role.value">{{ role.label }}</option>
                    </select>
                </template>
            </Column>
            <Column header="Activo" style="width:15%; text-align: center;" headerStyle="text-align: center;">
                <template #body="{ data }">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" :id="`activo-${data.id}`"
                            v-model="data.activo" :disabled="!!data.deleted_at">
                        <label class="custom-control-label" :for="`activo-${data.id}`"></label>
                    </div>
                </template>
            </Column>
            <Column header="Acciones" style="width:15%; text-align: center;" headerStyle="text-align: center;">
                <template #body="{ data, index }">
                    <button v-if="!data.deleted_at" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm"
                        @click="removeUserLocal(index)" title="Quitar">
                        <i class="fas fa-times"></i>
                    </button>
                    <button v-else class="btn btn-success btn-sm px-3 shadow-sm" @click="restoreUser(data)">
                        <i class="fas fa-undo"></i> Restaurar
                    </button>
                </template>
            </Column>
        </DataTable>

        <ModalHijo ref="userModal" :fetch-url="user_route" target-id="id" target-desc="name"
            @update-target="handleUserSelection" />
    </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import { route } from 'ziggy-js';
import ModalHijo from '../generales/ModalHijo.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
// import BlockUI from 'primevue/blockui'; // Removed
// import ProgressSpinner from 'primevue/progressspinner'; // Removed
import Dropdown from 'primevue/dropdown'; // Import Dropdown


export default {
    name: 'Usuarioslist',
    props: {
        ouo: {
            type: Object,
            required: true
        }
    },
    components: {
        ModalHijo,
        DataTable,
        Column,
        // BlockUI, // Removed
        // ProgressSpinner, // Removed
        Dropdown // Register Dropdown
    },
    setup(props, { emit }) {
        const userModal = ref(null);
        const selectedUser = ref({ id: null, name: '', email: '' });
        const assignedUsers = reactive([]);
        const loading = ref(true);
        const saving = ref(false);

        const user_route = route('users.gestores'); // Route to fetch gestor users
        const showDeleted = ref(false);

        const ouoRoles = ref([
            { label: 'Titular', value: 'titular' },
            { label: 'Encargado', value: 'encargado' },
            { label: 'Facilitador', value: 'facilitador' },
            { label: 'Colaborador', value: 'colaborador' },
        ]);

        const fetchAssignedUsers = async () => {
            if (!props.ouo || !props.ouo.id) {
                assignedUsers.splice(0);
                return;
            }
            loading.value = true;
            try {
                const url = showDeleted.value
                    ? route('ouos.users.deleted', props.ouo.id)
                    : route('ouos.users.index', props.ouo.id);

                const response = await axios.get(url);
                assignedUsers.splice(0);
                response.data.forEach(u => {
                    assignedUsers.push({
                        id: u.id,
                        name: u.name,
                        email: u.email,
                        role_in_ouo: u.pivot.role_in_ouo,
                        activo: !!u.pivot.activo,
                        deleted_at: u.pivot.deleted_at,
                        isNew: false
                    });
                });
            } catch (error) {
                console.error('Error fetching assigned users:', error);
                Swal.fire('Error', 'Error al cargar los usuarios.', 'error');
            } finally {
                loading.value = false;
            }
        };

        onMounted(() => {
            fetchAssignedUsers();
        });

        watch(() => props.ouo, (newOuo) => {
            if (newOuo) fetchAssignedUsers();
        });

        const openUserModal = () => {
            userModal.value.open();
        };

        const handleUserSelection = (data) => {
            const userId = data.idValue;
            const isDuplicate = assignedUsers.some(u => u.id === userId);

            if (isDuplicate) {
                Swal.fire('Aviso', 'Este usuario ya está en la lista.', 'info');
                return;
            }

            assignedUsers.unshift({
                id: userId,
                name: data.descValue,
                email: data.email || '',
                role_in_ouo: 'colaborador',
                activo: true,
                deleted_at: null,
                isNew: true
            });

            selectedUser.value = { id: null, name: '', email: '' }; // Reset search display
        };

        const removeUserLocal = (index) => {
            assignedUsers.splice(index, 1);
        };

        const saveAllChanges = async () => {
            saving.value = true;
            try {
                // Filter out soft-deleted users from being "synced" if they are currently viewed? 
                // Actually sync will handle deletions if they are missing from the array.
                // If showDeleted is true, we should probably warn or filter.

                const payload = assignedUsers
                    .filter(u => !u.deleted_at) // Only sync active mappings
                    .map(u => ({
                        id: u.id,
                        role_in_ouo: u.role_in_ouo,
                        activo: u.activo
                    }));

                await axios.post(route('ouos.users.sync', props.ouo.id), {
                    users: payload
                });

                Swal.fire('Éxito', 'Todos los cambios han sido guardados.', 'success');
                await fetchAssignedUsers();
                emit('users-updated');
            } catch (error) {
                console.error('Error saving all changes:', error);
                Swal.fire('Error', 'No se pudieron guardar los cambios masivos.', 'error');
            } finally {
                saving.value = false;
            }
        };

        const restoreUser = async (userToRestore) => {
            try {
                await axios.post(route('ouos.users.attach', props.ouo.id), {
                    user_id: userToRestore.id,
                    role_in_ouo: userToRestore.role_in_ouo,
                    activo: true,
                });
                Swal.fire('Restaurado', 'Enlace restaurado.', 'success');
                await fetchAssignedUsers();
                emit('users-updated');
            } catch (error) {
                Swal.fire('Error', 'No se pudo restaurar.', 'error');
            }
        };

        const toggleShowDeleted = () => {
            showDeleted.value = !showDeleted.value;
            fetchAssignedUsers();
        };

        return {
            userModal,
            selectedUser,
            assignedUsers,
            loading,
            saving,
            user_route,
            ouoRoles,
            openUserModal,
            handleUserSelection,
            removeUserLocal,
            saveAllChanges,
            restoreUser,
            showDeleted,
            toggleShowDeleted,
        };
    },
};
</script>

<style scoped>
/* Add any specific styles for this component here */
.p-datatable .p-datatable-tbody>tr>td {
    padding: 1.5rem 1rem;
    /* Further increased vertical padding */
}

/* Ensure form-check-inline elements have some margin for better spacing */
.form-check-inline {
    margin-right: 1rem;
    /* Increased margin */
    margin-bottom: 0.5rem;
    /* Add some vertical margin for wrapping */
}

/* Adjust button margins for better spacing */
.btn-sm {
    margin-left: 0.25rem;
    margin-right: 0.25rem;
}
</style>
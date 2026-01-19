<template>
    <div>
        <div class="d-flex align-items-center my-4">
            <div class="input-group mr-3">
                <input type="hidden" v-model="selectedUser.id" />

                <input type="text" class="form-control" placeholder="Seleccione el Usuario a Asignar"
                    v-model="selectedUser.name" readonly />
                <div class="input-group-append">
                    <button type="button" class="btn btn-dark" @click="openUserModal">
                        <i class="fas fa-search"></i>
                    </button>
                    <button type="button" class="btn btn-danger" :disabled="!selectedUser.id" @click="addUser">
                        <i class="fas fa-link"></i> Añadir
                    </button>
                    <button type="button" class="btn btn-info" @click="toggleShowDeleted">
                        <i class="fas fa-history"></i> {{ showDeleted ? 'Activos' : 'Eliminados' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- PrimeVue DataTable for Assigned Users -->
        <DataTable :value="assignedUsers" dataKey="id" responsiveLayout="scroll" class="p-datatable-sm"
            :loading="loading" :rowClass="data => data.deleted_at ? 'table-danger' : ''">
            <template #empty>
                No hay usuarios asignados.
            </template>
            <template #loading>
                <div class="d-flex justify-content-center align-items-center p-4">
                    <div class="spinner-border text-danger" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </template>
            <Column field="id" header="ID" style="width:5%"></Column>
            <Column field="name" header="Nombre" style="width:20%"></Column>
            <Column field="email" header="Email" style="width:25%"></Column>
            <Column header="Rol en OUO" style="width:20%; text-align: center;" headerStyle="text-align: center;">
                <template #body="{ data }">
                    <select class="form-control form-control-sm" v-model="data.role_in_ouo"
                        :disabled="!!data.deleted_at">
                        <option v-for="role in ouoRoles" :key="role.value" :value="role.value">{{ role.label }}</option>
                    </select>
                </template>
            </Column>
            <Column header="Activo" style="width:10%; text-align: center;" headerStyle="text-align: center;">
                <template #body="{ data }">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" :id="`activo-${data.id}`" v-model="data.activo"
                            :disabled="!!data.deleted_at">
                        <label class="form-check-label" :for="`activo-${data.id}`"></label>
                    </div>
                </template>
            </Column>
            <Column header="Acciones" style="width:20%; text-align: center;" headerStyle="text-align: center;">
                <template #body="{ data, index }">
                    <button v-if="!data.deleted_at" class="btn btn-danger btn-sm ml-1" @click="removeUser(data)">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button v-else class="btn btn-success btn-sm ml-1" @click="restoreUser(data)">
                        <i class="fas fa-undo"></i> Restaurar
                    </button>
                    <button type="button" class="btn btn-dark btn-sm ml-1" @click="updateUserRow(data)"
                        :disabled="!!data.deleted_at">
                        <i class="fas fa-save"></i>
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

        const user_route = route('users.gestores'); // Route to fetch gestor users
        const showDeleted = ref(false); // New reactive variable to control display of deleted users

        const ouoRoles = ref([
            { label: 'Titular', value: 'titular' },
            { label: 'Encargado', value: 'encargado' },
            { label: 'Facilitador', value: 'facilitador' },
            { label: 'Colaborador', value: 'colaborador' },
        ]);

        // Fetch assigned users when component mounts or ouo prop changes
        const fetchAssignedUsers = async () => {
            if (!props.ouo || !props.ouo.id) {
                assignedUsers.splice(0); // Clear array if no OUO is selected
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
                        activo: !!u.pivot.activo, // Convert to boolean
                        deleted_at: u.pivot.deleted_at, // Include deleted_at
                    });
                });
            } catch (error) {
                console.error('Error fetching assigned users:', error);
                const msg = error.response?.data?.message || 'Verifique la consola para más detalles.';
                Swal.fire('Error', 'Error al cargar los usuarios asignados: ' + msg, 'error');
            } finally {
                loading.value = false;
            }
        };

        onMounted(() => {
            fetchAssignedUsers();
        });

        watch(() => props.ouo, (newOuo, oldOuo) => {
            if (newOuo && newOuo.id !== (oldOuo ? oldOuo.id : null)) {
                fetchAssignedUsers();
            }
        }, { deep: true });


        const openUserModal = () => {
            userModal.value.open();
        };

        const handleUserSelection = (data) => {
            selectedUser.value = { id: data.idValue, name: data.descValue, email: data.email }; // Assuming email is also returned by ModalHijo
        };

        const addUser = async () => {
            if (selectedUser.value.id) {
                const isDuplicate = assignedUsers.some(
                    (u) => u.id === selectedUser.value.id
                );

                if (!isDuplicate) {
                    const newUser = {
                        id: selectedUser.value.id,
                        name: selectedUser.value.name,
                        email: selectedUser.value.email,
                        role_in_ouo: 'colaborador', // Default role
                        activo: true, // Default to active
                    };

                    try {
                        if (!props.ouo || !props.ouo.id) {
                            Swal.fire('Atención', 'No hay OUO seleccionada para añadir el usuario.', 'warning');
                            return;
                        }

                        await axios.post(route('ouos.users.attach', props.ouo.id), {
                            user_id: newUser.id,
                            role_in_ouo: newUser.role_in_ouo,
                            activo: newUser.activo,
                        });

                        Swal.fire('Guardado', 'Usuario añadido y guardado con éxito.', 'success');
                        selectedUser.value = { id: null, name: '', email: '' };
                        await fetchAssignedUsers(); // Refresh the list from DB
                        emit('users-updated'); // Notify parent to update counts
                    } catch (error) {
                        console.error('Error adding and saving user:', error);
                        const msg = error.response?.data?.message || 'Verifique la consola para más detalles.';
                        Swal.fire('Error', 'Error al añadir y guardar el usuario: ' + msg, 'error');
                    }
                } else {
                    Swal.fire('Atención', 'Este usuario ya ha sido añadido.', 'warning');
                }
            }
        };

        const removeUser = async (userToRemove) => {
            Swal.fire({
                title: '¿Desvincular Usuario?',
                text: `Se desvinculará a ${userToRemove.name} de esta OUO.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, desvincular',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        if (!props.ouo || !props.ouo.id || !userToRemove || !userToRemove.id) {
                            Swal.fire('Error', 'No hay OUO o usuario seleccionado para eliminar.', 'error');
                            return;
                        }

                        await axios.delete(route('ouos.users.detach', { ouo: props.ouo.id, user: userToRemove.id }));

                        Swal.fire('Desvinculado', 'Usuario eliminado de la asignación con éxito.', 'success');
                        await fetchAssignedUsers(); // Refresh the list from DB
                        emit('users-updated'); // Notify parent to update counts
                    } catch (error) {
                        console.error('Error removing user:', error);
                        Swal.fire('Error', 'Error al eliminar el usuario de la asignación.', 'error');
                    }
                }
            });
        };

        const updateUserRow = async (userToUpdate) => {
            if (!props.ouo || !props.ouo.id || !userToUpdate || !userToUpdate.id) {
                Swal.fire('Error', 'No hay OUO o usuario seleccionado para guardar los cambios.', 'error');
                return;
            }

            try {
                await axios.put(route('ouos.users.updatePivot', { ouo: props.ouo.id, user: userToUpdate.id }), {
                    role_in_ouo: userToUpdate.role_in_ouo,
                    activo: userToUpdate.activo,
                });

                Swal.fire('Actualizado', 'Cambios del usuario guardados con éxito.', 'success');
            } catch (error) {
                console.error('Error saving user row:', error);
                Swal.fire('Error', 'Error al guardar los cambios del usuario.', 'error');
            }
        };

        const restoreUser = async (userToRestore) => {
            Swal.fire({
                title: '¿Restaurar Usuario?',
                text: `Se restaurará la asignación de ${userToRestore.name}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        if (!props.ouo || !props.ouo.id || !userToRestore || !userToRestore.id) {
                            Swal.fire('Error', 'No hay OUO o usuario seleccionado para restaurar.', 'error');
                            return;
                        }

                        // Reuse attachUser endpoint for restoration
                        await axios.post(route('ouos.users.attach', props.ouo.id), {
                            user_id: userToRestore.id,
                            role_in_ouo: userToRestore.role_in_ouo,
                            activo: true, // Restore as active
                        });

                        Swal.fire('Restaurado', 'Usuario restaurado a la asignación con éxito.', 'success');
                        await fetchAssignedUsers(); // Refresh the list from DB
                        emit('users-updated'); // Notify parent to update counts
                    } catch (error) {
                        console.error('Error restoring user:', error);
                        Swal.fire('Error', 'Error al restaurar el usuario a la asignación.', 'error');
                    }
                }
            });
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
            user_route,
            ouoRoles,
            openUserModal,
            handleUserSelection,
            addUser,
            removeUser,
            updateUserRow,
            restoreUser, // Expose to template
            showDeleted, // Expose to template
            toggleShowDeleted, // Expose to template
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
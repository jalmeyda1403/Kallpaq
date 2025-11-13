<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Gestión de Asignación de Usuarios a OUOs</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="ouo_select">Seleccionar OUO</label>
                            <select id="ouo_select" class="form-control" v-model="selectedOuoId" @change="fetchOuoUsers">
                                <option value="">-- Seleccione una OUO --</option>
                                <option v-for="ouo in ouos" :key="ouo.id" :value="ouo.id">{{ ouo.ouo_nombre }}</option>
                            </select>
                        </div>

                        <div v-if="selectedOuoId" class="mt-4">
                            <h4>Usuarios Asignados a {{ selectedOuoName }}</h4>
                            <div v-if="loadingOuoUsers" class="text-center">Cargando usuarios...</div>
                            <div v-else-if="!ouoUsers.length" class="alert alert-info">No hay usuarios asignados a esta OUO.</div>
                            <div v-else class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID Usuario</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Rol en OUO</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ouoUser in ouoUsers" :key="ouoUser.id">
                                            <td>{{ ouoUser.id }}</td>
                                            <td>{{ ouoUser.name }}</td>
                                            <td>{{ ouoUser.email }}</td>
                                            <td>{{ ouoUser.role_in_ouo }}</td>
                                            <td>
                                                <button class="btn btn-danger btn-sm" @click="detachOuoUser(ouoUser.id)">
                                                    <i class="fas fa-trash"></i> Desvincular
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h4 class="mt-4">Asignar Nuevo Usuario</h4>
                            <form @submit.prevent="attachOuoUser">
                                <div class="form-group">
                                    <label for="user_select">Usuario</label>
                                    <select id="user_select" class="form-control" v-model="newUser.user_id" required>
                                        <option value="">-- Seleccione un usuario --</option>
                                        <option v-for="user in availableUsers" :key="user.id" :value="user.id">{{ user.name }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="role_select">Rol en OUO</label>
                                    <select id="role_select" class="form-control" v-model="newUser.role_in_ouo" required>
                                        <option value="">-- Seleccione un rol --</option>
                                        <option value="owner">Propietario</option>
                                        <option value="titular">Titular</option>
                                        <option value="suplente">Suplente</option>
                                        <option value="facilitador">Facilitador</option>
                                        <option value="member">Miembro</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success mt-2" :disabled="loadingAttach">
                                    <i class="fas fa-plus"></i> Asignar Usuario
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';

const ouos = ref([]);
const users = ref([]); // All available users
const selectedOuoId = ref('');
const ouoUsers = ref([]); // Users assigned to the selected OUO
const newUser = ref({ user_id: '', role_in_ouo: '' });
const loadingOuos = ref(false);
const loadingUsers = ref(false);
const loadingOuoUsers = ref(false);
const loadingAttach = ref(false);

const selectedOuoName = computed(() => {
    const ouo = ouos.value.find(o => o.id === selectedOuoId.value);
    return ouo ? ouo.ouo_nombre : 'N/A';
});

const availableUsers = computed(() => {
    const assignedUserIds = ouoUsers.value.map(u => u.id);
    return users.value.filter(user => !assignedUserIds.includes(user.id));
});

onMounted(async () => {
    await fetchOuos();
    await fetchUsers();
});

async function fetchOuos() {
    loadingOuos.value = true;
    try {
        const response = await axios.get(route('ouos.listar')); // Assuming this route lists all OUOs
        ouos.value = response.data;
    } catch (error) {
        console.error('Error fetching OUOs:', error);
    } finally {
        loadingOuos.value = false;
    }
}

async function fetchUsers() {
    loadingUsers.value = true;
    try {
        const response = await axios.get(route('api.users.list')); // Assuming this route lists all users
        users.value = response.data;
    } catch (error) {
        console.error('Error fetching users:', error);
    } finally {
        loadingUsers.value = false;
    }
}

async function fetchOuoUsers() {
    if (!selectedOuoId.value) {
        ouoUsers.value = [];
        return;
    }
    loadingOuoUsers.value = true;
    try {
        const response = await axios.get(route('api.ouos.users.list', { ouo: selectedOuoId.value }));
        ouoUsers.value = response.data;
    } catch (error) {
        console.error('Error fetching OUO users:', error);
    } finally {
        loadingOuoUsers.value = false;
    }
}

async function attachOuoUser() {
    if (!selectedOuoId.value || !newUser.value.user_id || !newUser.value.role_in_ouo) {
        alert('Por favor, seleccione una OUO, un usuario y un rol.');
        return;
    }
    loadingAttach.value = true;
    try {
        await axios.post(route('api.ouos.users.attach', { ouo: selectedOuoId.value }), newUser.value);
        alert('Usuario asignado con éxito.');
        newUser.value = { user_id: '', role_in_ouo: '' }; // Reset form
        await fetchOuoUsers(); // Refresh list
    } catch (error) {
        console.error('Error attaching OUO user:', error);
        alert('Error al asignar usuario: ' + (error.response?.data?.message || error.message));
    } finally {
        loadingAttach.value = false;
    }
}

async function detachOuoUser(userId) {
    if (!selectedOuoId.value || !userId) return;
    if (!confirm('¿Está seguro de que desea desvincular este usuario de la OUO?')) return;

    try {
        await axios.delete(route('api.ouos.users.detach', { ouo: selectedOuoId.value, user: userId }));
        alert('Usuario desvinculado con éxito.');
        await fetchOuoUsers(); // Refresh list
    } catch (error) {
        console.error('Error detaching OUO user:', error);
        alert('Error al desvincular usuario: ' + (error.response?.data?.message || error.message));
    }
}
</script>

<style scoped>
/* Add any specific styles here */
</style>

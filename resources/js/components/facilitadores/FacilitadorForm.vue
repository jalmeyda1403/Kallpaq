<template>
    <div class="modal fade" tabindex="-1" aria-labelledby="facilitadorModalLabel" aria-hidden="true" ref="modal"
        id="facilitadorModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ facilitadorStore.isEditing ? 'Editar Facilitador' : 'Nuevo Facilitador' }}</h5>
                    <button type="button" class="close text-white" @click="facilitadorStore.closeFormModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="handleSubmit">
                        <div class="form-group">
                            <label for="user_id">Usuario</label>
                            <select id="user_id" class="form-control" v-model="facilitadorStore.form.user_id" required>
                                <option value="">Seleccione un usuario</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                            <div v-if="facilitadorStore.errors.user_id" class="text-danger">{{ facilitadorStore.errors.user_id[0] }}</div>
                        </div>

                        <div class="form-group">
                            <label for="cargo">Cargo</label>
                            <select id="cargo" class="form-control" v-model="facilitadorStore.form.cargo" required>
                                <option value="">Seleccione un cargo</option>
                                <option value="facilitador">Facilitador</option>
                                <option value="propietario">Propietario</option>
                            </select>
                            <div v-if="facilitadorStore.errors.cargo" class="text-danger">{{ facilitadorStore.errors.cargo[0] }}</div>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" class="form-control" v-model="facilitadorStore.form.estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                            <div v-if="facilitadorStore.errors.estado" class="text-danger">{{ facilitadorStore.errors.estado[0] }}</div>
                        </div>

                        <div class="form-group mt-3">
                            <label>Procesos Asignados</label>
                            <div class="input-group mb-3">
                                <input type="hidden" v-model="selectedProceso.id" />
                                <input type="text" class="form-control" placeholder="Seleccione el Proceso a Asignar"
                                    v-model="selectedProceso.descripcion" readonly />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-dark" @click="openProcesoModal">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" :disabled="!selectedProceso.id"
                                        @click="attachProceso">
                                        <i class="fas fa-link"></i> Asignar
                                    </button>
                                </div>
                            </div>
                            <ul class="list-group">
                                <li v-for="proceso in facilitadorStore.form.procesos" :key="proceso.id" class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ proceso.proceso_nombre }}
                                    <button type="button" class="btn btn-danger btn-sm" @click="detachProceso(proceso.id)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </li>
                                <li v-if="!facilitadorStore.form.procesos || facilitadorStore.form.procesos.length === 0" class="list-group-item text-muted">
                                    No hay procesos asignados.
                                </li>
                            </ul>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="facilitadorStore.closeFormModal">Cancelar</button>
                            <button type="submit" class="btn btn-danger" :disabled="facilitadorStore.loading">
                                <i class="fas fa-save"></i> {{ facilitadorStore.isEditing ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- <modal-hijo ref="procesoModal" :fetch-url="proceso_route" target-id="id" target-desc="proceso_nombre"
        @update-target="handleProcesoSelection" /> -->
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useFacilitadorStore } from '@/stores/facilitadorStore';
import axios from 'axios';
import { route } from 'ziggy-js';
// import ModalHijo from '../generales/ModalHijo.vue'; // Commented out

const facilitadorStore = useFacilitadorStore();
const users = ref([]);
const selectedProceso = ref({ id: null, descripcion: '' });
// const procesoModal = ref(null); // Commented out
// const proceso_route = route('procesos.buscar'); // Commented out

onMounted(async () => {
    await fetchUsers();
});

const fetchUsers = async () => {
    try {
        const response = await axios.get(route('api.users.list'));
        users.value = response.data;
    } catch (error) {
        console.error('Error fetching users:', error);
    }
};

const handleSubmit = () => {
    facilitadorStore.saveFacilitador();
};

const openProcesoModal = () => {
    // facilitadorStore.closeFormModal(); // Hide FacilitadorForm.vue
    // procesoModal.value.open(); // Open ModalHijo.vue
    console.log('ModalHijo would open here'); // Placeholder
};

const handleProcesoSelection = ({ idValue, descValue }) => {
    selectedProceso.value.id = idValue;
    selectedProceso.value.descripcion = descValue;
    // facilitadorStore.openFormModal(); // Re-show FacilitadorForm.vue
};

const attachProceso = async () => {
    if (!selectedProceso.value.id || !facilitadorStore.form.id) return;
    try {
        await facilitadorStore.attachProceso(facilitadorStore.form.id, selectedProceso.value.id);
        selectedProceso.value = { id: null, descripcion: '' }; // Limpiar selección
    } catch (error) {
        console.error('Error attaching proceso:', error);
    }
};

const detachProceso = async (procesoId) => {
    if (!facilitadorStore.form.id || !procesoStore.form.id) return;
    if (confirm('¿Está seguro de que desea desasociar este proceso?')) {
        try {
            await facilitadorStore.detachProceso(facilitadorStore.form.id, procesoId);
        } catch (error) {
            console.error('Error detaching proceso:', error);
        }
    }
};
</script>

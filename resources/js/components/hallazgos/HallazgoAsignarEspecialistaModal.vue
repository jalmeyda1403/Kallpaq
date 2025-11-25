<template>
    <div class="modal fade" id="asignarEspecialistaModal" tabindex="-1" role="dialog" aria-labelledby="asignarEspecialistaModalLabel" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="asignarEspecialistaModalLabel">Asignar Especialista para Verificación</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="cerrarModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="asignarEspecialista">
                        <div class="form-group">
                            <label for="especialista">Especialista:</label>
                            <select v-model="form.especialista_id" class="form-control" required>
                                <option value="" disabled>Seleccione un especialista</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModal">Cancelar</button>
                            <button type="submit" class="btn btn-danger" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Asignar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    visible: Boolean,
    hallazgoId: Number
});

const emit = defineEmits(['cerrar', 'asignado']);

const modalRef = ref(null);
const users = ref([]);
const loading = ref(false);
const form = reactive({
    especialista_id: ''
});

// Obtener lista de usuarios (especialistas)
const fetchUsers = async () => {
    try {
        const response = await axios.get('/users/list'); // Asumiendo que existe esta ruta o similar
        users.value = response.data;
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
    }
};

const asignarEspecialista = async () => {
    if (!form.especialista_id) return;

    loading.value = true;
    try {
        // Obtener el usuario actual para enviar quién asignó
        const currentUser = window.App?.user || { id: 1, name: 'Admin' }; // Fallback

        await axios.post(`/hallazgos/${props.hallazgoId}/asignaciones`, {
            especialista_id: form.especialista_id,
            assigned_by_user_id: currentUser.id,
            assigned_by_user_name: currentUser.name
        });

        Swal.fire({
            icon: 'success',
            title: 'Asignado',
            text: 'Especialista asignado correctamente para verificación.',
            timer: 2000,
            showConfirmButton: false
        });

        emit('asignado');
        cerrarModal();
    } catch (error) {
        console.error('Error al asignar:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo asignar el especialista.'
        });
    } finally {
        loading.value = false;
    }
};

const cerrarModal = () => {
    emit('cerrar');
    $(modalRef.value).modal('hide');
};

const abrirModal = () => {
    $(modalRef.value).modal('show');
};

watch(() => props.visible, (newVal) => {
    if (newVal) {
        fetchUsers();
        form.especialista_id = '';
        abrirModal();
    } else {
        cerrarModal();
    }
});

onMounted(() => {
    // Inicialización si es necesaria
});
</script>

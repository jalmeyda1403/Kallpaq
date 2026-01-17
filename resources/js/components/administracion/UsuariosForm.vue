<template>
    <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ user ? 'Editar' : 'Crear' }} Usuario</h5>
                    <button type="button" class="close text-white" aria-label="Close" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveUser">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="position-relative">
                                <img :src="photoPreview || '/images/user-default.png'" alt="Avatar"
                                    class="rounded-circle shadow-sm border"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                                <label for="user_foto_url"
                                    class="btn btn-sm btn-light position-absolute rounded-circle shadow-sm"
                                    style="bottom: 0; right: 0; padding: 5px 8px; cursor: pointer;">
                                    <i class="fas fa-camera text-muted"></i>
                                </label>
                                <input type="file" id="user_foto_url" class="d-none" @change="handlePhotoChange"
                                    accept="image/*">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="font-weight-bold custom-label">Nombre <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" v-model="form.name" required
                                placeholder="Ingrese el nombre completo">
                        </div>
                        <div class="form-group">
                            <label for="email" class="font-weight-bold custom-label">Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" v-model="form.email" required
                                placeholder="Ingrese el correo electrónico">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="user_cod_personal" class="font-weight-bold custom-label">Código
                                    Personal</label>
                                <input type="text" class="form-control" id="user_cod_personal"
                                    v-model="form.user_cod_personal" placeholder="Opcional">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="user_iniciales" class="font-weight-bold custom-label">Iniciales</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="user_iniciales"
                                        v-model="form.user_iniciales" maxlength="10" placeholder="Ej: JPEREZ">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            @click="generateInitials" :disabled="generatingInitials"
                                            title="Generar Iniciales">
                                            <i class="fas"
                                                :class="generatingInitials ? 'fa-spinner fa-spin' : 'fa-sync-alt'"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!user" class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> La contraseña se generará automáticamente y se enviará un
                            correo para restablecerla si es necesario.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="close">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger ml-2" @click="saveUser">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, watch } from 'vue';
import { Modal } from 'bootstrap';
import { useUserStore } from '@/stores/userStore';
import axios from 'axios';
import { route } from 'ziggy-js';
import Swal from 'sweetalert2';

const props = defineProps({
    user: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['saved', 'close']);
const store = useUserStore();

const modalEl = ref(null);
let modalInstance = null;
const generatingInitials = ref(false);

const form = reactive({
    name: '',
    email: '',
    password: 'password123', // Default dummy password for creation
    user_cod_personal: '',
    user_iniciales: '',
    user_foto_url: null
});

const photoPreview = ref(null);

const handlePhotoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.user_foto_url = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

onMounted(() => {
    if (modalEl.value) {
        modalInstance = new Modal(modalEl.value, {
            backdrop: 'static',
            keyboard: false
        });
    }
});

watch(() => props.user, (newUser) => {
    if (newUser) {
        form.name = newUser.name;
        form.email = newUser.email;
        form.password = ''; // Is update, password nullable
        form.user_cod_personal = newUser.user_cod_personal || '';
        form.user_iniciales = newUser.user_iniciales || '';
        form.user_foto_url = null;
        photoPreview.value = newUser.user_foto_url || null;
    } else {
        resetForm();
    }
});

const resetForm = () => {
    form.name = '';
    form.email = '';
    form.password = 'password123'; // Default temp password
    form.user_cod_personal = '';
    form.user_iniciales = '';
    form.user_foto_url = null;
    photoPreview.value = null;
};

const open = () => {
    if (!props.user) resetForm();
    modalInstance.show();
};

const close = () => {
    modalInstance.hide();
    emit('close');
};

const generateInitials = async () => {
    if (!form.name) return;

    generatingInitials.value = true;
    try {
        const response = await axios.post(route('api.admin.usuarios.generate-initials'), {
            name: form.name
        });
        form.user_iniciales = response.data.initials;
    } catch (error) {
        console.error('Error generating initials:', error);
    } finally {
        generatingInitials.value = false;
    }
};

const saveUser = async () => {
    try {
        if (props.user) {
            await store.updateUser(props.user.id, form);
        } else {
            await store.createUser(form);
        }
        emit('saved');
        close();
    } catch (error) {
        console.error('Error saving user:', error);
        Swal.fire('Error', 'Error al guardar usuario. Verifique los datos.', 'error');
    }
};

defineExpose({
    open,
    close
});
</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-weight: 600 !important;
    color: #495057 !important;
}
</style>

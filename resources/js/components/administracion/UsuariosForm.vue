<template>
    <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box mr-3 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 40px; height: 40px;">
                            <i class="fas" :class="user ? 'fa-user-edit text-danger' : 'fa-user-plus text-danger'"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold">
                                {{ modalTitle }}
                            </h5>
                            <small class="text-white-50">{{ modalSubtitle }}</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white outline-none" aria-label="Close" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light p-4">
                    <form @submit.prevent="saveUser">
                        <div class="d-flex justify-content-center mb-4">
                            <div class="position-relative">
                                <div class="avatar-wrapper rounded-circle p-1 bg-white shadow-sm border">
                                    <img :src="photoPreview || '/images/user-default.png'" alt="Avatar"
                                        class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <label for="user_foto_url"
                                    class="btn btn-sm btn-danger position-absolute rounded-circle shadow ripple"
                                    style="bottom: 5px; right: 5px; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                    <i class="fas fa-camera text-white small"></i>
                                </label>
                                <input type="file" id="user_foto_url" class="d-none" @change="handlePhotoChange"
                                    accept="image/*">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="name" class="font-weight-600 small text-uppercase text-muted">Nombre Completo
                                <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white border-0 shadow-sm px-3 py-4" id="name"
                                v-model="form.name" required placeholder="Ej: Juan Pérez">
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="font-weight-600 small text-uppercase text-muted">Correo
                                Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control bg-white border-0 shadow-sm px-3 py-4" id="email"
                                v-model="form.email" required placeholder="usuario@contraloria.gob.pe">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 mb-3">
                                <label for="user_cod_personal"
                                    class="font-weight-600 small text-uppercase text-muted">Código Personal</label>
                                <input type="text" class="form-control bg-white border-0 shadow-sm px-3 py-4"
                                    id="user_cod_personal" v-model="form.user_cod_personal" placeholder="CP-0000">
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="user_iniciales"
                                    class="font-weight-600 small text-uppercase text-muted">Iniciales</label>
                                <div class="input-group shadow-sm rounded overflow-hidden">
                                    <input type="text" class="form-control bg-white border-0 px-3 py-4"
                                        id="user_iniciales" v-model="form.user_iniciales" maxlength="10"
                                        placeholder="Ej: JPEREZ">
                                    <div class="input-group-append">
                                        <button class="btn btn-white border-0 text-danger" type="button"
                                            @click="generateInitials" :disabled="generatingInitials"
                                            title="Generar Iniciales">
                                            <i class="fas"
                                                :class="generatingInitials ? 'fa-spinner fa-spin' : 'fa-magic'"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!user"
                            class="alert alert-light border-0 shadow-sm mt-3 py-3 px-3 d-flex align-items-start">
                            <i class="fas fa-info-circle text-info mt-1 mr-2"></i>
                            <small class="text-muted">La contraseña se generará automáticamente y se enviará un enlace
                                de acceso al correo institucional.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-white border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4 border-0" @click="close">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger px-4 shadow-sm font-weight-bold" @click="saveUser">
                        <i class="fas fa-save mr-1"></i> {{ user ? 'Actualizar' : 'Registrar' }} Usuario
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, watch, computed } from 'vue';
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

const modalTitle = computed(() => props.user ? 'Editar Usuario' : 'Nuevo Usuario');
const modalSubtitle = computed(() => props.user ? 'Actualice los datos del sistema' : 'Registre un nuevo acceso');

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

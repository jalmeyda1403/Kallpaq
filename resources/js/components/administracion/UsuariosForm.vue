<template>
    <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" :class="{ 'modal-massive': isMassive }">
                <div class="modal-header bg-danger text-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-box mr-3 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 40px; height: 40px;">
                            <i class="fas"
                                :class="isMassive ? 'fa-users text-danger' : (user ? 'fa-user-edit text-danger' : 'fa-user-plus text-danger')"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold">
                                {{ isMassive ? 'Registro Masivo' : modalTitle }}
                            </h5>
                            <small class="text-white-50">{{ isMassive ? 'Cree múltiples usuarios a la vez' :
                                modalSubtitle }}</small>
                        </div>
                    </div>

                    <div v-if="!user"
                        class="custom-control custom-switch ml-auto bg-white-10 rounded-pill px-3 py-1 border border-white-20">
                        <input type="checkbox" class="custom-control-input" id="massiveSwitch" v-model="isMassive">
                        <label class="custom-control-label text-white small font-weight-bold" style="cursor: pointer;"
                            for="massiveSwitch">
                            Modo Masivo
                        </label>
                    </div>

                    <button type="button" class="close text-white outline-none ml-2" aria-label="Close" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light p-4 overflow-auto" :style="{ maxHeight: isMassive ? '70vh' : 'auto' }">
                    <form @submit.prevent="saveUser">
                        <div v-if="!isMassive">
                            <div class="d-flex justify-content-center mb-4">
                                <div class="position-relative">
                                    <div class="avatar-wrapper rounded-circle p-1 bg-white shadow-sm border">
                                        <img :src="photoPreview || '/images/user-default.png'" alt="Avatar"
                                            class="rounded-circle"
                                            style="width: 100px; height: 100px; object-fit: cover;">
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
                                <label for="name" class="font-weight-600 small text-uppercase text-muted">Nombre
                                    Completo
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-white border-0 shadow-sm px-3 py-4" id="name"
                                    v-model="form.name" required placeholder="Ej: Juan Pérez">
                            </div>
                            <div class="form-group mb-3">
                                <label for="email" class="font-weight-600 small text-uppercase text-muted">Correo
                                    Electrónico <span class="text-danger">*</span></label>
                                <input type="email" class="form-control bg-white border-0 shadow-sm px-3 py-4"
                                    id="email" v-model="form.email" required placeholder="usuario@contraloria.gob.pe">
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
                        </div>

                        <!-- MODO MASIVO -->
                        <div v-else class="animate__animated animate__fadeIn">
                            <div class="table-responsive">
                                <table class="table table-borderless table-sm">
                                    <thead>
                                        <tr class="text-muted small text-uppercase">
                                            <th style="width: 30%">Nombre <span class="text-danger">*</span></th>
                                            <th style="width: 30%">Email <span class="text-danger">*</span></th>
                                            <th style="width: 15%">Código</th>
                                            <th style="width: 20%">Iniciales</th>
                                            <th style="width: 5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(u, index) in massiveUsers" :key="index"
                                            class="animate__animated animate__fadeInUp">
                                            <td>
                                                <input type="text" v-model="u.name"
                                                    class="form-control border-0 shadow-sm py-3"
                                                    placeholder="Nombre Completo" required>
                                            </td>
                                            <td>
                                                <input type="email" v-model="u.email"
                                                    class="form-control border-0 shadow-sm py-3"
                                                    placeholder="correo@ejemplo.com" required>
                                            </td>
                                            <td>
                                                <input type="text" v-model="u.user_cod_personal"
                                                    class="form-control border-0 shadow-sm py-3" placeholder="CP-000">
                                            </td>
                                            <td>
                                                <div class="input-group shadow-sm rounded overflow-hidden">
                                                    <input type="text" class="form-control border-0 py-3 px-2"
                                                        v-model="u.user_iniciales" maxlength="10" placeholder="INI">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-white border-0 text-danger px-2"
                                                            type="button" @click="generateMassiveInitials(index)"
                                                            title="Generar Iniciales">
                                                            <i class="fas fa-magic"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button v-if="massiveUsers.length > 1" type="button"
                                                    class="btn btn-link text-danger p-0 h4 mb-0"
                                                    @click="removeMassiveRow(index)">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button"
                                class="btn btn-sm btn-outline-danger btn-block border-dashed mt-2 py-2"
                                @click="addMassiveRow">
                                <i class="fas fa-plus-circle mr-1"></i> Agregar otra fila
                            </button>
                        </div>

                        <div v-if="!user"
                            class="alert alert-light border-0 shadow-sm mt-3 py-3 px-3 d-flex align-items-start">
                            <i class="fas fa-info-circle text-info mt-1 mr-2"></i>
                            <small class="text-muted">La contraseña se generará automáticamente (password123 por
                                defecto) y se enviará un enlace
                                de acceso al correo institucional.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-white border-0 py-3">
                    <button v-if="user" type="button"
                        class="btn btn-warning px-3 shadow-sm mr-auto font-weight-bold tex-white"
                        @click="sendResetLink">
                        <i class="fas fa-key mr-1"></i> Enviar Reset Password
                    </button>
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
const isMassive = ref(false);
const generatingInitials = ref(false);
const massiveUsers = ref([
    { name: '', email: '', user_cod_personal: '', user_iniciales: '' }
]);

const modalTitle = computed(() => {
    if (isMassive.value) return 'Registro Masivo';
    return props.user ? 'Editar Usuario' : 'Nuevo Usuario';
});
const modalSubtitle = computed(() => {
    if (isMassive.value) return 'Cree múltiples usuarios a la vez';
    return props.user ? 'Actualice los datos del sistema' : 'Registre un nuevo acceso';
});

const form = reactive({
    name: '',
    email: '',
    password: 'password123', // Default dummy password for creation
    user_cod_personal: '',
    user_iniciales: '',
    user_foto_url: null
});

const addMassiveRow = () => {
    massiveUsers.value.push({ name: '', email: '', user_cod_personal: '' });
};

const removeMassiveRow = (index) => {
    massiveUsers.value.splice(index, 1);
};

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
        isMassive.value = false; // Never massive for editing
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
    isMassive.value = false;
    massiveUsers.value = [{ name: '', email: '', user_cod_personal: '', user_iniciales: '' }];
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

const generateMassiveInitials = async (index) => {
    const user = massiveUsers.value[index];
    if (!user.name) return;

    try {
        const response = await axios.post(route('api.admin.usuarios.generate-initials'), {
            name: user.name
        });
        user.user_iniciales = response.data.initials;
    } catch (error) {
        console.error('Error generating initials:', error);
    }
};

const saveUser = async () => {
    try {
        if (isMassive.value) {
            // Validar que al menos las filas básicas estén llenas
            const validUsers = massiveUsers.value.filter(u => u.name && u.email);
            if (validUsers.length === 0) {
                Swal.fire('Atención', 'Debe completar al menos un usuario con nombre y correo.', 'warning');
                return;
            }
            await store.createMassiveUsers(validUsers);
        } else {
            // Normalizar nombre a tipo "Altibajas" (Title Case)
            if (form.name) {
                form.name = form.name
                    .toLowerCase()
                    .trim()
                    .replace(/\s+/g, ' ') // Eliminar espacios extra
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }

            // Normalizar correo a minúsculas
            if (form.email) {
                form.email = form.email.toLowerCase().trim();
            }

            if (props.user) {
                await store.updateUser(props.user.id, form);
            } else {
                await store.createUser(form);
            }
        }
        emit('saved');
        close();
    } catch (error) {
        console.error('Error saving user:', error);
        let errorMsg = 'Error al guardar usuario. Verifique los datos.';
        if (error.response && error.response.data && error.response.data.message) {
            errorMsg = error.response.data.message;
        }
        Swal.fire('Error', errorMsg, 'error');
    }
};

const sendResetLink = async () => {
    if (!props.user || !props.user.id) return;

    // Confirmación opcional
    const result = await Swal.fire({
        title: '¿Enviar enlace?',
        text: `Se enviará un correo a ${props.user.email} con el enlace de restauración.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    });

    if (!result.isConfirmed) return;

    try {
        await axios.post(route('api.admin.users.reset-link', props.user.id));
        Swal.fire('Enviado', 'Se ha enviado el enlace de restauración correctamente.', 'success');
    } catch (error) {
        console.error('Error sending reset link:', error);
        let msg = 'No se pudo enviar el enlace.';
        if (error.response?.data?.message) {
            msg += ' ' + error.response.data.message;
        }
        Swal.fire('Error', msg, 'error');
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

.bg-white-10 {
    background-color: rgba(255, 255, 255, 0.1);
}

.border-white-20 {
    border-color: rgba(255, 255, 255, 0.2) !important;
}

.border-dashed {
    border-style: dashed !important;
    border-width: 2px;
}

.modal-massive {
    transition: all 0.3s ease;
}

/* Fix for switch text color */
.custom-control-input:checked~.custom-control-label::before {
    background-color: #fff;
    border-color: #fff;
}

.custom-control-input:checked~.custom-control-label::after {
    background-color: var(--danger);
}
</style>

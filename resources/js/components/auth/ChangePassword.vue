<template>
    <div class="change-password-container d-flex align-items-center justify-content-center min-vh-100 bg-light">
        <div class="card shadow-lg border-0" style="width: 100%; max-width: 450px; border-radius: 1.5rem;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="icon-circle bg-danger-soft text-danger mb-3 mx-auto">
                        <i class="fas fa-lock fa-2x"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark">Cambiar Contraseña</h3>
                    <p class="text-muted small">Tu contraseña ha sido restablecida por un administrador. Es obligatorio
                        cambiarla para continuar.</p>
                </div>

                <form @submit.prevent="handleSubmit">
                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-muted text-uppercase mb-2">Contraseña Actual (Código
                            Personal)</label>
                        <div
                            class="input-group input-group-merge border-0 shadow-sm rounded-pill overflow-hidden bg-white">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-0 pl-4">
                                    <i class="fas fa-key text-muted"></i>
                                </span>
                            </div>
                            <input type="password" v-model="form.current_password"
                                class="form-control border-0 py-4 h-auto bg-white"
                                placeholder="Ingresa tu código personal" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="small font-weight-bold text-muted text-uppercase mb-2">Nueva Contraseña</label>
                        <div
                            class="input-group input-group-merge border-0 shadow-sm rounded-pill overflow-hidden bg-white">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-0 pl-4">
                                    <i class="fas fa-shield-alt text-muted"></i>
                                </span>
                            </div>
                            <input type="password" v-model="form.password"
                                class="form-control border-0 py-4 h-auto bg-white" placeholder="Mínimo 8 caracteres"
                                required>
                        </div>
                        <div v-if="errors.password" class="text-danger small mt-1 pl-3">{{ errors.password[0] }}</div>
                    </div>

                    <div class="form-group mb-5">
                        <label class="small font-weight-bold text-muted text-uppercase mb-2">Confirmar Nueva
                            Contraseña</label>
                        <div
                            class="input-group input-group-merge border-0 shadow-sm rounded-pill overflow-hidden bg-white">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-0 pl-4">
                                    <i class="fas fa-check-circle text-muted"></i>
                                </span>
                            </div>
                            <input type="password" v-model="form.password_confirmation"
                                class="form-control border-0 py-4 h-auto bg-white"
                                placeholder="Repite la nueva contraseña" required>
                        </div>
                        <div v-if="!passwordsMatch" class="text-danger small mt-1 pl-3">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Las contraseñas no coinciden.
                        </div>
                    </div>

                    <button type="submit"
                        class="btn btn-danger btn-block rounded-pill py-3 font-weight-bold shadow-danger"
                        :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm mr-2" role="status"
                            aria-hidden="true"></span>
                        ACTUALIZAR CONTRASEÑA
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="#" class="text-muted small font-weight-600" @click.prevent="logout">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useAuthStore } from '@/stores/authStore';
import { route } from 'ziggy-js';

const authStore = useAuthStore();
const loading = ref(false);
const errors = reactive({});

const form = reactive({
    current_password: '',
    password: '',
    password_confirmation: ''
});

const passwordsMatch = computed(() => {
    if (!form.password_confirmation) return true;
    return form.password === form.password_confirmation;
});

const handleSubmit = async () => {
    if (!passwordsMatch.value) {
        Swal.fire('Error', 'Las contraseñas no coinciden.', 'error');
        return;
    }

    loading.value = true;
    Object.keys(errors).forEach(key => delete errors[key]);

    try {
        const response = await axios.put(route('password.update'), form);

        await Swal.fire({
            title: '¡Éxito!',
            text: 'Tu contraseña ha sido actualizada correctamente. Serás redirigido al inicio.',
            icon: 'success',
            confirmButtonColor: '#d32f2f'
        });

        window.location.href = '/home';
    } catch (error) {
        if (error.response && error.response.status === 422) {
            Object.assign(errors, error.response.data.errors);
            const msg = error.response.data.message || 'Error de validación';
            Swal.fire('Error', msg, 'error');
        } else {
            console.error('Error changing password:', error);
            Swal.fire('Error', 'Ocurrió un error inesperado al cambiar la contraseña (404 o Error del Servidor).', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const logout = () => {
    authStore.logout();
};
</script>

<style scoped>
.change-password-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
}

.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-danger-soft {
    background-color: rgba(211, 47, 47, 0.1);
}

.shadow-danger {
    box-shadow: 0 4px 14px 0 rgba(211, 47, 47, 0.39);
}

.input-group-merge .input-group-text {
    padding-right: 0;
}

.form-control:focus {
    box-shadow: none !important;
}

.font-weight-600 {
    font-weight: 600;
}
</style>

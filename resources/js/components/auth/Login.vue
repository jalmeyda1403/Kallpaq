<template>
    <div class="login-page">
        <div class="login-overlay"></div>
        <div class="login-box">
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                <div class="card-header bg-danger text-white text-center py-4">
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <div class="d-flex align-items-center mb-2">
                            <!-- Use dynamic binding for icon -->
                            <img :src="'/images/kallpaq_ico.png'" alt="Icono" class="img-fluid mr-2"
                                style="max-height: 40px;">
                            <h2 class="font-weight-bold mb-0">KALLPAQ</h2>
                        </div>
                        <p class="mb-0 small text-white-50 font-italic" style="font-size: 0.8rem;">"Excelencia y control
                            en cada
                            proceso"</p>
                    </div>
                </div>
                <div class="card-body login-card-body p-4">
                    <div class="text-center mb-4">
                        <img :src="'/images/logo.png'" alt="Kallpaq Logo" class="img-fluid mb-2"
                            style="max-height: 50px;">
                        <p class="text-muted font-weight-bold">Bienvenido nuevamente</p>
                    </div>

                    <form @submit.prevent="handleLogin">
                        <div class="input-group mb-3">
                            <input type="email" v-model="form.email" class="form-control form-control-lg"
                                placeholder="Correo Electrónico" required autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text bg-white border-left-0">
                                    <span class="fas fa-envelope text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" v-model="form.password" class="form-control form-control-lg"
                                placeholder="Contraseña" required>
                            <div class="input-group-append">
                                <div class="input-group-text bg-white border-left-0">
                                    <span class="fas fa-lock text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <div class="col-8">
                                <div class="icheck-danger">
                                    <input type="checkbox" id="remember" v-model="form.remember">
                                    <label for="remember" class="font-weight-normal text-secondary">
                                        Recordarme
                                    </label>
                                </div>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-danger btn-block font-weight-bold shadow-sm"
                                    :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    <span v-else>Entrar</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="social-auth-links text-center mb-3">
                        <p class="mb-1">
                            <a href="/password/reset" class="text-danger font-weight-bold">¿Olvidaste tu contraseña?</a>
                        </p>
                    </div>

                    <p class="mb-1" v-if="errorMessage">
                    <div class="alert alert-danger text-sm p-2 mt-3 mb-0 rounded">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ errorMessage }}
                    </div>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useAuthStore } from '../../stores/authStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const form = reactive({
    email: '',
    password: '',
    remember: false
});

const loading = ref(false);
const errorMessage = ref('');

const handleLogin = async () => {
    loading.value = true;
    errorMessage.value = '';

    try {
        await authStore.login(form);
        // Redirect is handled in the store or we can do it here
        // router.push('/home'); 
    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
            errorMessage.value = Object.values(error.response.data.errors).flat().join(', ');
        } else if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = 'Ocurrió un error al iniciar sesión.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-image: url('/images/login-bg.png');
    /* Assumes image will be placed here */
    background-size: cover;
    background-position: center right;
    position: relative;
}

.login-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.4);
    /* Light overlay for readability */
    backdrop-filter: blur(2px);
}

.login-box {
    width: 400px;
    z-index: 2;
    padding: 20px;
}

.login-logo a {
    color: #dc3545;
    /* Bootstrap danger red */
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card {
    border-radius: 5px;
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

.card-header {
    border-top-left-radius: 5px !important;
    border-top-right-radius: 5px !important;
    background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.input-group-text {
    border-color: #ced4da;
}
</style>

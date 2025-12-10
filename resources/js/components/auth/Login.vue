<template>
    <div class="login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="#"><b>Kallpaq</b>SIG</a>
            </div>
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Sign in to start your session</p>

                    <form @submit.prevent="handleLogin">
                        <div class="input-group mb-3">
                            <input type="email" v-model="form.email" class="form-control" placeholder="Email" required autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" v-model="form.password" class="form-control" placeholder="Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="remember" v-model="form.remember">
                                    <label for="remember">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span v-else>Sign In</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <p class="mb-1" v-if="errorMessage">
                        <span class="text-danger">{{ errorMessage }}</span>
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
            errorMessage.value = 'An error occurred during login.';
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
    background-color: #e9ecef;
}
.login-box {
    width: 360px;
}
</style>

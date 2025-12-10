import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(window.App?.user || null);

    const isAuthenticated = computed(() => !!user.value);

    const roles = computed(() => user.value?.roles || []);

    const hasRole = (roleName) => {
        return roles.value.includes(roleName);
    };

    const hasAnyRole = (roleNames) => {
        return roleNames.some(role => hasRole(role));
    };

    const login = async (credentials) => {
        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.post('/login', credentials);
        user.value = response.data.user; // Assuming backend returns user object
        // Redirect handled by component or router
        window.location.href = '/home'; // Force reload to ensure all state is fresh, or use router.push('/home') for SPA feel if backend session is stateless enough
    };

    const logout = async () => {
        await axios.post('/logout');
        user.value = null;
        window.location.href = '/login';
    };

    return {
        user,
        isAuthenticated,
        roles,
        hasRole,
        hasAnyRole,
        login,
        logout
    };
});

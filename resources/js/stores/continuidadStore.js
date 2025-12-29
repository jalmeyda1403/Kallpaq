import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useContinuidadStore = defineStore('continuidad', () => {
    // Estado
    const activos = ref([]);
    const escenarios = ref([]);
    const planes = ref([]);
    const pruebas = ref([]);
    const dashboard = ref(null);

    const activoActual = ref(null);
    const escenarioActual = ref(null);
    const planActual = ref(null);

    const tiposActivo = ref({});
    const categorias = ref({});
    const tiposPlan = ref({});
    const tiposPrueba = ref({});

    const isLoading = ref(false);
    const error = ref(null);

    // ========== ACTIVOS ==========
    const fetchActivos = async (filtros = {}) => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await axios.get('/api/continuidad/activos', { params: filtros });
            activos.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar activos';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const fetchActivo = async (id) => {
        isLoading.value = true;
        try {
            const response = await axios.get(`/api/continuidad/activos/${id}`);
            activoActual.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar activo';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const createActivo = async (data) => {
        const response = await axios.post('/api/continuidad/activos', data);
        activos.value.push(response.data.data);
        return response.data;
    };

    const updateActivo = async (id, data) => {
        const response = await axios.put(`/api/continuidad/activos/${id}`, data);
        const index = activos.value.findIndex(a => a.id === id);
        if (index !== -1) {
            activos.value[index] = response.data.data;
        }
        return response.data;
    };

    const deleteActivo = async (id) => {
        await axios.delete(`/api/continuidad/activos/${id}`);
        activos.value = activos.value.filter(a => a.id !== id);
    };

    const fetchTiposActivo = async () => {
        const response = await axios.get('/api/continuidad/tipos/activos');
        tiposActivo.value = response.data;
        return response.data;
    };

    // ========== ESCENARIOS ==========
    const fetchEscenarios = async (filtros = {}) => {
        isLoading.value = true;
        try {
            const response = await axios.get('/api/continuidad/escenarios', { params: filtros });
            escenarios.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar escenarios';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const createEscenario = async (data) => {
        const response = await axios.post('/api/continuidad/escenarios', data);
        escenarios.value.push(response.data.data);
        return response.data;
    };

    const updateEscenario = async (id, data) => {
        const response = await axios.put(`/api/continuidad/escenarios/${id}`, data);
        const index = escenarios.value.findIndex(e => e.id === id);
        if (index !== -1) {
            escenarios.value[index] = response.data.data;
        }
        return response.data;
    };

    const fetchCategorias = async () => {
        const response = await axios.get('/api/continuidad/tipos/categorias');
        categorias.value = response.data;
        return response.data;
    };

    // ========== PLANES ==========
    const fetchPlanes = async (filtros = {}) => {
        isLoading.value = true;
        try {
            const response = await axios.get('/api/continuidad/planes', { params: filtros });
            planes.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar planes';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const fetchPlan = async (id) => {
        isLoading.value = true;
        try {
            const response = await axios.get(`/api/continuidad/planes/${id}`);
            planActual.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar plan';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const createPlan = async (data) => {
        const response = await axios.post('/api/continuidad/planes', data);
        planes.value.push(response.data.data);
        return response.data;
    };

    const updatePlan = async (id, data) => {
        const response = await axios.put(`/api/continuidad/planes/${id}`, data);
        const index = planes.value.findIndex(p => p.id === id);
        if (index !== -1) {
            planes.value[index] = response.data.data;
        }
        if (planActual.value?.id === id) {
            planActual.value = { ...planActual.value, ...response.data.data };
        }
        return response.data;
    };

    const fetchTiposPlan = async () => {
        const response = await axios.get('/api/continuidad/tipos/planes');
        tiposPlan.value = response.data;
        return response.data;
    };

    // ========== PRUEBAS ==========
    const fetchPruebas = async (filtros = {}) => {
        isLoading.value = true;
        try {
            const response = await axios.get('/api/continuidad/pruebas', { params: filtros });
            pruebas.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar pruebas';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const createPrueba = async (data) => {
        const response = await axios.post('/api/continuidad/pruebas', data);
        pruebas.value.push(response.data.data);
        return response.data;
    };

    const registrarResultados = async (id, data) => {
        const response = await axios.post(`/api/continuidad/pruebas/${id}/resultados`, data);
        const index = pruebas.value.findIndex(p => p.id === id);
        if (index !== -1) {
            pruebas.value[index] = response.data.data;
        }
        return response.data;
    };

    const fetchTiposPrueba = async () => {
        const response = await axios.get('/api/continuidad/tipos/pruebas');
        tiposPrueba.value = response.data;
        return response.data;
    };

    // ========== DASHBOARD ==========
    const fetchDashboard = async () => {
        isLoading.value = true;
        try {
            const response = await axios.get('/api/continuidad/dashboard');
            dashboard.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar dashboard';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    // Computed
    const activosCriticos = computed(() => {
        return activos.value.filter(a => ['alto', 'critico'].includes(a.nivel_criticidad));
    });

    const planesAprobados = computed(() => {
        return planes.value.filter(p => p.estado === 'aprobado');
    });

    const pruebasPendientes = computed(() => {
        return pruebas.value.filter(p => p.estado === 'programada');
    });

    return {
        // Estado
        activos,
        escenarios,
        planes,
        pruebas,
        dashboard,
        activoActual,
        escenarioActual,
        planActual,
        tiposActivo,
        categorias,
        tiposPlan,
        tiposPrueba,
        isLoading,
        error,
        // Computed
        activosCriticos,
        planesAprobados,
        pruebasPendientes,
        // Acciones
        fetchActivos,
        fetchActivo,
        createActivo,
        updateActivo,
        deleteActivo,
        fetchTiposActivo,
        fetchEscenarios,
        createEscenario,
        updateEscenario,
        fetchCategorias,
        fetchPlanes,
        fetchPlan,
        createPlan,
        updatePlan,
        fetchTiposPlan,
        fetchPruebas,
        createPrueba,
        registrarResultados,
        fetchTiposPrueba,
        fetchDashboard
    };
});

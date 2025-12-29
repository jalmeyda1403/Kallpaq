import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useRevisionDireccionStore = defineStore('revisionDireccion', () => {
    // Estado
    const revisiones = ref([]);
    const revisionActual = ref(null);
    const tiposEntrada = ref({});
    const tiposSalida = ref({});
    const isLoading = ref(false);
    const error = ref(null);
    const modalVisible = ref(false);
    const modalMode = ref('create'); // 'create', 'edit', 'view'

    // Getters
    const revisionesOrdenadas = computed(() => {
        return [...revisiones.value].sort((a, b) =>
            new Date(b.fecha_programada) - new Date(a.fecha_programada)
        );
    });

    const compromisosPendientes = computed(() => {
        if (!revisionActual.value?.compromisos) return [];
        return revisionActual.value.compromisos.filter(c =>
            ['pendiente', 'en_proceso', 'vencido'].includes(c.estado)
        );
    });

    // Acciones
    const fetchRevisiones = async (filtros = {}) => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await axios.get('/api/revision-direccion', { params: filtros });
            revisiones.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar revisiones';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const fetchRevision = async (id) => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await axios.get(`/api/revision-direccion/${id}`);
            revisionActual.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar revisión';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const createRevision = async (data) => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await axios.post('/api/revision-direccion', data);
            revisiones.value.push(response.data.data);
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al crear revisión';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const updateRevision = async (id, data) => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await axios.put(`/api/revision-direccion/${id}`, data);
            const index = revisiones.value.findIndex(r => r.id === id);
            if (index !== -1) {
                revisiones.value[index] = response.data.data;
            }
            if (revisionActual.value?.id === id) {
                revisionActual.value = { ...revisionActual.value, ...response.data.data };
            }
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al actualizar revisión';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const deleteRevision = async (id) => {
        isLoading.value = true;
        error.value = null;
        try {
            await axios.delete(`/api/revision-direccion/${id}`);
            revisiones.value = revisiones.value.filter(r => r.id !== id);
            if (revisionActual.value?.id === id) {
                revisionActual.value = null;
            }
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al eliminar revisión';
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    // Entradas
    const fetchTiposEntrada = async () => {
        try {
            const response = await axios.get('/api/revision-direccion/tipos/entradas');
            tiposEntrada.value = response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar tipos de entrada', err);
        }
    };

    const addEntrada = async (revisionId, data) => {
        try {
            const response = await axios.post(`/api/revision-direccion/${revisionId}/entradas`, data);
            if (revisionActual.value?.id === revisionId) {
                revisionActual.value.entradas.push(response.data.data);
            }
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al agregar entrada';
            throw err;
        }
    };

    const updateEntrada = async (entradaId, data) => {
        try {
            const response = await axios.put(`/api/revision-direccion/entradas/${entradaId}`, data);
            if (revisionActual.value?.entradas) {
                const index = revisionActual.value.entradas.findIndex(e => e.id === entradaId);
                if (index !== -1) {
                    revisionActual.value.entradas[index] = response.data.data;
                }
            }
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al actualizar entrada';
            throw err;
        }
    };

    const deleteEntrada = async (entradaId) => {
        try {
            await axios.delete(`/api/revision-direccion/entradas/${entradaId}`);
            if (revisionActual.value?.entradas) {
                revisionActual.value.entradas = revisionActual.value.entradas.filter(e => e.id !== entradaId);
            }
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al eliminar entrada';
            throw err;
        }
    };

    // Salidas
    const fetchTiposSalida = async () => {
        try {
            const response = await axios.get('/api/revision-direccion/tipos/salidas');
            tiposSalida.value = response.data;
            return response.data;
        } catch (err) {
            console.error('Error al cargar tipos de salida', err);
        }
    };

    const addSalida = async (revisionId, data) => {
        try {
            const response = await axios.post(`/api/revision-direccion/${revisionId}/salidas`, data);
            if (revisionActual.value?.id === revisionId) {
                revisionActual.value.salidas.push(response.data.data);
            }
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al agregar salida';
            throw err;
        }
    };

    const updateSalida = async (salidaId, data) => {
        try {
            const response = await axios.put(`/api/revision-direccion/salidas/${salidaId}`, data);
            if (revisionActual.value?.salidas) {
                const index = revisionActual.value.salidas.findIndex(s => s.id === salidaId);
                if (index !== -1) {
                    revisionActual.value.salidas[index] = response.data.data;
                }
            }
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al actualizar salida';
            throw err;
        }
    };

    const deleteSalida = async (salidaId) => {
        try {
            await axios.delete(`/api/revision-direccion/salidas/${salidaId}`);
            if (revisionActual.value?.salidas) {
                revisionActual.value.salidas = revisionActual.value.salidas.filter(s => s.id !== salidaId);
            }
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al eliminar salida';
            throw err;
        }
    };

    // Compromisos
    const addCompromiso = async (revisionId, data) => {
        try {
            const response = await axios.post(`/api/revision-direccion/${revisionId}/compromisos`, data);
            if (revisionActual.value?.id === revisionId) {
                const nuevoCompromiso = response.data.data;
                revisionActual.value.compromisos.push(nuevoCompromiso);

                // Si está vinculado a una salida, actualizar la salida localmente
                if (nuevoCompromiso.salida_id && revisionActual.value.salidas) {
                    const salida = revisionActual.value.salidas.find(s => s.id == nuevoCompromiso.salida_id);
                    if (salida) {
                        if (!salida.compromisos) salida.compromisos = [];
                        salida.compromisos.push(nuevoCompromiso);
                    }
                }
            }
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al agregar compromiso';
            throw err;
        }
    };

    const updateCompromiso = async (compromisoId, data) => {
        try {
            const response = await axios.put(`/api/revision-direccion/compromisos/${compromisoId}`, data);
            if (revisionActual.value) {
                const index = revisionActual.value.compromisos.findIndex(c => c.id === compromisoId);
                if (index !== -1) {
                    revisionActual.value.compromisos[index] = response.data.data;
                }
            }
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al actualizar compromiso';
            throw err;
        }
    };

    const registrarSeguimiento = async (compromisoId, data) => {
        try {
            const response = await axios.post(`/api/revision-direccion/compromisos/${compromisoId}/seguimiento`, data);
            if (revisionActual.value) {
                const index = revisionActual.value.compromisos.findIndex(c => c.id === compromisoId);
                if (index !== -1) {
                    revisionActual.value.compromisos[index] = response.data.compromiso;
                }
            }
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al registrar seguimiento';
            throw err;
        }
    };

    // Dashboard
    const fetchDashboardCompromisos = async () => {
        try {
            const response = await axios.get('/api/revision-direccion/dashboard/compromisos');
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar dashboard';
            throw err;
        }
    };

    // Modal
    const openModal = (mode = 'create', revision = null) => {
        modalMode.value = mode;
        revisionActual.value = revision;
        modalVisible.value = true;
    };

    const closeModal = () => {
        modalVisible.value = false;
        modalMode.value = 'create';
    };

    // Limpiar estado
    const clearRevisionActual = () => {
        revisionActual.value = null;
    };

    return {
        // Estado
        revisiones,
        revisionActual,
        tiposEntrada,
        tiposSalida,
        isLoading,
        error,
        modalVisible,
        modalMode,
        // Getters
        revisionesOrdenadas,
        compromisosPendientes,
        // Acciones
        fetchRevisiones,
        fetchRevision,
        createRevision,
        updateRevision,
        deleteRevision,
        fetchTiposEntrada,
        addEntrada,
        updateEntrada,
        deleteEntrada,
        fetchTiposSalida,
        addSalida,
        updateSalida,
        deleteSalida,
        addCompromiso,
        updateCompromiso,
        registrarSeguimiento,
        fetchDashboardCompromisos,
        openModal,
        closeModal,
        clearRevisionActual
    };
});

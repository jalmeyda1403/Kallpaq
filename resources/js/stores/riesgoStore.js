import { defineStore } from 'pinia';
import axios from 'axios';

export const useRiesgoStore = defineStore('riesgo', {
    state: () => ({
        riesgos: [],
        riesgoActual: null,
        loading: false,
        error: null,
        filters: {
            codigo: '',
            nombre: '',
            valoracion: ''
        }
    }),

    getters: {
        getRiesgoById: (state) => (id) => {
            return state.riesgos.find(r => r.id === id);
        },
        // Getter para calcular el nivel de riesgo (si se necesita en frontend)
        getNivelRiesgo: () => (probabilidad, impacto) => {
            const valor = probabilidad * impacto;
            if (valor >= 80) return { nivel: 'Muy Alto', color: 'bg-danger' };
            if (valor >= 48) return { nivel: 'Alto', color: 'bg-warning' };
            if (valor >= 32) return { nivel: 'Medio', color: 'bg-info' };
            return { nivel: 'Bajo', color: 'bg-success' };
        }
    },

    actions: {
        async fetchMisRiesgos() {
            this.loading = true;
            this.error = null;
            try {
                // Construct query parameters from filters
                const params = {};
                if (this.filters.codigo) params.codigo = this.filters.codigo;
                if (this.filters.nombre) params.nombre = this.filters.nombre;
                if (this.filters.valoracion) params.valoracion = this.filters.valoracion;

                const response = await axios.get('/api/riesgos/mis-riesgos', { params });
                this.riesgos = response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al cargar mis riesgos';
                console.error('Error fetching mis riesgos:', error);
            } finally {
                this.loading = false;
            }
        },

        async fetchRiesgoCompleto(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/api/riesgos/${id}/completo`);
                this.riesgoActual = response.data;
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al cargar el riesgo';
                console.error('Error fetching riesgo completo:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async saveRiesgo(data) {
            this.loading = true;
            try {
                let response;
                if (data.id) {
                    response = await axios.put(`/api/riesgos/${data.id}`, data);
                    const index = this.riesgos.findIndex(r => r.id === data.id);
                    if (index !== -1) {
                        this.riesgos[index] = { ...this.riesgos[index], ...response.data };
                    }
                } else {
                    response = await axios.post('/api/riesgos', data);
                    // Si es nuevo, recargar lista para asegurar consistencia
                    await this.fetchMisRiesgos();
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al guardar el riesgo';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteRiesgo(id) {
            this.loading = true;
            try {
                await axios.delete(`/api/riesgos/${id}`);
                this.riesgos = this.riesgos.filter(r => r.id !== id);
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al eliminar el riesgo';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        // Acciones del Plan de Acción
        async fetchAcciones(riesgoId) {
            try {
                const response = await axios.get(`/api/riesgos/${riesgoId}/acciones`);
                return response.data;
            } catch (error) {
                console.error('Error fetching acciones:', error);
                throw error;
            }
        },

        async saveAccion(riesgoId, data) {
            try {
                let response;
                if (data.id) {
                    response = await axios.put(`/api/riesgo-acciones/${data.id}`, data);
                } else {
                    response = await axios.post(`/api/riesgos/${riesgoId}/acciones`, data);
                }
                return response.data;
            } catch (error) {
                console.error('Error saving accion:', error);
                throw error;
            }
        },

        async deleteAccion(id) {
            try {
                await axios.delete(`/api/riesgo-acciones/${id}`);
            } catch (error) {
                console.error('Error deleting accion:', error);
                throw error;
            }
        },

        async updateEvaluacion(id, data) {
            this.loading = true;
            try {
                const response = await axios.put(`/api/riesgos/${id}/evaluacion`, data);
                const index = this.riesgos.findIndex(r => r.id === id);
                if (index !== -1) {
                    this.riesgos[index] = { ...this.riesgos[index], ...response.data };
                }
                if (this.riesgoActual && this.riesgoActual.id === id) {
                    this.riesgoActual = { ...this.riesgoActual, ...response.data };
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar evaluación';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateTratamiento(id, data) {
            this.loading = true;
            try {
                const response = await axios.put(`/api/riesgos/${id}/tratamiento`, data);
                if (this.riesgoActual && this.riesgoActual.id === id) {
                    this.riesgoActual = { ...this.riesgoActual, ...response.data };
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar tratamiento';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateVerificacion(id, data) {
            this.loading = true;
            try {
                const response = await axios.put(`/api/riesgos/${id}/verificacion`, data);
                if (this.riesgoActual && this.riesgoActual.id === id) {
                    this.riesgoActual = { ...this.riesgoActual, ...response.data };
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar verificación';
                throw error;
            } finally {
                this.loading = false;
            }
        }
    }
});

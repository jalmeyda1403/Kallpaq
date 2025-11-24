import { defineStore } from 'pinia';
import axios from 'axios';

export const useRiesgoStore = defineStore('riesgo', {
    state: () => ({
        riesgos: [],
        riesgoActual: null,
        loading: false,
        error: null,
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
                const response = await axios.get('/api/riesgos/mis-riesgos');
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

        async updateEvaluacion(id, data) {
            this.loading = true;
            try {
                const response = await axios.put(`/api/riesgos/${id}/evaluacion`, data);
                // Actualizar el riesgo en la lista local si existe
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
                // Actualizar estado local
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
                // Actualizar estado local
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

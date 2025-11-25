import { defineStore } from 'pinia';
import axios from 'axios';

export const useEncuestasStore = defineStore('encuestas', {
    state: () => ({
        encuestas: [],
        dashboardData: {
            nps_trend: [],
            category_averages: []
        },
        loading: false,
        error: null
    }),

    actions: {
        async fetchEncuestas(filters = {}) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get('/api/encuestas-satisfaccion', { params: filters });
                this.encuestas = response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al cargar las encuestas';
                console.error('Error fetching encuestas:', error);
            } finally {
                this.loading = false;
            }
        },

        async createEncuesta(formData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post('/api/encuestas-satisfaccion', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                this.encuestas.unshift(response.data);
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al crear la encuesta';
                console.error('Error creating encuesta:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateEncuesta(id, formData) {
            this.loading = true;
            this.error = null;
            try {
                // Using POST with _method=PUT or just handling it in controller as POST for file uploads
                const response = await axios.post(`/api/encuestas-satisfaccion/${id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                const index = this.encuestas.findIndex(e => e.id === id);
                if (index !== -1) {
                    this.encuestas[index] = response.data;
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar la encuesta';
                console.error('Error updating encuesta:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteEncuesta(id) {
            this.loading = true;
            this.error = null;
            try {
                await axios.delete(`/api/encuestas-satisfaccion/${id}`);
                this.encuestas = this.encuestas.filter(e => e.id !== id);
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al eliminar la encuesta';
                console.error('Error deleting encuesta:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchDashboardData() {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get('/api/encuestas-satisfaccion/dashboard');
                this.dashboardData = response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al cargar datos del dashboard';
                console.error('Error fetching dashboard data:', error);
            } finally {
                this.loading = false;
            }
        }
    }
});

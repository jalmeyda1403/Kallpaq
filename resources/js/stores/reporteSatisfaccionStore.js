import { defineStore } from 'pinia';
import axios from 'axios';

export const useReporteSatisfaccionStore = defineStore('reporteSatisfaccion', {
    state: () => ({
        reportes: [],
        currentReporte: null,
        loading: false,
        error: null,
        draftData: null, // Datos para el wizard
    }),

    actions: {
        async fetchReportes(filters = {}) {
            this.loading = true;
            try {
                const response = await axios.get('/api/reportes-satisfaccion', { params: filters });
                this.reportes = response.data;
            } catch (error) {
                this.error = error.message;
            } finally {
                this.loading = false;
            }
        },

        async fetchReporte(id) {
            this.loading = true;
            try {
                const response = await axios.get(`/api/reportes-satisfaccion/${id}`);
                this.currentReporte = response.data;
                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async getQuarterData(params) {
            // params: { proceso_id, anio, trimestre }
            this.loading = true;
            try {
                const response = await axios.get('/api/reportes-satisfaccion/quarter-data', { params });
                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async generateDraft(data) {
            this.loading = true;
            try {
                const response = await axios.post('/api/reportes-satisfaccion/generate-draft', data);
                return response.data; // { oportunidades, conclusiones }
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createReporte(data) {
            this.loading = true;
            try {
                data.estado = 'generado';
                data.fecha_generacion = new Date().toISOString().slice(0, 10);
                const response = await axios.post('/api/reportes-satisfaccion', data);
                await this.fetchReportes();
                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateReporte(id, data) {
            this.loading = true;
            try {
                data.estado = 'generado';
                const response = await axios.put(`/api/reportes-satisfaccion/${id}`, data);
                await this.fetchReportes();
                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteReporte(id) {
            this.loading = true;
            try {
                await axios.delete(`/api/reportes-satisfaccion/${id}`);
                await this.fetchReportes();
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async saveReporte(data) {
            // Legacy method for compatibility
            if (data.id) {
                return await this.updateReporte(data.id, data);
            } else {
                return await this.createReporte(data);
            }
        },

        async uploadSigned(id, file) {
            this.loading = true;
            try {
                const formData = new FormData();
                formData.append('archivo', file);

                const response = await axios.post(`/api/reportes-satisfaccion/${id}/upload-signed`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                await this.fetchReportes();
                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        }
    }
});

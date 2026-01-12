import { defineStore } from 'pinia';
import axios from 'axios';

export const useProgramaAuditoriaStore = defineStore('programaAuditoria', {
    state: () => ({
        programas: [],
        currentPrograma: null,
        loading: false,
        error: null,
    }),

    actions: {
        async fetchProgramas(year = null) {
            this.loading = true;
            this.error = null;
            try {
                const params = year ? { year } : {};
                const response = await axios.get('/api/auditoria/programas', { params });
                this.programas = response.data;
            } catch (error) {
                this.error = error;
                console.error('Error fetching programas:', error);
            } finally {
                this.loading = false;
            }
        },

        setPrograma(programa) {
            this.currentPrograma = programa;
        },

        async fetchProgramaById(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/api/programa/${id}`);
                this.currentPrograma = response.data;
                return response.data;
            } catch (error) {
                this.error = error;
                console.error('Error fetching programa:', error);
            } finally {
                this.loading = false;
            }
        },

        clearCurrentPrograma() {
            this.currentPrograma = null;
        }
    },
});

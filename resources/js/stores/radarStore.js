import { defineStore } from 'pinia';
import axios from 'axios';

export const useRadarStore = defineStore('radar', {
    state: () => ({
        normas: [],
        loading: false,
        error: null
    }),

    actions: {
        async fetchNormas() {
            this.loading = true;
            try {
                const response = await axios.get('/api/radar');
                this.normas = response.data;
            } catch (error) {
                this.error = error.response?.data?.message || error.message;
                console.error('Error fetching radar norms:', error);
            } finally {
                this.loading = false;
            }
        },

        async scanNormas() {
            this.loading = true;
            try {
                await axios.post('/api/radar/scan');
                await this.fetchNormas(); // Refresh list
            } catch (error) {
                this.error = error.response?.data?.message || error.message;
                console.error('Error scanning norms:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async approveNorma(id, data) {
            try {
                await axios.post(`/api/radar/${id}/approve`, data);
                await this.fetchNormas(); // Refresh list
            } catch (error) {
                console.error('Error approving norma:', error);
                throw error;
            }
        },

        async rejectNorma(id, reason) {
            try {
                await axios.post(`/api/radar/${id}/reject`, { analisis_humano: reason });
                await this.fetchNormas(); // Refresh list
            } catch (error) {
                console.error('Error rejecting norma:', error);
                throw error;
            }
        }
    }
});

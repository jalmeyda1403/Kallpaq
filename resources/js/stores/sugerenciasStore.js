import { defineStore } from 'pinia';
import axios from 'axios';
import { route } from 'ziggy-js';

export const useSugerenciasStore = defineStore('sugerencias', {
    state: () => ({
        sugerencias: [],
        currentSugerencia: null,
        loading: false,
        error: null
    }),

    getters: {
        getAllSugerencias: (state) => state.sugerencias,
        getCurrentSugerencia: (state) => state.currentSugerencia,
        isLoading: (state) => state.loading,
        hasError: (state) => !!state.error
    },

    actions: {
        async fetchSugerencias(filters = {}) {
            this.loading = true;
            this.error = null;

            try {
                const params = new URLSearchParams();

                if (filters.estado) params.append('estado', filters.estado);
                if (filters.proceso_id) params.append('proceso_id', filters.proceso_id);
                if (filters.proceso_nombre) params.append('proceso_nombre', filters.proceso_nombre);
                if (filters.clasificacion) params.append('clasificacion', filters.clasificacion);

                const response = await axios.get(`${route('api.sugerencias.index')}?${params.toString()}`);
                this.sugerencias = response.data;
            } catch (error) {
                this.error = error.message;
                console.error('Error fetching sugerencias:', error);
            } finally {
                this.loading = false;
            }
        },

        async createSugerencia(data) {
            this.loading = true;
            this.error = null;

            try {
                let payload;
                const config = {};

                if (data instanceof FormData) {
                    payload = data;
                    // Let Axios/Browser handle the Content-Type header with boundary
                } else {
                    // Prepare FormData if it's a plain object
                    payload = new FormData();
                    Object.keys(data).forEach(key => {
                        if (data[key] !== null && data[key] !== undefined) {

                            if (Array.isArray(data[key])) {
                                data[key].forEach((item, index) => {
                                    payload.append(`${key}[${index}]`, item);
                                });
                            } else if (data[key] instanceof File) {
                                payload.append(key, data[key], data[key].name);
                            } else if (typeof data[key] === 'object' && data[key] !== null) {
                                payload.append(key, JSON.stringify(data[key]));
                            } else {
                                payload.append(key, data[key]);
                            }
                        }
                    });
                }

                const response = await axios.post(route('api.sugerencias.store'), payload, config);

                // Update list
                await this.fetchSugerencias();

                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateSugerencia(id, data) {
            this.loading = true;
            this.error = null;

            try {
                let response;

                // Si data es FormData, usamos POST con method spoofing
                if (data instanceof FormData) {
                    // Agregar _method para simular PUT
                    data.append('_method', 'PUT');

                    response = await axios.post(route('api.sugerencias.update', { id }), data, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                } else {
                    // Si es un objeto normal, enviamos con PUT
                    response = await axios.put(route('api.sugerencias.update', { id }), data);
                }

                // Actualizamos la lista de sugerencias
                await this.fetchSugerencias();

                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchSugerenciaById(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(route('api.sugerencias.show', { id }));
                this.currentSugerencia = response.data;
                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteSugerencia(id) {
            this.loading = true;
            this.error = null;

            try {
                await axios.delete(route('api.sugerencias.destroy', { id }));
                // Actualizamos la lista de sugerencias
                await this.fetchSugerencias();
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async validateSugerencia(id, data) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.post(route('api.sugerencias.validate', { id }), data);

                // Actualizamos la lista de sugerencias
                await this.fetchSugerencias();

                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchProcesos() {
            try {
                const response = await axios.get(route('procesos.buscar'));
                return response.data;
            } catch (error) {
                console.error('Error fetching procesos:', error);
                throw error;
            }
        },

        async fetchProcesoById(id) {
            try {
                const response = await axios.get(route('procesos.show', { proceso_id: id }));
                return response.data;
            } catch (error) {
                console.error('Error fetching proceso by ID:', error);
                throw error;
            }
        },

        resetCurrentSugerencia() {
            this.currentSugerencia = null;
        }
    }
});
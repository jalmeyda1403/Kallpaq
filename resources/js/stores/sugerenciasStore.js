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

                if (filters.buscar_sugerencia) params.append('buscar_sugerencia', filters.buscar_sugerencia);
                if (filters.estado) params.append('estado', filters.estado);
                if (filters.proceso_id) params.append('proceso_id', filters.proceso_id);

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
                // Preparamos los datos para enviar al backend
                const formData = new FormData();

                // Agregamos todos los campos
                Object.keys(data).forEach(key => {
                    if (data[key] !== null && data[key] !== undefined) {
                        if (Array.isArray(data[key])) {
                            data[key].forEach((item, index) => {
                                formData.append(`${key}[${index}]`, item);
                            });
                        } else if (data[key] instanceof File) {
                            formData.append(key, data[key], data[key].name);
                        } else if (typeof data[key] === 'object' && data[key] !== null) {
                            formData.append(key, JSON.stringify(data[key]));
                        } else {
                            formData.append(key, data[key]);
                        }
                    }
                });

                const response = await axios.post(route('api.sugerencias.store'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

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

        async updateSugerencia(id, data) {
            this.loading = true;
            this.error = null;

            try {
                let response;
                
                // Si data es FormData, usamos PUT directamente
                if (data instanceof FormData) {
                    response = await axios.put(route('api.sugerencias.update', { id }), data, {
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
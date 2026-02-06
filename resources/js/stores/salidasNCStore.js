import { defineStore } from 'pinia';
import axios from 'axios';
import { route } from 'ziggy-js';

export const useSalidasNCStore = defineStore('salidasNC', {
    state: () => ({
        salidas: [],
        currentSNC: null,
        tratamiento: {},
        loading: false,
        error: null
    }),

    getters: {
        getAllSalidas: (state) => state.salidas,
        getCurrentSNC: (state) => state.currentSNC,
        isLoading: (state) => state.loading,
        hasError: (state) => !!state.error
    },

    actions: {
        async fetchSalidasNC(filters = {}) {
            this.loading = true;
            this.error = null;

            try {
                const params = new URLSearchParams();

                if (filters.buscar_snc) params.append('buscar_snc', filters.buscar_snc);
                if (filters.estado) params.append('estado', filters.estado);
                if (filters.origen) params.append('origen', filters.origen);
                if (filters.clasificacion) params.append('clasificacion', filters.clasificacion);

                const response = await axios.get(`${route('api.salidas-nc.index')}?${params.toString()}`);
                this.salidas = response.data;
            } catch (error) {
                this.error = error.message;
                console.error('Error fetching salidas NC:', error);
            } finally {
                this.loading = false;
            }
        },

        async createSNC(data) {
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

                const response = await axios.post(route('api.salidas-nc.store'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Actualizamos la lista de salidas
                await this.fetchSalidasNC();

                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateSNC(id, data) {
            this.loading = true;
            this.error = null;

            try {
                let response;

                // Si data es FormData, usamos POST con method spoofing
                if (data instanceof FormData) {
                    // Agregar _method para simular PUT
                    data.append('_method', 'PUT');

                    response = await axios.post(route('api.salidas-nc.update', { id }), data, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                } else {
                    // Si es un objeto normal, enviamos con PUT
                    response = await axios.put(route('api.salidas-nc.update', { id }), data);
                }

                // Actualizamos la lista de salidas
                await this.fetchSalidasNC();

                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchSNCById(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get(route('api.salidas-nc.show', { id }));
                this.currentSNC = response.data;
                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteSNC(id) {
            this.loading = true;
            this.error = null;

            try {
                await axios.delete(route('api.salidas-nc.destroy', { id }));
                // Actualizamos la lista de salidas
                await this.fetchSalidasNC();
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchUsers() {
            try {
                const response = await axios.get(route('api.admin.usuarios.index'));
                return response.data;
            } catch (error) {
                console.error('Error fetching users:', error);
                throw error;
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

        async updateTratamiento(sncId, data) {
            this.loading = true;
            this.error = null;

            try {
                let response;

                // Si data es FormData, usamos POST con method spoofing
                if (data instanceof FormData) {
                    // Agregar _method para simular PUT
                    data.append('_method', 'PUT');

                    response = await axios.post(route('api.salidas-nc.update', { id: sncId }), data, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });
                } else {
                    // Si es un objeto normal, enviamos con PUT
                    response = await axios.put(route('api.salidas-nc.update', { id: sncId }), data);
                }

                // No actualizamos manualmente la lista local para evitar inconsistencias.
                // Dejamos que el componente llame a fetchSalidasNC() para recargar con los filtros activos.

                // Actualizamos la SNC actual si coincide
                if (this.currentSNC && this.currentSNC.id === sncId) {
                    this.currentSNC = response.data.data;
                }

                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async validateSNC(sncId, data) {
            this.loading = true;
            this.error = null;

            try {
                // Send as simple object using PUT, controller handles request data automatically
                const response = await axios.put(route('api.salidas-nc.validate', { id: sncId }), data);

                // Actualizamos la SNC actual si coincide
                if (this.currentSNC && this.currentSNC.id === sncId) {
                    this.currentSNC = response.data.data;
                }

                // Actualizamos la lista
                await this.fetchSalidasNC();

                return response.data;
            } catch (error) {
                this.error = error.message;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        resetCurrentSNC() {
            this.currentSNC = null;
        }
    }
});
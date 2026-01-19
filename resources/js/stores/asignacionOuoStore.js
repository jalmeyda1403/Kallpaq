import { defineStore } from 'pinia';
import axios from 'axios';
import { route } from 'ziggy-js';

export const useAsignacionOuoStore = defineStore('asignacionOuo', {
    state: () => ({
        ouos: [],
        ouoPadresForDropdown: [], // New state for parent OUOs dropdown
        loading: false,
        error: null,
        pagination: {
            currentPage: 1,
            perPage: 20,
            total: 0,
            lastPage: 1,
        },
        filters: {
            search: '',
            ouo_padre_id: null,
            estado: '', // New filter state
        },
    }),
    actions: {
        async fetchOuos() {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(route('api.asignacion-ouos.index'), {
                    params: {
                        page: this.pagination.currentPage,
                        per_page: this.pagination.perPage,
                        search: this.filters.search,
                        ouo_padre_id: this.filters.ouo_padre_id,
                        estado: this.filters.estado, // Add estado filter
                    },
                });
                this.ouos = response.data.data;
                this.pagination.total = response.data.total;
                this.pagination.lastPage = response.data.last_page;
                this.pagination.currentPage = response.data.current_page;
            } catch (error) {
                this.error = 'Error al cargar las OUOs.';
                console.error('Error fetching OUOs:', error);
            } finally {
                this.loading = false;
            }
        },
        async fetchOuoPadresForDropdown() {
            try {
                const response = await axios.get(route('api.ouos.padres.dropdown'));
                this.ouoPadresForDropdown = response.data;
            } catch (error) {
                console.error('Error fetching parent OUOs for dropdown:', error);
            }
        },
        setPage(page) {
            this.pagination.currentPage = page;
            this.fetchOuos();
        },
        setPerPage(perPage) {
            this.pagination.perPage = perPage;
            this.fetchOuos();
        },
        setFilter(key, value) {
            this.filters[key] = value;
            this.fetchOuos();
        },
        resetFilters() {
            this.filters = {
                search: '',
                ouo_padre_id: null,
                estado: '', // Reset estado
            };
            this.pagination.currentPage = 1;
            this.fetchOuos();
        },
        async createOuo(ouoData) {
            this.loading = true;
            this.error = null;
            try {
                // Assuming route for store is 'ouos.store' or similar. 
                // Based on standard Laravel resource controller and previous grep: 'ouos.store' exists.
                // Re-checking router list provided earlier: "ouos.store":{"uri":"ouos","methods":["POST"]}
                const response = await axios.post(route('ouos.store'), ouoData);
                this.fetchOuos(); // Refresh list
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al crear la OUO.';
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async updateOuo(id, ouoData) {
            this.loading = true;
            this.error = null;
            try {
                // Based on router list: "ouos.update":{"uri":"ouos/{ouo}","methods":["PUT","PATCH"]}
                const response = await axios.put(route('ouos.update', id), ouoData);
                this.fetchOuos(); // Refresh list
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar la OUO.';
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async downloadTemplate() {
            try {
                const response = await axios.get(route('api.ouos.template'), {
                    responseType: 'blob',
                });
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'ouos_template.xlsx');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error) {
                console.error('Error downloading template:', error);
                throw error;
            }
        },
        async importOuos(formData) {
            this.loading = true;
            try {
                const response = await axios.post(route('api.ouos.import'), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });
                this.fetchOuos();
                return response.data;
            } catch (error) {
                console.error('Error importing OUOs:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
    getters: {
        getOuos: (state) => state.ouos,
        getOuoPadresForDropdown: (state) => state.ouoPadresForDropdown, // New getter
        isLoading: (state) => state.loading,
        hasError: (state) => state.error !== null,
        getErrorMessage: (state) => state.error,
        getPagination: (state) => state.pagination,
        getFilters: (state) => state.filters,
    },
});

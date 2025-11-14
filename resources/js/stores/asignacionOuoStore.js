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
            ouo_padre_id: null, // New filter state
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
                        search: this.filters.search, // Use specific search filter
                        ouo_padre_id: this.filters.ouo_padre_id, // Include ouo_padre_id filter
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
            };
            this.pagination.currentPage = 1;
            this.fetchOuos();
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

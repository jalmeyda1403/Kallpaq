import { defineStore } from 'pinia';
import { ref, reactive } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';

export const useUserStore = defineStore('userStore', () => {
    const users = ref([]);
    const pagination = reactive({
        currentPage: 1,
        perPage: 20,
        total: 0,
        lastPage: 1,
    });
    const filters = reactive({
        search: '',
    });

    const fetchUsers = async () => {
        try {
            const response = await axios.get(route('api.admin.usuarios.index'), {
                params: {
                    page: pagination.currentPage,
                    per_page: pagination.perPage,
                    search: filters.search,
                },
            });
            users.value = response.data.data;
            pagination.total = response.data.total;
            pagination.lastPage = response.data.last_page;
            pagination.currentPage = response.data.current_page;
        } catch (error) {
            console.error('Error fetching users:', error);
            // Handle error appropriately
        }
    };

    const setPage = (page) => {
        pagination.currentPage = page;
        fetchUsers();
    };

    const setPerPage = (perPage) => {
        pagination.perPage = perPage;
        pagination.currentPage = 1; // Reset to first page when perPage changes
        fetchUsers();
    };

    const applyFilters = () => {
        pagination.currentPage = 1;
        fetchUsers();
    };

    const resetFilters = () => {
        filters.search = '';
        pagination.currentPage = 1;
        fetchUsers();
    };

    return {
        users,
        pagination,
        filters,
        fetchUsers,
        setPage,
        setPerPage,
        applyFilters,
        resetFilters,
    };
});

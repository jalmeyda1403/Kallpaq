import { defineStore } from 'pinia';
import { ref, reactive } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';

export const useUserStore = defineStore('userStore', () => {
    const users = ref([]);
    const roles = ref([]); // Store available roles for filter
    const loading = ref(false); // Add loading state
    const pagination = reactive({
        currentPage: 1,
        perPage: 20,
        total: 0,
        lastPage: 1,
    });
    const filters = reactive({
        search: '',
        role: '' // Add role filter
    });
    const sorting = reactive({
        field: 'name',
        order: 1 // 1 for asc, -1 for desc (PrimeVue format)
    });

    const fetchUsers = async () => {
        loading.value = true;
        try {
            const response = await axios.get(route('api.admin.usuarios.index'), {
                params: {
                    page: pagination.currentPage,
                    per_page: pagination.perPage,
                    search: filters.search,
                    role: filters.role, // Send role filter
                    sort_field: sorting.field,
                    sort_order: sorting.order === 1 ? 'asc' : 'desc'
                },
            });
            users.value = response.data.data;
            pagination.total = response.data.total;
            pagination.lastPage = response.data.last_page;
            pagination.currentPage = response.data.current_page;
        } catch (error) {
            console.error('Error fetching users:', error);
            // Handle error appropriately
        } finally {
            loading.value = false;
        }
    };

    const setPage = (page) => {
        pagination.currentPage = page;
        fetchUsers();
    };

    const setSort = (field, order) => {
        sorting.field = field;
        sorting.order = order;
        fetchUsers();
    };

    const setPerPage = (perPage) => {
        if (pagination.perPage !== perPage) {
            pagination.perPage = perPage;
            pagination.currentPage = 1; // Reset to first page when perPage changes
            fetchUsers();
        }
    };

    const applyFilters = () => {
        pagination.currentPage = 1;
        fetchUsers();
    };

    const resetFilters = () => {
        filters.search = '';
        filters.role = '';
        pagination.currentPage = 1;
        fetchUsers();
    };

    const createUser = async (userData) => {
        const formData = new FormData();
        Object.keys(userData).forEach(key => {
            if (userData[key] !== null && userData[key] !== undefined) {
                formData.append(key, userData[key]);
            }
        });

        await axios.post(route('api.admin.usuarios.store'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        fetchUsers();
    };

    const updateUser = async (id, userData) => {
        const formData = new FormData();
        Object.keys(userData).forEach(key => {
            if (userData[key] !== null && userData[key] !== undefined) {
                formData.append(key, userData[key]);
            }
        });
        formData.append('_method', 'PUT');

        await axios.post(route('api.admin.usuarios.update', id), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        fetchUsers();
    };

    const deleteUser = async (id) => {
        await axios.delete(route('api.admin.usuarios.destroy', id));
        fetchUsers();
    };

    const importUsers = async (formData) => {
        await axios.post(route('api.admin.usuarios.import'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        fetchUsers();
    };

    const resetPassword = async (email) => {
        await axios.post(route('api.admin.usuarios.reset-password'), { email });
    };

    const downloadTemplate = async () => {
        try {
            const response = await axios.get(route('api.admin.usuarios.template'), {
                responseType: 'blob',
            });
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'users_template.xlsx');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (error) {
            console.error('Error downloading template:', error);
        }
    };

    const fetchRoles = async () => {
        const response = await axios.get(route('api.admin.roles.index'));
        roles.value = response.data; // Store roles for filter select
        return response.data;
    };

    const assignRoles = async (userId, roles) => {
        await axios.post(route('api.admin.usuarios.assign-roles', userId), { roles });
        // Component will handle fetching to update UI
    };

    return {
        users,
        roles, // Expose roles
        loading,
        pagination,
        filters,
        sorting,
        fetchUsers,
        setPage,
        setSort,
        setPerPage,
        applyFilters,
        resetFilters,
        createUser,
        updateUser,
        deleteUser,
        importUsers,
        resetPassword,
        downloadTemplate,
        fetchRoles,
        assignRoles
    };
});

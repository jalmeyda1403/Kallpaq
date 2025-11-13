import { defineStore } from 'pinia';
import axios from 'axios';
import { route } from 'ziggy-js';

export const useFacilitadorStore = defineStore('facilitador', {
    state: () => ({
        facilitadores: [],
        users: [], // For the user selection dropdown
        form: {
            id: null,
            user_id: '',
            cargo: '',
            estado: 'activo',
            procesos: [], // Para almacenar los procesos asociados en el formulario
        },
        isFormModalOpen: false,
        loading: false,
        errors: {},
    }),

    getters: {
        isEditing: (state) => !!state.form.id,
    },

    actions: {
        async fetchFacilitadores(filters = {}) {
            this.loading = true;
            try {
                const response = await axios.get(route('facilitadores.index'), { params: filters });
                this.facilitadores = response.data;
            } catch (error) {
                console.error('Error fetching facilitadores:', error);
                this.errors = error.response?.data?.errors || { global: 'No se pudieron cargar los facilitadores.' };
            } finally {
                this.loading = false;
            }
        },

        async fetchUsers() {
            // Avoid re-fetching if already populated
            if (this.users.length > 0) return;
            try {
                const response = await axios.get(route('api.users.list'));
                this.users = response.data;
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        },

        openFormModal(facilitador = null) {
            this.resetForm();
            if (facilitador) {
                this.form = { ...facilitador };
                this.form.procesos = facilitador.procesos || [];
            }
            this.isFormModalOpen = true;
        },

        closeFormModal() {
            this.isFormModalOpen = false;
            this.resetForm();
        },

        resetForm() {
            this.form = {
                id: null,
                user_id: '',
                cargo: '',
                estado: 'activo',
                procesos: [],
            };
            this.errors = {};
        },

        async saveFacilitador() {
            this.loading = true;
            this.errors = {};
            try {
                let response;
                if (this.isEditing) {
                    response = await axios.put(route('facilitadores.update', { facilitador: this.form.id }), this.form);
                } else {
                    response = await axios.post(route('facilitadores.store'), this.form);
                }
                this.closeFormModal();
                this.fetchFacilitadores(); // Refrescar la lista
            } catch (error) {
                console.error('Error saving facilitador:', error);
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    this.errors = { global: 'Ocurrió un error al guardar el facilitador.' };
                }
            } finally {
                this.loading = false;
            }
        },

        async deleteFacilitador(id) {
            this.loading = true;
            try {
                await axios.delete(route('facilitadores.destroy', { facilitador: id }));
                this.fetchFacilitadores(); // Refrescar la lista
            } catch (error) {
                console.error('Error deleting facilitador:', error);
                this.errors = error.response?.data?.errors || { global: 'Ocurrió un error al eliminar el facilitador.' };
            } finally {
                this.loading = false;
            }
        },

        async attachProceso(facilitadorId, procesoId) {
            this.loading = true;
            try {
                const response = await axios.post(route('facilitadores.procesos.attach', { facilitador: facilitadorId }), { proceso_id: procesoId });
                // Actualizar la lista de procesos del facilitador en el formulario
                const facilitador = this.facilitadores.find(f => f.id === facilitadorId);
                if (facilitador) {
                    const proceso = await axios.get(route('procesos.show', { proceso_id: procesoId })); // Fetch full proceso data
                    this.form.procesos.push(proceso.data); // Also update form's processes
                }
                return response.data;
            } catch (error) {
                console.error('Error attaching proceso:', error);
                this.errors = error.response?.data?.errors || { global: 'Ocurrió un error al asociar el proceso.' };
                throw error; // Re-throw to be caught by component
            } finally {
                this.loading = false;
            }
        },

        async detachProceso(facilitadorId, procesoId) {
            this.loading = true;
            try {
                const response = await axios.delete(route('facilitadores.procesos.detach', { facilitador: facilitadorId, proceso: procesoId }));
                // Actualizar la lista de procesos del facilitador en el formulario
                const facilitador = this.facilitadores.find(f => f.id === facilitadorId);
                if (facilitador) {
                    facilitador.procesos = facilitador.procesos.filter(p => p.id !== procesoId);
                    this.form.procesos = this.form.procesos.filter(p => p.id !== procesoId); // Also update form's processes
                }
                return response.data;
            } catch (error) {
                console.error('Error detaching proceso:', error);
                this.errors = error.response?.data?.errors || { global: 'Ocurrió un error al desasociar el proceso.' };
                throw error; // Re-throw to be caught by component
            } finally {
                this.loading = false;
            }
        },
    },
});

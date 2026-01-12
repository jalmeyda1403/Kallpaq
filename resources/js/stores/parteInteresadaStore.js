import { defineStore } from 'pinia';
import axios from 'axios';
import Swal from 'sweetalert2';

export const useParteStore = defineStore('parteInteresada', {
    state: () => ({
        partes: [],
        currentParte: null, // For editing
        loading: false,
        error: null,
        // UI States
        showFormModal: false,
        showExpectativaModal: false,
    }),

    actions: {
        async fetchPartes() {
            this.loading = true;
            try {
                const response = await axios.get('/api/partes-interesadas');
                this.partes = response.data;
            } catch (error) {
                console.error("Error fetching partes:", error);
                this.error = error;
                Swal.fire('Error', 'No se pudieron cargar las partes interesadas', 'error');
            } finally {
                this.loading = false;
            }
        },

        async saveParte(form) {
            this.loading = true;
            try {
                let response;
                if (form.id) {
                    response = await axios.put(`/api/partes-interesadas/${form.id}`, form);
                    // Update local list
                    const index = this.partes.findIndex(p => p.id === form.id);
                    if (index !== -1) {
                        // Merge response data to get computed fields back if needed, or just re-fetch
                        // For simplicity, let's re-fetch to get computed attributes correctly
                        await this.fetchPartes();
                    }
                } else {
                    response = await axios.post('/api/partes-interesadas', form);
                    await this.fetchPartes();
                }
                this.showFormModal = false;
                Swal.fire('Guardado', 'Parte interesada guardada correctamente', 'success');
                return response.data;
            } catch (error) {
                console.error("Error saving parte:", error);
                Swal.fire('Error', 'Error al guardar', 'error');
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteParte(id) {
            try {
                const result = await Swal.fire({
                    title: '¿Estás seguro?',
                    text: "No podrás revertir esto",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    await axios.delete(`/api/partes-interesadas/${id}`);
                    this.partes = this.partes.filter(p => p.id !== id);
                    Swal.fire('Eliminado', 'La parte interesada ha sido eliminada.', 'success');
                }
            } catch (error) {
                console.error("Error deleting parte:", error);
                Swal.fire('Error', 'No se pudo eliminar', 'error');
            }
        },

        // Expectations
        async saveExpectativa(form) {
            try {
                if (form.id) {
                    await axios.put(`/api/expectativas/${form.id}`, form);
                } else {
                    await axios.post('/api/expectativas', form);
                }
                // Refresh parent to see new expectations
                await this.fetchPartes();
                Swal.fire('Guardado', 'Expectativa guardada', 'success');
            } catch (error) {
                console.error("Error saving expectativa:", error);
                throw error;
            }
        },

        async deleteExpectativa(id) {
            try {
                const result = await Swal.fire({
                    title: 'Eliminar Expectativa',
                    text: "¿Seguro?",
                    icon: 'warning',
                    showCancelButton: true
                });
                if (result.isConfirmed) {
                    await axios.delete(`/api/expectativas/${id}`);
                    await this.fetchPartes();
                    Swal.fire('Eliminado', 'Expectativa eliminada', 'success');
                }
            } catch (error) {
                console.error("Error deleting expectativa:", error);
                Swal.fire('Error', 'No se pudo eliminar', 'error');
            }
        },

        // Commitments
        async saveCompromiso(form) {
            try {
                if (form.id) {
                    await axios.put(`/api/compromisos/${form.id}`, form);
                } else {
                    await axios.post('/api/compromisos', form);
                }
                await this.fetchPartes(); // Refresh deep structure
                Swal.fire('Guardado', 'Compromiso guardado', 'success');
            } catch (error) {
                console.error("Error saving compromiso:", error);
                throw error;
            }
        },

        openFormModal(parte = null) {
            this.currentParte = parte ? JSON.parse(JSON.stringify(parte)) : {
                pi_nombre: '',
                pi_tipo: 'interna',
                pi_nivel_influencia: 'medio',
                pi_nivel_interes: 'medio',
                pi_descripcion: ''
            };
            this.showFormModal = true;
        }
    }
});

import { defineStore } from 'pinia';
import axios from 'axios';

export const useRiesgoStore = defineStore('riesgo', {
    state: () => ({
        riesgos: [],
        riesgoActual: null,
        loading: false,
        error: null,
        filters: {
            codigo: '',
            nombre: '',
            nivel: '',
            factor: '',
            tipo: ''
        },
        // Modal State
        isModalOpen: false,
        currentTab: 'RiesgoForm',
        modalTitle: 'Nuevo Riesgo',
        isEditing: false,

        // Form State
        riesgoForm: {
            id: null,
            proceso_id: null,
            proceso_nombre: '',
            riesgo_cod: '',
            riesgo_tipo: '',
            riesgo_nombre: '',
            factor_id: null,
            riesgo_matriz: '',
            riesgo_probabilidad: 1,
            riesgo_impacto: 1,
            riesgo_controles: '',
            riesgo_tratamiento: '',
            riesgo_nivel: '', // Calculated in backend but useful to have in state if needed
            riesgo_estado: 'proyecto'
        },
        errors: {},
        // Acciones State
        acciones: [],
        loadingAcciones: false,
        // Especialistas State
        especialistas: [],
        especialistaActual: null,
        loadingAsignaciones: false,
    }),

    getters: {
        getRiesgoById: (state) => (id) => {
            return state.riesgos.find(r => r.id === id);
        },
        // Getter para calcular el nivel de riesgo (si se necesita en frontend)
        getNivelRiesgo: () => (probabilidad, impacto) => {
            const valor = probabilidad * impacto;
            if (valor >= 80) return { nivel: 'Muy Alto', color: 'bg-danger' };
            if (valor >= 48) return { nivel: 'Alto', color: 'bg-warning' };
            if (valor >= 32) return { nivel: 'Medio', color: 'bg-info' };
            return { nivel: 'Bajo', color: 'bg-success' };
        }
    },

    actions: {
        // Modal Actions
        openModal(riesgo = null) {
            this.isModalOpen = true;
            this.errors = {};
            if (riesgo) {
                this.isEditing = true;
                this.modalTitle = 'Editar Riesgo';
                this.riesgoActual = riesgo;
                // Populate form
                this.riesgoForm = { ...riesgo };
                // Ensure nested properties like proceso_nombre are set if they exist in the risk object
                if (riesgo.proceso) {
                    this.riesgoForm.proceso_nombre = riesgo.proceso.proceso_nombre;
                }
            } else {
                this.isEditing = false;
                this.modalTitle = 'Nuevo Riesgo';
                this.riesgoActual = null;
                this.resetForm();
            }
            this.currentTab = 'RiesgoForm';
        },

        closeModal() {
            this.isModalOpen = false;
            this.resetForm();
        },

        setCurrentTab(tab) {
            this.currentTab = tab;
        },

        resetForm() {
            this.riesgoForm = {
                id: null,
                proceso_id: null,
                proceso_nombre: '',
                riesgo_cod: '',
                riesgo_tipo: '',
                riesgo_nombre: '',
                factor_id: null,
                riesgo_matriz: '',
                riesgo_probabilidad: 1,
                riesgo_impacto: 1,
                riesgo_controles: '',
                riesgo_tratamiento: '',
                riesgo_nivel: '',
                riesgo_estado: 'proyecto'
            };
            this.errors = {};
        },

        async fetchMisRiesgos() {
            this.loading = true;
            this.error = null;
            try {
                // Construct query parameters from filters
                const params = {};
                if (this.filters.codigo) params.codigo = this.filters.codigo;
                if (this.filters.nombre) params.nombre = this.filters.nombre;
                if (this.filters.nivel) params.nivel = this.filters.nivel;
                if (this.filters.factor) params.factor = this.filters.factor;
                if (this.filters.tipo) params.tipo = this.filters.tipo;

                const response = await axios.get('/api/riesgos/mis-riesgos', { params });
                this.riesgos = response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al cargar mis riesgos';
                console.error('Error fetching mis riesgos:', error);
            } finally {
                this.loading = false;
            }
        },

        async fetchRiesgoCompleto(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/api/riesgos/${id}/completo`);
                this.riesgoActual = response.data;
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al cargar el riesgo';
                console.error('Error fetching riesgo completo:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async saveRiesgo() {
            this.loading = true;
            this.errors = {};
            try {
                let response;
                const data = this.riesgoForm;

                if (data.id) {
                    response = await axios.put(`/api/riesgos/${data.id}`, data);
                    const index = this.riesgos.findIndex(r => r.id === data.id);
                    if (index !== -1) {
                        // Update the risk in the list with the response data
                        // Ensure we keep the existing structure if response is partial, but usually response is full model
                        // We might need to refresh the list or handle the 'proceso' relation update manually if the backend doesn't return it populated
                        this.riesgos[index] = { ...this.riesgos[index], ...response.data };
                        // If backend returns updated risk, we might need to ensure 'proceso' relationship is there for display
                        if (response.data.proceso_id && !response.data.proceso) {
                            // Optimistic update or fetch again? 
                            // For now, let's assume we might need to re-fetch or just update what we have
                        }
                    }
                } else {
                    response = await axios.post('/api/riesgos', data);
                    // Add new risk to list
                    // this.riesgos.push(response.data); // Or fetchMisRiesgos to be safe with sorting/filtering
                    await this.fetchMisRiesgos();
                }

                this.closeModal();
                // Toast success?
                return response.data;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    this.error = error.response?.data?.message || 'Error al guardar el riesgo';
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteRiesgo(id) {
            this.loading = true;
            try {
                await axios.delete(`/api/riesgos/${id}`);
                this.riesgos = this.riesgos.filter(r => r.id !== id);

            } catch (error) {
                this.error = error.response?.data?.message || 'Error al eliminar el riesgo';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        // Acciones del Plan de Acción
        async fetchAcciones(riesgoId) {
            try {
                const response = await axios.get(`/api/riesgos/${riesgoId}/acciones`);
                return response.data;
            } catch (error) {
                console.error('Error fetching acciones:', error);
                throw error;
            }
        },

        async saveAccion(riesgoId, data) {
            try {
                let response;
                if (data.id) {
                    response = await axios.put(`/api/riesgo-acciones/${data.id}`, data);
                } else {
                    response = await axios.post(`/api/riesgos/${riesgoId}/acciones`, data);
                }
                return response.data;
            } catch (error) {
                console.error('Error saving accion:', error);
                throw error;
            }
        },

        async deleteAccion(id) {
            try {
                await axios.delete(`/api/riesgos/acciones/${id}`);
                await this.fetchAcciones();
                return true;
            } catch (error) {
                console.error('Error deleting accion:', error);
                throw error;
            }
        },

        async reprogramarAccion(id, data) {
            try {
                await axios.post(route('api.riesgos.acciones.reprogramar', { id }), data);
                await this.fetchAcciones();
                return true;
            } catch (error) {
                console.error('Error reprogramando accion:', error);
                throw error;
            }
        },

        // Especialistas Actions
        async fetchAsignaciones() {
            if (!this.riesgoForm.id) return;
            this.loadingAsignaciones = true;
            try {
                // Fetch specialists if not loaded
                if (this.especialistas.length === 0) {
                    const response = await axios.get(route('especialistas.index'));
                    this.especialistas = response.data;
                }

                const response = await axios.get(route('riesgo.asignaciones.listar', { riesgo: this.riesgoForm.id }));
                this.especialistaActual = response.data.actual;
            } catch (error) {
                console.error("Error al cargar las asignaciones:", error);
            } finally {
                this.loadingAsignaciones = false;
            }
        },

        async asignarEspecialista(especialistaId) {
            if (!this.riesgoForm.id) return;
            this.loadingAsignaciones = true;
            try {
                const response = await axios.post(
                    route('riesgo.asignaciones.asignar', { riesgo: this.riesgoForm.id }),
                    {
                        especialista_id: especialistaId,
                        assigned_by_user_id: window.App.user.id,
                        assigned_by_user_name: window.App.user.name
                    }
                );
                this.especialistaActual = response.data.actual;
                // Update local form state if needed
                this.riesgoForm.especialista_id = especialistaId;
            } catch (error) {
                console.error("Error al asignar el especialista:", error);
            } finally {
                this.loadingAsignaciones = false;
            }
        },

        async updateEvaluacion(id, data) {
            this.loading = true;
            try {
                const response = await axios.put(`/api/riesgos/${id}/evaluacion`, data);
                const index = this.riesgos.findIndex(r => r.id === id);
                if (index !== -1) {
                    this.riesgos[index] = { ...this.riesgos[index], ...response.data };
                }
                if (this.riesgoActual && this.riesgoActual.id === id) {
                    this.riesgoActual = { ...this.riesgoActual, ...response.data };
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar evaluación';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateTratamiento(id, data) {
            this.loading = true;
            try {
                const response = await axios.put(`/api/riesgos/${id}/tratamiento`, data);
                if (this.riesgoActual && this.riesgoActual.id === id) {
                    this.riesgoActual = { ...this.riesgoActual, ...response.data };
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar tratamiento';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateVerificacion(id, data) {
            this.loading = true;
            try {
                const response = await axios.put(`/api/riesgos/${id}/verificacion`, data);
                if (this.riesgoActual && this.riesgoActual.id === id) {
                    this.riesgoActual = { ...this.riesgoActual, ...response.data };
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al actualizar verificación';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        // Acciones Methods
        async fetchAcciones(riesgoId) {
            this.loadingAcciones = true;
            try {
                const response = await axios.get(route('api.riesgos.acciones.index', riesgoId));
                this.acciones = response.data;
            } catch (error) {
                console.error('Error fetching acciones:', error);
                this.error = 'Error al cargar los planes de tratamiento';
            } finally {
                this.loadingAcciones = false;
            }
        },

        async createAccion(riesgoId, accionData) {
            this.loadingAcciones = true;
            try {
                const response = await axios.post(route('api.riesgos.acciones.store', riesgoId), accionData);
                this.acciones.push(response.data);
                return response.data;
            } catch (error) {
                console.error('Error creating accion:', error);
                throw error;
            } finally {
                this.loadingAcciones = false;
            }
        },

        async updateAccion(accionId, accionData) {
            this.loadingAcciones = true;
            try {
                const response = await axios.put(route('api.riesgos.acciones.update', accionId), accionData);
                const index = this.acciones.findIndex(a => a.id === accionId);
                if (index !== -1) {
                    this.acciones[index] = response.data;
                }
                return response.data;
            } catch (error) {
                console.error('Error updating accion:', error);
                throw error;
            } finally {
                this.loadingAcciones = false;
            }
        },

        async deleteAccion(accionId) {
            this.loadingAcciones = true;
            try {
                await axios.delete(route('api.riesgos.acciones.destroy', accionId));
                this.acciones = this.acciones.filter(a => a.id !== accionId);
            } catch (error) {
                console.error('Error deleting accion:', error);
                throw error;
            } finally {
                this.loadingAcciones = false;
            }
        },

        async reprogramarAccion(id, formData) {
            this.loadingAcciones = true;
            try {
                const response = await axios.post(route('api.riesgos.acciones.reprogramar', id), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                // Update the specific action in the list
                const index = this.acciones.findIndex(a => a.id === id);
                if (index !== -1) {
                    this.acciones[index] = response.data.accion;
                }
                return response.data;
            } catch (error) {
                console.error('Error reprogramando accion:', error);
                throw error;
            } finally {
                this.loadingAcciones = false;
            }
        }
    }
});

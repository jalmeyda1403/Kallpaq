import { defineStore } from 'pinia';
import axios from 'axios';

export const useRiesgoStore = defineStore('riesgo', {
    state: () => ({
        riesgos: [],
        riesgoActual: null,
        riesgoForm: {
            id: null,
            proceso_id: null,
            proceso_nombre: '',
            riesgo_cod: '',
            riesgo_tipo: '',
            riesgo_nombre: '',
            riesgo_consecuencia: '',
            factor_id: null,
            riesgo_matriz: '',
            riesgo_nivel: '',
            riesgo_estado: '',
            controles_ids: []
        },
        errors: {},
        acciones: [],
        loading: false,
        loadingAcciones: false,
        isModalOpen: false,
        currentTab: 'RiesgoForm',
        modalTitle: 'Nuevo Riesgo',
        isEditing: false,
        isActionPlanMode: false,
        currentScope: null, // Track the current fetch scope (e.g., 'ouo')
        filters: {
            codigo: '',
            nombre: '',
            factor: '',
            tipo: '',
            nivel: '',
            matriz: '',
            especialista_id: ''
        },
        especialistas: [],
        especialistaActual: null,
        loadingAsignaciones: false,
        asignacionesLoadedForRiesgoId: null
    }),

    actions: {
        async fetchMisRiesgos(options = {}) {
            this.loading = true;

            // Update current scope if provided in options
            if (options.scope !== undefined) {
                this.currentScope = options.scope;
            }

            // Prepare params, using currentScope if not explicitly overridden
            const params = { ...this.filters, ...options };
            if (params.scope === undefined && this.currentScope) {
                params.scope = this.currentScope;
            }

            try {
                const response = await axios.get('/api/riesgos/mis-riesgos', { params });
                this.riesgos = response.data;
            } catch (error) {
                console.error('Error fetching riesgos:', error);
            } finally {
                this.loading = false;
            }
        },

        async fetchAcciones(riesgoId) {
            this.loadingAcciones = true;
            try {
                const response = await axios.get(`/api/riesgos/${riesgoId}/acciones`);
                this.acciones = response.data;
            } catch (error) {
                console.error('Error fetching acciones:', error);
                this.acciones = [];
            } finally {
                this.loadingAcciones = false;
            }
        },

        openModal(riesgo = null) {
            this.isModalOpen = true;
            this.errors = {};
            this.asignacionesLoadedForRiesgoId = null; // Reset cache when opening modal
            if (riesgo) {
                this.riesgoActual = riesgo;
                this.riesgoForm = {
                    ...riesgo,
                    proceso_nombre: riesgo.proceso ? riesgo.proceso.proceso_nombre : '',
                    controles_ids: riesgo.controles ? riesgo.controles.map(c => c.id) : []
                };
                this.isEditing = true;
                this.modalTitle = `Editar Riesgo: ${riesgo.id}`;
                this.currentTab = 'RiesgoForm';
            } else {
                this.riesgoActual = null;
                this.riesgoForm = {
                    id: null,
                    proceso_id: null,
                    proceso_nombre: '',
                    riesgo_cod: '',
                    riesgo_tipo: '',
                    riesgo_nombre: '',
                    riesgo_consecuencia: '',
                    factor_id: null,
                    riesgo_matriz: '',
                    riesgo_nivel: '',
                    riesgo_estado: '',
                    controles_ids: []
                };
                this.isEditing = false;
                this.modalTitle = 'Nuevo Riesgo';
                this.currentTab = 'RiesgoForm';
                this.acciones = [];
            }
            // Pre-fetch specialists list early in the background
            if (this.especialistas.length === 0) {
                this.fetchSpecialistsList();
            }
        },

        async fetchSpecialistsList() {
            if (this.especialistas.length > 0) return;
            try {
                const response = await axios.get('/especialistas');
                this.especialistas = response.data;
            } catch (error) {
                console.error('Error fetching specialists list:', error);
            }
        },

        openAccionesModal(riesgo) {
            this.isActionPlanMode = true;
            this.openModal(riesgo);
            this.currentTab = 'RiesgoAcciones';
            this.modalTitle = `Plan de Tratamiento - ${riesgo.riesgo_cod}`;
        },

        closeModal() {
            this.isModalOpen = false;
            this.isActionPlanMode = false;
            this.riesgoActual = null;
            this.isEditing = false;
            this.acciones = [];
            this.currentTab = 'RiesgoForm';
            this.errors = {};
        },

        setCurrentTab(tabName) {
            this.currentTab = tabName;
        },

        async saveRiesgo() {
            this.loading = true;
            this.errors = {};
            try {
                let response;
                if (this.isEditing) {
                    response = await axios.put(`/api/riesgos/${this.riesgoForm.id}`, this.riesgoForm);
                } else {
                    response = await axios.post('/api/riesgos', this.riesgoForm);
                }

                // Update local list
                await this.fetchMisRiesgos();

                // Close modal or update state
                this.closeModal();

                return response.data;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createAccion(riesgoId, data) {
            this.loadingAcciones = true;
            try {
                const response = await axios.post(`/api/riesgos/${riesgoId}/acciones`, data);
                await this.fetchAcciones(riesgoId);
                return response.data;
            } catch (error) {
                console.error('Error creating accion:', error);
                throw error;
            } finally {
                this.loadingAcciones = false;
            }
        },

        async updateAccion(accionId, data) {
            this.loadingAcciones = true;
            try {
                const response = await axios.put(`/api/riesgo-acciones/${accionId}`, data);
                if (this.riesgoActual && this.riesgoActual.id) {
                    await this.fetchAcciones(this.riesgoActual.id);
                }
                return response.data;
            } catch (error) {
                console.error('Error updating accion:', error);
                throw error;
            } finally {
                this.loadingAcciones = false;
            }
        },

        async deleteAccion(id) {
            try {
                await axios.delete(`/api/riesgo-acciones/${id}`);
                if (this.riesgoActual && this.riesgoActual.id) {
                    this.fetchAcciones(this.riesgoActual.id);
                }
            } catch (error) {
                console.error('Error deleting accion:', error);
                throw error;
            }
        },

        async saveAccionAvance(id, formData) {
            this.loadingAcciones = true;
            try {
                const response = await axios.post(`/api/riesgo-acciones/${id}/avance`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (this.riesgoActual && this.riesgoActual.id) {
                    await this.fetchAcciones(this.riesgoActual.id);
                }

                return response.data;
            } catch (error) {
                console.error('Error saving accion avance:', error);
                throw error;
            } finally {
                this.loadingAcciones = false;
            }
        },

        async reprogramarAccion(id, formData) {
            this.loadingAcciones = true;
            try {
                const response = await axios.post(`/api/riesgo-acciones/${id}/reprogramar`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                return response.data;
            } catch (error) {
                console.error('Error in reprogramarAccion:', error);
                throw error;
            } finally {
                this.loadingAcciones = false;
            }
        },

        async aprobarReprogramacion(id, comentario) {
            try {
                const response = await axios.post(`/api/riesgo-acciones/reprogramaciones/${id}/aprobar`, { comentario });
                return response.data;
            } catch (error) {
                console.error('Error approving reprogramming:', error);
                throw error;
            }
        },

        async rechazarReprogramacion(id, comentario) {
            try {
                const response = await axios.post(`/api/riesgo-acciones/reprogramaciones/${id}/rechazar`, { comentario });
                return response.data;
            } catch (error) {
                console.error('Error rejecting reprogramming:', error);
                throw error;
            }
        },

        async improveRiskDescription(text) {
            try {
                const response = await axios.post('/api/riesgos/improve-description', { description: text });
                return response.data.improved_description;
            } catch (error) {
                console.error('Error improving description:', error);
                throw error;
            }
        },

        async improveRiskConsecuencia(description, consecuencia) {
            try {
                const response = await axios.post('/api/riesgos/improve-consecuencia', {
                    risk_description: description,
                    current_consequence: consecuencia
                });
                return response.data.result;
            } catch (error) {
                console.error('Error improving consecuencia:', error);
                throw error;
            }
        },

        async fetchAsignaciones(force = false) {
            if (!this.riesgoActual || !this.riesgoActual.id) return;

            // Optimization: Avoid re-fetching if already loaded for this risk
            if (!force && this.asignacionesLoadedForRiesgoId === this.riesgoActual.id) {
                return;
            }

            this.loadingAsignaciones = true;
            try {
                // Fetch available specialists only if not already loaded
                await this.fetchSpecialistsList();

                // Optimization: If risk already has specialist object, use it instead of API call
                if (this.riesgoActual && this.riesgoActual.especialista) {
                    this.especialistaActual = this.riesgoActual.especialista;
                    this.asignacionesLoadedForRiesgoId = this.riesgoActual.id;
                    this.loadingAsignaciones = false;
                    return;
                }

                // Fetch current assignment (fallback or if not loaded)
                const asignacionResponse = await axios.get(`/api/riesgos/${this.riesgoActual.id}/asignaciones`);
                this.especialistaActual = asignacionResponse.data.actual;
                this.asignacionesLoadedForRiesgoId = this.riesgoActual.id; // Mark as loaded for this risk

            } catch (error) {
                console.error('Error fetching asignaciones:', error);
                this.especialistaActual = null;
                this.asignacionesLoadedForRiesgoId = null;
            } finally {
                this.loadingAsignaciones = false;
            }
        },

        async asignarEspecialista(especialistaId) {
            if (!this.riesgoActual || !this.riesgoActual.id) return;

            try {
                const response = await axios.post(`/api/riesgos/${this.riesgoActual.id}/asignaciones`, {
                    especialista_id: especialistaId
                });
                this.especialistaActual = response.data.actual;
                // this.asignacionesLoadedForRiesgoId = null; // Removed to prevent reload on tab switch, assuming response is up to date
                // Optionally show a success message or notification
            } catch (error) {
                console.error('Error assigning especialista:', error);
                throw error;
            }
        },

        async saveVerificacion(formData) {
            this.loading = true;
            try {
                if (!this.riesgoActual || !this.riesgoActual.id) throw new Error("No hay riesgo seleccionado");

                const response = await axios.post(`/api/riesgos/${this.riesgoActual.id}/verificacion`, formData);

                // Update local risk data
                this.riesgoActual = response.data;

                // Also update the list to reflect changes
                await this.fetchMisRiesgos();

                return response.data;
            } catch (error) {
                console.error('Error saving verificacion:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        }
    }
});

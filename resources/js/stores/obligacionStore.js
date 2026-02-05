import { defineStore } from 'pinia';
import axios from 'axios';
import { route } from 'ziggy-js';

export const useObligacionStore = defineStore('obligacion', {
    state: () => ({
        obligaciones: [],
        loading: false,
        error: null,
        selectedObligacion: null,
        showObligacionModal: false,
        showRiesgosModal: false,
        riesgos: [],
        filters: {
            documento: '',
            proceso: '',
            fuente: '',
        },
        // Master lists for dropdowns
        areas: [],
        subareas: [],
        procesos: [],
        loadingListas: false,
        // Otros estados que puedas necesitar para el formulario de obligación
        form: {
            id: null,
            proceso_id: null,
            proceso_nombre: '',
            area_compliance_id: null,
            area_compliance_nombre: '',
            subarea_compliance_id: null, // Add field
            obligacion_documento: '',
            obligacion_principal: '',
            obligacion_consecuencia: '',
            obligacion_documento_deroga: '',
            obligacion_estado: 'pendiente',
            obligacion_tipo: 'Legal',
            obligacion_frecuencia: null,
            obligacion_fecha_identificacion: null,
        },
    }),

    actions: {
        async fetchObligaciones() {
            this.loading = true;
            this.obligaciones = []; // Clear cached data
            try {
                const response = await axios.get(route('api.obligaciones.index'), {
                    params: this.filters
                });
                this.obligaciones = response.data;
                this.error = null;
            } catch (error) {
                this.error = 'Error al cargar las obligaciones.';
                console.error('Error fetching obligaciones:', error);
            } finally {
                this.loading = false;
            }
        },

        async fetchMisObligaciones(params = {}) {
            this.loading = true;
            this.obligaciones = []; // Clear cached data
            try {
                const response = await axios.get(route('api.obligaciones.mine'), {
                    params: { ...this.filters, ...params }
                });
                this.obligaciones = response.data;
                this.error = null;
            } catch (error) {
                this.error = 'Error al cargar mis obligaciones.';
                console.error('Error fetching mis obligaciones:', error);
            } finally {
                this.loading = false;
            }
        },

        async fetchObligacion(id) {
            this.loading = true;
            try {
                const response = await axios.get(route('api.obligaciones.show', id));
                this.selectedObligacion = response.data;
                this.error = null;
                return response.data;
            } catch (error) {
                this.error = 'Error al cargar la obligación.';
                console.error('Error fetching obligacion:', error);
            } finally {
                this.loading = false;
            }
        },

        async saveObligacion(obligacionData) {
            this.loading = true;
            try {
                let response;
                if (obligacionData.id) {
                    response = await axios.put(route('api.obligaciones.update', obligacionData.id), obligacionData);
                } else {
                    response = await axios.post(route('api.obligaciones.store'), obligacionData);
                }
                this.fetchObligaciones(); // Recargar la lista
                this.error = null;
                return response.data;
            } catch (error) {
                this.error = 'Error al guardar la obligación.';
                console.error('Error saving obligacion:', error);
                throw error; // Re-throw para que el componente pueda manejarlo
            } finally {
                this.loading = false;
            }
        },

        async deleteObligacion(id) {
            this.loading = true;
            try {
                await axios.delete(route('api.obligaciones.destroy', id));
                this.fetchObligaciones(); // Recargar la lista
                this.error = null;
            } catch (error) {
                this.error = 'Error al eliminar la obligación.';
                console.error('Error deleting obligacion:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchRiesgos(obligacionId) {
            this.loading = true;
            try {
                const response = await axios.get(route('api.obligaciones.listariesgos', obligacionId));
                this.riesgos = response.data;
                this.error = null;
            } catch (error) {
                this.error = 'Error al cargar los riesgos.';
                console.error('Error fetching riesgos:', error);
            } finally {
                this.loading = false;
            }
        },

        openObligacionModal(obligacion = null) {
            this.selectedObligacion = obligacion;
            if (obligacion) {
                // Llenar el formulario con los datos de la obligación para editar
                this.form.id = obligacion.id;
                this.form.proceso_id = obligacion.proceso_id;
                this.form.proceso_nombre = obligacion.proceso.proceso_nombre; // Asumiendo que la relación 'proceso' está cargada
                this.form.area_compliance_id = obligacion.area_compliance_id;
                this.form.area_compliance_nombre = obligacion.area_compliance?.area_compliance_nombre;
                this.form.subarea_compliance_id = obligacion.subarea_compliance_id; // Add mapping
                this.form.obligacion_documento = obligacion.obligacion_documento;
                this.form.obligacion_principal = obligacion.obligacion_principal;
                this.form.obligacion_consecuencia = obligacion.obligacion_consecuencia;
                this.form.obligacion_documento_deroga = obligacion.obligacion_documento_deroga;
                this.form.obligacion_estado = obligacion.obligacion_estado;
                this.form.obligacion_tipo = obligacion.obligacion_tipo;
                this.form.obligacion_frecuencia = obligacion.obligacion_frecuencia;
                this.form.obligacion_fecha_identificacion = obligacion.obligacion_fecha_identificacion;

                // Mapear IDs de procesos para el componente de asociación
                if (obligacion.procesos) {
                    this.form.procesos_ids = obligacion.procesos.map(p => p.id);
                } else {
                    this.form.procesos_ids = [];
                }
            } else {
                // Resetear el formulario para una nueva obligación
                this.resetForm();
            }
            this.showObligacionModal = true;
        },

        closeObligacionModal() {
            this.showObligacionModal = false;
            this.resetForm();
        },

        openRiesgosModal(obligacionId) {
            this.fetchRiesgos(obligacionId);
            this.showRiesgosModal = true;
        },

        closeRiesgosModal() {
            this.showRiesgosModal = false;
            this.riesgos = []; // Limpiar riesgos al cerrar
        },

        resetForm() {
            this.form = {
                id: null,
                proceso_id: null,
                proceso_nombre: '',
                area_compliance_id: null,
                area_compliance_nombre: '',
                subarea_compliance_id: null, // Initial state
                obligacion_documento: '',
                obligacion_principal: '',
                obligacion_consecuencia: '',
                obligacion_documento_deroga: '',
                obligacion_estado: 'pendiente',
                obligacion_tipo: 'legal',
                obligacion_frecuencia: null,
                obligacion_fecha_identificacion: null,
            };
        },

        setFilter(key, value) {
            this.filters[key] = value;
        },

        clearFilters() {
            this.filters = {
                documento: '',
                proceso: '',
                fuente: '',
            };
            this.fetchObligaciones();
        },

        async ensureListas() {
            if (this.areas.length > 0 && this.subareas.length > 0 && this.procesos.length > 0) return;

            this.loadingListas = true;
            try {
                const [procRes, areaRes, subRes] = await Promise.all([
                    axios.get('/api/procesos'),
                    axios.get('/api/areas-compliance'),
                    axios.get('/api/subareas-compliance')
                ]);
                this.procesos = procRes.data;
                this.areas = areaRes.data;
                this.subareas = subRes.data;
            } catch (error) {
                console.error('Error loading master lists:', error);
            } finally {
                this.loadingListas = false;
            }
        }
    },
});

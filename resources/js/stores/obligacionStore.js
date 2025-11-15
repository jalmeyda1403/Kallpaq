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
        // Otros estados que puedas necesitar para el formulario de obligación
        form: {
            id: null,
            proceso_id: null,
            proceso_nombre: '',
            area_compliance_id: null,
            area_compliance_nombre: '',
            documento_tecnico_normativo: '',
            obligacion_principal: '',
            obligacion_controles: '',
            consecuencia_incumplimiento: '',
            documento_deroga: '',
            estado_obligacion: 'pendiente',
        },
    }),

    actions: {
        async fetchObligaciones() {
            this.loading = true;
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
                this.form.area_compliance_nombre = obligacion.area_compliance.area_compliance_nombre; // Asumiendo que la relación 'area_compliance' está cargada
                this.form.documento_tecnico_normativo = obligacion.documento_tecnico_normativo;
                this.form.obligacion_principal = obligacion.obligacion_principal;
                this.form.obligacion_controles = obligacion.obligacion_controles;
                this.form.consecuencia_incumplimiento = obligacion.consecuencia_incumplimiento;
                this.form.documento_deroga = obligacion.documento_deroga;
                this.form.estado_obligacion = obligacion.estado_obligacion;
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
                documento_tecnico_normativo: '',
                obligacion_principal: '',
                obligacion_controles: '',
                consecuencia_incumplimiento: '',
                documento_deroga: '',
                estado_obligacion: 'pendiente',
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
        }
    },
});

import { defineStore } from 'pinia';
import { route } from 'ziggy-js';
import axios from 'axios';

export const useHallazgoStore = defineStore('hallazgo', {
    state: () => ({
        // Estado para el formulario principal del hallazgo
        hallazgoForm: {
            id: null,
            auditor_id: null,
            hallazgo_cod: '',
            hallazgo_resumen: '',
            hallazgo_descripcion: '',
            hallazgo_criterio: '',
            hallazgo_clasificacion: 'NCMe', // Valor por defecto
            hallazgo_origen: 'Interno',      // Valor por defecto
            hallazgo_estado: 'Pendiente',    // Valor por defecto
            hallazgo_fecha_identificacion: new Date().toISOString().slice(0, 10),
            hallazgo_fecha_asignacion: '',
            hallazgo_evidencia: '',
            hallazgo_sig: [],
        },

        // Estado para la interfaz y el control del modal
        modalTitle: '',
        isModalOpen: false,
        loading: false,
        errors: {},
        currentTab: 'HallazgoForm', // Pestaña inicial del modal

        // Datos para los selects
        procesos: [],
        auditores: [],

        //Estado para Procesos
        associatedProcesos: [],
        loadingProcesos: false,
        procesosCargadasParaDocumentoId: null,

        //Estado Asignacion Especlistas
        especialistaActual: null,
        historialAsignaciones: [],
        loadingAsignaciones: false,
        especialistas: [],

        //Estado Planes de Acción
        isGestionPlanModalOpen: false,
        procesoParaGestionar: null,
        loadingPlan: false,

        //Estado Analisis de Causa Raiz
        causaRaiz: {
            hc_metodo: 'cinco_porques',
        },
        isCausaRaizModalOpen: false, // New state property
        accionesDelPlan: [],
        todasLasAcciones: [], // Nueva propiedad para almacenar todas las acciones de un hallazgo

    }),

    getters: {
        // Getter para saber si estamos en modo edición
        isEditing: (state) => !!state.hallazgoForm.id,

        // Getter para verificar si las acciones y el análisis de causa raíz están permitidos
        accionesPermitidas: (state) => {
            const estado = state.hallazgoForm.hallazgo_estado;
            return estado === 'creado' || estado === 'modificado' || estado === 'evaluado';
        },

        // Getter para verificar si las acciones de gestión (reprogramar y concluir) están permitidas
        gestionAccionesPermitida: (state) => {
            const estado = state.hallazgoForm.hallazgo_estado;
            // Las acciones de gestión están deshabilitadas para 'creado', 'modificado' y 'desestimado'
            return !['creado', 'modificado', 'desestimado'].includes(estado);
        },
    },

    actions: {
        // Cambia la pestaña activa en el modal
        setCurrentTab(tabName) {
            // Previene cambiar de pestaña si el hallazgo no ha sido creado primero
            if (!this.isEditing) {
                alert('Primero debe guardar la información general del hallazgo.');
                return;
            }
            this.currentTab = tabName;
        },
        // Helper para formatear fechas para los inputs de tipo "date"
        formatDateForInput(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },

        // Carga los datos necesarios para los selects del formulario
        async loadGlobalData() {
            if (this.procesos.length > 0 && this.especialistas.length > 0 && this.auditores.length > 0) return; // Evita recargar si ya existen datos
            this.loading = true;
            try {
                const [especialistasResponse, auditoresResponse] = await Promise.all([
                    axios.get(route('especialistas.index')), // Fetch specialists
                    axios.get(route('usuario.auditores')) // Fetch auditors
                ]);

                this.especialistas = especialistasResponse.data; // Store specialists
                this.auditores = auditoresResponse.data; // Store auditors
                this.errors = {};
            } catch (error) {
                this.errors.global = 'No se pudieron cargar los datos necesarios.';
            } finally {
                this.loading = false;
            }
        },

        // Abre el modal, ya sea para crear uno nuevo o para editar
        async openModal(hallazgo = null) {
            this.resetForm();
            this.loadGlobalData();

            this.currentTab = 'HallazgoForm';

            if (hallazgo) {
                this.modalTitle = hallazgo.id ? 'Editar Solicitud de Mejora' : 'Nueva Solicitud de Mejora';
                await this.fetchHallazgo(hallazgo.id); // Carga los datos completos del hallazgo
            } else {
                this.modalTitle = 'Registrar Nueva Solicitud de Mejora';
            }
            this.isModalOpen = true;
        },

        // Cierra el modal y resetea el formulario
        closeModal() {
            this.isModalOpen = false;
            this.resetForm();
        },

        // Obtiene los datos de un hallazgo específico desde la API
        async fetchHallazgo(id) {
            try {
                this.loading = true;
                const response = await axios.get(route('hallazgo.show', { hallazgo: id }));
                const data = response.data;

                // Asigna los datos al formulario, formateando las fechas
                Object.assign(this.hallazgoForm, data);
                this.hallazgoForm.hallazgo_fecha_identificacion = this.formatDateForInput(data.hallazgo_fecha_identificacion);
                this.hallazgoForm.hallazgo_fecha_asignacion = this.formatDateForInput(data.hallazgo_fecha_asignacion);
                this.hallazgoForm.hallazgo_sig = data.hallazgo_sig || [];

                // Verifica si el campo auditor está en la respuesta y actualiza el ID
                if (data.auditor && data.auditor.id) {
                    this.hallazgoForm.auditor_id = data.auditor.id;
                }

                this.errors = {};

            } catch (error) {
                console.error('Error al cargar el hallazgo:', error);
                this.errors.fetch = 'No se pudo cargar la solicitud.';
            } finally {
                this.loading = false;
            }
        },

        // Guarda un hallazgo nuevo o actualiza uno existente
        async saveHallazgo() {
            this.loading = true;
            this.errors = {};
            try {
                let response;
                if (this.isEditing) {
                    // Actualizar
                    response = await axios.put(route('hallazgo.update', { hallazgo: this.hallazgoForm.id }), this.hallazgoForm);
                } else {
                    // Crear
                    response = await axios.post(route('hallazgo.store'), this.hallazgoForm);
                }

                this.closeModal();
                // Opcional: emitir un evento global para que la tabla principal se recargue
                window.dispatchEvent(new CustomEvent('hallazgos-actualizados'));
                alert('Hallazgo guardado con éxito.');

            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    console.error("Error al guardar el hallazgo:", error);
                    alert("Ocurrió un error inesperado.");
                }
            } finally {
                this.loading = false;
            }
        },

        // Metodos para asociar Procesos
        async fetchAssociatedProcesos(force = false) {
            // Es una buena práctica asegurarse de que tenemos un ID antes de continuar.
            if (!this.hallazgoForm.id) return;
            if (!force && this.procesosCargadasParaDocumentoId === this.hallazgoForm.id) {
                return;
            }
            this.loadingProcesos = true;
            try {

                const response = await axios.get(route('hallazgo.procesos.listar', {
                    hallazgo: this.hallazgoForm.id
                }));

                this.associatedProcesos = response.data;
                this.procesosCargadasParaDocumentoId = this.hallazgoForm.id;
            } catch (error) {
                console.error("Error al cargar los procesos asociados:", error);
            } finally {
                this.loadingProcesos = false;
            }
        },
        async asociarProceso(procesoId) {

            if (!this.hallazgoForm.id) {
                alert('Por favor, guarde primero el hallazgo antes de asociar procesos.');
                return;
            }
            this.loadingProcesos = true;
            try {
                await axios.post(
                    route('hallazgo.procesos.asociar', { hallazgo: this.hallazgoForm.id }),
                    { proceso_id: procesoId } // <-- ESTA ES LA PARTE QUE FALTABA
                );
                // CRÍTICO: Refresca el estado para mantener la UI sincronizada
                await this.fetchAssociatedProcesos(true);
            } catch (error) {
                console.error("Error al asociar el proceso:", error);
                alert('Error: ' + (error.response?.data?.message || error.message));
            } finally {
                this.loadingProcesos = false;
            }
        },
        async disociarProceso(procesoId) {
            if (!this.hallazgoForm.id || !confirm('¿Está seguro?')) return;
            this.loadingProcesos = true;
            try {
                await axios.delete(route('hallazgo.procesos.disociar', { hallazgo: this.hallazgoForm.id, proceso: procesoId }));
                await this.fetchAssociatedProcesos(true); // Recargar la lista
            } catch (error) {
                console.error("Error al desasociar el proceso:", error);
            } finally {
                this.loadingProcesos = false;
            }
        },

        //Metodos para asignar especialista
        async fetchAsignaciones() {
            if (!this.hallazgoForm.id) return;
            this.loadingAsignaciones = true;
            try {
                const response = await axios.get(route('hallazgo.asignaciones.listar', { hallazgo: this.hallazgoForm.id }));
                this.especialistaActual = response.data.actual;
                this.historialAsignaciones = response.data.historial;
            } catch (error) {
                console.error("Error al cargar las asignaciones:", error);
            } finally {
                this.loadingAsignaciones = false;
            }
        },
        async asignarEspecialista(especialistaId) {
            if (!this.hallazgoForm.id) return;
            this.loadingAsignaciones = true;
            try {
                const response = await axios.post(
                    route('hallazgo.asignaciones.asignar', { hallazgo: this.hallazgoForm.id }),
                    {
                        especialista_id: especialistaId,
                        assigned_by_user_id: window.App.user.id, // Pass the current user's ID
                        assigned_by_user_name: window.App.user.name // Pass the current user's name
                    }
                );
                this.especialistaActual = response.data.actual;
                this.historialAsignaciones = response.data.historial;

                // IMPORTANT: Update hallazgoForm.especialista_id to reflect the new assignment
                this.hallazgoForm.especialista_id = especialistaId;
            } catch (error) {
                console.error("Error al asignar el especialista:", error);
            } finally {
                this.loadingAsignaciones = false;
            }
        },

        //Metodos para Gestionar Plan de Acción
        async fetchPlanDeAccionCompleto() {
            if (!this.hallazgoForm.id || !this.procesoParaGestionar) return;

            this.loadingPlan = true;
            try {
                const [causasResponse, accionesResponse] = await Promise.all([
                    axios.get(route('hallazgos.causas.listar', { hallazgo: this.hallazgoForm.id })),
                    axios.get(route('hallazgos.acciones.listar', { hallazgo: this.hallazgoForm.id, proceso: this.procesoParaGestionar.id }))
                ]);

                if (causasResponse.data) {
                    this.causaRaiz = causasResponse.data;
                } else {
                    // Si no hay causa guardada, resetea al estado inicial
                    this.causaRaiz = { hc_metodo: 'cinco_porques' };
                }
                this.accionesDelPlan = accionesResponse.data;

            } catch (error) {
                console.error("Error al cargar el plan de acción:", error);
                alert("No se pudo cargar la información del plan de acción.");
            } finally {
                this.loadingPlan = false;
            }
        },

        async saveAccion(accionData) {
            try {
                let response;
                if (accionData.id) { // If accionData has an ID, it's an update
                    response = await axios.put(
                        route('acciones.update', { accion: accionData.id }),
                        accionData
                    );
                } else { // Otherwise, it's a new action
                    // Note: This legacy method might need adjustment if used directly. 
                    // Prefer createAccion and updateAccion wrappers.
                    response = await axios.post(
                        route('hallazgos.acciones.store', { hallazgo: this.hallazgoForm.id, proceso: this.procesoParaGestionar?.id || accionData.proceso_id }),
                        accionData
                    );
                }

                // Refrescamos solo la lista de acciones para una respuesta más rápida
                // If we are in the context of managing a specific process
                if (this.procesoParaGestionar) {
                    const responseList = await axios.get(route('hallazgos.acciones.listar', { hallazgo: this.hallazgoForm.id, proceso: this.procesoParaGestionar.id }));
                    this.accionesDelPlan = responseList.data;
                }

                return response.data;
            } catch (error) {
                console.error("Error al guardar la acción:", error);
                if (error.response) {
                    console.error("Error response data:", error.response.data);
                    console.error("Error response status:", error.response.status);
                }
                throw error;
            } finally {
            }
        },

        async createAccion(hallazgoId, data) {
            try {
                const response = await axios.post(route('hallazgos.acciones.store', {
                    hallazgo: hallazgoId,
                    proceso: data.proceso_id
                }), data);
                return response.data;
            } catch (error) {
                console.error("Error al crear la acción:", error);
                throw error;
            }
        },

        async updateAccion(accionId, data) {
            try {
                const response = await axios.put(route('acciones.update', { accion: accionId }), data);
                return response.data;
            } catch (error) {
                console.error("Error al actualizar la acción:", error);
                throw error;
            }
        },

        async saveAccionAvance(accionId, formData) {
            try {
                const response = await axios.post(route('acciones.avance', { accion: accionId }), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                return response.data;
            } catch (error) {
                console.error("Error al registrar avance:", error);
                throw error;
            }
        },

        async deleteAccion(accionId) {
            // Confirmación debe ser manejada por la vista (componente)
            try {
                await axios.delete(route('acciones.destroy', { accion: accionId }));

                // Refrescamos la lista SOLO si estamos en el contexto de gestionar un proceso específico
                if (this.procesoParaGestionar) {
                    const response = await axios.get(route('hallazgos.acciones.listar', { hallazgo: this.hallazgoForm.id, proceso: this.procesoParaGestionar.id }));
                    this.accionesDelPlan = response.data;
                }
            } catch (error) {
                console.error("Error al eliminar la acción:", error);
                if (error.response) {
                    console.error("Error response data:", error.response.data);
                    console.error("Error response status:", error.response.status);
                }
                throw error; // Re-lanzar para que el componente maneje la UI de error
            } finally {
            }
        },

        //Metodos para Análisis de causa raíz
        async saveCausaRaiz() {
            this.loadingPlan = true;
            try {
                const response = await axios.post(
                    route('hallazgos.causas.storeOrUpdate', { hallazgo: this.hallazgoForm.id }),
                    this.causaRaiz
                );
                this.causaRaiz = response.data;
                alert("Análisis de causa guardado con éxito.");
            } catch (error) {
                console.error("Error al guardar el análisis de causa:", error);
                alert("Ocurrió un error al guardar el análisis.");
            } finally {
                this.loadingPlan = false;
            }
        },

        //Modales
        openGestionPlanModal(proceso) {
            this.procesoParaGestionar = proceso;
            this.isGestionPlanModalOpen = true;
            this.fetchPlanDeAccionCompleto(); // Carga todo lo necesario al abrir
        },
        closeGestionPlanModal() {
            this.isGestionPlanModalOpen = false;
            this.procesoParaGestionar = null; // Limpiar el contexto
        },
        openCausaRaizModal() { // New action
            this.isCausaRaizModalOpen = true;
        },
        closeCausaRaizModal() { // New action
            this.isCausaRaizModalOpen = false;
        },


        // Resetea el formulario a sus valores por defecto
        resetForm() {
            this.hallazgoForm = {
                id: null,
                hallazgo_cod: '',
                proceso_id: null,
                especialista_id: null,
                hallazgo_resumen: '',
                hallazgo_descripcion: '',
                hallazgo_criterio: '',
                hallazgo_clasificacion: 'Ncme',
                hallazgo_origen: 'IN',
                hallazgo_estado: 'Pendiente',
                hallazgo_fecha_identificacion: new Date().toISOString().slice(0, 10),
                hallazgo_fecha_asignacion: '',
                hallazgo_evidencia: '',
                hallazgo_sig: [],
            };

            this.loading = false;
            this.errors = {};
            this.modalTitle = '';
            this.currentTab = 'HallazgoForm';
            this.especialistaActual = null;
            this.historialAsignaciones = [];
            this.loadingAsignaciones = false;
            this.isGestionPlanModalOpen = false;
            this.procesoParaGestionar = null;
            this.isCausaRaizModalOpen = false; // Reset new state property
            this.causaRaiz = { hc_metodo: 'cinco_porques' };
            this.accionesDelPlan = [];
            this.loadingPlan = false;
        },
        async fetchAcciones() {
            if (!this.hallazgoForm.id || !this.procesoParaGestionar) return;
            const response = await axios.get(route('hallazgos.acciones.listar', { hallazgo: this.hallazgoForm.id, proceso: this.procesoParaGestionar.id }));
            this.accionesDelPlan = response.data;
        },
        // Nueva acción para la vista general de Planes de Acción
        async fetchTodasLasAcciones(hallazgoId) {
            this.loading = true;
            try {
                const response = await axios.get(route('api.acciones.por-hallazgo', { hallazgo: hallazgoId }));
                this.todasLasAcciones = response.data;
            } catch (error) {
                console.error("Error al cargar todas las acciones:", error);
                this.errors.fetch = 'No se pudieron cargar las acciones.';
            } finally {
                this.loading = false;
            }
        },
        async fetchCausaRaiz(hallazgoId) {
            try {
                const response = await axios.get(route('hallazgos.causas.listar', { hallazgo: hallazgoId }));
                if (response.data) {
                    this.causaRaiz = response.data;
                } else {
                    this.causaRaiz = { hc_metodo: 'cinco_porques' };
                }
            } catch (error) {
                console.error("Error al cargar el análisis de causa:", error);
                this.causaRaiz = { hc_metodo: 'cinco_porques' };
            }
        },
        /**
         * Método optimizado que obtiene hallazgo, acciones y causa raíz en una sola llamada
         * Esto reduce significativamente el tiempo de carga al evitar múltiples peticiones HTTP
         */
        async fetchPlanesAccionCompleto(hallazgoId) {
            this.loading = true;
            try {
                const response = await axios.get(route('api.hallazgos.planes-accion-completo', { hallazgo: hallazgoId }));

                // Asignar los datos recibidos al estado
                if (response.data.hallazgo) {
                    Object.assign(this.hallazgoForm, response.data.hallazgo);
                    this.hallazgoForm.hallazgo_fecha_identificacion = this.formatDateForInput(response.data.hallazgo.hallazgo_fecha_identificacion);
                    this.hallazgoForm.hallazgo_fecha_asignacion = this.formatDateForInput(response.data.hallazgo.hallazgo_fecha_asignacion);
                    this.hallazgoForm.hallazgo_sig = response.data.hallazgo.hallazgo_sig || [];
                }

                this.todasLasAcciones = response.data.acciones || [];
                this.causaRaiz = response.data.causaRaiz || { hc_metodo: 'cinco_porques' };

            } catch (error) {
                console.error("Error al cargar los datos completos de planes de acción:", error);
                this.errors.fetch = 'No se pudieron cargar los datos.';
            } finally {
                this.loading = false;
            }
        }
    },
});
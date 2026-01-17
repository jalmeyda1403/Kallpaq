import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuditoriaEjecucionStore = defineStore('auditoriaEjecucion', {
    state: () => ({
        auditId: null,
        agendaItems: [], // Now contains everything (status, checklists, auditor)
        loading: false,
        selectedExecutionId: null, // This is now agenda_id
        // Cache detailed execution data (agenda item with checklists)
        executionDetails: {}
    }),

    getters: {
        // Renamed/Simplified for clarity, though we can keep name for compatibility if needed.
        // But since we are refactoring, let's just expose agendaItems directly or sorted.
        getExecutionList: (state) => state.agendaItems,

        currentExecutionDetail: (state) => {
            if (!state.selectedExecutionId) return null;
            return state.executionDetails[state.selectedExecutionId] || null;
        },

        // Get auditor name consistently
        getAuditorName: (state) => (id) => {
            // Find in details or list
            let item = state.executionDetails[id] || state.agendaItems.find(a => a.id === id);

            if (!item) return 'Cargando...';

            if (['apertura', 'cierre', 'gabinete'].includes(item.aea_tipo)) {
                return 'Equipo Auditor';
            }

            // Relationship is direct now: agenda item -> auditor -> user
            if (item.auditor && item.auditor.user) {
                return item.auditor.user.name;
            }

            return 'Por definir';
        },

        getAuditorForAgenda: (state) => (item) => {
            if (['apertura', 'cierre', 'gabinete'].includes(item.aea_tipo)) {
                return 'Equipo Auditor';
            }
            if (item.auditor && item.auditor.user) {
                return item.auditor.user.name;
            }
            return 'Por definir';
        },

        getRequirementsForAgenda: (state) => (item) => {
            if (!item.aea_requisito) return '';
            if (Array.isArray(item.aea_requisito)) {
                return item.aea_requisito.map(r => r.numeral || r.label).join(', ');
            }
            return item.aea_requisito;
        },

        getProcessName: (state) => (id) => {
            let item = state.executionDetails[id] || state.agendaItems.find(a => a.id === id);
            if (!item) return 'Cargando...';

            if (item.proceso && item.proceso.proceso_nombre) return item.proceso.proceso_nombre;
            return item.aea_actividad || 'Proceso General';
        }
    },

    actions: {
        async loadData(auditId) {
            this.auditId = auditId;
            this.loading = true;
            try {
                // Fetch consolidated agenda items from AuditoriaEjecucionController::index
                const url = window.route ? window.route('api.auditoria.ejecucion.index', { ae_id: auditId }) : `/api/auditoria/ejecucion/por-auditoria/${auditId}`;
                const res = await axios.get(url);
                this.agendaItems = res.data;
            } catch (error) {
                console.error("Store Load Error", error);
            } finally {
                this.loading = false;
            }
        },

        async initExecution(agendaItem) {
            this.loading = true;
            try {
                const url = window.route ? window.route('api.auditoria.ejecucion.init') : '/api/auditoria/ejecucion/init';
                // Post agenda_id to start execution (change status)
                const res = await axios.post(url, {
                    agenda_id: agendaItem.id
                });

                // Update item in state
                const idx = this.agendaItems.findIndex(e => e.id === agendaItem.id);
                if (idx >= 0) {
                    // Merge info (status changed)
                    this.agendaItems[idx] = { ...this.agendaItems[idx], ...res.data };
                }

                this.selectedExecutionId = agendaItem.id;
                return res.data;
            } catch (error) {
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchExecutionDetail(id, force = false) {
            // Check cache
            if (!force && this.executionDetails[id]) {
                return;
            }

            this.loading = true;
            try {
                const url = window.route ? window.route('api.auditoria.ejecucion.show', { id: id }) : `/api/auditoria/ejecucion/detalle/${id}`;
                const res = await axios.get(url);
                this.executionDetails[id] = res.data;

                // Update list if present
                const idx = this.agendaItems.findIndex(e => e.id === id);
                if (idx >= 0) {
                    this.agendaItems[idx] = { ...this.agendaItems[idx], ...res.data };
                }
            } catch (error) {
                console.error("Detail Error", error);
            } finally {
                this.loading = false;
            }
        },

        async refreshAgendaItem(agendaId) {
            // Actualiza solo el item específico de la agenda sin recargar todo
            try {
                const url = window.route ? window.route('api.auditoria.ejecucion.show', { id: agendaId }) : `/api/auditoria/ejecucion/detalle/${agendaId}`;
                const res = await axios.get(url);

                // Actualizar en executionDetails
                this.executionDetails[agendaId] = res.data;

                // Actualizar en agendaItems
                const idx = this.agendaItems.findIndex(e => e.id === agendaId);
                if (idx >= 0) {
                    this.agendaItems[idx] = { ...this.agendaItems[idx], ...res.data };
                }
            } catch (error) {
                console.error("Refresh Item Error", error);
            }
        }
    }
});

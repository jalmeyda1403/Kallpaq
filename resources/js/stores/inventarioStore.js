import { defineStore } from 'pinia';
import axios from 'axios';
import { route } from 'ziggy-js';

export const useInventarioStore = defineStore('inventario', {
  state: () => ({
    isModalOpen: false,
    currentInventario: {
      id: null,
      nombre: '',
      descripcion: '',
      documento_aprueba: null,
      documento_aprueba_nombre: '',
      enlace: '',
      vigencia: '',
      estado_flujo: 'borrador',
      estado: 1
    },
    currentTab: 'InventarioForm',
    isEditing: false,
    availableProcesses: [],
    allProcesos: [],
    selectedProceso: null,
    displayNewProcessModal: false,
    displayAssignProcessModal: false,
    displayModifyOwnerModal: false,
    localLoading: true,
    users: [],
  }),

  getters: {
    modalTitle: (state) => {
      return state.isEditing
        ? `Editar Inventario - ${state.currentInventario ? state.currentInventario.nombre : ''}`
        : 'Crear Nuevo Inventario';
    },
  },

  actions: {
    // Helper para formatear fechas para los inputs de tipo "date"
    formatDateForInput(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return ''; // Si no es una fecha válida, devolver vacío
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    },

    async fetchAvailableProcesses() {
      if (!this.currentInventario?.id) return;
      try {
        const response = await axios.get(route('api.inventarios.procesos.disponibles', this.currentInventario.id));
        this.availableProcesses = response.data;
      } catch (error) {
        console.error('Error al cargar procesos disponibles:', error);
      }
    },

    async loadProcesos() {
      if (!this.currentInventario?.id) return;
      this.localLoading = true;
      try {
        const response = await axios.get(route('api.inventario.procesos', this.currentInventario.id));
        this.allProcesos = response.data;
      } catch (error) {
        console.error('Error al cargar procesos asociados:', error);
      } finally {
        this.localLoading = false;
      }
    },

    async handleProcesoCreado(nuevoProceso) {
      try {
        await axios.post(route('api.inventarios.procesos.asociar', this.currentInventario.id), {
            proceso_id: nuevoProceso.id
        });
        this.loadProcesos();
        this.closeNewProcessModal();
      } catch (error) {
        console.error('Error al asociar el nuevo proceso:', error);
      }
    },

    async disassociateProcess(proceso) {
      try {
        await axios.delete(route('api.inventario-proceso.destroy', { inventario: this.currentInventario.id, proceso: proceso.id }));
        // Remove the disassociated proceso from the allProcesos array directly
        this.allProcesos = this.allProcesos.filter(p => p.id !== proceso.id);
      } catch (error) {
        console.error('Error al desasociar el proceso:', error);
      }
    },

    async loadUsers() {
        try {
            const response = await axios.get(route('api.users.index'));
            this.users = response.data;
        } catch (error) {
            console.error('Error al cargar usuarios:', error);
        }
    },

    async savePropietario() {
        if (!this.selectedProceso) return;
        try {
            await axios.put(route('api.procesos.propietario', this.selectedProceso.id), {
                propietario_id: this.selectedProceso.id_ouo_propietario
            });
            this.loadProcesos();
            this.closeModifyOwnerModal();
        } catch (error) {
            console.error('Error al modificar el propietario:', error);
        }
    },
    
    async assignProcesos(selectedProcesos) {
        try {
            const procesosIds = selectedProcesos.map(p => p.id);
            await axios.post(route('api.inventarios.procesos.add', this.currentInventario.id), {
                procesos_ids: procesosIds
            });
            // Add the newly assigned processes to the allProcesos array directly
            this.allProcesos = [...this.allProcesos, ...selectedProcesos];
            this.closeAssignProcessModal();
        } catch (error) {
            console.error('Error al asignar procesos:', error);
        }
    },

    openNewProcessModal() {
        this.displayNewProcessModal = true;
    },

    closeNewProcessModal() {
        this.displayNewProcessModal = false;
    },

    openAssignProcessModal() {
        this.fetchAvailableProcesses();
        this.displayAssignProcessModal = true;
    },

    closeAssignProcessModal() {
        this.displayAssignProcessModal = false;
    },

    openModifyOwnerModal(proceso) {
        this.selectedProceso = proceso;
        this.loadUsers();
        this.displayModifyOwnerModal = true;
    },

    closeModifyOwnerModal() {
        this.displayModifyOwnerModal = false;
    },

    openModal(inventario = null) {
      this.resetForm();

      if (inventario) {
        this.currentInventario = { ...inventario };
        if (this.currentInventario.vigencia) {
          this.currentInventario.vigencia = this.formatDateForInput(this.currentInventario.vigencia);
        }
        this.isEditing = true;
      } else {
        this.currentInventario = {
          id: null,
          nombre: '',
          descripcion: '',
          documento_aprueba: null,
          documento_aprueba_nombre: '',
          enlace: '',
          vigencia: new Date().toISOString().slice(0, 10),
          estado_flujo: 'borrador',
          estado: 1
        };
        this.isEditing = false;
      }

      this.isModalOpen = true;
      this.currentTab = 'InventarioForm';
    },

    closeModal() {
      this.isModalOpen = false;
    },

    setCurrentTab(tabName) {
      if (this.isEditing || tabName === 'InventarioForm') {
        this.currentTab = tabName;
      }
    },

    resetForm() {
      this.currentInventario = {
        id: null,
        nombre: '',
        descripcion: '',
        documento_aprueba: null,
        documento_aprueba_nombre: '',
        enlace: '',
        vigencia: '',
        estado_flujo: 'borrador',
        estado: 1
      };
      this.isEditing = false;
      this.currentTab = 'InventarioForm';
      this.allProcesos = [];
      this.availableProcesses = [];
      this.selectedProceso = null;
      this.localLoading = true;
    },
  },
});
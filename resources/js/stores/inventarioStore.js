import { defineStore } from 'pinia';

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

    openModal(inventario = null) {
      this.resetForm();

      if (inventario) {
        // Clonar el inventario y formatear las fechas adecuadamente
        this.currentInventario = { ...inventario };
        // Formatear la fecha de vigencia para que funcione con el input date
        if (this.currentInventario.vigencia) {
          this.currentInventario.vigencia = this.formatDateForInput(this.currentInventario.vigencia);
        }
        this.isEditing = true;
      } else {
        // Inicializar con valores por defecto
        this.currentInventario = {
          id: null,
          nombre: '',
          descripcion: '',
          documento_aprueba: null,
          documento_aprueba_nombre: '',
          enlace: '',
          vigencia: new Date().toISOString().slice(0, 10), // Fecha actual en formato YYYY-MM-DD
          estado_flujo: 'borrador',
          estado: 1
        };
        this.isEditing = false;
      }

      this.isModalOpen = true;
      this.currentTab = 'InventarioForm'; // Default tab when opening
    },

    closeModal() {
      this.isModalOpen = false;
    },

    setCurrentTab(tabName) {
      // Only allow switching tabs if in edit mode or if it's the default form tab
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
    },
  },
});
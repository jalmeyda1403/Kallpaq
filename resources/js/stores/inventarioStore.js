import { defineStore } from 'pinia';
import axios from 'axios';

export const useInventarioStore = defineStore('inventario', {
  state: () => ({
    inventarios: [],
    procesos: [], // Procesos del inventario seleccionado (ya filtrados por estado 1)
    procesosFiltrados: [], // Procesos filtrados por busqueda local (nombre, macro)
    selectedInventarioId: null,
    filters: {
      buscar_proceso: '',
      proceso_padre_id: '',
      cod_proceso_padre_seleccionado: null, // Nuevo filtro
      // estado: 1 // Filtro fijo aplicado al cargar
    },
    loading: false,
    error: null,
  }),

  getters: {
    // Procesos del inventario seleccionado con estado 1 por defecto (sin filtros locales)
    procesosOriginales: (state) => state.procesos,
    ultimoInventario: (state) => state.inventarios.length > 0 ? state.inventarios[0] : null,
  },

  actions: {
    async fetchInventarios() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/inventarios');
        // Asegurar que estén ordenados por vigencia descendente (backend lo hace)
        this.inventarios = response.data;
      } catch (error) {
        console.error('Error fetching inventarios:', error);
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchProcesos(inventarioId) {
      if (!inventarioId) {
        this.procesos = [];
        this.procesosFiltrados = [];
        return;
      }
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(`/api/inventario/${inventarioId}/procesos-con-ouos`);
        // Asegurarse de que response.data es un array antes de filtrar
        const rawData = Array.isArray(response.data) ? response.data : [];
        this.procesos = rawData.filter(p => p.pivot_estado === 1);
        // Inicializar procesosFiltrados con todos los procesos del estado 1
        this.procesosFiltrados = this.procesos;
      } catch (error) {
        console.error('Error fetching procesos:', error);
        this.error = error.message;
        this.procesos = [];
        this.procesosFiltrados = [];
      } finally {
        this.loading = false;
      }
    },

    // Aplicar filtros locales (nombre, macro, proceso padre) a procesosFiltrados
    applyFiltrosLocales() {
        // Asegurar que parte de un array
        let filtered = Array.isArray(this.procesos) ? this.procesos : [];

        if (this.filters.buscar_proceso) {
          const term = this.filters.buscar_proceso.toLowerCase();
          filtered = filtered.filter(p =>
            (p.proceso_nombre && p.proceso_nombre.toLowerCase().includes(term)) ||
            (p.cod_proceso && p.cod_proceso.toLowerCase().includes(term))
          );
        }

        if (this.filters.proceso_padre_id) {
          const parentId = parseInt(this.filters.proceso_padre_id, 10);
          filtered = filtered.filter(p => p.proceso_padre_id == parentId); // Comparación flexible
        }

        // Nuevo filtro: filtrar por proceso padre seleccionado (cod_proceso_padre)
        if (this.filters.cod_proceso_padre_seleccionado !== null) {
            const parentIdFilter = parseInt(this.filters.cod_proceso_padre_seleccionado, 10);
            filtered = filtered.filter(p => p.cod_proceso_padre == parentIdFilter); // Comparación con el campo devuelto por API
        }

        this.procesosFiltrados = filtered;
    },

    selectInventario(id) {
      this.selectedInventarioId = id;
    },

    async loadUltimoInventarioYProcesos() {
      await this.fetchInventarios();
      if (this.ultimoInventario) {
        this.selectedInventarioId = this.ultimoInventario.id;
        await this.fetchProcesos(this.ultimoInventario.id);
      }
    },

    setParentProcessFilter(id) {
        this.filters.cod_proceso_padre_seleccionado = id;
        this.applyFiltrosLocales();
    },

    clearParentProcessFilter() {
        this.filters.cod_proceso_padre_seleccionado = null;
        this.applyFiltrosLocales();
    },

    // Resetear filtros
    resetFilters() {
      this.filters = {
        buscar_proceso: '',
        proceso_padre_id: '',
        cod_proceso_padre_seleccionado: null, // Asegurar que se reinicie este filtro también
      };
      // Opcional: volver a aplicar los filtros locales para limpiar tabla
      this.applyFiltrosLocales();
    },
  },
});
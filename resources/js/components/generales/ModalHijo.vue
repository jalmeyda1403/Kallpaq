<template>
  <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h6 class="modal-title">Seleccionar Registro</h6>

          <button type="button" class="close" aria-label="Close" @click="close">
            <span aria-hidden="true" style="color: white">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control" v-model="search" placeholder="Buscar por ID o descripción..." />
          </div>

          <div v-if="loading" class="text-center my-3">
            <div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div>
          </div>

          <div class="table-responsive" style="max-height: 300px; overflow-y: auto;" v-else>
            <table class="table table-hover table-sm">
              <thead class="thead-light">
                <tr>
                  <th>Id</th>
                  <th>Descripción</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in filteredItems" :key="item.id" @click="selectItem(item)">
                  <td>{{ item.id }}</td>
                  <td>{{ item.descripcion }}</td>
                </tr>
                <tr v-if="filteredItems.length === 0 && !loading">
                    <td colspan="2" class="text-center text-muted">No se encontraron resultados.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="modal-footer">
            <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  props: {
    fetchUrl: { type: String, required: true },
    targetId: { type: String, required: true },
    targetDesc: { type: String, required: true }
  },
  data() {
    return {
      modalInstance: null,
      items: [],
      loading: false,
      search: '',
    };
  },
  computed: {
    filteredItems() {
      if (!this.search) return this.items;
      const q = this.search.toLowerCase();
      // Búsqueda en el id y la descripción
      return this.items.filter(item =>
        String(item.id).toLowerCase().includes(q) ||
        (item.descripcion && item.descripcion.toLowerCase().includes(q))
      );
    }
  },
  mounted() {
    const modalEl = this.$refs.modalEl;
    if (modalEl) {
      this.modalInstance = new Modal(modalEl, {
        backdrop: 'static',
        keyboard: false
      });
    }

  },
  methods: {
    async loadItems() {
      this.loading = true;
      try {
        const res = await fetch(this.fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
        if (!res.ok) {
            throw new Error(`Error al cargar los datos: ${res.statusText}`);
        }
        this.items = await res.json();
      } catch (e) {
        console.error(e);
        this.items = []; // Opcional: mostrar un mensaje de error al usuario
      } finally {
        this.loading = false;
      }
    },
    open() {
      this.loadItems(); // Cargar datos cada vez que se abre el modal
      this.modalInstance.show();
    },
    selectItem(item) {
      this.$emit('update-target', {
        targetId: this.targetId,
        targetDesc: this.targetDesc,
        idValue: item.id,
        descValue: item.descripcion
      });
      this.close();
    },
    close() {
      this.modalInstance.hide();
      this.search = '';   
    }
  }
};
</script>

<style scoped>
.modal.fade.modal-md {
  max-width: 500px;
}

.table {
  font-size: 13px;
}
</style>
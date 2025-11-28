<template>
  <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
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

          <div v-if="loading" class="text-center my-4">
            <div class="spinner-border text-danger" role="status">
              <span class="sr-only">Cargando...</span>
            </div>
          </div>

          <div class="table-responsive" style="max-height: 300px; overflow-y: auto;" v-else>
            <table class="table table-hover table-sm">
              <thead class="thead-light" style="position: sticky; top: 0; background-color: white; z-index: 10;">
                <tr>
                  <th style="width: 15%;">Id</th>
                  <th>Descripción</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in filteredItems" :key="item.id"
                    @click="selectItem(item)"
                    class="selectable-row"
                    :class="{'selected-row': selectedItemId === item.id}">
                  <td>{{ item.id }}</td>
                  <td>{{ item.descripcion }}</td>
                </tr>
                <tr v-if="filteredItems.length === 0 && !loading">
                  <td colspan="2" class="text-center text-muted py-4">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <div>No se encontraron resultados</div>
                    <small class="text-secondary">Intente con otros términos de búsqueda</small>
                  </td>
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
      selectedItemId: null
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
      this.selectedItemId = item.id;
      this.$emit('update-target', {
        targetId: this.targetId,
        targetDesc: this.targetDesc,
        idValue: item.id,
        descValue: item.descripcion
      });
      // Add a small delay to show selection feedback before closing
      setTimeout(() => {
        this.close();
      }, 150);
    },
    close() {
      this.modalInstance.hide();
      this.search = '';
      this.selectedItemId = null;
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

/* Modal header styling */
.modal-header {
  background-color: #dc3545;
  color: white;
  border-top-left-radius: 0.3rem;
  border-top-right-radius: 0.3rem;
}

.modal-header .close {
  color: white;
  opacity: 1;
  font-size: 1.5rem;
}

.modal-header .close:hover {
  color: #e9ecef;
  opacity: 0.8;
}

/* Search input styling */
.input-group-text {
  background-color: #f8f9fa;
  border: 1px solid #ced4da;
}

.form-control {
  border: 1px solid #ced4da;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
  color: #495057;
  background-color: #fff;
  border-color: #dc3545;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Table styling */
.table th {
  border-top: none;
  font-weight: 600;
  color: #495057;
}

.table-hover tbody tr.selectable-row:hover {
  background-color: #f8f9fa;
  cursor: pointer;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  transition: all 0.15s ease;
}

.selectable-row {
  transition: all 0.15s ease;
}

.selected-row {
  background-color: #ffece8 !important;
  border-left: 3px solid #dc3545;
}

.table tbody tr.selected-row:hover {
  background-color: #ffd6cc !important;
}

/* Modal footer styling */
.modal-footer {
  background-color: #f8f9fa;
  padding: 1rem;
  border-bottom-right-radius: 0.3rem;
  border-bottom-left-radius: 0.3rem;
}

/* Loading spinner */
.spinner-border.text-danger {
  width: 2rem;
  height: 2rem;
  border-width: 0.25em;
}

/* Empty state styling */
.table td.text-muted {
  border-top: 1px solid #e9ecef;
}
</style>
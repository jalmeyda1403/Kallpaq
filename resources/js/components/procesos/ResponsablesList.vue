<template>
  <div>
    <h6 class="mb-3 font-weight-bold d-flex align-items-center">
      <span class="text-secondary">{{ procesoNombre }}</span>
      <span class="mx-2 text-secondary">
        <i class="fas fa-chevron-right fa-xs"></i>
      </span>
      <span class="text-dark">Responsables y Unidades Orgánicas</span>
    </h6>
    <div class="d-flex align-items-center mb-4">
      <div class="input-group mr-3">
        <input type="hidden" v-model="selectedOuO.id" />
        <input type="text" class="form-control" placeholder="Seleccione el Órgano o Unidad Orgánica a Asociar"
          v-model="selectedOuO.descripcion" readonly />
        <div class="input-group-append">
          <button type="button" class="btn btn-dark" @click="openOuOModal">
            <i class="fas fa-search"></i>
          </button>
          <button type="button" class="btn btn-danger" :disabled="!selectedOuO.id" @click="associateOuO">
            <i class="fas fa-link"></i> Asociar
          </button>
        </div>
      </div>

    </div>

    <div class="form-overlay-container">
      <div v-if="loading" class="loading-overlay">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
      </div>

      <h6 class="mb-3 font-weight-bold">Unidades Orgánicas Asociadas</h6>
      <div class="table-responsive">
        <table id="associatedOuOsTable" class="table table-sm table-bordered">
          <thead class="bg-light">
            <tr>
              <th>Código OUO</th>
              <th>Nombre OUO</th>
              <th class="text-center">R</th>
              <th class="text-center">D</th>
              <th class="text-center" v-for="system in managementSystems" :key="system">{{ system.toUpperCase() }}</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ouo in associatedOuOs" :key="ouo.id">
              <td>{{ ouo.ouo_codigo }}</td>
              <td>{{ ouo.ouo_nombre }}</td>

              <td class="text-center"><input type="checkbox" v-model="ouo.responsable" /></td>

              <td class="text-center"><input type="checkbox" v-model="ouo.delegada" /></td>

              <td class="text-center" v-for="system in managementSystems" :key="system">
                <input type="checkbox" v-model="ouo[system]" />
              </td>

              <td class="text-center text-nowrap">
                <button class="btn btn-warning btn-sm mr-1" @click="updateOuOFlags(ouo)">
                  <i class="fas fa-save"></i>
                </button>
                <button class="btn btn-danger btn-sm" @click="deleteOuO(ouo.id)">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <modal-hijo ref="ouoModal" :fetch-url="ouo_route" target-id="ouo_id" target-desc="ouo_nombre"
        @update-target="handleOuOSelection" />
    </div>
  </div>
</template>

<script>
import { route } from 'ziggy-js';
import ModalHijo from '../generales/ModalHijo.vue';

export default {
  components: { ModalHijo },
  emits: ['form-submitted', 'close-modal', 'data-loaded'], // Aquí se define emits
  props: {
    procesoId: { type: [Number, String], required: true },
  },
  data() {
    return {
      ouo_route: route('ouos.buscar'), // Ruta de tu API para buscar OUOs
      associatedOuOs: [], // OUOs ya asociadas
      selectedOuO: { id: null, descripcion: '' }, // La OUO seleccionada en el modal
      managementSystems: ['sgc', 'sgas', 'sgcm', 'sgsi', 'sgco'],
      loading: false,
      procesoNombre: '',
      dataTableInstance: null,
    };
  },
  watch: {
    procesoId: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.fetchProcesoName(newVal);
          this.loadAssociatedOuOs(); // Llamamos también a esta función para cargar la tabla
        }
      }
    }
  },
  mounted() {
    this.loadAssociatedOuOs();
  },
  methods: {
    async fetchProcesoName(id) {
      try {
        const response = await fetch(route('procesos.show', { id }), {
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        if (!response.ok) throw new Error("Error al obtener el nombre del proceso.");
        const data = await response.json();
        this.procesoNombre = data.proceso_nombre;
      } catch (error) {
        console.error(error);
        this.procesoNombre = 'Error al cargar el nombre';
      }
    },
    async loadAssociatedOuOs() {
      this.loading = true;
      try {
        // Realiza la petición GET al endpoint de la API
        const response = await fetch(route('procesos.listarOUO', { proceso_id: this.procesoId }), {
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        console.log(response);
        if (!response.ok) {
          throw new Error("Error al obtener las OUOs asociadas.");
        }

        // Asigna los datos a la propiedad 'associatedOuOs'
        this.associatedOuOs = await response.json();
        // Si ya existe una instancia, la destruimos
        if (this.dataTableInstance) {
          this.dataTableInstance.destroy();
        }
        // Esperamos un momento para que Vue actualice el DOM
        this.$nextTick(() => {
          this.dataTableInstance = $('#associatedOuOsTable').DataTable({
            "responsive": true,
            "autoWidth": true,
            "info": false,
            "language": {
              "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            },
            "columnDefs": [{ // <-- Agrega esta sección
               "orderable": false, 
              "targets": '_all'
            }]
          });
        });
      } catch (error) {
        console.error(error);
        alert("Hubo un problema al cargar los responsables.");
      } finally {
        this.loading = false;
      }

    },
    async associateOuO() {
      // Validación básica
      if (!this.selectedOuO.id) {
        alert('Por favor, selecciona una Unidad Orgánica para asociar.');
        return;
      }

      try {
        // Define el URL para crear la asociación usando la ruta nombrada
        const url = route('procesos.asociarOUO', { proceso: this.procesoId });

        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: JSON.stringify({
            ouo_id: this.selectedOuO.id,
            // Establece los flags en false por defecto
            responsable: false,
            delegada: false,
            sgc: false,
            sgas: false,
            sgcm: false,
            sgsi: false,
          })
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'No se pudo asociar la Unidad Orgánica.');
        }

        alert('Unidad Orgánica asociada correctamente.');

        // Limpia el campo de selección del modal
        this.selectedOuO = { id: null, descripcion: '' };

        // Recarga la tabla para mostrar la nueva asociación
        await this.loadAssociatedOuOs();

      } catch (error) {
        console.error(error);
        alert('Error: ' + error.message);
      }
    },
    openOuOModal() {
      this.$refs.ouoModal.open();
    },
    handleOuOSelection({ idValue, descValue }) {
      this.selectedOuO.id = idValue;
      this.selectedOuO.descripcion = descValue;
    },
    async updateOuOFlags(ouo) {
      try {
        // El URL apunta a la asociación específica que se quiere actualizar
        const url = route('procesos.ouos.update', {
          proceso: this.procesoId,
          ouo: ouo.id // ID del modelo Ouo a actualizar
        });

        const response = await fetch(url, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: JSON.stringify({
            responsable: ouo.responsable,
            delegada: ouo.delegada,
            sgc: ouo.sgc,
            sgas: ouo.sgas,
            sgcm: ouo.sgcm,
            sgsi: ouo.sgsi,
            // ... el resto de los flags ...
          })
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'No se pudo actualizar la asociación.');
        }

        // Opcional: mostrar una alerta o un mensaje de éxito
        alert('Asociación actualizada correctamente.');

      } catch (error) {
        console.error(error);
        alert('Error: ' + error.message);
      }
    },
    async deleteOuO(ouoId) {
      if (!confirm('¿Estás seguro de que quieres eliminar esta asociación?')) {
        return;
      }

      try {
        const url = route('procesos.disociarOUO', {
          proceso: this.procesoId,
          ouo: ouoId // ID del OUO a eliminar de la tabla pivote
        });

        const response = await fetch(url, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          }
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'No se pudo eliminar la asociación.');
        }

        // Muestra un mensaje de éxito
        alert('Asociación eliminada correctamente.');

        // Recarga la tabla para reflejar el cambio
        await this.loadAssociatedOuOs();

      } catch (error) {
        console.error(error);
        alert('Error al eliminar el registro: ' + error.message);
      }
    },
    beforeDestroy() {
      // Destruimos la instancia de DataTables antes de que el componente sea destruido
      if (this.dataTableInstance) {
        this.dataTableInstance.destroy();
      }
    }

  },
};

</script>

<style scoped>
/* Estilos para el overlay del spinner */
.form-overlay-container {
  position: relative;
  min-height: 200px;
  /* Asegura que el contenedor tenga una altura mínima */
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.35);
  /* Fondo semi-transparente */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
  /* Asegura que esté por encima del formulario */
}

/* Estilos de los campos de formulario */
.form-group small {
  font-size: 0.75rem;
}

.form-label.text-danger {
  font-weight: bold;
}
.table th,
.table td {
  font-size: 0.8rem;
  vertical-align: middle;
}

.table td input[type="checkbox"] {
  transform: scale(0.9);
}
</style>

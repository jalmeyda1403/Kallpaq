<template>
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-md-6 text-md-left">
            <h3 class="card-title mb-0">Gestión del Inventario</h3>
          </div>
          <div class="col-md-6 text-md-right">
            <button class="btn btn-primary btn-sm" @click="openCreateForm">
              <i class="fas fa-plus-circle"></i> Nuevo Inventario
            </button>
          </div>
        </div>
      </div>
      <div class="card-body">
        <DataTable
          :value="inventarios"
          :loading="loading"
          stripedRows
          paginator
          :rows="10"
          :rowsPerPageOptions="[10, 20, 50]"
          responsiveLayout="scroll"
        >
          <Column field="id" header="ID" sortable></Column>
          <Column field="nombre" header="Nombre" sortable></Column>
          <Column field="descripcion" header="Descripción"></Column>
          <Column header="Documento de Aprobación">
            <template #body="slotProps">
              <a v-if="slotProps.data.documento_aprueba" :href="slotProps.data.documento_aprueba" target="_blank" rel="noopener noreferrer">Ver Documento</a>
              <span v-else>Sin documento</span>
            </template>
          </Column>
          <Column field="enlace" header="Enlace">
            <template #body="slotProps">
              <a v-if="slotProps.data.enlace" :href="slotProps.data.enlace" target="_blank" rel="noopener noreferrer">Enlace</a>
              <span v-else>Sin enlace</span>
            </template>
          </Column>
          <Column field="vigencia" header="Vigencia" sortable>
            <template #body="slotProps">
              {{ formatDate(slotProps.data.vigencia) }}
            </template>
          </Column>
          <Column field="estado" header="Estado (Vigente)" sortable>
            <template #body="slotProps">
              <span :class="{
                'badge badge-success': slotProps.data.estado === 1,
                'badge badge-secondary': slotProps.data.estado === 0,
              }">
                {{ slotProps.data.estado === 1 ? 'Vigente' : 'Inactivo' }}
              </span>
            </template>
          </Column>
          <Column field="estado_flujo" header="Estado Flujo" sortable>
            <template #body="slotProps">
              <span :class="{
                'badge badge-info': slotProps.data.estado_flujo === 'borrador',
                'badge badge-success': slotProps.data.estado_flujo === 'aprobado',
                'badge badge-warning': slotProps.data.estado_flujo === 'cerrado',
              }">
                {{ capitalizeFirst(slotProps.data.estado_flujo) || 'No definido' }}
              </span>
            </template>
          </Column>
          <Column field="inventario_cierre" header="CIerra Inventario">
            <template #body="slotProps">
              <!-- Mostrar nombre del inventario que cierra si está disponible -->
              {{ inventarios.find(i => i.id === slotProps.data.inventario_cierre)?.nombre || '-' }}
            </template>
          </Column>
          <Column field="fecha_cierre" header="Fecha Cierre">
            <template #body="slotProps">
              {{ slotProps.data.fecha_cierre ? formatDate(slotProps.data.fecha_cierre) : '-' }}
            </template>
          </Column>
          <Column header="Acciones" :exportable="false" style="min-width: 160px">
            <template #body="slotProps">
              <button class="btn btn-sm btn-outline-primary mr-1" @click="openEditForm(slotProps.data)" :disabled="slotProps.data.estado_flujo !== 'borrador'" title="Editar">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger mr-1" @click="deleteInventario(slotProps.data.id)" :disabled="slotProps.data.estado_flujo !== 'borrador'" title="Eliminar">
                <i class="fas fa-trash-alt"></i>
              </button>
              <button class="btn btn-sm btn-outline-warning mr-1" @click="manageProcesses(slotProps.data.id)" :disabled="slotProps.data.estado_flujo !== 'borrador'" title="Gestionar Procesos">
                <i class="fas fa-tasks"></i>
              </button>
              <button class="btn btn-sm btn-outline-info" @click="viewProcesses(slotProps.data.id)" :disabled="slotProps.data.estado_flujo !== 'aprobado'" title="Ver Procesos">
                <i class="fas fa-list"></i>
              </button>
              <button class="btn btn-sm btn-outline-success mr-1" @click="approveInventario(slotProps.data.id)" :disabled="!(slotProps.data.estado_flujo === 'borrador' && slotProps.data.procesos_count > 0)" title="Aprobar">
                <i class="fas fa-check-circle"></i>
              </button>
            </template>
          </Column>
        </DataTable>

        <!-- Modal de Formulario (Crear/Editar) -->
        <div class="modal fade" id="inventarioFormModal" tabindex="-1" role="dialog" aria-labelledby="inventarioFormModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="inventarioFormModalLabel">{{ inventarioForm.id ? 'Editar Inventario' : 'Crear Nuevo Inventario' }}</h5>
                <button type="button" class="close" @click="closeForm" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form @submit.prevent="saveInventario">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" class="form-control" id="nombre" v-model="inventarioForm.nombre" required>
                  </div>
                  <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" rows="3" v-model="inventarioForm.descripcion"></textarea>
                  </div>
                  <div class="form-group">
                    <label for="documento_aprueba">Documento de Aprobación</label>
                    <input type="file" class="form-control" id="documento_aprueba" @change="onFileChange" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.rtf,.odt,.ods,.odp">
                    <div v-if="inventarioForm.documento_aprueba_nombre" class="mt-2">
                      <small class="text-muted">Archivo seleccionado: {{ inventarioForm.documento_aprueba_nombre }}</small>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="enlace">Enlace</label>
                    <input type="url" class="form-control" id="enlace" v-model="inventarioForm.enlace" placeholder="https://...">
                  </div>
                  <div class="form-group">
                    <label for="vigencia">Vigencia *</label>
                    <input type="date" class="form-control" id="vigencia" v-model="inventarioForm.vigencia" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" @click="closeForm">Cancelar</button>
                  <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Modal para InventarioProcesosManager -->
        <div class="modal fade" id="inventarioProcesosManagerModal" tabindex="-1" role="dialog" aria-labelledby="inventarioProcesosManagerModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="inventarioProcesosManagerModalLabel">Gestión de Procesos del Inventario</h5>
                <button type="button" class="close" @click="closeProcessManagerModal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <InventarioProcesosManager
                  :inventario-id="inventarioIdToManage"
                  v-if="inventarioIdToManage"
                  @closed="onProcessManagerClosed" />
              </div>
            </div>
          </div>
        </div>

        <!-- Modal para InventarioProcesos (Ver Procesos) -->
        <div class="modal fade" id="inventarioProcesosModal" tabindex="-1" role="dialog" aria-labelledby="inventarioProcesosModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="inventarioProcesosModalLabel">Procesos del Inventario</h5>
                <button type="button" class="close" @click="closeProcessesModal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <InventarioProcesos
                  :inventario-id="inventarioIdToView"
                  v-if="inventarioIdToView"
                  @closed="onProcessesClosed" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import axios from 'axios'; // Asegúrate que axios esté disponible y configurado con CSRF token
import InventarioProcesosManager from './InventarioProcesosManager.vue'; // Importar el nuevo componente de gestión de procesos
import InventarioProcesos from './InventarioProcesos.vue'; // Importar el componente de visualización de procesos

// Refs
const inventarios = ref([]);
const inventarioForm = ref({
  id: null,
  nombre: '',
  descripcion: '',
  documento_aprueba: null, // File object
  documento_aprueba_nombre: '', // Display name
  enlace: '',
  vigencia: '' // YYYY-MM-DD
});
const loading = ref(false);
const showForm = ref(false);
const showProcessManager = ref(false); // Nuevo ref para controlar la visibilidad del modal/componente de gestión
const inventarioIdToManage = ref(null); // Nuevo ref para almacenar el ID del inventario a gestionar
const inventarioIdToView = ref(null); // Ref para almacenar el ID del inventario a visualizar

// Router (opcional, si se quiere navegar a otra vista para gestionar procesos)
const router = useRouter();

// Methods
const fetchInventarios = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/inventarios');
    // Asumiendo que la API devuelve un array de inventarios
    // Si devuelve {data: [...]} o {inventarios: [...]}, ajusta response.data.data o response.data.inventarios
    inventarios.value = response.data;
  } catch (error) {
    console.error('Error fetching inventarios:', error);
    // Mostrar mensaje de error al usuario (usando una librería de UI o un toast)
    alert('Error al cargar los inventarios: ' + error.message);
  } finally {
    loading.value = false;
  }
};

const openCreateForm = () => {
  inventarioForm.value = {
    id: null,
    nombre: '',
    descripcion: '',
    documento_aprueba: null,
    documento_aprueba_nombre: '',
    enlace: '',
    vigencia: ''
  };
  showForm.value = true;
  // Usar jQuery para mostrar el modal (asumiendo que está disponible)
  $('#inventarioFormModal').modal('show');
};

const openEditForm = (inventario) => {
  // Clonar el objeto para evitar mutaciones accidentales
  inventarioForm.value = { ...inventario };
  inventarioForm.value.documento_aprueba_nombre = inventario.documento_aprueba ? inventario.documento_aprueba.split('/').pop() : ''; // Extraer nombre del archivo si es una URL
  showForm.value = true;
  $('#inventarioFormModal').modal('show');
};

const closeForm = () => {
  showForm.value = false;
  $('#inventarioFormModal').modal('hide');
};

const onFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    inventarioForm.value.documento_aprueba = file;
    inventarioForm.value.documento_aprueba_nombre = file.name;
  } else {
    inventarioForm.value.documento_aprueba = null;
    inventarioForm.value.documento_aprueba_nombre = '';
  }
};

const saveInventario = async () => {
  const formData = new FormData();
  formData.append('nombre', inventarioForm.value.nombre);
  formData.append('descripcion', inventarioForm.value.descripcion);
  if (inventarioForm.value.documento_aprueba instanceof File) {
    // Si es un archivo nuevo, lo subimos
    formData.append('documento_aprueba', inventarioForm.value.documento_aprueba);
  } else if (typeof inventarioForm.value.documento_aprueba === 'string' && !inventarioForm.value.id) {
    // Si no es un archivo, es una URL y el inventario es nuevo, la enviamos como enlace
    formData.append('enlace', inventarioForm.value.documento_aprueba);
  }
  formData.append('enlace', inventarioForm.value.enlace);
  formData.append('vigencia', inventarioForm.value.vigencia);

  try {
    let response;
    if (inventarioForm.value.id) {
      // Update
      response = await axios.post(`/api/inventarios/${inventarioForm.value.id}`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
      });
      // Actualizar el inventario en la lista local
      const index = inventarios.value.findIndex(i => i.id === inventarioForm.value.id);
      if (index !== -1) {
        inventarios.value[index] = { ...inventarioForm.value, ...response.data };
      }
    } else {
      // Create
      response = await axios.post('/api/inventarios', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
      });
      // Añadir nuevo inventario a la lista local
      inventarios.value.push(response.data);
    }
    closeForm();
    // Opcional: Mostrar mensaje de éxito
    alert('Inventario guardado correctamente.');
  } catch (error) {
    console.error('Error saving inventario:', error);
    // Mostrar mensaje de error al usuario
    let errorMessage = 'Error al guardar el inventario.';
    if (error.response && error.response.data.errors) {
      // Mostrar errores de validación
      Object.values(error.response.data.errors).forEach(messages => {
        messages.forEach(msg => errorMessage += `\n- ${msg}`);
      });
    }
    alert(errorMessage);
  }
};

const deleteInventario = async (id) => {
  if (confirm('¿Estás seguro de que quieres eliminar este inventario en borrador?')) {
    try {
      await axios.delete(`/api/inventarios/${id}`);
      // Remover el inventario de la lista local
      inventarios.value = inventarios.value.filter(i => i.id !== id);
      // Opcional: Mostrar mensaje de éxito
      alert('Inventario eliminado correctamente.');
    } catch (error) {
      console.error('Error deleting inventario:', error);
      // Mostrar mensaje de error al usuario
      alert('Error al eliminar el inventario: ' + error.message);
    }
  }
};

const approveInventario = async (id) => {
  if (confirm('¿Estás seguro de que quieres aprobar este inventario? Esta acción no se puede deshacer.')) {
    try {
      await axios.post(`/api/inventarios/${id}/aprobar`);
      // Actualizar el inventario en la lista local
      const index = inventarios.value.findIndex(i => i.id === id);
      if (index !== -1) {
        inventarios.value[index].estado_flujo = 'aprobado';
        // Si la API devuelve el inventario actualizado, se puede hacer inventarios.value[index] = response.data
      }
      // Opcional: Mostrar mensaje de éxito
      alert('Inventario aprobado correctamente.');
    } catch (error) {
      console.error('Error approving inventario:', error);
      // Mostrar mensaje de error al usuario
      let errorMessage = 'Error al aprobar el inventario.';
      if (error.response && error.response.data.error) {
        errorMessage += ` ${error.response.data.error}`;
      }
      alert(errorMessage);
    }
  }
};

const manageProcesses = (id) => {
  // Abrir el modal/componente de gestión de procesos
  console.log('Gestionar procesos para inventario ID:', id);
  inventarioIdToManage.value = id;
  showProcessManager.value = true;
  // Usar jQuery para mostrar el modal (asumiendo que Bootstrap y jQuery están disponibles)
  $('#inventarioProcesosManagerModal').modal('show');
};

// Función para manejar el cierre del modal de gestión de procesos
const onProcessManagerClosed = () => {
  showProcessManager.value = false;
  inventarioIdToManage.value = null;
  // Opcional: Recargar la lista de inventarios si se realizaron cambios que la afecten
  // fetchInventarios(); // Descomentar si es necesario
  $('#inventarioProcesosManagerModal').modal('hide');
};

// Función para cerrar el modal de gestión de procesos
const closeProcessManagerModal = () => {
  $('#inventarioProcesosManagerModal').modal('hide');
};

// Función para ver los procesos del inventario
const viewProcesses = (id) => {
  // Abrir el modal/componente para ver los procesos del inventario
  console.log('Ver procesos para inventario ID:', id);
  inventarioIdToView.value = id;
  // Usar jQuery para mostrar el modal (asumiendo que Bootstrap y jQuery están disponibles)
  $('#inventarioProcesosModal').modal('show');
};

// Función para cerrar el modal de procesos
const closeProcessesModal = () => {
  $('#inventarioProcesosModal').modal('hide');
};

// Función para manejar el cierre del modal de procesos
const onProcessesClosed = () => {
  inventarioIdToView.value = null;
  $('#inventarioProcesosModal').modal('hide');
};

// Helpers
const formatDate = (dateString) => {
  if (!dateString) return '-';
  // Ajustar formato según tu preferencia (DD/MM/YYYY, YYYY-MM-DD, etc.)
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const capitalizeFirst = (str) => {
  if (!str) return str;
  return str.charAt(0).toUpperCase() + str.slice(1);
}

// Lifecycle
onMounted(async () => {
  await fetchInventarios();
});
</script>

<style scoped>
/* Puedes agregar estilos específicos para este componente aquí */
</style>
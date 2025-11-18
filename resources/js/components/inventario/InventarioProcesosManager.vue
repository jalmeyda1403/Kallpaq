<template>
  <div class="modal fade" id="inventarioProcesosManagerModal" tabindex="-1" role="dialog" aria-labelledby="inventarioProcesosManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="inventarioProcesosManagerModalLabel">
            Gestionar Procesos - {{ inventarioNombre || 'Cargando...' }}
          </h5>
          <button type="button" class="close" @click="cerrarModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Panel Izquierdo: Procesos Disponibles -->
            <div class="col-md-6">
              <h4>Procesos Disponibles (Nivel 0 y 1)</h4>
              <div class="form-group mb-2">
                <input
                  type="text"
                  class="form-control form-control-sm"
                  v-model="filters.buscarProcesoDisponible"
                  placeholder="Filtrar procesos disponibles..."
                  @keyup.enter="applyFilters"
                />
              </div>
              <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                <div
                  v-for="proceso in procesosDisponiblesFiltrados"
                  :key="proceso.id"
                  class="list-group-item d-flex justify-content-between align-items-start"
                  :class="{ 'list-group-item-info': isProcessSelected(proceso.id, 'disponibles') }"
                >
                  <div class="ms-2 me-auto">
                    <div class="fw-bold">{{ proceso.cod_proceso }} - {{ proceso.proceso_nombre }}</div>
                    <small>Nivel: {{ proceso.proceso_nivel }} | Padre ID: {{ proceso.proceso_padre_id || 'Ninguno' }}</small>
                  </div>
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      :value="proceso.id"
                      :id="'chkDisp_' + proceso.id"
                      v-model="procesosSeleccionadosDisponibles"
                    >
                  </div>
                </div>
              </div>
            </div>

            <!-- Botones Centrales -->
            <div class="col-md-1 d-flex flex-column align-items-center justify-content-center">
              <button
                type="button"
                class="btn btn-primary btn-sm mb-2"
                @click="moveToInventario"
                :disabled="procesosSeleccionadosDisponibles.length === 0 || loadingSync"
                title="Agregar seleccionados"
              >
                <i class="fas fa-arrow-right"></i>
              </button>
              <button
                type="button"
                class="btn btn-danger btn-sm"
                @click="moveFromInventario"
                :disabled="procesosSeleccionadosDelInventario.length === 0 || loadingSync"
                title="Remover seleccionados"
              >
                <i class="fas fa-arrow-left"></i>
              </button>
            </div>

            <!-- Panel Derecho: Procesos del Inventario -->
            <div class="col-md-5">
              <h4>Procesos del Inventario</h4>
              <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                <div
                  v-for="proceso in procesosDelInventario"
                  :key="proceso.id"
                  class="list-group-item d-flex justify-content-between align-items-start"
                  :class="{ 'list-group-item-success': isProcessSelected(proceso.id, 'asociados') }"
                >
                  <div class="ms-2 me-auto">
                    <div class="fw-bold">{{ proceso.cod_proceso }} - {{ proceso.proceso_nombre }}</div>
                    <small>Nivel: {{ proceso.proceso_nivel }} | Padre ID: {{ proceso.proceso_padre_id || 'Ninguno' }}</small>
                  </div>
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      :value="proceso.id"
                      :id="'chkInv_' + proceso.id"
                      v-model="procesosSeleccionadosDelInventario"
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="cerrarModal">Cerrar</button>
          <button type="button" class="btn btn-primary" @click="syncSelection" :disabled="loadingSync">
            <i v-if="loadingSync" class="fas fa-spinner fa-spin"></i>
            <span v-else>Guardar Selección</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import $ from 'jquery'; // Asumiendo que jQuery está disponible globalmente para Bootstrap Modal

// --- PROPS ---
const props = defineProps({
  inventarioId: {
    type: Number,
    required: true
  }
});

// --- REFS ---
const inventarioNombre = ref('');
const procesosDisponibles = ref([]); // Procesos nivel 0 y 1 no en este inventario
const procesosDelInventario = ref([]); // Procesos nivel 0 y 1 en este inventario
const procesosSeleccionadosDisponibles = ref([]); // IDs de procesos seleccionados en izquierda
const procesosSeleccionadosDelInventario = ref([]); // IDs de procesos seleccionados en derecha
const loading = ref(false);
const loadingSync = ref(false);
const filters = ref({
  buscarProcesoDisponible: '' // Filtro para la lista izquierda
});

// --- COMPUTED ---
const procesosDisponiblesFiltrados = computed(() => {
  if (!filters.value.buscarProcesoDisponible) {
    return procesosDisponibles.value;
  }
  const term = filters.value.buscarProcesoDisponible.toLowerCase();
  return procesosDisponibles.value.filter(p =>
    p.proceso_nombre.toLowerCase().includes(term) || p.cod_proceso.toLowerCase().includes(term)
  );
});

// --- METHODS ---

// Función privada para verificar si un proceso está seleccionado
const isProcessSelected = (id, listName) => {
  if (listName === 'disponibles') {
    return procesosSeleccionadosDisponibles.value.includes(id);
  } else if (listName === 'asociados') {
    return procesosSeleccionadosDelInventario.value.includes(id);
  }
  return false;
};

// Cargar procesos disponibles y asociados al montar
onMounted(async () => {
  await cargarDatos();
});

const cargarDatos = async () => {
  loading.value = true;
  try {
    // Opcional: Obtener nombre del inventario si no se pasa como prop o si se quiere mostrar el nombre específico de este ID
    // const invResponse = await axios.get(`/api/inventarios/${props.inventarioId}`);
    // inventarioNombre.value = invResponse.data.nombre;

    const [dispResponse, asocResponse] = await Promise.all([
      axios.get(`/api/inventarios/${props.inventarioId}/procesos-disponibles`),
      axios.get(`/api/inventarios/${props.inventarioId}/procesos-asociados`)
    ]);

    procesosDisponibles.value = dispResponse.data; // Asumiendo que la API devuelve { id, cod_proceso, proceso_nombre, proceso_nivel, proceso_padre_id }
    procesosDelInventario.value = asocResponse.data; // Asumiendo el mismo formato

    // Opcional: Si la API devuelve tambien el nombre del inventario
    inventarioNombre.value = asocResponse.data.length > 0 ? asocResponse.data[0].inventario_nombre : 'Cargando...'; // Asumiendo que la API de asociados tambien devuelve el nombre del inventario

  } catch (error) {
    console.error('Error fetching processes for inventory manager:', error);
    // Mostrar mensaje de error al usuario (e.g., Toast)
  } finally {
    loading.value = false;
  }
};

// Mover seleccionados de Disponibles a Asociados
const moveToInventario = () => {
  if (procesosSeleccionadosDisponibles.value.length === 0) return;

  // Calcular IDs completos (padres + hijos) a mover
  const idsConHijosAAgregar = [];
  for (const idPadre of procesosSeleccionadosDisponibles.value) {
    idsConHijosAAgregar.push(idPadre);
    // Calcular hijos de este padre y añadirlos
    const hijos = calculateChildren(idPadre, procesosDisponibles.value);
    idsConHijosAAgregar.push(...hijos);
  }

  // Añadir padres + hijos a la lista derecha
  const procesosAAgregar = procesosDisponibles.value.filter(p => idsConHijosAAgregar.includes(p.id));
  procesosDelInventario.value.push(...procesosAAgregar);

  // Remover padres + hijos de la lista izquierda
  procesosDisponibles.value = procesosDisponibles.value.filter(p => !idsConHijosAAgregar.includes(p.id));

  // Limpiar selección izquierda
  procesosSeleccionadosDisponibles.value = [];
};

// Mover seleccionados de Asociados a Disponibles
const moveFromInventario = () => {
  if (procesosSeleccionadosDelInventario.value.length === 0) return;

  // Calcular IDs completos (padres + hijos) a remover
  const idsConHijosARemover = [];
  for (const idPadre of procesosSeleccionadosDelInventario.value) {
    idsConHijosARemover.push(idPadre);
    // Calcular hijos de este padre y añadirlos
    const hijos = calculateChildren(idPadre, procesosDelInventario.value);
    idsConHijosARemover.push(...hijos);
  }

  // Añadir padres + hijos a la lista izquierda
  const procesosARetornar = procesosDelInventario.value.filter(p => idsConHijosARemover.includes(p.id));
  procesosDisponibles.value.push(...procesosARetornar);

  // Remover padres + hijos de la lista derecha
  procesosDelInventario.value = procesosDelInventario.value.filter(p => !idsConHijosARemover.includes(p.id));

  // Limpiar selección derecha
  procesosSeleccionadosDelInventario.value = [];
};

// Función auxiliar para calcular hijos recursivamente
const calculateChildren = (parentId, allProcesses) => {
  const children = [];
  const queue = [parentId];

  while (queue.length > 0) {
    const currentParentId = queue.shift();
    const directChildren = allProcesses.filter(p => p.proceso_padre_id == currentParentId);

    for (const child of directChildren) {
      children.push(child.id);
      // Añadir el ID del hijo a la cola para buscar sus propios hijos
      queue.push(child.id);
    }
  }

  return children;
};

// Sincronizar la selección con el backend
const syncSelection = async () => {
  if (loadingSync.value) return; // Evitar doble clic

  loadingSync.value = true;
  try {
    const procesosIdsParaSync = procesosDelInventario.value.map(p => p.id);

    await axios.post(`/api/inventarios/${props.inventarioId}/procesos/sync`, {
      procesos_ids: procesosIdsParaSync
    });

    // Mensaje de éxito (opcional: Toast)
    alert('Selección de procesos guardada correctamente.');

    // Cerrar modal o dejarlo abierto para más cambios (decisión de UX)
    // cerrarModal(); // Descomentar si se quiere cerrar despues de guardar

  } catch (error) {
    console.error('Error syncing processes:', error);
    // Mostrar mensaje de error al usuario (e.g., Toast)
    alert('Error al guardar la selección de procesos: ' + (error.response?.data?.message || error.message));
  } finally {
    loadingSync.value = false;
  }
};

// Cerrar el modal
const cerrarModal = () => {
  $('#inventarioProcesosManagerModal').modal('hide');
  // Opcionalmente emitir un evento para notificar al padre que se cerró
  // emit('closed');
};

// Aplicar filtros a la lista izquierda
const applyFilters = () => {
  // `procesosDisponiblesFiltrados` es computed, no necesita lógica aquí
  // La reactividad de Vue hará que se actualice solo
};

// --- WATCHERS (si se necesita reaccionar a cambios externos en props.inventarioId) ---
// import { watch } from 'vue';
// watch(() => props.inventarioId, (newId) => {
//   if (newId) {
//     cargarDatos();
//   }
// });

</script>

<style scoped>
/* Opcional: Estilos específicos para este componente */
/* Por ejemplo, ajustar tamaños de fuente si es necesario */
.list-group-item {
  font-size: 0.9em;
}
</style>
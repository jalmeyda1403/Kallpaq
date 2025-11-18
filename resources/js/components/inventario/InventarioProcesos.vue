<template>
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-md-6 text-md-left">
            <h3 class="card-title mb-0">Inventario de Procesos</h3>
          </div>
          <div class="col-md-6 text-md-right">
            <!-- Botón de refresco eliminado -->
          </div>
        </div>
        <!-- Filtros (Bootstrap) -->
        <div class="card-body">
          <form @submit.prevent="applyFilters">
            <div class="form-row">
              <!-- Filtro por Inventario (Bootstrap) -->
              <div class="col">
                <select
                  v-model="selectedInventarioId"
                  class="form-control"
                  :disabled="loading"
                  @change="onInventarioChange"
                >
                  <option value="" selected disabled>Selecciona un inventario...</option>
                  <option
                    v-for="inventario in inventarios"
                    :key="inventario.id"
                    :value="inventario.id"
                  >
                    {{ inventario.nombre }} ({{ new Date(inventario.vigencia).toLocaleDateString() }})
                  </option>
                </select>
              </div>

              <!-- Filtro por Nombre/Código (Bootstrap) -->
              <div class="col">
                <input
                  type="text"
                  v-model="filters.buscar_proceso"
                  placeholder="Buscar por Proceso"
                  class="form-control"
                  :disabled="!selectedInventarioId || loading"
                />
              </div>

              <!-- Filtro por Macroproceso (Bootstrap) -->
              <div class="col">
                <select
                  v-model="filters.proceso_padre_id"
                  class="form-control"
                  :disabled="!selectedInventarioId || loading"
                >
                  <option value="">Todos los macroprocesos</option>
                  <option
                    v-for="macroproceso in macroprocesos"
                    :key="macroproceso.id"
                    :value="macroproceso.id"
                  >
                    {{ macroproceso.cod_proceso }}. {{ macroproceso.proceso_nombre }}
                  </option>
                </select>
              </div>

              <!-- Botón Buscar (Bootstrap) -->
              <div class="col-auto">
                <button
                  type="submit"
                  class="btn bg-dark"
                  :disabled="!selectedInventarioId || loading"
                >
                    <i class="fas fa-search"></i> Buscar
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card-body">
        <h2 class="text-xl font-semibold mb-4" v-if="selectedInventarioId">
          <h4 class="card-title mb-0">Inventario de Procesos {{ selectedInventarioNombre || 'Cargando...' }}</h4>
        </h2>
        <br></br>

        <!-- DataTable de PrimeVue para mostrar los datos -->
        <DataTable
          :value="procesosFiltrados"
          :loading="loading"
          stripedRows
          :rowClass="rowClass"
          paginator
          :rows="10"
          :rowsPerPageOptions="[10, 20, 50]"
          responsiveLayout="scroll"
        >
          <Column field="cod_proceso" header="Cod Proceso" sortable></Column>
          <Column field="proceso_nombre" header="Nombre" sortable></Column>
          <Column field="proceso_tipo" header="Tipo" sortable></Column>
          <Column field="proceso_sigla" header="Sigla" sortable></Column>
          <Column field="proceso_nivel" header="Nivel" sortable></Column>
          <Column field="nombre_ouo_propietario" header="Propietario">
            <template #body="slotProps">
              <!-- Mostrar nombre del OUO propietario si está disponible -->
              {{ slotProps.data.nombre_ouo_propietario || 'No asignado' }}
            </template>
          </Column>
          <Column field="nombre_ouo_delegado" header="Delegado">
            <template #body="slotProps">
              {{ slotProps.data.nombre_ouo_delegado || 'No asignado' }}
            </template>
          </Column>
          <Column field="nombre_ouo_ejecutor" header="Ejecutor">
            <template #body="slotProps">
              {{ slotProps.data.nombre_ouo_ejecutor || 'No asignado' }}
            </template>
          </Column>
          <Column header="Subprocesos" class="text-center">
            <template #body="slotProps">
              <!-- Botón clickable para navegar a subprocesos -->
              <button
                v-if="slotProps.data.subprocesos_count > 0"
                @click="navigateToSubprocesses(slotProps.data.id)"
                class="btn-subprocesos btn btn-link text-primary text-decoration-underline p-0 border-0"
                title="Ver Subprocesos"
              >
                {{ slotProps.data.subprocesos_count }}
              </button>
              <span v-else>{{ slotProps.data.subprocesos_count || 0 }}</span>
            </template>
          </Column>
          <Column header="Acciones" class="text-center">
            <template #body="slotProps">
              <div class="dropdown">
                <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-link"></i> Asociaciones
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item"
                    :href="`/procesos/${slotProps.data.id}/caracterizacion`">
                    <i class="fas fa-file-alt fa-fw mr-2"></i>Documentación
                  </a>
                  <a class="dropdown-item"
                    :href="`/indicadores?proceso_id=${slotProps.data.id}`">
                    <i class="fas fa-chart-bar fa-fw mr-2"></i>Indicadores
                  </a>
                  <a class="dropdown-item"
                    :href="`/obligaciones?proceso_id=${slotProps.data.id}`">
                    <i class="fas fa-list-ul fa-fw mr-2"></i>Obligaciones
                  </a>
                  <a class="dropdown-item"
                    :href="`/riesgos?proceso_id=${slotProps.data.id}`">
                    <i class="fas fa-exclamation-triangle fa-fw mr-2"></i>Riesgos
                  </a>
                  <a class="dropdown-item" href="#">
                    <i class="fas fa-fire fa-fw mr-2"></i>Hallazgos
                  </a>
                </div>
              </div>
            </template>
          </Column>
        </DataTable>

        <!-- Mensajes -->
        <div class="p-4 bg-gray-50 text-sm text-gray-500 mt-3" v-if="!selectedInventarioId">
          Selecciona una versión de inventario para ver sus procesos.
        </div>
        <div class="p-4 bg-gray-50 text-sm text-gray-500 mt-3" v-else-if="procesos && procesos.length > 0 && procesosFiltrados && procesosFiltrados.length === 0 && !loading">
          No se encontraron procesos con los filtros aplicados.
        </div>
        <div class="p-4 bg-gray-50 text-sm text-gray-500 mt-3" v-else-if="selectedInventarioId && procesos && procesos.length === 0 && !loading">
          No hay procesos disponibles para este inventario.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
// Importar componentes de PrimeVue (solo DataTable y Column)
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// Instancias del router y route
const router = useRouter();
const route = useRoute();

// Refs locales
const inventarios = ref([]);
const procesos = ref([]);
const macroprocesos = ref([]);
const loading = ref(false);
const selectedInventarioId = ref(null);
const filters = ref({
  buscar_proceso: '',
  proceso_padre_id: ''
});
const parentProcessFilter = ref(null);

// Funciones para cargar datos
const fetchInventarios = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/inventarios');
    inventarios.value = response.data;

    // Si hay un inventario en la URL, lo seleccionamos
    const inventarioIdFromUrl = route.query.inventario_id;
    if (inventarioIdFromUrl) {
      const parsedId = parseInt(inventarioIdFromUrl, 10);
      if (!isNaN(parsedId)) {
        selectedInventarioId.value = parsedId;
      }
    } else if (inventarios.value.length > 0) {
      // Seleccionar el último inventario si no hay ninguno en la URL
      selectedInventarioId.value = inventarios.value[inventarios.value.length - 1].id;
    }
  } catch (error) {
    console.error('Error fetching inventarios:', error);
    inventarios.value = [];
  } finally {
    loading.value = false;
  }
};

const fetchProcesos = async (inventarioId) => {
  if (!inventarioId) return;

  loading.value = true;
  try {
    const response = await axios.get(`/api/inventario/${inventarioId}/procesos-con-ouos`);
    procesos.value = response.data;
  } catch (error) {
    console.error('Error fetching procesos:', error);
    procesos.value = [];
  } finally {
    loading.value = false;
  }
};

// Computed para nombre del inventario seleccionado
const selectedInventarioNombre = computed(() => {
  if (!selectedInventarioId.value || inventarios.value.length === 0) return '';
  const inventario = inventarios.value.find(inv => inv.id == selectedInventarioId.value);
  return inventario ? inventario.nombre : '';
});

// Computed para procesos filtrados
const procesosFiltrados = computed(() => {
  if (!procesos.value || procesos.value.length === 0) {
    return [];
  }

  let filtered = procesos.value;

  // Filtrar por nombre/código de proceso
  if (filters.value.buscar_proceso) {
    const search = filters.value.buscar_proceso.toLowerCase();
    filtered = filtered.filter(proceso =>
      proceso.cod_proceso.toLowerCase().includes(search) ||
      proceso.proceso_nombre.toLowerCase().includes(search)
    );
  }

  // Filtrar por macroproceso
  if (filters.value.proceso_padre_id) {
    filtered = filtered.filter(proceso =>
      proceso.proceso_padre_id == filters.value.proceso_padre_id
    );
  }

  // Filtrar por proceso padre (usado para ver subprocesos)
  if (parentProcessFilter.value) {
    filtered = filtered.filter(proceso =>
      proceso.proceso_padre_id == parentProcessFilter.value
    );
  }

  return filtered;
});

// Función para clase de fila
const rowClass = (rowData) => {
  return {
    'row-celeste-bajo': rowData && rowData.proceso_anterior_id === null, // Color si es nuevo (no tiene anterior)
    'row-beige': rowData && rowData.proceso_anterior_id !== null, // Color si proviene de otro proceso
    'row-rosado-suficiente': rowData && rowData.proceso_estado == '0' // Color si proceso_estado es 0 (cadena o número)
  };
};

// Funciones de evento
const onInventarioChange = async () => {
  if (selectedInventarioId.value) {
    await fetchProcesos(selectedInventarioId.value);
  } else {
    procesos.value = [];
  }
};

// Actualizar procesos filtrados cuando se pulse "Buscar"
const applyFilters = () => {
  // Los filtros se aplican automáticamente con el computed
  console.log('Filtros locales aplicados desde el botón Buscar.');
};

// Función para navegar a subprocesos
const navigateToSubprocesses = (processId) => {
  console.log(`Navigating to sub-processes for process ID: ${processId}`);
  // Cambiar URL y aplicar filtro
  router.push({
    name: route.name, // Mantiene el nombre de la ruta actual
    params: { ...route.params }, // Mantiene los params actuales
    query: { ...route.query, parent_process_id: processId } // Añade el nuevo query param
  });
};

// Función para establecer filtro de proceso padre
const setParentProcessFilter = (processId) => {
  parentProcessFilter.value = processId;
};

// Función para limpiar filtro de proceso padre
const clearParentProcessFilter = () => {
  parentProcessFilter.value = null;
};

// Cargar inventarios y procesos al montar
onMounted(async () => {
  await fetchInventarios();

  // Cargar macroprocesos
  try {
    const response = await axios.get('/procesos/macro');
    macroprocesos.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching macroprocesos:', error);
    macroprocesos.value = [];
  }

  // Si hay un inventario seleccionado, cargar sus procesos
  if (selectedInventarioId.value) {
    await fetchProcesos(selectedInventarioId.value);
  }

  // Leer parent_process_id de la URL al montar
  const initialParentId = route.query.parent_process_id;
  if (initialParentId) {
    const parsedId = parseInt(initialParentId, 10);
    if (!isNaN(parsedId)) {
      setParentProcessFilter(parsedId);
    }
  }
});

// Watch para detectar cambios en parent_process_id en la URL
watch(() => route.query.parent_process_id, (newParentId) => {
  if (newParentId) {
    const parsedId = parseInt(newParentId, 10);
    if (!isNaN(parsedId)) {
      console.log("Watch: Aplicando filtro de proceso padre:", parsedId);
      setParentProcessFilter(parsedId);
    }
  } else {
    // Si la URL ya no tiene parent_process_id, limpiar el filtro
    console.log("Watch: Limpiando filtro de proceso padre");
    clearParentProcessFilter();
  }
}, { immediate: true }); // immediate: true asegura que se ejecute una vez al inicio
</script>

<style scoped>
:deep(.row-celeste-bajo) td {
  background-color: #e6f7ff !important;
}

:deep(.row-beige) td {
  background-color: #fff3cd !important; /* Beige suave */
}

/* Nuevo estilo para proceso_estado = 0 */
:deep(.row-rosado-suficiente) td {
  background-color: #f8d7da !important; /* Rosado suave */
}

/* Estilo para el botón de subprocesos para que se vea como texto plano en la celda */
:deep(.row-celeste-bajo) td button.btn-subprocesos,
:deep(.row-beige) td button.btn-subprocesos,
:deep(.row-rosado-suficiente) td button.btn-subprocesos,
.p-datatable .p-datatable-tbody > tr > td button.btn-subprocesos {
  /* Heredar estilos de fuente de la celda */
  font-family: inherit;
  font-size: inherit; /* Muy importante */
  line-height: inherit; /* Muy importante */
  color: inherit; /* Mantiene color de texto de la celda o usa el color de enlace */
  text-decoration: underline; /* Si se quiere subrayado como enlace */
  background: none; /* Elimina fondo */
  border: none; /* Elimina borde */
  padding: 0; /* Puede ser necesario mantenerlo o ajustarlo */
  cursor: pointer; /* Muestra que es cliqueable */
  /* Opcional: Ajustar color y subrayado específicos para botón */
  /* color: #007bff; */
  /* text-decoration-color: #007bff; */
}

/* Opcional: Estilo en hover */
.p-datatable .p-datatable-tbody > tr > td button.btn-subprocesos:hover {
  opacity: 0.8; /* O cualquier efecto de hover deseado */
  text-decoration: underline; /* Asegurar subrayado en hover si se quitó por herencia */
}
</style>

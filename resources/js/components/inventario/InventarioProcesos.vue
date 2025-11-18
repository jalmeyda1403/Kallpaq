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
                  v-model="store.selectedInventarioId"
                  class="form-control"
                  :disabled="loading"
                  @change="onInventarioChange"
                >
                  <option value="" selected disabled>Selecciona un inventario...</option>
                  <option
                    v-for="inventario in store.inventarios"
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
                  v-model="store.filters.buscar_proceso"
                  placeholder="Buscar por Proceso"
                  class="form-control"
                  :disabled="!store.selectedInventarioId || loading"
                />
              </div>

              <!-- Filtro por Macroproceso (Bootstrap) -->
              <div class="col">
                <select
                  v-model="store.filters.proceso_padre_id"
                  class="form-control"
                  :disabled="!store.selectedInventarioId || loading"
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

              <!-- Botón Buscar (Bootstrap con PrimeVue Button para icono) -->
              <!-- OJO: El botón ahora es PrimeVue pero el estilo puede ajustarse -->
              <!-- Para mantenerlo como botón Bootstrap estándar, usar <button type="submit" class="btn btn-dark">Buscar</button> -->
              <div class="col-auto">
                <button
                  type="submit"
                  class="btn bg-dark"
                  :disabled="!store.selectedInventarioId || loading"
                >
                    <i class="fas fa-search"></i> Buscar
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card-body">
        <h2 class="text-xl font-semibold mb-4" v-if="store.selectedInventarioId">
          <h4 class="card-title mb-0">Inventario de Procesos {{ selectedInventarioNombre || 'Cargando...' }}</h4>
        </h2>
        <br></br>


        <!-- DataTable de PrimeVue para mostrar los datos -->
        <DataTable
          :value="store.procesosFiltrados"
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
        <div class="p-4 bg-gray-50 text-sm text-gray-500 mt-3" v-if="!store.selectedInventarioId">
          Selecciona una versión de inventario para ver sus procesos.
        </div>
        <div class="p-4 bg-gray-50 text-sm text-gray-500 mt-3" v-else-if="store.procesos && store.procesos.length > 0 && store.procesosFiltrados && store.procesosFiltrados.length === 0 && !loading">
          No se encontraron procesos con los filtros aplicados.
        </div>
        <div class="p-4 bg-gray-50 text-sm text-gray-500 mt-3" v-else-if="store.selectedInventarioId && store.procesos && store.procesos.length === 0 && !loading">
          No hay procesos disponibles para este inventario.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'; // Añadido watch
import { useInventarioStore } from '@/stores/inventarioStore.js';
import { useRouter, useRoute } from 'vue-router'; // Importar useRouter y useRoute
import axios from 'axios';
// Importar componentes de PrimeVue (solo DataTable y elementos relacionados)
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
// No es necesario importar InputText, Button, ProgressBar si solo se usan en la tabla o componentes específicos
// import InputText from 'primevue/inputtext';
// import Button from 'primevue/button'; // Ya no se usa para el botón de buscar
import ProgressBar from 'primevue/progressbar';
import { FilterMatchMode } from 'primevue/api'; // Importante para los filtros de PrimeVue

const store = useInventarioStore();

// Instancias del router y route
const router = useRouter();
const route = useRoute();

// Watch para ver cambios en procesosFiltrados
watch(() => store.procesosFiltrados, (newVal) => {
  console.log("procesosFiltrados actualizados:", newVal, "Longitud:", newVal.length);
}, { deep: true }); // Profundidad para arrays/objetos

// Refs locales
const macroprocesos = ref([]);
// No necesitamos dtFilters si usamos la lógica del store
// const dtFilters = ref({ ... });
const loading = computed(() => store.loading);

// Computed para nombre del inventario seleccionado
const selectedInventarioNombre = computed(() => {
  if (!store.selectedInventarioId || store.inventarios.length === 0) return '';
  const inventario = store.inventarios.find(inv => inv.id == store.selectedInventarioId);
  return inventario ? inventario.nombre : '';
});

// Función para obtener nombre de OUO (opcional)
const getNombreOUO = (id) => {
  // Placeholder
  return `OUO-${id}`;
};

// Función para clase de fila
const rowClass = (rowData) => {
  console.log("rowClass rowData:", rowData); // Log temporal (opcional, puedes quitarlo después)
  if(rowData) {
    console.log("- proceso_anterior_id:", rowData.proceso_anterior_id, "Tipo:", typeof rowData.proceso_anterior_id); // Log temporal (opcional, puedes quitarlo después)
    console.log("- proceso_estado:", rowData.proceso_estado, "Tipo:", typeof rowData.proceso_estado); // Log temporal (opcional, puedes quitarlo después)
  }
  return {
    'row-celeste-bajo': rowData && rowData.proceso_anterior_id === null, // Color si es nuevo (no tiene anterior)
    'row-beige': rowData && rowData.proceso_anterior_id !== null, // Color si proviene de otro proceso
    'row-rosado-suficiente': rowData && rowData.proceso_estado == '0' // Color si proceso_estado es 0 (cadena o número)
  };
};

// Funciones de evento
const onInventarioChange = async () => {
  if (store.selectedInventarioId) {
    await store.fetchProcesos(store.selectedInventarioId);
  } else {
    store.procesos = [];
  }
};

// Actualizar procesosFiltrados en el store cuando se pulse "Buscar"
const applyFilters = () => {
    store.applyFiltrosLocales(); // Llama a la acción en el store
    console.log('Filtros locales aplicados desde el botón Buscar.');
};

const refreshData = async () => {
  if (store.selectedInventarioId) {
    await store.fetchProcesos(store.selectedInventarioId);
  } else if (store.ultimoInventario) {
    await store.loadUltimoInventarioYProcesos();
  }
};

// Función para navegar a subprocesos
const navigateToSubprocesses = (processId) => {
  console.log(`Navigating to sub-processes for process ID: ${processId}`); // Log temporal
  // Cambiar URL y aplicar filtro
  router.push({
    name: route.name, // Mantiene el nombre de la ruta actual
    params: { ...route.params }, // Mantiene los params actuales (ej. id inventario)
    query: { ...route.query, parent_process_id: processId } // Añade el nuevo query param
  });
  // El onMounted o un watch de route.query se encargará de aplicar el filtro en la nueva carga.
};

// Cargar inventarios y procesos del último al montar
onMounted(async () => {
  // Leer inventario_id de la URL al montar
  const inventarioIdFromUrl = route.query.inventario_id;
  console.log("Inventario ID desde URL:", inventarioIdFromUrl); // Log temporal
  if (inventarioIdFromUrl) {
    const parsedId = parseInt(inventarioIdFromUrl, 10);
    if (!isNaN(parsedId)) {
      store.selectedInventarioId = parsedId;
      await store.fetchProcesos(parsedId);
    } else {
      await store.loadUltimoInventarioYProcesos();
    }
  } else {
    await store.loadUltimoInventarioYProcesos();
  }

  // Leer parent_process_id de la URL al montar (esta lógica ahora está redundante con el watch, pero no hace mal)
  const initialParentId = route.query.parent_process_id;
  console.log("Parent ID desde URL:", initialParentId); // Log temporal
  if (initialParentId) {
    const parsedId = parseInt(initialParentId, 10);
    if (!isNaN(parsedId)) {
      store.setParentProcessFilter(parsedId);
    }
  }

  console.log("Procesos filtrados iniciales:", store.procesosFiltrados); // Log temporal
  try {
    const response = await axios.get('/api/procesos/macro');
    macroprocesos.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching macroprocesos:', error);
    macroprocesos.value = [];
  }
});

// Watch para detectar cambios en parent_process_id en la URL
watch(() => route.query.parent_process_id, (newParentId) => {
  if (newParentId) {
    const parsedId = parseInt(newParentId, 10);
    if (!isNaN(parsedId)) {
      console.log("Watch: Aplicando filtro de proceso padre:", parsedId);
      store.setParentProcessFilter(parsedId);
    }
  } else {
    // Si la URL ya no tiene parent_process_id, limpiar el filtro
    console.log("Watch: Limpiando filtro de proceso padre");
    store.clearParentProcessFilter();
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

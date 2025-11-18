<template>
  <div class="container-fluid">
    <div class="text-left mb-4">
      <div class="header-container">
        <h6 class="mb-0 d-flex align-items-center">
          <span class="text-dark">{{ inventarioStore.modalTitle }}</span>
          <span class="mx-2 text-secondary">
            <i class="fas fa-chevron-right fa-xs"></i>
          </span>
          <span class="text-dark">{{ inventarioStore.currentInventario?.nombre || 'Nuevo Inventario' }}</span>
        </h6>
      </div>
      <div v-if="localLoading" class="loading-spinner w-100 text-center my-5">
        <div class="spinner-border text-danger" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
      </div>
      <div v-else>
        <div class="row">
          <div class="col-12">
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <h6 class="text-left"><b>Procesos Asociados al Inventario</b></h6>
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label font-weight-bold">Filtros de Procesos</label>
                    </div>
                    
                    <div class="form-row">
                      <div class="col-md-4">
                        <input 
                          type="text" 
                          v-model="searchTerm" 
                          class="form-control" 
                          placeholder="Buscar procesos..."
                        >
                      </div>
                      <div class="col-md-3">
                        <select v-model="nivelFilter" class="form-control">
                          <option value="">Todos los niveles</option>
                          <option value="0">Nivel 0</option>
                          <option value="1">Nivel 1</option>
                          <option value="2">Nivel 2</option>
                          <option value="3">Nivel 3</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <select v-model="estadoFilter" class="form-control">
                          <option value="">Todos los estados</option>
                          <option value="1">Vigente</option>
                          <option value="0">Inactivo</option>
                        </select>
                      </div>
                      <div class="col-md-2">
                        <button class="btn btn-primary btn-block" @click="applyFilters">
                          <i class="fas fa-filter"></i> Filtrar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label font-weight-bold">Estructura de Procesos</label>
                      <small class="text-muted">{{ filteredProcesos.length }} de {{ allProcesos.length }} procesos</small>
                    </div>
                    
                    <div class="border rounded p-3" style="max-height: 500px; overflow-y: auto;">
                      <!-- Mostrar jerarquía de procesos -->
                      <div v-for="proceso in rootProcesos" :key="proceso.id" class="mb-3">
                        <div class="proceso-card p-3 border rounded mb-2">
                          <div class="d-flex justify-content-between align-items-center">
                            <div>
                              <i :class="getNivelIcon(proceso.proceso_nivel)"></i>
                              <span :class="getNivelClass(proceso.proceso_nivel)">
                                {{ proceso.cod_proceso }}
                              </span>
                              - {{ proceso.proceso_nombre }}
                              <span class="badge badge-secondary ml-1">{{ proceso.proceso_nivel }}</span>
                              <span class="badge ml-1" :class="proceso.estado === 1 ? 'badge-success' : 'badge-secondary'">
                                {{ proceso.estado === 1 ? 'Vigente' : 'Inactivo' }}
                              </span>
                            </div>
                            <div>
                              <span class="text-muted small">
                                {{ proceso.nombre_ouo_propietario ? proceso.nombre_ouo_propietario : 'Sin propietario' }}
                              </span>
                            </div>
                          </div>
                          
                          <!-- Mostrar hijos si existen -->
                          <div v-if="hasChildren(proceso.id)" class="mt-2 ml-3">
                            <div v-for="hijo1 in getChildProcesos(proceso.id)" :key="hijo1.id" class="mb-2">
                              <div class="proceso-card p-2 border rounded mb-1">
                                <div class="d-flex justify-content-between align-items-center">
                                  <div>
                                    <i :class="getNivelIcon(hijo1.proceso_nivel)"></i>
                                    <span :class="getNivelClass(hijo1.proceso_nivel)">
                                      {{ hijo1.cod_proceso }}
                                    </span>
                                    - {{ hijo1.proceso_nombre }}
                                    <span class="badge badge-secondary ml-1">{{ hijo1.proceso_nivel }}</span>
                                    <span class="badge ml-1" :class="hijo1.estado === 1 ? 'badge-success' : 'badge-secondary'">
                                      {{ hijo1.estado === 1 ? 'Vigente' : 'Inactivo' }}
                                    </span>
                                  </div>
                                  <div>
                                    <span class="text-muted small">
                                      {{ hijo1.nombre_ouo_propietario ? hijo1.nombre_ouo_propietario : 'Sin propietario' }}
                                    </span>
                                  </div>
                                </div>
                                
                                <!-- Nivel 2 -->
                                <div v-if="hasChildren(hijo1.id)" class="mt-1 ml-3">
                                  <div v-for="hijo2 in getChildProcesos(hijo1.id)" :key="hijo2.id" class="mb-1">
                                    <div class="proceso-card p-1 border rounded mb-1">
                                      <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                          <i :class="getNivelIcon(hijo2.proceso_nivel)"></i>
                                          <span :class="getNivelClass(hijo2.proceso_nivel)">
                                            {{ hijo2.cod_proceso }}
                                          </span>
                                          - {{ hijo2.proceso_nombre }}
                                          <span class="badge badge-secondary ml-1">{{ hijo2.proceso_nivel }}</span>
                                          <span class="badge ml-1" :class="hijo2.estado === 1 ? 'badge-success' : 'badge-secondary'">
                                            {{ hijo2.estado === 1 ? 'Vigente' : 'Inactivo' }}
                                          </span>
                                        </div>
                                        <div>
                                          <span class="text-muted small">
                                            {{ hijo2.nombre_ouo_propietario ? hijo2.nombre_ouo_propietario : 'Sin propietario' }}
                                          </span>
                                        </div>
                                      </div>
                                      
                                      <!-- Nivel 3 -->
                                      <div v-if="hasChildren(hijo2.id)" class="mt-1 ml-2">
                                        <div v-for="hijo3 in getChildProcesos(hijo2.id)" :key="hijo3.id" class="mb-1">
                                          <div class="proceso-card p-1 border rounded">
                                            <div class="d-flex justify-content-between align-items-center">
                                              <div>
                                                <i :class="getNivelIcon(hijo3.proceso_nivel)"></i>
                                                <span :class="getNivelClass(hijo3.proceso_nivel)">
                                                  {{ hijo3.cod_proceso }}
                                                </span>
                                                - {{ hijo3.proceso_nombre }}
                                                <span class="badge badge-secondary ml-1">{{ hijo3.proceso_nivel }}</span>
                                                <span class="badge ml-1" :class="hijo3.estado === 1 ? 'badge-success' : 'badge-secondary'">
                                                  {{ hijo3.estado === 1 ? 'Vigente' : 'Inactivo' }}
                                                </span>
                                              </div>
                                              <div>
                                                <span class="text-muted small">
                                                  {{ hijo3.nombre_ouo_propietario ? hijo3.nombre_ouo_propietario : 'Sin propietario' }}
                                                </span>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div v-if="rootProcesos.length === 0" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No hay procesos asociados a este inventario</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Estadísticas -->
              <div class="row">
                <div class="col-md-6">
                  <div class="card card-body bg-light">
                    <h6 class="font-weight-bold">Estadísticas del Inventario</h6>
                    <p class="mb-1">Total procesos: <strong>{{ allProcesos.length }}</strong></p>
                    <p class="mb-1">Procesos nivel 0: <strong>{{ procesosNivel(0) }}</strong></p>
                    <p class="mb-1">Procesos nivel 1: <strong>{{ procesosNivel(1) }}</strong></p>
                    <p class="mb-0">Procesos nivel 2-3: <strong>{{ procesosNivel(2) + procesosNivel(3) }}</strong></p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card card-body bg-light">
                    <h6 class="font-weight-bold">Estados</h6>
                    <p class="mb-1">Vigentes: <strong class="text-success">{{ procesosVigentes }}</strong></p>
                    <p class="mb-0">Inactivos: <strong class="text-secondary">{{ procesosInactivos }}</strong></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useInventarioStore } from '@/stores/inventarioStore';
import axios from 'axios';

const inventarioStore = useInventarioStore();

// Reactive data
const allProcesos = ref([]);
const localLoading = ref(false);
const searchTerm = ref('');
const nivelFilter = ref('');
const estadoFilter = ref('');

// Función para cargar procesos asociados al inventario
const loadProcesos = async () => {
  if (!inventarioStore.currentInventario?.id) return;
  
  localLoading.value = true;
  try {
    const response = await axios.get(`/api/inventarios/${inventarioStore.currentInventario.id}/procesos-asociados`);
    allProcesos.value = response.data.map(proc => ({
      ...proc,
      estado_texto: proc.estado === 1 ? 'Vigente' : 'Inactivo'
    }));
  } catch (error) {
    console.error('Error al cargar procesos asociados:', error);
  } finally {
    localLoading.value = false;
  }
};

// Procesos filtrados
const filteredProcesos = computed(() => {
  return allProcesos.value.filter(proceso => {
    const matchesSearch = !searchTerm.value || 
      proceso.proceso_nombre.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      proceso.cod_proceso.toLowerCase().includes(searchTerm.value.toLowerCase());
    
    const matchesNivel = !nivelFilter.value || proceso.proceso_nivel.toString() === nivelFilter.value;
    const matchesEstado = !estadoFilter.value || proceso.estado.toString() === estadoFilter.value;
    
    return matchesSearch && matchesNivel && matchesEstado;
  });
});

// Procesos raíz (sin padre)
const rootProcesos = computed(() => {
  return filteredProcesos.value.filter(p => !p.proceso_padre_id || p.proceso_padre_id === null);
});

// Funciones auxiliares
const getChildProcesos = (parentId) => {
  return filteredProcesos.value.filter(p => p.proceso_padre_id == parentId);
};

const hasChildren = (id) => {
  return getChildProcesos(id).length > 0;
};

const getNivelIcon = (nivel) => {
  switch (nivel) {
    case 0: return 'fas fa-cube';
    case 1: return 'fas fa-sitemap';
    case 2: return 'fas fa-project-diagram';
    case 3: return 'fas fa-circle';
    default: return 'fas fa-asterisk';
  }
};

const getNivelClass = (nivel) => {
  switch (nivel) {
    case 0: return 'font-weight-bold';
    case 1: return 'font-weight-semibold';
    default: return '';
  }
};

const procesosNivel = (nivel) => {
  return allProcesos.value.filter(p => p.proceso_nivel == nivel).length;
};

const procesosVigentes = computed(() => {
  return allProcesos.value.filter(p => p.estado === 1).length;
});

const procesosInactivos = computed(() => {
  return allProcesos.value.filter(p => p.estado === 0).length;
});

// Aplicar filtros
const applyFilters = () => {
  // Los filtros se aplican automáticamente gracias al computed
};

// Hook de ciclo de vida
onMounted(async () => {
  await loadProcesos();
});
</script>

<style scoped>
.header-container {
  padding: 0.75rem;
  margin-bottom: 1.5rem;
  background-color: #f8f9fa;
  border-radius: 0.25rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  border-left: 0.5rem solid #ff851b;
  display: flex;
  align-items: center;
}

.form-control {
  font-size: 0.9rem;
}

.font-weight-semibold {
  font-weight: 500;
}

.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
}

.proceso-card {
  background-color: #fff;
  transition: all 0.2s ease-in-out;
}

.proceso-card:hover {
  background-color: #f8f9fa;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
}

.badge {
  font-size: 0.7em;
}

/* Estilos para listas de procesos */
.border {
  border: 1px solid #ced4da;
}

/* Efectos hover para interactividad */
.card:hover {
  transition: all 0.2s ease-in-out;
}
</style>
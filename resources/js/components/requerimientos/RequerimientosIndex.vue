<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link :to="{ name: 'requerimientos.index' }">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Bandeja de Requerimientos</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">Lista de Requerimientos</h3>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="#" class="btn btn-primary btn-sm ml-1"
                                    @click.prevent="goToCreateRequerimiento">
                                    <i class="fas fa-plus-circle"></i> Nuevo Requerimiento
                                </a>

                                <button class="btn btn-danger btn-sm ml-1" id="btnEliminar" :disabled="!selectedRequerimientoId"
                                    @click="confirmDelete(selectedRequerimientoId)">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <form @submit.prevent="fetchRequerimientos">
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="text" name="buscar_requerimiento" id="buscar_requerimiento"
                                                class="form-control" placeholder="Buscar por Requerimiento"
                                                v-model="filters.buscar_requerimiento">
                                        </div>
                                        <div class="col">
                                            <select name="especialista_id" id="especialista_id" class="form-control"
                                                v-model="filters.especialista_id">
                                                <option value="">Todos los especialistas</option>
                                                <option v-for="especialista in especialistas" :key="especialista.id"
                                                    :value="especialista.id">
                                                    {{ especialista.user.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <select name="estado" id="estado" class="form-control"
                                                v-model="filters.estado">
                                                <option value="">Todos los estados</option>
                                                <option v-for="status in statuses" :key="status"
                                                    :value="status">
                                                    {{ status }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn bg-dark">
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="requerimientos" class="table table-bordered table-hover table-sm table-requerimientos"
                            style="width:100%">
                            <thead class="table-header">
                                <tr>
                                    <th>ID</th>
                                    <th style="width:15%">Proceso</th>
                                    <th style="width:15%">Asunto</th>
                                    <th>Complejidad</th>
                                    <th>Estado</th>
                                    <th>Especialista</th>
                                    <th>Fecha Asignación</th>
                                    <th>Fecha Límite</th>
                                    <th>Fecha Atención</th>
                                    <th>Ultimo Avance</th>
                                    <th>Avance</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="requerimiento in requerimientos" :key="requerimiento.id">
                                    <td>{{ requerimiento.id }}</td>
                                    <td>{{ requerimiento.proceso?.proceso_nombre }}</td>
                                    <td>{{ requerimiento.asunto }}</td>
                                    <td>{{ requerimiento.complejidad }}</td>
                                    <td>{{ requerimiento.estado }}</td>
                                    <td>{{ requerimiento.especialista?.name }}</td>
                                    <td>{{ formatDate(requerimiento.fecha_asignacion) }}</td>
                                    <td>{{ formatDate(requerimiento.fecha_limite) }}</td>
                                    <td>{{ formatDate(requerimiento.fecha_fin) }}</td>
                                    <td>{{ formatDate(requerimiento.avance?.updated_at) }}</td>
                                    <td>
                                        <template v-if="['creado', 'desestimado'].includes(requerimiento.estado)">
                                            <span class="small text-muted">Sin avance</span>
                                        </template>
                                        <template v-else-if="requerimiento.avance">
                                            <div class="small text-center">
                                                {{ parseInt(requerimiento.avance.avance_registrado) }}%
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-info"
                                                        :style="{ width: parseInt(requerimiento.avance.avance_registrado) + '%' }">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <span class="small text-muted">Sin avance</span>
                                        </template>
                                    </td>
                                                                        <td class="text-nowrap">

                                                                            <template v-if="!isMyRequerimientosView">
                                                                                <a href="#" title="Evaluar Requerimiento"
                                                                                    class="mr-2 d-inline-block btn-modal-trigger"
                                                                                    @click.prevent="openModal('mostrarEvaluacion', requerimiento)">
                                                                                    <i class="fas fa-clipboard-check text-primary"></i>
                                                                                </a>
                                                                                <a href="#" title="Asignar Requerimiento"
                                                                                    class="mr-2 d-inline-block btn-modal-trigger"
                                                                                    @click.prevent="openModal('mostrarAsignacion', requerimiento)">
                                                                                    <i class="fas fa-user-check text-dark"></i>
                                                                                </a>
                                                                                <a href="#" title="Registrar Avance Requerimiento"
                                                                                    class="mr-2 d-inline-block btn-modal-trigger"
                                                                                    @click.prevent="openModal('abrirAvanceRequerimientoModal', requerimiento)">
                                    
                                                                                    <i class="fas fa-list-alt text-dark"></i>
                                                                                </a>
                                                                            </template>
                                                                            <a href="#" title="Ver Avance Requerimiento"
                                                                                class="mr-2 d-inline-block btn-modal-trigger"
                                                                                @click.prevent="openModal('mostrarSeguimiento', requerimiento)">
                                                                                <i class="fas fa-stream text-success"></i>
                                                                            </a>
                                                                            <template v-if="isMyRequerimientosView && requerimiento.estado === 'creado'">
                                                                                <a href="#" title="Eliminar Requerimiento"
                                                                                    class="mr-2 d-inline-block btn-modal-trigger"
                                                                                    @click.prevent="confirmDelete(requerimiento.id)">
                                                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                                                </a>
                                                                            </template>
                                                                        </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Existing Vue Modals (ensure they are imported and registered) -->
        <requerimiento-asignacion-modal :especialistas="especialistas" @asignacion-guardada="fetchRequerimientos"></requerimiento-asignacion-modal>
        <requerimiento-evaluacion-modal @evaluacion-guardada="fetchRequerimientos"></requerimiento-evaluacion-modal>
        <requerimiento-seguimiento-modal></requerimiento-seguimiento-modal>
        <requerimiento-avance-modal @avance-guardado="fetchRequerimientos"></requerimiento-avance-modal>
        <evidencias-modal></evidencias-modal>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';

const router = useRouter();

const requerimientos = ref([]);
const especialistas = ref([]);
const statuses = ref([]);

const filters = ref({
    buscar_requerimiento: '',
    especialista_id: '',
    estado: '',
});

const isMyRequerimientosView = computed(() => router.currentRoute.value.name === 'requerimientos.mine');

const fetchRequerimientos = async () => {
    try {
         const response = await axios.get(route('web.requerimientos.data'), {
            params: filters.value
        });
        requerimientos.value = response.data.requerimientos;
        especialistas.value = response.data.especialistas;
        statuses.value = response.data.statuses;
    } catch (error) {
        console.error('Error fetching requerimientos:', error);
    }
};



const openModal = (eventName, requerimiento) => {
    document.dispatchEvent(new CustomEvent(eventName, {
        detail: requerimiento
    }));
};

const goToCreateRequerimiento = () => {
    router.push({ name: 'requerimientos.create' });
};



const confirmDelete = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este requerimiento?')) {
        // Implement delete logic here, e.g., axios.delete(`/api/requerimientos/${id}`)
        console.log('Deleting requerimiento:', id);
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

onMounted(() => {
    // Check if 'mine=true' query parameter is present for 'My Requerimientos' view
    const route = useRouter().currentRoute.value;
    if (route.query.mine === 'true' && window.App && window.App.user && window.App.user.id) {
        filters.value.user_id = window.App.user.id;
    }
    fetchRequerimientos();
});
// Script logic will go here
</script>

<style scoped>
#requerimientos.table-requerimientos {
    font-size: 12px !important;
}
#requerimientos.table-requerimientos thead th {
    vertical-align: top !important;
}
.table-active {
    background-color: #e2e6ea; /* Bootstrap's light gray for active row */
}
</style>
<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link :to="{ name: 'requerimientos.index' }">Inicio</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Mis Requerimientos</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">Mis Requerimientos</h3>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="#" class="btn btn-primary btn-sm ml-1"
                                    @click.prevent="goToCreateRequerimiento">
                                    <i class="fas fa-plus-circle"></i> Nuevo Requerimiento
                                </a>

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
                                            <select name="estado" id="estado" class="form-control"
                                                v-model="filters.estado">
                                                <option value="">Todos los estados</option>
                                                <option v-for="status in statuses" :key="status" :value="status">
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
                        <table id="requerimientos"
                            class="table table-bordered table-hover table-sm table-requerimientos" style="width:100%">
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


                                        <a href="#" title="Ver Avance Requerimiento"
                                            class="mr-2 d-inline-block btn-modal-trigger"
                                            @click.prevent="openModal('mostrarSeguimiento', requerimiento)">
                                            <i class="fas fa-stream text-success"></i>

                                        </a>
                                        <a href="#" title="Editar Requerimiento"
                                            class="mr-2 d-inline-block btn-modal-trigger"
                                            @click.prevent="requerimiento.estado === 'creado' && editRequerimiento(requerimiento.id)"
                                            :class="{ 'disabled': requerimiento.estado !== 'creado' }">
                                            <i class="fas fa-pencil-alt" :class="{ 'text-warning': requerimiento.estado === 'creado', 'text-secondary': requerimiento.estado !== 'creado' }"></i>
                                        </a>

                                        <a href="#" title="Eliminar Requerimiento"
                                            class="mr-2 d-inline-block btn-modal-trigger"
                                            @click.prevent="requerimiento.estado === 'creado' && confirmDelete(requerimiento.id)"
                                            :class="{ 'disabled': requerimiento.estado !== 'creado' }">
                                            <i class="fas fa-trash-alt" :class="{ 'text-danger': requerimiento.estado === 'creado', 'text-secondary': requerimiento.estado !== 'creado' }"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Existing Vue Modals (ensure they are imported and registered) -->

        <requerimiento-seguimiento-modal></requerimiento-seguimiento-modal>

        <evidencias-modal></evidencias-modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';

const router = useRouter();

const requerimientos = ref([]);
const statuses = ref([]);



const filters = ref({
    buscar_requerimiento: '',
    estado: '',
});

const fetchRequerimientos = async () => {
    try {
        const response = await axios.get(route('web.requerimientos.data'), {
            params: { ...filters.value, mine: true }
        });
        requerimientos.value = response.data.requerimientos;
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

const editRequerimiento = (id) => {
    router.push({ name: 'requerimientos.edit', params: { id: id } });
};



const confirmDelete = async (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este requerimiento?')) {
        try {
            await axios.delete(route('web.requerimientos.destroy', id));
            fetchRequerimientos(); // Refresh the list after deletion
        } catch (error) {
            console.error('Error deleting requerimiento:', error);
            alert('Error al eliminar el requerimiento.');
        }
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

onMounted(() => {
    filters.value.user_id = window.App.user.id;
    fetchRequerimientos();
});
</script>

<style scoped>
#requerimientos.table-requerimientos {
    font-size: 12px !important;
}

#requerimientos.table-requerimientos thead th {
    vertical-align: top !important;
}

.table-active {
    background-color: #e2e6ea;
    /* Bootstrap's light gray for active row */
}
</style>
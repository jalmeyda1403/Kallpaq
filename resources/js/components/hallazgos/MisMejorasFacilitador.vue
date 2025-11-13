<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">Mis Mejoras (Hallazgos Asignados)</h3>
                    </div>
                    <div class="card-body">
                        <div v-if="loading" class="text-center my-5">
                            <div class="spinner-border text-info" role="status">
                                <span class="sr-only">Cargando hallazgos...</span>
                            </div>
                        </div>
                        <div v-else-if="!hallazgos.length" class="alert alert-info text-center">
                            No tienes hallazgos asignados a tus procesos.
                        </div>
                        <div v-else class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Resumen</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                        <th>Proceso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="hallazgo in hallazgos" :key="hallazgo.id">
                                        <td>{{ hallazgo.hallazgo_cod }}</td>
                                        <td>{{ hallazgo.hallazgo_resumen }}</td>
                                        <td>{{ hallazgo.hallazgo_descripcion }}</td>
                                        <td>
                                            <span :class="{'badge badge-warning': hallazgo.hallazgo_estado === 'Pendiente', 'badge badge-success': hallazgo.hallazgo_estado === 'Cerrado'}">
                                                {{ hallazgo.hallazgo_estado }}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-for="hp in hallazgo.hallazgo_procesos" :key="hp.id" class="badge badge-secondary mr-1">
                                                {{ hp.proceso.proceso_nombre }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" @click="viewHallazgo(hallazgo.id)">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';

const hallazgos = ref([]);
const loading = ref(true);
const router = useRouter();

onMounted(async () => {
    await fetchHallazgos();
});

const fetchHallazgos = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('facilitador.dashboard.hallazgos'));
        hallazgos.value = response.data;
    } catch (error) {
        console.error('Error fetching facilitator hallazgos:', error);
        alert('No se pudieron cargar los hallazgos asignados.');
    } finally {
        loading.value = false;
    }
};

const viewHallazgo = (id) => {
    // Assuming you have a route to view a specific hallazgo
    router.push({ name: 'hallazgos.show', params: { id: id } });
};
</script>

<style scoped>
/* Add any specific styles here */
</style>

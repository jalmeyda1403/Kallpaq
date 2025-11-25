<template>
    <div>
        <div v-if="loading" class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
        </div>
        <div v-else>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Mis Procesos (Gestor)</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item" v-for="proceso in procesos" :key="proceso.id">
                                    <a href="#" class="nav-link" 
                                       :class="{ active: selectedProceso && selectedProceso.id === proceso.id }"
                                       @click.prevent="selectProceso(proceso)">
                                        <i class="fas fa-cogs"></i> {{ proceso.proceso_nombre }}
                                    </a>
                                </li>
                            </ul>
                            <div v-if="procesos.length === 0" class="p-3 text-muted">
                                No tienes procesos asignados como Gestor.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div v-if="selectedProceso" class="card">
                        <div class="card-header">
                            <h3 class="card-title">Indicadores de: {{ selectedProceso.proceso_nombre }}</h3>
                            <div class="card-tools">
                                <button class="btn btn-primary btn-sm" @click="openCreateModal">
                                    <i class="fas fa-plus"></i> Nuevo Indicador
                                </button>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Frecuencia</th>
                                        <th>Meta</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="indicador in indicadores" :key="indicador.id">
                                        <td>{{ indicador.nombre }}</td>
                                        <td>{{ indicador.frecuencia }}</td>
                                        <td>{{ indicador.meta }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm" @click="editIndicador(indicador)">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="indicadores.length === 0">
                                        <td colspan="4" class="text-center">No hay indicadores definidos.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-else class="alert alert-info">
                        Selecciona un proceso para ver sus indicadores.
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Crear/Editar Indicador -->
        <div class="modal fade" id="indicadorModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Editar' : 'Crear' }} Indicador</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <indicador-form 
                            v-if="showForm" 
                            :proceso="selectedProceso" 
                            :indicador-data="currentIndicador"
                            @saved="onIndicadorSaved"
                            @cancel="closeModal">
                        </indicador-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import IndicadorForm from './IndicadorForm.vue';

export default {
    components: {
        IndicadorForm
    },
    data() {
        return {
            loading: false,
            procesos: [],
            selectedProceso: null,
            indicadores: [],
            showForm: false,
            isEditing: false,
            currentIndicador: null
        }
    },
    mounted() {
        this.fetchProcesos();
    },
    methods: {
        async fetchProcesos() {
            this.loading = true;
            try {
                const response = await axios.get('/api/indicadores-vue/procesos-gestion');
                this.procesos = response.data;
            } catch (error) {
                console.error("Error fetching procesos", error);
                // Toast error
            } finally {
                this.loading = false;
            }
        },
        async selectProceso(proceso) {
            this.selectedProceso = proceso;
            this.fetchIndicadores(proceso.id);
        },
        async fetchIndicadores(procesoId) {
            try {
                const response = await axios.get(`/api/indicadores-vue/procesos/${procesoId}`);
                this.indicadores = response.data;
            } catch (error) {
                console.error("Error fetching indicadores", error);
            }
        },
        openCreateModal() {
            this.isEditing = false;
            this.currentIndicador = null;
            this.showForm = true;
            $('#indicadorModal').modal('show');
        },
        editIndicador(indicador) {
            this.isEditing = true;
            this.currentIndicador = indicador;
            this.showForm = true;
            $('#indicadorModal').modal('show');
        },
        closeModal() {
            $('#indicadorModal').modal('hide');
            this.showForm = false;
        },
        onIndicadorSaved() {
            this.closeModal();
            this.fetchIndicadores(this.selectedProceso.id);
        }
    }
}
</script>

<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Continuidad</li>
                <li class="breadcrumb-item active">Pruebas y Ejercicios</li>
            </ol>
        </nav>

        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show">
            {{ successMessage }}
            <button type="button" class="close" @click="successMessage = ''">×</button>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-vial mr-2"></i>
                            Pruebas y Ejercicios
                        </h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-light" @click="showModal = true">
                            <i class="fas fa-plus"></i> Nueva Prueba
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <SkeletonLoader v-if="isLoading" type="table" :rows="5" />

                <EmptyState 
                    v-else-if="!isLoading && pruebas.length === 0"
                    title="No hay pruebas programadas"
                    description="Programa pruebas para validar tus planes de continuidad"
                    icon="fas fa-clipboard-check"
                    action-text="Nueva Prueba"
                    @action="showModal = true"
                />

                <div v-else class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Plan</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Calificación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="prueba in pruebas" :key="prueba.id" 
                                :class="{ 'table-warning': prueba.esta_vencida }">
                                <td><strong>{{ prueba.codigo }}</strong></td>
                                <td>{{ prueba.nombre }}</td>
                                <td>{{ prueba.plan?.codigo }}</td>
                                <td>{{ tiposPrueba[prueba.tipo_prueba] || prueba.tipo_prueba }}</td>
                                <td>
                                    {{ formatDate(prueba.fecha_programada) }}
                                    <span v-if="prueba.esta_vencida" class="badge badge-danger ml-1">Vencida</span>
                                </td>
                                <td>
                                    <span class="badge" :class="'badge-' + prueba.estado_color">
                                        {{ prueba.estado }}
                                    </span>
                                </td>
                                <td>
                                    <span v-if="prueba.calificacion">
                                        <i v-for="i in 5" :key="i" class="fas fa-star" 
                                           :class="i <= prueba.calificacion ? 'text-warning' : 'text-muted'"></i>
                                    </span>
                                    <span v-else class="text-muted">-</span>
                                </td>
                                <td>
                                    <button v-if="prueba.estado === 'programada'" 
                                            class="btn btn-sm btn-success mr-1" @click="registrar(prueba)"
                                            title="Registrar Resultados">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" @click="verDetalle(prueba)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Nueva Prueba -->
        <div v-if="showModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Nueva Prueba</h5>
                        <button type="button" class="close text-white" @click="cerrarModal">×</button>
                    </div>
                    <form @submit.prevent="guardar">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="required">Nombre</label>
                                        <input type="text" v-model="form.nombre" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Plan a Probar</label>
                                        <select v-model="form.plan_id" class="form-control" required>
                                            <option value="">Seleccionar...</option>
                                            <option v-for="plan in planes" :key="plan.id" :value="plan.id">
                                                {{ plan.codigo }} - {{ plan.nombre }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Tipo de Prueba</label>
                                        <select v-model="form.tipo_prueba" class="form-control" required>
                                            <option v-for="(label, value) in tiposPrueba" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Fecha Programada</label>
                                        <input type="date" v-model="form.fecha_programada" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Responsable</label>
                                        <select v-model="form.responsable_id" class="form-control" required>
                                            <option value="">Seleccionar...</option>
                                            <option v-for="user in usuarios" :key="user.id" :value="user.id">
                                                {{ user.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="required">Objetivo</label>
                                <textarea v-model="form.objetivo" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Participantes</label>
                                <textarea v-model="form.participantes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Programar Prueba</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue';
import { useContinuidadStore } from '@/stores/continuidadStore';
import SkeletonLoader from '@/components/generales/SkeletonLoader.vue';
import EmptyState from '@/components/generales/EmptyState.vue';
import axios from 'axios';

const store = useContinuidadStore();

const showModal = ref(false);
const successMessage = ref('');
const usuarios = ref([]);

const isLoading = computed(() => store.isLoading);
const pruebas = computed(() => store.pruebas);
const planes = computed(() => store.planes);
const tiposPrueba = computed(() => store.tiposPrueba);

const form = reactive({
    nombre: '',
    plan_id: '',
    tipo_prueba: 'simulacion',
    fecha_programada: '',
    responsable_id: '',
    objetivo: '',
    participantes: ''
});

const cerrarModal = () => {
    showModal.value = false;
    Object.keys(form).forEach(k => form[k] = '');
    form.tipo_prueba = 'simulacion';
};

const guardar = async () => {
    try {
        await store.createPrueba(form);
        successMessage.value = 'Prueba programada exitosamente';
        cerrarModal();
        await store.fetchPruebas();
    } catch (err) {
        alert('Error al programar prueba');
    }
};

const registrar = (prueba) => {
    // TODO: Modal para registrar resultados
    alert('Registrar resultados - Por implementar');
};

const verDetalle = (prueba) => {
    // TODO: Ver detalle
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-PE');
};

onMounted(async () => {
    try {
        const res = await axios.get('/users/list');
        usuarios.value = res.data;
    } catch (err) {
        console.error('Error cargando usuarios');
    }
    await store.fetchTiposPrueba();
    await store.fetchPlanes();
    await store.fetchPruebas();
});
</script>

<style scoped>
label.required::after { content: ' *'; color: red; }
</style>

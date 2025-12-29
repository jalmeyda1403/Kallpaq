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
                <!-- Empty -->
                <EmptyState v-if="!isLoading && pruebas.length === 0" title="No hay pruebas programadas"
                    description="Programa pruebas para validar tus planes de continuidad" icon="fas fa-clipboard-check"
                    action-text="Nueva Prueba" @action="showModal = true" />

                <div v-else>
                    <DataTable :value="pruebas" :loading="isLoading" :paginator="true" :rows="10"
                        responsiveLayout="scroll" :rowClass="rowClass">
                        <Column field="codigo" header="Código">
                            <template #body="{ data }">
                                <strong>{{ data.codigo }}</strong>
                            </template>
                        </Column>
                        <Column field="nombre" header="Nombre"></Column>
                        <Column field="plan.codigo" header="Plan">
                            <template #body="{ data }">
                                {{ data.plan?.codigo }}
                            </template>
                        </Column>
                        <Column field="tipo_prueba" header="Tipo">
                            <template #body="{ data }">
                                {{ tiposPrueba[data.tipo_prueba] || data.tipo_prueba }}
                            </template>
                        </Column>
                        <Column field="fecha_programada" header="Fecha">
                            <template #body="{ data }">
                                {{ formatDate(data.fecha_programada) }}
                                <span v-if="data.esta_vencida" class="badge badge-danger ml-1">Vencida</span>
                            </template>
                        </Column>
                        <Column header="Estado">
                            <template #body="{ data }">
                                <span class="badge" :class="'badge-' + data.estado_color">
                                    {{ data.estado }}
                                </span>
                            </template>
                        </Column>
                        <Column header="Calificación">
                            <template #body="{ data }">
                                <span v-if="data.calificacion">
                                    <i v-for="i in 5" :key="i" class="fas fa-star"
                                        :class="i <= data.calificacion ? 'text-warning' : 'text-muted'"></i>
                                </span>
                                <span v-else class="text-muted">-</span>
                            </template>
                        </Column>
                        <Column header="Acciones">
                            <template #body="{ data }">
                                <Button v-if="data.estado === 'programada'" icon="pi pi-check"
                                    class="p-button-rounded p-button-success p-button-text mr-1"
                                    @click="registrar(data)" title="Registrar Resultados" />
                                <Button icon="pi pi-eye" class="p-button-rounded p-button-info p-button-text"
                                    @click="verDetalle(data)" />
                            </template>
                        </Column>
                    </DataTable>
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
                                        <input type="date" v-model="form.fecha_programada" class="form-control"
                                            required>
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
import EmptyState from '@/components/generales/EmptyState.vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

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

const rowClass = (data) => {
    return data.esta_vencida ? 'bg-warning-light' : '';
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
label.required::after {
    content: ' *';
    color: red;
}

.bg-warning-light {
    background-color: #fff3cd !important;
}

/* Custom loader styles */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0) !important;
}

.p-datatable-loading-icon {
    color: red !important;
    font-size: 2rem !important;
}
</style>

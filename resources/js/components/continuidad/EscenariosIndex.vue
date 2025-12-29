<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Continuidad</li>
                <li class="breadcrumb-item active">Escenarios de Riesgo</li>
            </ol>
        </nav>

        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show">
            {{ successMessage }}
            <button type="button" class="close" @click="successMessage = ''">×</button>
        </div>

        <div class="card">
            <div class="card-header bg-warning">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Escenarios de Interrupción
                        </h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-dark" @click="showModal = true">
                            <i class="fas fa-plus"></i> Nuevo Escenario
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Empty -->
                <EmptyState v-if="!isLoading && escenarios.length === 0" title="No hay escenarios definidos"
                    description="Identifica los posibles escenarios de interrupción" icon="fas fa-bolt"
                    action-text="Nuevo Escenario" @action="showModal = true" />

                <div v-else>
                    <DataTable :value="escenarios" :loading="isLoading" :paginator="true" :rows="10"
                        responsiveLayout="scroll">
                        <Column field="codigo" header="Código">
                            <template #body="{ data }">
                                <strong>{{ data.codigo }}</strong>
                            </template>
                        </Column>
                        <Column field="nombre" header="Nombre"></Column>
                        <Column field="categoria" header="Categoría">
                            <template #body="{ data }">
                                {{ categorias[data.categoria] || data.categoria }}
                            </template>
                        </Column>
                        <Column field="probabilidad" header="Probabilidad"></Column>
                        <Column field="impacto" header="Impacto"></Column>
                        <Column header="Nivel Riesgo">
                            <template #body="{ data }">
                                <span class="badge badge-pill" :class="'badge-' + data.nivel_riesgo_color">
                                    {{ data.nivel_riesgo_label }} ({{ data.nivel_riesgo }})
                                </span>
                            </template>
                        </Column>
                        <Column header="Acciones">
                            <template #body="{ data }">
                                <Button icon="pi pi-pencil" class="p-button-rounded p-button-info p-button-text mr-1"
                                    @click="editar(data)" />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Modal simple para crear/editar escenario -->
        <div v-if="showModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">{{ isEdit ? 'Editar' : 'Nuevo' }} Escenario</h5>
                        <button type="button" class="close" @click="cerrarModal">×</button>
                    </div>
                    <form @submit.prevent="guardar">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="required">Nombre</label>
                                <input type="text" v-model="form.nombre" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="required">Descripción</label>
                                <textarea v-model="form.descripcion" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Categoría</label>
                                        <select v-model="form.categoria" class="form-control" required>
                                            <option value="">Seleccionar...</option>
                                            <option v-for="(label, value) in categorias" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Probabilidad</label>
                                        <select v-model="form.probabilidad" class="form-control" required>
                                            <option value="muy_baja">Muy Baja</option>
                                            <option value="baja">Baja</option>
                                            <option value="media">Media</option>
                                            <option value="alta">Alta</option>
                                            <option value="muy_alta">Muy Alta</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="required">Impacto</label>
                                        <select v-model="form.impacto" class="form-control" required>
                                            <option value="insignificante">Insignificante</option>
                                            <option value="menor">Menor</option>
                                            <option value="moderado">Moderado</option>
                                            <option value="mayor">Mayor</option>
                                            <option value="catastrofico">Catastrófico</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Controles Existentes</label>
                                <textarea v-model="form.controles_existentes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal">Cancelar</button>
                            <button type="submit" class="btn btn-warning">Guardar</button>
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
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const store = useContinuidadStore();

const showModal = ref(false);
const escenarioEditar = ref(null);
const successMessage = ref('');

const isLoading = computed(() => store.isLoading);
const escenarios = computed(() => store.escenarios);
const categorias = computed(() => store.categorias);
const isEdit = computed(() => !!escenarioEditar.value);

const form = reactive({
    nombre: '',
    descripcion: '',
    categoria: '',
    probabilidad: 'media',
    impacto: 'moderado',
    controles_existentes: ''
});

const editar = (esc) => {
    escenarioEditar.value = esc;
    Object.assign(form, {
        nombre: esc.nombre,
        descripcion: esc.descripcion,
        categoria: esc.categoria,
        probabilidad: esc.probabilidad,
        impacto: esc.impacto,
        controles_existentes: esc.controles_existentes || ''
    });
    showModal.value = true;
};

const cerrarModal = () => {
    showModal.value = false;
    escenarioEditar.value = null;
    Object.assign(form, { nombre: '', descripcion: '', categoria: '', probabilidad: 'media', impacto: 'moderado', controles_existentes: '' });
};

const guardar = async () => {
    try {
        if (isEdit.value) {
            await store.updateEscenario(escenarioEditar.value.id, form);
            successMessage.value = 'Escenario actualizado';
        } else {
            await store.createEscenario(form);
            successMessage.value = 'Escenario creado';
        }
        cerrarModal();
        await store.fetchEscenarios();
    } catch (err) {
        alert('Error al guardar');
    }
};

onMounted(async () => {
    await store.fetchCategorias();
    await store.fetchEscenarios();
});
</script>

<style scoped>
label.required::after {
    content: ' *';
    color: red;
}

.badge-orange {
    background-color: #fd7e14;
    color: white;
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

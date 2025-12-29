<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-check mr-2"></i>
                        {{ isEdit ? 'Editar Revisión' : 'Nueva Revisión por la Dirección' }}
                    </h5>
                    <button type="button" class="close text-white" @click="$emit('close')">
                        <span>×</span>
                    </button>
                </div>

                <form @submit.prevent="guardar">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="required">Título de la Revisión</label>
                                    <input type="text" v-model="form.titulo" class="form-control"
                                        :class="{ 'is-invalid': errors.titulo }"
                                        placeholder="Ej: Revisión por la Dirección Q1 2025" required>
                                    <div class="invalid-feedback">{{ errors.titulo }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">Periodo</label>
                                    <select v-model="form.periodo" class="form-control" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Q1">Q1 (Ene-Mar)</option>
                                        <option value="Q2">Q2 (Abr-Jun)</option>
                                        <option value="Q3">Q3 (Jul-Sep)</option>
                                        <option value="Q4">Q4 (Oct-Dic)</option>
                                        <option value="S1">S1 (Ene-Jun)</option>
                                        <option value="S2">S2 (Jul-Dic)</option>
                                        <option value="A">Anual</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">Año</label>
                                    <select v-model="form.anio" class="form-control" required>
                                        <option v-for="a in anios" :key="a" :value="a">{{ a }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">Fecha Programada</label>
                                    <input type="date" v-model="form.fecha_programada" class="form-control"
                                        :class="{ 'is-invalid': errors.fecha_programada }" required>
                                    <div class="invalid-feedback">{{ errors.fecha_programada }}</div>
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
                            <label>Participantes</label>
                            <textarea v-model="form.participantes" class="form-control" rows="2"
                                placeholder="Lista de participantes de la reunión..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Agenda</label>
                            <textarea v-model="form.agenda" class="form-control" rows="3"
                                placeholder="Puntos a tratar en la reunión..."></textarea>
                        </div>

                        <div v-if="isEdit" class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha de Reunión (real)</label>
                                    <input type="date" v-model="form.fecha_reunion" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select v-model="form.estado" class="form-control">
                                        <option value="programada">Programada</option>
                                        <option value="en_preparacion">En Preparación</option>
                                        <option value="realizada">Realizada</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$emit('close')">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="isLoading">
                            <i class="fas fa-save mr-1"></i>
                            {{ isLoading ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';
import axios from 'axios';

const emit = defineEmits(['close', 'saved']);
const store = useRevisionDireccionStore();

const usuarios = ref([]);
const errors = reactive({});

const isEdit = computed(() => store.modalMode === 'edit');
const isLoading = computed(() => store.isLoading);

const anios = computed(() => {
    const currentYear = new Date().getFullYear();
    return [currentYear + 1, currentYear, currentYear - 1];
});

const form = reactive({
    titulo: '',
    periodo: '',
    anio: new Date().getFullYear(),
    fecha_programada: '',
    fecha_reunion: '',
    responsable_id: '',
    participantes: '',
    agenda: '',
    estado: 'programada'
});

// Cargar datos si es edición
onMounted(async () => {
    // Cargar usuarios
    try {
        const response = await axios.get('/users/list');
        usuarios.value = response.data;
    } catch (err) {
        console.error('Error al cargar usuarios', err);
    }

    // Si es edición, cargar datos
    if (isEdit.value && store.revisionActual) {
        Object.assign(form, {
            titulo: store.revisionActual.titulo,
            periodo: store.revisionActual.periodo,
            anio: store.revisionActual.anio,
            fecha_programada: store.revisionActual.fecha_programada?.split('T')[0] || '',
            fecha_reunion: store.revisionActual.fecha_reunion?.split('T')[0] || '',
            responsable_id: store.revisionActual.responsable_id,
            participantes: store.revisionActual.participantes || '',
            agenda: store.revisionActual.agenda || '',
            estado: store.revisionActual.estado
        });
    }
});

const guardar = async () => {
    Object.keys(errors).forEach(k => delete errors[k]);

    try {
        if (isEdit.value) {
            await store.updateRevision(store.revisionActual.id, form);
            emit('saved', 'Revisión actualizada exitosamente');
        } else {
            await store.createRevision(form);
            emit('saved', 'Revisión creada exitosamente');
        }
        emit('close');
    } catch (err) {
        if (err.response?.status === 422) {
            Object.assign(errors, err.response.data.errors);
        } else {
            alert('Error al guardar la revisión');
        }
    }
};
</script>

<style scoped>
.modal.show {
    display: block;
}
label.required::after {
    content: ' *';
    color: red;
}
</style>

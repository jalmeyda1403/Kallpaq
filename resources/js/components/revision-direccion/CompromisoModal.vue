<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="fas fa-tasks mr-2"></i> 
                        {{ isEdit ? 'Editar Compromiso' : 'Nuevo Compromiso de la Dirección' }}
                    </h5>
                    <button type="button" class="close" @click="$emit('close')">×</button>
                </div>

                <form @submit.prevent="guardar">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="required">Descripción del Compromiso</label>
                            <textarea v-model="form.descripcion" class="form-control" rows="3" required
                                placeholder="Describa el compromiso adquirido por la Alta Dirección..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">Responsable</label>
                                    <select v-model="form.responsable_id" class="form-control" required>
                                        <option value="">Seleccionar responsable...</option>
                                        <option v-for="user in usuarios" :key="user.id" :value="user.id">
                                            {{ user.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">Fecha Límite</label>
                                    <input type="date" v-model="form.fecha_limite" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Prioridad</label>
                                    <select v-model="form.prioridad" class="form-control">
                                        <option value="baja">Baja</option>
                                        <option value="media">Media</option>
                                        <option value="alta">Alta</option>
                                        <option value="critica">Crítica</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" v-if="isEdit">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select v-model="form.estado" class="form-control">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="en_proceso">En Proceso</option>
                                        <option value="completado">Completado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" v-if="isEdit">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Avance (%)</label>
                                    <input type="range" v-model.number="form.avance" class="form-control-range" 
                                           min="0" max="100" step="5">
                                    <div class="text-center">{{ form.avance }}%</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Recursos Necesarios</label>
                            <textarea v-model="form.recursos_necesarios" class="form-control" rows="2"
                                placeholder="Recursos requeridos para cumplir el compromiso (opcional)"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea v-model="form.observaciones" class="form-control" rows="2"
                                placeholder="Notas u observaciones adicionales (opcional)"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$emit('close')">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning" :disabled="isLoading">
                            <i class="fas fa-save mr-1"></i>
                            {{ isLoading ? 'Guardando...' : 'Guardar Compromiso' }}
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

const props = defineProps({
    revisionId: { type: [Number, String], required: true },
    compromiso: { type: Object, default: null }
});

const emit = defineEmits(['close', 'saved']);
const store = useRevisionDireccionStore();

const isLoading = ref(false);
const usuarios = ref([]);

const isEdit = computed(() => !!props.compromiso);

const form = reactive({
    descripcion: '',
    responsable_id: '',
    fecha_limite: '',
    prioridad: 'media',
    estado: 'pendiente',
    avance: 0,
    recursos_necesarios: '',
    observaciones: ''
});

onMounted(async () => {
    try {
        const res = await axios.get('/users/list');
        usuarios.value = res.data;
    } catch (err) {
        console.error('Error cargando usuarios');
    }

    // Si es edición, poblar el formulario
    if (isEdit.value) {
        Object.assign(form, {
            descripcion: props.compromiso.descripcion,
            responsable_id: props.compromiso.responsable_id,
            fecha_limite: props.compromiso.fecha_limite?.split('T')[0] || '',
            prioridad: props.compromiso.prioridad || 'media',
            estado: props.compromiso.estado,
            avance: props.compromiso.avance || 0,
            recursos_necesarios: props.compromiso.recursos_necesarios || '',
            observaciones: props.compromiso.observaciones || ''
        });
    }
});

const guardar = async () => {
    isLoading.value = true;
    try {
        if (isEdit.value) {
            await store.updateCompromiso(props.compromiso.id, form);
            emit('saved', 'Compromiso actualizado exitosamente');
        } else {
            await store.addCompromiso(props.revisionId, form);
            emit('saved', 'Compromiso creado exitosamente');
        }
        emit('close');
    } catch (err) {
        alert('Error al guardar: ' + (err.response?.data?.message || err.message));
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
label.required::after {
    content: ' *';
    color: red;
}
</style>

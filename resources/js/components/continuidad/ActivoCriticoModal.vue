<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-cube mr-2"></i>
                        {{ isEdit ? 'Editar Activo Crítico' : 'Nuevo Activo Crítico' }}
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
                                    <label class="required">Nombre del Activo</label>
                                    <input type="text" v-model="form.nombre" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">Tipo</label>
                                    <select v-model="form.tipo" class="form-control" required>
                                        <option value="">Seleccionar...</option>
                                        <option v-for="(label, value) in tiposActivo" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea v-model="form.descripcion" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Proceso Asociado</label>
                                    <select v-model="form.proceso_id" class="form-control">
                                        <option :value="null">Sin proceso</option>
                                        <option v-for="proceso in procesos" :key="proceso.id" :value="proceso.id">
                                            {{ proceso.proceso_nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Responsable</label>
                                    <select v-model="form.responsable_id" class="form-control">
                                        <option :value="null">Sin asignar</option>
                                        <option v-for="user in usuarios" :key="user.id" :value="user.id">
                                            {{ user.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Nivel de Criticidad</label>
                                    <select v-model="form.nivel_criticidad" class="form-control" required>
                                        <option value="bajo">Bajo</option>
                                        <option value="medio">Medio</option>
                                        <option value="alto">Alto</option>
                                        <option value="critico">Crítico</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>RTO (horas)</label>
                                    <input type="number" v-model.number="form.rto" class="form-control" 
                                           min="0" placeholder="0">
                                    <small class="form-text text-muted">Tiempo máximo de recuperación</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>RPO (horas)</label>
                                    <input type="number" v-model.number="form.rpo" class="form-control" 
                                           min="0" placeholder="0">
                                    <small class="form-text text-muted">Pérdida máxima de datos</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>MTPD (horas)</label>
                                    <input type="number" v-model.number="form.mtpd" class="form-control" 
                                           min="0" placeholder="0">
                                    <small class="form-text text-muted">Período máximo tolerable</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Ubicación</label>
                            <input type="text" v-model="form.ubicacion" class="form-control" 
                                   placeholder="Ubicación física o lógica del activo">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$emit('close')">
                            Cancelar
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
import { useContinuidadStore } from '@/stores/continuidadStore';
import axios from 'axios';

const props = defineProps({
    activo: { type: Object, default: null }
});

const emit = defineEmits(['close', 'saved']);
const store = useContinuidadStore();

const usuarios = ref([]);
const procesos = ref([]);
const isLoading = ref(false);

const isEdit = computed(() => !!props.activo);
const tiposActivo = computed(() => store.tiposActivo);

const form = reactive({
    nombre: '',
    descripcion: '',
    tipo: '',
    proceso_id: null,
    responsable_id: null,
    nivel_criticidad: 'medio',
    rto: null,
    rpo: null,
    mtpd: null,
    ubicacion: ''
});

onMounted(async () => {
    // Cargar usuarios y procesos
    try {
        const [usersRes, procesosRes] = await Promise.all([
            axios.get('/users/list'),
            axios.get('/api/procesos/list')
        ]);
        usuarios.value = usersRes.data;
        procesos.value = procesosRes.data;
    } catch (err) {
        console.error('Error cargando datos', err);
    }

    // Si es edición, cargar datos
    if (isEdit.value) {
        Object.assign(form, {
            nombre: props.activo.nombre,
            descripcion: props.activo.descripcion || '',
            tipo: props.activo.tipo,
            proceso_id: props.activo.proceso_id,
            responsable_id: props.activo.responsable_id,
            nivel_criticidad: props.activo.nivel_criticidad,
            rto: props.activo.rto,
            rpo: props.activo.rpo,
            mtpd: props.activo.mtpd,
            ubicacion: props.activo.ubicacion || ''
        });
    }
});

const guardar = async () => {
    isLoading.value = true;
    try {
        if (isEdit.value) {
            await store.updateActivo(props.activo.id, form);
            emit('saved', 'Activo actualizado exitosamente');
        } else {
            await store.createActivo(form);
            emit('saved', 'Activo creado exitosamente');
        }
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

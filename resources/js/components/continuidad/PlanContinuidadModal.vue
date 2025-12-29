<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-medical mr-2"></i>
                        {{ isEdit ? 'Editar Plan' : 'Nuevo Plan de Continuidad' }}
                    </h5>
                    <button type="button" class="close text-white" @click="$emit('close')">×</button>
                </div>

                <form @submit.prevent="guardar">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="required">Nombre del Plan</label>
                                    <input type="text" v-model="form.nombre" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">Tipo de Plan</label>
                                    <select v-model="form.tipo_plan" class="form-control" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="bcp">BCP - Plan de Continuidad</option>
                                        <option value="drp">DRP - Recuperación ante Desastres</option>
                                        <option value="irp">IRP - Respuesta a Incidentes</option>
                                        <option value="crmp">CRMP - Gestión de Crisis</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="required">Objetivo</label>
                            <textarea v-model="form.objetivo" class="form-control" rows="2" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Escenario Asociado</label>
                                    <select v-model="form.escenario_id" class="form-control">
                                        <option :value="null">Ninguno</option>
                                        <option v-for="esc in escenarios" :key="esc.id" :value="esc.id">
                                            {{ esc.codigo }} - {{ esc.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Proceso Cubierto</label>
                                    <select v-model="form.proceso_id" class="form-control">
                                        <option :value="null">Todos</option>
                                        <option v-for="proc in procesos" :key="proc.id" :value="proc.id">
                                            {{ proc.proceso_nombre }}
                                        </option>
                                    </select>
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
                            <label>Alcance</label>
                            <textarea v-model="form.alcance" class="form-control" rows="2" 
                                placeholder="Define qué áreas, procesos o sistemas cubre este plan"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Procedimientos de Activación</label>
                            <textarea v-model="form.procedimientos_activacion" class="form-control" rows="2"
                                placeholder="¿Cuándo y cómo se activa este plan?"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Procedimientos de Recuperación</label>
                            <textarea v-model="form.procedimientos_recuperacion" class="form-control" rows="2"
                                placeholder="Pasos para restaurar las operaciones"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Recursos Necesarios</label>
                                    <textarea v-model="form.recursos_necesarios" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Comunicación de Crisis</label>
                                    <textarea v-model="form.comunicacion_crisis" class="form-control" rows="2"
                                        placeholder="Protocolo de comunicación durante la emergencia"></textarea>
                                </div>
                            </div>
                        </div>

                        <div v-if="isEdit" class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select v-model="form.estado" class="form-control">
                                        <option value="borrador">Borrador</option>
                                        <option value="en_revision">En Revisión</option>
                                        <option value="aprobado">Aprobado</option>
                                        <option value="obsoleto">Obsoleto</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Versión</label>
                                    <input type="text" v-model="form.version" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Fecha Aprobación</label>
                                    <input type="date" v-model="form.fecha_aprobacion" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Próxima Revisión</label>
                                    <input type="date" v-model="form.proxima_revision" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancelar</button>
                        <button type="submit" class="btn btn-success" :disabled="isLoading">
                            <i class="fas fa-save mr-1"></i> {{ isLoading ? 'Guardando...' : 'Guardar' }}
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
    plan: { type: Object, default: null }
});

const emit = defineEmits(['close', 'saved']);
const store = useContinuidadStore();

const usuarios = ref([]);
const procesos = ref([]);
const isLoading = ref(false);

const isEdit = computed(() => !!props.plan);
const escenarios = computed(() => store.escenarios);

const form = reactive({
    nombre: '',
    objetivo: '',
    tipo_plan: '',
    escenario_id: null,
    proceso_id: null,
    responsable_id: '',
    alcance: '',
    procedimientos_activacion: '',
    procedimientos_recuperacion: '',
    recursos_necesarios: '',
    comunicacion_crisis: '',
    estado: 'borrador',
    version: '1.0',
    fecha_aprobacion: null,
    proxima_revision: null
});

onMounted(async () => {
    try {
        const [usersRes, procesosRes] = await Promise.all([
            axios.get('/users/list'),
            axios.get('/api/procesos/list')
        ]);
        usuarios.value = usersRes.data;
        procesos.value = procesosRes.data;
        await store.fetchEscenarios();
    } catch (err) {
        console.error('Error cargando datos', err);
    }

    if (isEdit.value) {
        Object.assign(form, {
            nombre: props.plan.nombre,
            objetivo: props.plan.objetivo,
            tipo_plan: props.plan.tipo_plan,
            escenario_id: props.plan.escenario_id,
            proceso_id: props.plan.proceso_id,
            responsable_id: props.plan.responsable_id,
            alcance: props.plan.alcance || '',
            procedimientos_activacion: props.plan.procedimientos_activacion || '',
            procedimientos_recuperacion: props.plan.procedimientos_recuperacion || '',
            recursos_necesarios: props.plan.recursos_necesarios || '',
            comunicacion_crisis: props.plan.comunicacion_crisis || '',
            estado: props.plan.estado,
            version: props.plan.version,
            fecha_aprobacion: props.plan.fecha_aprobacion?.split('T')[0] || null,
            proxima_revision: props.plan.proxima_revision?.split('T')[0] || null
        });
    }
});

const guardar = async () => {
    isLoading.value = true;
    try {
        if (isEdit.value) {
            await store.updatePlan(props.plan.id, form);
            emit('saved', 'Plan actualizado exitosamente');
        } else {
            await store.createPlan(form);
            emit('saved', 'Plan creado exitosamente');
        }
    } catch (err) {
        alert('Error al guardar: ' + (err.response?.data?.message || err.message));
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
label.required::after { content: ' *'; color: red; }
</style>

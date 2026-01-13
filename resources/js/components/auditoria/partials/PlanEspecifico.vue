<template>
    <div>
        <div class="header-container mb-4">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Información General del Plan</span>
                <span v-if="form.ae_codigo" class="mx-2 text-secondary">
                    <i class="fas fa-chevron-right fa-xs"></i>
                </span>
                <span class="text-dark font-weight-bold">{{ form.ae_codigo }}</span>
            </h6>
        </div>

        <div class="form-overlay-container">
            <div v-if="loading" class="loading-overlay">
                <div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>

            <form @submit.prevent="save">
                <!-- 1. Código de Auditoría y Estado -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group small">
                            <label class="form-label text-danger font-weight-bold p-0 m-0">Tipo de Auditoría</label>
                            <select v-model="form.ae_tipo" class="form-control" @change="generateCode" required>
                                <option value="Interna">Interna (IN)</option>
                                <option value="Externa">Externa (EX)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group small">
                            <label class="form-label text-danger font-weight-bold p-0 m-0">Código Auditoría</label>
                            <div class="input-group">
                                <input type="text" v-model="form.ae_codigo" class="form-control"
                                    placeholder="Autogenerado: AÑO - ### - IN/EX" readonly />
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btn-xs" type="button"
                                        @click="generateCode">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group small">
                            <label class="form-label text-danger font-weight-bold p-0 m-0">Estado</label>
                            <select v-model="form.ae_estado" class="form-control" required>
                                <option v-for="est in estados" :key="est" :value="est">{{ est }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group small">
                            <label class="form-label text-danger font-weight-bold p-0 m-0">HH Est.</label>
                            <input type="number" step="0.1" v-model="form.ae_horas_hombre" class="form-control" readonly
                                title="Se actualiza desde la Planificación Detallada" />
                        </div>
                    </div>
                </div>

                <!-- Sistemas de Gestión -->
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label class="form-label text-danger font-weight-bold p-0 m-0 small">Sistemas de Gestión
                            Auditados</label>
                        <div class="d-flex flex-wrap border rounded p-2 bg-white mt-1">
                            <div v-for="(label, key) in sistemasDisponibles" :key="key"
                                class="custom-control custom-checkbox mr-4">
                                <input type="checkbox" class="custom-control-input" :id="'sys-' + key" :value="key"
                                    v-model="form.ae_sistema">
                                <label class="custom-control-label small cursor-pointer" :for="'sys-' + key">
                                    {{ label }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Objetivos de la Auditoría -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <label class="form-label text-danger font-weight-bold p-0 m-0 small">Objetivos de la
                                Auditoría</label>
                            <small class="text-muted">{{ form.ae_objetivo?.length || 0 }}/1000</small>
                        </div>
                        <textarea v-model="form.ae_objetivo" rows="3" class="form-control" maxlength="1000"
                            placeholder="Escribe el objetivo..."></textarea>
                    </div>
                </div>

                <!-- 3. Alcance de la Auditoría -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <label class="form-label text-danger font-weight-bold p-0 m-0 small">Alcance de la
                                Auditoría</label>
                            <small class="text-muted">{{ form.ae_alcance?.length || 0 }}/1000</small>
                        </div>
                        <textarea v-model="form.ae_alcance" rows="3" class="form-control" maxlength="1000"
                            placeholder="Describa el alcance..."></textarea>
                    </div>
                </div>

                <!-- 4. Criterios de Auditoría -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <label class="form-label text-danger font-weight-bold p-0 m-0 small">Criterios de
                                Auditoría</label>
                            <small class="text-muted">{{ form.ae_criterios?.length || 0 }}/1000</small>
                        </div>
                        <textarea v-model="form.ae_criterios" rows="3" class="form-control" maxlength="1000"
                            placeholder="Ej: ISO 9001:2015, Normas internas..."></textarea>
                    </div>
                </div>

                <!-- 5. Dirección / Lugar -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <label class="form-label text-danger font-weight-bold p-0 m-0 small">Dirección / Lugar de
                                Auditoría</label>
                            <small class="text-muted">{{ form.ae_direccion?.length || 0 }}/500</small>
                        </div>
                        <textarea v-model="form.ae_direccion" rows="2" class="form-control" maxlength="500"
                            placeholder="Ej: Av. Salaverry 123 - Oficina Central"></textarea>
                    </div>
                </div>

                <!-- 6. Fecha Inicio y Fecha Fin -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group small">
                            <label class="form-label text-danger font-weight-bold p-0 m-0">Fecha Inicio</label>
                            <input type="date" v-model="form.ae_fecha_inicio" class="form-control" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group small">
                            <label class="form-label text-danger font-weight-bold p-0 m-0">Fecha Fin</label>
                            <input type="date" v-model="form.ae_fecha_fin" class="form-control" required />
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer justify-content-center bg-light border-top mt-4 p-3"
            style="margin: 0 -1.5rem -1rem -1.5rem; border-bottom-right-radius: 0.3rem;">
            <button type="button" class="btn btn-danger btn-sm px-4 shadow-sm" @click="save" :disabled="saving">
                <i v-if="saving" class="fas fa-spinner fa-spin mr-1"></i>
                <i v-else class="fas fa-save mr-1"></i>
                {{ auditId ? 'Actualizar Plan' : 'Guardar Plan' }}
            </button>
            <button type="button" class="btn btn-secondary btn-sm px-4 ml-2 shadow-sm" @click="$emit('close')">
                <i class="fas fa-times mr-1"></i> Cancelar
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, defineProps, defineEmits, watch } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps(['auditId', 'programaId']);
const emit = defineEmits(['saved', 'close']);
const toast = useToast();
const loading = ref(false);
const saving = ref(false);

const sistemasDisponibles = {
    'sgc': 'ISO 9001 (SGC)',
    'sgas': 'ISO 37001 (SGAS)',
    'sgco': 'ISO 21001 (SGCO)',
    'sgsi': 'ISO 27001 (SGSI)',
    'sgcm': 'ISO 37301 (SGCM)'
};

const form = ref({
    pa_id: props.programaId,
    ae_codigo: '',
    ae_tipo: 'Interna',
    ae_objetivo: '',
    ae_criterios: '',
    ae_alcance: '',
    ae_fecha_inicio: '',
    ae_fecha_fin: '',
    ae_estado: 'Programada',
    ae_direccion: '',
    ae_horas_hombre: 0,
    ae_ciclo: 1,
    ae_sistema: []
});

const estados = ['Programada', 'Ejecución', 'Cerrada', 'Cancelada', 'Suspendida'];

const generateCode = async () => {
    try {
        const year = new Date().getFullYear();
        const suffix = form.value.ae_tipo === 'Interna' ? 'IN' : 'EX';

        const response = await axios.get('/api/auditorias/next-sequence', {
            params: { year, type: form.value.ae_tipo }
        });
        const sequence = String(response.data.count + 1).padStart(3, '0');

        form.value.ae_codigo = `${year} - ${sequence} - ${suffix}`;
    } catch (e) {
        console.error("Error generating code", e);
    }
};

const loadData = async () => {
    if (!props.auditId) {
        generateCode();
        return;
    }
    loading.value = true;
    try {
        const response = await axios.get(`/api/auditorias/${props.auditId}`);
        const data = response.data;

        form.value = {
            ...data,
            ae_fecha_inicio: data.ae_fecha_inicio ? data.ae_fecha_inicio.substring(0, 10) : '',
            ae_fecha_fin: data.ae_fecha_fin ? data.ae_fecha_fin.substring(0, 10) : '',
            ae_sistema: Array.isArray(data.ae_sistema) ? data.ae_sistema : []
        };
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo cargando datos' });
    } finally {
        loading.value = false;
    }
};

const save = async () => {
    saving.value = true;
    try {
        const payload = { ...form.value };

        // Remove nested relationship objects that might cause sync issues in backend
        delete payload.procesos;
        delete payload.equipo;
        delete payload.agenda;
        delete payload.evaluaciones;
        delete payload.programa;

        let response;
        if (props.auditId) {
            response = await axios.put(`/api/auditorias/${props.auditId}`, payload);
            toast.add({ severity: 'success', summary: 'Actualizado', detail: 'Auditoría actualizada' });
        } else {
            response = await axios.post(`/api/auditorias`, payload);
            toast.add({ severity: 'success', summary: 'Creado', detail: 'Nueva auditoría creada' });
        }
        emit('saved', response.data);
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo guardando datos' });
        console.error(e);
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    loadData();
});

watch(() => props.auditId, (newVal) => {
    loadData();
});
</script>

<style scoped>
.form-overlay-container {
    position: relative;
    min-height: 200px;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.header-container {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}

.cursor-pointer {
    cursor: pointer;
}

.btn-xs {
    padding: 0.1rem 0.3rem;
    font-size: 0.75rem;
}
</style>

<template>
    <form>

        <!-- Documento y Obligacion Principal al inicio -->
        <div class="form-group">
            <label class="font-weight-bold small text-uppercase text-muted">Documento Técnico Normativo</label>
            <input type="text" class="form-control" v-model="localModel.obligacion_documento"
                placeholder="Ej. Ley N° 30424">
        </div>

        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div>
                    <label class="font-weight-bold mb-0">Obligación Principal <span class="text-danger">*</span></label>
                    <button type="button" class="btn btn-sm btn-outline-info ml-2" @click.prevent="handleAIAssistant"
                        :disabled="loading" title="Redactar con IA">
                        <i class="fas fa-magic"></i>
                        <span v-if="!loading"> </span>
                        <span v-else> Cargando...</span>
                    </button>
                </div>
                <small class="text-muted">
                    {{ localModel.obligacion_principal ? localModel.obligacion_principal.length : 0 }}/500
                </small>
            </div>
            <textarea class="form-control" rows="4" v-model="localModel.obligacion_principal" required maxlength="500"
                placeholder="Describa el deber ser de la obligación..."></textarea>
        </div>


        <div class="form-row">
            <div class="form-group col-md-6">
                <label class="font-weight-bold small text-uppercase text-muted">Tipo</label>
                <select class="form-control" v-model="localModel.obligacion_tipo">
                    <option value="legal">Legal</option>
                    <option value="contractual">Contractual</option>
                    <option value="voluntaria">Voluntaria</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label class="font-weight-bold small text-uppercase text-muted">Estado</label>
                <select class="form-control" v-model="localModel.obligacion_estado">
                    <option value="pendiente">Pendiente</option>
                    <option value="mitigada">Mitigada</option>
                    <option value="controlada">Controlada</option>
                    <option value="vencida">Vencida</option>
                    <option value="inactiva">Inactiva</option>
                </select>
            </div>
        </div>

        <!-- Area y Subarea al final, como solicitado -->
        <div class="form-row">
            <!-- Area Compliance -->
            <div class="form-group col-md-6">
                <label class="font-weight-bold small text-uppercase text-muted">
                    Área de Compliance <span class="text-danger">*</span>
                    <i v-if="loading" class="fas fa-spinner fa-spin ml-1 text-danger"></i>
                </label>
                <select class="form-control" v-model="localModel.area_compliance_id" @change="onAreaChange"
                    :disabled="loading">
                    <option value="" disabled>{{ loading ? 'Cargando áreas...' : 'Seleccione...' }}</option>
                    <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.area_compliance_nombre }}
                    </option>
                </select>
            </div>

            <!-- Subarea Compliance -->
            <div class="form-group col-md-6">
                <label class="font-weight-bold small text-uppercase text-muted">
                    Sub-Área
                    <i v-if="loading" class="fas fa-spinner fa-spin ml-1 text-danger"></i>
                </label>
                <select class="form-control" v-model="localModel.subarea_compliance_id"
                    :disabled="loading || !filteredSubareas.length">
                    <option value="">{{ loading ? 'Cargando sub-áreas...' : 'Seleccione...' }}</option>
                    <option v-for="sub in filteredSubareas" :key="sub.id" :value="sub.id">{{
                        sub.subarea_compliance_nombre }}</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label class="font-weight-bold small text-uppercase text-muted">Frecuencia de Revisión (Días)</label>
                <input type="number" class="form-control" v-model="localModel.obligacion_frecuencia"
                    placeholder="Ej. 180">
            </div>
            <div class="form-group col-md-6">
                <label class="font-weight-bold small text-uppercase text-muted">Fecha de Identificación</label>
                <input type="date" class="form-control" v-model="localModel.obligacion_fecha_identificacion">
            </div>
        </div>

        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div>
                    <label class="font-weight-bold mb-0">Consecuencia del Incumplimiento</label>
                    <button type="button" class="btn btn-sm btn-outline-info ml-2" @click="improveConsequenceWithAI"
                        :disabled="loadingIA || !localModel.obligacion_principal"
                        title="Sugerir o mejorar consecuencia con IA">
                        <i class="fas fa-magic" :class="{ 'fa-spin': loadingIA }"></i>
                        <span v-if="!loadingIA"> </span>
                        <span v-else> Generando...</span>
                    </button>
                </div>
                <small class="text-muted">
                    {{ localModel.obligacion_consecuencia ? localModel.obligacion_consecuencia.length : 0 }}/500
                </small>
            </div>
            <textarea class="form-control" rows="6" v-model="localModel.obligacion_consecuencia" maxlength="500"
                placeholder="Describa las consecuencias legales, financieras o reputacionales..."></textarea>
        </div>

        <div class="form-group">
            <label class="font-weight-bold small text-uppercase text-muted">Documento que Deroga / Motivo de
                Baja</label>
            <textarea class="form-control" rows="2" v-model="localModel.obligacion_documento_deroga"
                :disabled="localModel.obligacion_estado !== 'inactiva'"
                :required="localModel.obligacion_estado === 'inactiva'"
                placeholder="Indique el documento que deroga esta obligación o el motivo de su inactivación..."></textarea>
            <small v-if="localModel.obligacion_estado === 'inactiva'" class="text-danger">
                * Este campo es obligatorio cuando la obligación está inactiva.
            </small>
        </div>
    </form>
</template>

<script setup>
import { computed, ref } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    modelValue: Object, // The form object
    procesos: Array, // Not used directly in this form now
    areas: Array,
    subareas: Array,
    loading: Boolean
});

const emit = defineEmits(['update:modelValue', 'open-ai-assistant']);

const loadingIA = ref(false);

// Proxy object to allow v-model usage
const localModel = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
});

const filteredSubareas = computed(() => {
    // Logic from DocumentoMetadatos.vue: strict match might fail if types differ, 
    // but v-model handles types better than manual event.target.value.
    // However, for robustness we still use loose check or ensure types.
    const areaId = props.modelValue.area_compliance_id;
    if (!areaId) return [];

    // Using loose equality (==) covers both string/int cases. 
    // DocumentoMetadatos used '===' implies they matched types perfectly.
    // We try '==' to be safe.
    return props.subareas.filter(s => s.area_compliance_id == areaId);
});

const onAreaChange = () => {
    const updated = { ...props.modelValue, subarea_compliance_id: '' };
    emit('update:modelValue', updated);
};

const handleAIAssistant = () => {
    emit('open-ai-assistant');
};

const improveConsequenceWithAI = async () => {
    if (!props.modelValue.obligacion_principal || props.modelValue.obligacion_principal.length < 10) {
        Swal.fire('Atención', 'Primero debe ingresar una obligación principal válida para obtener sugerencias.', 'warning');
        return;
    }

    loadingIA.value = true;
    try {
        const response = await axios.post('/api/obligaciones/ai/improve-consequence', {
            obligacion_principal: props.modelValue.obligacion_principal,
            current_consequence: props.modelValue.obligacion_consecuencia || null
        });

        const result = response.data.improved;

        // Actualizar directamente la propiedad del modelo reactivo
        localModel.value.obligacion_consecuencia = result;

        // Mostrar mensaje según si generó opciones o mejoró
        if (result.includes('Opción 1') || result.includes('- ')) {
            Swal.fire('Sugerencias IA', 'Se han generado sugerencias. Por favor seleccione o edite la que mejor se ajuste.', 'success');
        } else {
            Swal.fire('Redacción Mejorada', 'La consecuencia ha sido mejorada por la IA.', 'success');
        }
    } catch (error) {
        console.error('Error improving consequence with AI:', error);
        Swal.fire('Error', 'No se pudo procesar la solicitud. Intente nuevamente.', 'error');
    } finally {
        loadingIA.value = false;
    }
};

</script>

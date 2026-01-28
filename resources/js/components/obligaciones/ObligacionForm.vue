<template>
    <form>
        <div class="form-row">
            <div class="form-group col-md-12">
                <div class="alert alert-light border shadow-sm p-3">
                    <small class="text-muted d-block mb-2"><i class="fas fa-info-circle mr-1"></i>
                        <strong>Nota:</strong> Esta obligación se vinculará principalmente a un proceso, pero puede
                        afectar a otros (ver pestaña "Asociación Procesos").</small>
                </div>
            </div>
        </div>

        <!-- Documento y Obligacion Principal al inicio -->
        <div class="form-group">
            <label class="font-weight-bold small text-uppercase text-muted">Documento Técnico Normativo</label>
            <input type="text" class="form-control" v-model="localModel.documento_tecnico_normativo"
                placeholder="Ej. Ley N° 30424">
        </div>

        <div class="form-group">
            <label class="font-weight-bold small text-uppercase text-muted">Obligación Principal <span
                    class="text-danger">*</span></label>
            <textarea class="form-control" rows="4" v-model="localModel.obligacion_principal" required
                placeholder="Describa el deber ser de la obligación..."></textarea>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label class="font-weight-bold small text-uppercase text-muted">Tipo</label>
                <select class="form-control" v-model="localModel.tipo_obligacion">
                    <option value="Legal">Legal</option>
                    <option value="Contractual">Contractual</option>
                    <option value="Voluntaria">Voluntaria</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label class="font-weight-bold small text-uppercase text-muted">Estado</label>
                <select class="form-control" v-model="localModel.estado_obligacion">
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
                <label class="font-weight-bold small text-uppercase text-muted">Área de Compliance <span
                        class="text-danger">*</span></label>
                <select class="form-control" v-model="localModel.area_compliance_id" @change="onAreaChange">
                    <option value="" disabled>Seleccione...</option>
                    <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.area_compliance_nombre }}
                    </option>
                </select>
            </div>

            <!-- Subarea Compliance -->
            <div class="form-group col-md-6">
                <label class="font-weight-bold small text-uppercase text-muted">Sub-Área</label>
                <select class="form-control" v-model="localModel.subarea_compliance_id"
                    :disabled="!filteredSubareas.length">
                    <option value="">Seleccione...</option>
                    <option v-for="sub in filteredSubareas" :key="sub.id" :value="sub.id">{{
                        sub.subarea_compliance_nombre }}</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label class="font-weight-bold small text-uppercase text-muted">Frecuencia de Revisión (Días)</label>
                <input type="number" class="form-control" v-model="localModel.frecuencia_revision"
                    placeholder="Ej. 180">
            </div>
        </div>

        <div class="form-group">
            <label class="font-weight-bold small text-uppercase text-muted">Consecuencia del Incumplimiento</label>
            <textarea class="form-control" rows="3" v-model="localModel.consecuencia_incumplimiento"
                placeholder="Describa las consecuencias legales, financieras o reputacionales..."></textarea>
        </div>
    </form>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: Object, // The form object
    procesos: Array, // Not used directly in this form now
    areas: Array,
    subareas: Array
});

const emit = defineEmits(['update:modelValue']);

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
    // Reset subarea when area changes
    // We modify the object directly through the emit logic if we want, 
    // but since we bound v-model to localModel.area_compliance_id, it emits the whole object.
    // We need to clear subarea.
    // The v-model update happens FIRST. Then this change event.
    // We need to wait for update, or manually force it.
    // Easier: Just emit the update with cleared subarea.
    const updated = { ...props.modelValue, subarea_compliance_id: '' };
    emit('update:modelValue', updated);
};

</script>

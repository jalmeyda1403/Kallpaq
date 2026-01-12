<template>
    <Dialog v-model:visible="visible" header="Evaluación de Desempeño del Auditor" :style="{ width: '50vw' }"
        :modal="true" @hide="closeModal">
        <div class="p-3">
            <div class="alert alert-info">
                <strong>Evaluación 360°:</strong> Califique el desempeño del auditor en base a su rol durante la
                auditoría.
            </div>

            <div class="mb-3">
                <label class="form-label">Auditor a Evaluar</label>
                <!-- Only show auditors from this audit team -->
                <Dropdown v-model="form.evaluado_id" :options="auditores" optionLabel="usuario.name"
                    optionValue="auditor_id" placeholder="Seleccione Auditor" class="w-100" />
            </div>

            <div class="mb-3">
                <label class="form-label">Rol desempeñado</label>
                <InputText v-model="form.aev_rol_evaluado" placeholder="Ej: Auditor Líder" class="w-100" />
            </div>

            <h6 class="mt-4">Criterios de Evaluación</h6>
            <div v-for="(criterio, index) in criterios" :key="index" class="mb-2 row align-items-center">
                <div class="col-md-8">
                    {{ criterio.label }}
                </div>
                <div class="col-md-4">
                    <Rating v-model="form.aev_criterios[criterio.key]" :stars="5" :cancel="false" />
                </div>
            </div>

            <div class="mt-4 mb-3">
                <label class="form-label">Comentarios / Oportunidades de Mejora</label>
                <Textarea v-model="form.aev_comentario" rows="3" class="w-100" />
            </div>

            <div class="text-end">
                <Button label="Guardar Evaluación" icon="pi pi-check" @click="saveEvaluacion" :loading="loading" />
            </div>
        </div>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Rating from 'primevue/rating';
import Button from 'primevue/button';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: Boolean,
    auditId: Number,
    auditores: Array // Passed from parent (team list)
});

const emit = defineEmits(['update:visible', 'saved']);
const toast = useToast();
const loading = ref(false);

const criterios = [
    { key: 'planificacion', label: 'Cumplimiento de la planificación y horarios' },
    { key: 'conocimiento', label: 'Conocimiento de la norma y procedimientos' },
    { key: 'comunicacion', label: 'Claridad en la comunicación y redacción' },
    { key: 'objetividad', label: 'Objetividad en el levantamiento de hallazgos' },
    { key: 'liderazgo', label: 'Liderazgo y manejo del equipo (si aplica)' },
];

const form = ref({
    evaluado_id: null,
    aev_rol_evaluado: '',
    aev_criterios: {},
    aev_comentario: ''
});

watch(() => props.visible, (newVal) => {
    if (newVal) {
        // Reset form
        form.value = {
            evaluado_id: null,
            aev_rol_evaluado: '',
            aev_criterios: {},
            aev_comentario: ''
        };
        // Initialize criteria
        criterios.forEach(c => form.value.aev_criterios[c.key] = 0);
    }
});

const saveEvaluacion = async () => {
    if (!form.value.evaluado_id) {
        toast.add({ severity: 'warn', summary: 'Falta datos', detail: 'Seleccione un auditor', life: 3000 });
        return;
    }

    loading.value = true;
    try {
        await axios.post('/api/auditoria/evaluacion', {
            ae_id: props.auditId,
            ...form.value
        });
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Evaluación registrada', life: 3000 });
        emit('saved');
        closeModal();
    } catch (error) {
        console.error(error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo guardar la evaluación', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const closeModal = () => {
    emit('update:visible', false);
};
</script>

<template>
    <div class="h-100">
        <ElaboracionInformeIndex v-if="!informeSeleccionado" :auditId="auditId" @ver-informe="verInforme" />

        <GenerarInforme v-else :informeId="informeSeleccionado" @back="volverAIndex" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import ElaboracionInformeIndex from './ElaboracionInformeIndex.vue';
import GenerarInforme from './GenerarInforme.vue';

const props = defineProps({
    auditId: { type: Number, required: true }
});

const informeSeleccionado = ref(null);

const verInforme = (informeId) => {
    informeSeleccionado.value = informeId;
};

const volverAIndex = () => {
    informeSeleccionado.value = null;
};
</script>

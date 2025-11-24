<template>
  <div class="container-fluid">
    <PlanAccionImprimir :hallazgoId="hallazgoId" />
  </div>
</template>

<script setup>
import PlanAccionImprimir from '@/components/acciones/PlanAccionImprimir.vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const hallazgoId = route.params.hallazgoId || route.params.hallazgo;
</script>
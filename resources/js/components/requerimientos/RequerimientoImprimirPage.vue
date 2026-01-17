<template>
  <div class="container-fluid">
    <RequerimientoImprimir :requerimientoId="requerimientoId" />
  </div>
</template>

<script setup>
import RequerimientoImprimir from '@/components/requerimientos/RequerimientoImprimir.vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const requerimientoId = route.params.id || route.params.requerimiento;
</script>

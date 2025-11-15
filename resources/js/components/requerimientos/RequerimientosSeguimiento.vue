<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mt-4">
                <resumen-general></resumen-general>
            </div>

            <div class="col-md-6">
                <div class="h-100 d-flex flex-column">
                    <resumen-grafico></resumen-grafico>
                </div>
            </div>
            <div class="col-md-6">
                <div class="h-100 d-flex flex-column">
                    <resumen-alertas></resumen-alertas>
                </div>
            </div>

            <div class="col-md-12 mt-4">
                <resumen-especialistas></resumen-especialistas>
            </div>
        </div>

        <detalle-especialista-modal></detalle-especialista-modal>
    </div>
</template>

<script>
import ResumenGeneral from '../dashboard/ResumenGeneral.vue';
import ResumenGrafico from '../dashboard/ResumenGrafico.vue';
import ResumenAlertas from '../dashboard/ResumenAlertas.vue';
import ResumenEspecialistas from '../dashboard/ResumenEspecialistas.vue';
import DetalleEspecialistaModal from '../dashboard/DetalleEspecialistaModal.vue';

export default {
    name: 'RequerimientosSeguimiento',
    components: {
        ResumenGeneral,
        ResumenGrafico,
        ResumenAlertas,
        ResumenEspecialistas,
        DetalleEspecialistaModal,
    },
};
</script>

<style scoped>
/* Si tienes estilos específicos para esta página, puedes ponerlos aquí */
</style>

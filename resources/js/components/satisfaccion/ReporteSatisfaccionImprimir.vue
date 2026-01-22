<template>
    <div class="reporte-imprimir pdf-document bg-white mx-auto p-5" ref="pdfContent"
        style="max-width: 210mm; min-height: 297mm; color: #000; font-family: Arial, sans-serif;">
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-12 text-center">
                <img :src="logoUrl" alt="Logo" style="max-height: 60px;" class="mb-3">
                <h4 class="font-weight-bold text-uppercase mb-1" style="font-size: 16px;">REPORTE TRIMESTRAL DE
                    SATISFACCIÓN AL CLIENTE</h4>
                <div class="border-top border-danger my-2" style="border-width: 2px !important;"></div>
            </div>
        </div>

        <!-- I. DATOS GENERALES -->
        <div class="section-title mb-2">
            <h5 class="font-weight-bold text-uppercase" style="font-size: 14px;">I. Datos generales</h5>
        </div>
        <table class="table table-bordered table-sm mb-4" style="font-size: 11px;">
            <tbody>
                <tr>
                    <th class="bg-light" style="width: 20%;">Proceso</th>
                    <td style="width: 40%;">{{ reporte.proceso?.proceso_nombre }}</td>
                    <th class="bg-light" style="width: 15%;">Tipo</th>
                    <td style="width: 25%;">{{ reporte.proceso?.tipo_proceso || 'Misional' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Mecanismos Table -->
        <table class="table table-bordered table-sm mb-4" style="font-size: 11px;">
            <thead class="bg-light">
                <tr class="text-center">
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Mecanismo de evaluación</th>
                    <th>Frecuencia</th>
                    <th>Periodo</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td>Ciudadanos, Entidades</td>
                    <td>Ex</td>
                    <td>ENCUESTA DE SATISFACCIÓN / RECLAMOS / SUGERENCIAS</td>
                    <td>Trimestral / Permanente</td>
                    <td>Trimestral</td>
                </tr>
            </tbody>
        </table>

        <!-- II. RESULTADOS -->
        <div class="section-title mb-2">
            <h5 class="font-weight-bold text-uppercase" style="font-size: 14px;">II. Resultados de la medición de los
                mecanismos de satisfacción</h5>
        </div>

        <div class="mb-4">
            <h6 class="font-weight-bold" style="font-size: 12px;">2.1 Encuesta de satisfacción al cliente</h6>
            <div class="text-justify" style="font-size: 11px; white-space: pre-wrap;">{{ reporte.resumen_encuestas }}
            </div>
        </div>

        <div class="mb-4">
            <h6 class="font-weight-bold" style="font-size: 12px;">2.2 Sugerencias</h6>
            <div class="text-justify" style="font-size: 11px; white-space: pre-wrap;">{{ reporte.resumen_sugerencias }}
            </div>
        </div>

        <div class="mb-4">
            <h6 class="font-weight-bold" style="font-size: 12px;">2.3 Reclamos</h6>
            <div class="text-justify" style="font-size: 11px; white-space: pre-wrap;">{{ reporte.reclamos
                || 'No se registraron reclamos.' }}</div>
        </div>

        <div class="mb-4">
            <h6 class="font-weight-bold" style="font-size: 12px;">2.4 Quejas por defecto de tramitación / SNC</h6>
            <div class="text-justify" style="font-size: 11px; white-space: pre-wrap;">{{ reporte.resumen_snc }}</div>
        </div>

        <!-- III. MEJORAS -->
        <div class="section-title mb-2">
            <h5 class="font-weight-bold text-uppercase" style="font-size: 14px;">III. Oportunidades de mejora
                identificadas</h5>
        </div>
        <div class="mb-4 text-justify" style="font-size: 11px; white-space: pre-wrap;">{{ reporte.oportunidades_mejora
        }}</div>

        <!-- IV. CONCLUSIONES -->
        <div class="section-title mb-2">
            <h5 class="font-weight-bold text-uppercase" style="font-size: 14px;">IV. Conclusiones</h5>
        </div>
        <div class="mb-4 text-justify" style="font-size: 11px; white-space: pre-wrap;">{{ reporte.conclusiones }}</div>

        <!-- Signature Space -->
        <div class="mt-5 pt-5">
            <div class="row mt-5">
                <div class="col-6 offset-3 text-center">
                    <div class="border-top border-dark pt-2">
                        <p class="mb-0 font-weight-bold" style="font-size: 11px;">Firma del Propietario del Proceso</p>
                        <p class="mb-0" style="font-size: 11px;">{{ reporte.proceso?.proceso_nombre }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    reporte: {
        type: Object,
        required: true
    },
    logoUrl: {
        type: String,
        default: '/images/logo.png'
    }
});
</script>

<style scoped>
.reporte-imprimir {
    line-height: 1.5;
}

.section-title {
    border-left: 4px solid #dc3545;
    padding-left: 10px;
    margin-top: 20px;
}
</style>

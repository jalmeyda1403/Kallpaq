<template>
    <div v-if="visible" class="modal fade show" tabindex="-1"
        style="display: block; background-color: rgba(0,0,0,0.6); z-index: 9999;">
        <div class="modal-dialog modal-xl" style="height: 90vh; max-width: 90vw;">
            <div class="modal-content" style="height: 100%;">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Vista de Impresión - Plan de Auditoría</h5>
                    <button type="button" class="close text-white" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light p-0 d-flex flex-column">
                    <!-- Toolbar -->
                    <div class="d-flex justify-content-end p-2 bg-white border-bottom">
                        <button class="btn btn-outline-danger btn-sm mr-2" @click="downloadServerPdf">
                            <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
                        </button>
                        <button class="btn btn-secondary btn-sm" @click="printClient">
                            <i class="fas fa-print mr-1"></i> Imprimir (Navegador)
                        </button>
                    </div>

                    <!-- Scrollable Preview -->
                    <div class="flex-grow-1 overflow-auto p-4 d-flex justify-content-center">
                        <div class="shadow" id="print-area">
                            <PlanAuditoriaImprimir :auditData="auditData" :equipo="equipo" :agenda="agenda" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hidden Iframe for Printing -->
        <iframe ref="printFrame"
            style="position: absolute; width: 0; height: 0; border: 0; visibility: hidden;"></iframe>
    </div>
</template>

<script setup>
import { ref, defineExpose } from 'vue';
import PlanAuditoriaImprimir from './PlanAuditoriaImprimir.vue';

const visible = ref(false);
const auditData = ref({});
const equipo = ref([]);
const agenda = ref([]);
const auditId = ref(null);
const printFrame = ref(null);

const open = (data, team, items, id) => {
    auditData.value = data;
    equipo.value = team;
    agenda.value = items;
    auditId.value = id;
    visible.value = true;
};

const close = () => {
    visible.value = false;
};

const downloadServerPdf = () => {
    if (!auditId.value) {
        alert("Atención: El ID de la auditoría no está definido. Guarde y recargue.");
        return;
    }
    const url = `/api/auditoria/especifica/${auditId.value}/plan-pdf`;
    window.open(url, '_blank');
};

const printClient = () => {
    if (!printFrame.value) return;

    const printContent = document.getElementById('print-area').innerHTML;
    const doc = printFrame.value.contentWindow.document;

    // Copiar estilos
    const styles = Array.from(document.querySelectorAll('style, link[rel="stylesheet"]'))
        .map(style => style.outerHTML)
        .join('');

    doc.open();
    doc.write('<html><head><title>Imprimir Plan de Auditoría</title>');
    doc.write(styles);
    doc.write('<style>body { background: white; margin: 0; } @media print { .no-print { display: none; } body { -webkit-print-color-adjust: exact; } }</style>');
    doc.write('</head><body>');
    doc.write(printContent);
    doc.write('</body></html>');
    doc.close();

    // Esperar a que cargue y llamar a print en el iframe
    // Usamos focus() para asegurar que el navegador sepa qué ventana imprimir
    setTimeout(() => {
        printFrame.value.contentWindow.focus();
        printFrame.value.contentWindow.print();
    }, 500);
};

defineExpose({ open });
</script>

<style scoped></style>

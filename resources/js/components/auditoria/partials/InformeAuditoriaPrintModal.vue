<template>
    <div v-if="visible" class="modal fade show" tabindex="-1"
        style="display: block; background-color: rgba(0,0,0,0.6); z-index: 9999;">
        <div class="modal-dialog modal-xl" style="height: 90vh; max-width: 90vw;">
            <div class="modal-content" style="height: 100%;">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Vista de Impresión - Informe de Auditoría</h5>
                    <button type="button" class="close text-white" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light p-0 d-flex flex-column">
                    <!-- Toolbar -->
                    <div class="d-flex justify-content-end p-2 bg-white border-bottom">
                        <button class="btn btn-outline-primary btn-sm mr-2" @click="downloadWord">
                            <i class="fas fa-file-word mr-1"></i> Descargar Word
                        </button>
                        <button class="btn btn-outline-danger btn-sm mr-2" @click="downloadPdf">
                            <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
                        </button>
                        <button class="btn btn-secondary btn-sm" @click="printClient">
                            <i class="fas fa-print mr-1"></i> Imprimir (Navegador)
                        </button>
                    </div>

                    <!-- Scrollable Preview -->
                    <div class="flex-grow-1 overflow-auto p-4 d-flex justify-content-center">
                        <div class="print-preview shadow bg-white" id="print-area">
                            <InformeAuditoriaImprimir :auditData="auditData" :auditados="auditados"
                                :procesosAuditados="procesosAuditados" :hallazgosConformidad="hallazgosConformidad"
                                :hallazgosNoConformidad="hallazgosNoConformidad"
                                :oportunidadesMejora="oportunidadesMejora" :informe="informe"
                                :elaboradoPor="elaboradoPor" :aprobadoPor="aprobadoPor" />
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
import InformeAuditoriaImprimir from './InformeAuditoriaImprimir.vue';

const visible = ref(false);
const printFrame = ref(null);

const auditData = ref({});
const auditados = ref([]);
const procesosAuditados = ref([]);
const hallazgosConformidad = ref([]);
const hallazgosNoConformidad = ref([]);
const oportunidadesMejora = ref([]);
const informe = ref({});
const auditId = ref(null);
const elaboradoPor = ref('');
const aprobadoPor = ref('');

const open = (data) => {
    auditData.value = data.auditoria;
    auditados.value = data.auditados;
    procesosAuditados.value = data.procesos_auditados;
    hallazgosConformidad.value = data.hallazgos_conformidad;
    hallazgosNoConformidad.value = data.hallazgos_no_conformidad;
    oportunidadesMejora.value = data.oportunidades_mejora;
    informe.value = data.informe_content;
    auditId.value = data.auditId;
    elaboradoPor.value = data.elaboradoPor;
    aprobadoPor.value = data.aprobadoPor;
    visible.value = true;
};

const close = () => {
    visible.value = false;
};

const downloadPdf = () => {
    if (!auditId.value) return;
    const url = `/api/auditoria/informes/${auditId.value}/pdf`;
    window.open(url, '_blank');
};

const downloadWord = () => {
    if (!auditId.value) return;
    const url = `/api/auditoria/informes/${auditId.value}/word`;
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
    doc.write('<html><head><title>Imprimir Informe de Auditoría</title>');
    doc.write(styles);
    doc.write('<style>body { background: white; padding: 20px; } @media print { body { padding: 0; } }</style>');
    doc.write('</head><body>');
    doc.write(printContent);
    doc.write('</body></html>');
    doc.close();

    setTimeout(() => {
        printFrame.value.contentWindow.focus();
        printFrame.value.contentWindow.print();
    }, 500);
};

defineExpose({ open });
</script>

<style scoped>
.print-preview {
    width: 210mm;
    min-height: 297mm;
    padding: 20mm;
    margin: 0 auto;
}
</style>

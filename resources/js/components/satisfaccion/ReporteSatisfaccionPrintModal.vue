<template>
    <div class="modal fade" id="reportePrintModal" tabindex="-1" role="dialog" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-file-pdf mr-2"></i> Vista Previa del Reporte
                    </h5>
                    <button type="button" class="close text-white" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Mini Toolbar -->
                <div class="modal-body bg-light py-2 border-bottom shadow-sm">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                <i class="fas fa-info-circle mr-1"></i>
                                Visualización preliminar del documento a generar.
                            </span>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary d-flex align-items-center shadow-sm mr-2"
                                    @click="downloadPdf" :disabled="generating">
                                    <i class="fas mr-2" :class="generating ? 'fa-spinner fa-spin' : 'fa-file-pdf'"></i>
                                    {{ generating ? 'Generando...' : 'Descargar PDF' }}
                                </button>
                                <button class="btn btn-outline-secondary d-flex align-items-center shadow-sm"
                                    @click="printDoc">
                                    <i class="fas fa-print mr-2"></i> Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Area -->
                <div class="modal-body bg-secondary p-4" style="height: 70vh; overflow-y: auto;">
                    <div class="preview-container shadow-lg">
                        <ReporteSatisfaccionImprimir :reporte="reporteData" ref="printComponentRef" />
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold px-4" @click="close">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Modal } from 'bootstrap';
import html2pdf from 'html2pdf.js';
import ReporteSatisfaccionImprimir from './ReporteSatisfaccionImprimir.vue';

const modalRef = ref(null);
const printComponentRef = ref(null);
let modalInstance = null;
const generating = ref(false);
const reporteData = ref({});

const open = (reporte) => {
    reporteData.value = reporte;
    if (modalRef.value) {
        modalInstance = new Modal(modalRef.value);
        modalInstance.show();
    }
};

const close = () => {
    if (modalInstance) {
        modalInstance.hide();
    }
};

const downloadPdf = async () => {
    const element = printComponentRef.value?.$el;
    if (!element) return;

    generating.value = true;
    const opt = {
        margin: 10,
        filename: `Reporte_Satisfaccion_${reporteData.value.anio}_T${reporteData.value.trimestre}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    try {
        await html2pdf().set(opt).from(element).save();
    } catch (error) {
        console.error('PDF Error:', error);
    } finally {
        generating.value = false;
    }
};

const printDoc = () => {
    const element = printComponentRef.value?.$el;
    if (!element) return;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Imprimir Reporte</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
            <style>
                body { padding: 0; margin: 0; }
                .reporte-imprimir { box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
                @media print { 
                    body { -webkit-print-color-adjust: exact; }
                    .reporte-imprimir { border: none !important; }
                }
            </style>
        </head>
        <body>${element.outerHTML}</body>
        </html>
    `);
    printWindow.document.close();
    printWindow.onload = () => {
        printWindow.print();
        printWindow.close();
    };
};

defineExpose({ open, close });
</script>

<style scoped>
.preview-container {
    background-color: white;
    max-width: 210mm;
    margin: 0 auto;
}

.gap-2 {
    gap: 0.5rem;
}
</style>

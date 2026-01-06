<template>
    <div class="modal fade" ref="printModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-pdf mr-2"></i>Vista Previa - Requerimiento
                    </h5>
                    <button type="button" class="close text-white" @click="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <!-- Toolbar -->
                    <div class="d-flex justify-content-end p-2 bg-light border-bottom">
                        <button class="btn btn-primary btn-sm mr-2" @click="downloadPdf" :disabled="generating">
                            <i class="fas fa-download mr-1"></i>
                            {{ generating ? 'Generando...' : 'Descargar PDF' }}
                        </button>
                        <button class="btn btn-secondary btn-sm" @click="printDocument">
                            <i class="fas fa-print mr-1"></i> Imprimir
                        </button>
                    </div>
                    
                    <!-- Document Preview -->
                    <div class="pdf-container p-4" style="max-height: 70vh; overflow-y: auto;">
                        <div ref="pdfContent" class="pdf-document bg-white shadow mx-auto" style="max-width: 210mm; padding: 20mm;">
                            <!-- Header -->
                            <div class="text-center mb-4">
                                <img :src="logoUrl" alt="Logo" style="height: 60px;" class="mb-2">
                                <h4 class="text-dark mb-1">REQUERIMIENTO DE MEJORA</h4>
                                <p class="text-muted mb-0">Sistema Integrado de Gestión</p>
                            </div>
                            
                            <hr class="my-3">
                            
                            <!-- Metadata -->
                            <table class="table table-sm table-bordered mb-4">
                                <tbody>
                                    <tr>
                                        <td class="font-weight-bold bg-light" style="width: 25%;">N° Requerimiento</td>
                                        <td style="width: 25%;">{{ requerimiento.id || 'Pendiente' }}</td>
                                        <td class="font-weight-bold bg-light" style="width: 25%;">Fecha</td>
                                        <td style="width: 25%;">{{ formatDate(requerimiento.fecha_creacion) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold bg-light">Proceso</td>
                                        <td colspan="3">{{ requerimiento.proceso_nombre || 'No asignado' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- Asunto -->
                            <div class="mb-4">
                                <h6 class="text-danger border-bottom pb-2">ASUNTO</h6>
                                <p class="mb-0">{{ requerimiento.asunto || 'Sin asunto' }}</p>
                            </div>
                            
                            <!-- Descripción -->
                            <div class="mb-4">
                                <h6 class="text-danger border-bottom pb-2">DESCRIPCIÓN</h6>
                                <p class="mb-0" style="white-space: pre-wrap;">{{ requerimiento.descripcion || 'Sin descripción' }}</p>
                            </div>
                            
                            <!-- Justificación -->
                            <div class="mb-4">
                                <h6 class="text-danger border-bottom pb-2">JUSTIFICACIÓN</h6>
                                <p class="mb-0" style="white-space: pre-wrap;">{{ requerimiento.justificacion || 'Sin justificación' }}</p>
                            </div>
                            
                            <!-- Evaluación de Complejidad -->
                            <div class="mb-4" v-if="complejidad.nivel">
                                <h6 class="text-danger border-bottom pb-2">EVALUACIÓN DE COMPLEJIDAD</h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center" style="width: 80%;">Criterio de Evaluación</th>
                                            <th class="text-center" style="width: 20%;">Puntaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Cantidad de actividades principales del requerimiento</td>
                                            <td class="text-center font-weight-bold">{{ requerimiento.eval_actividades || '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Cantidad de unidades orgánicas involucradas</td>
                                            <td class="text-center font-weight-bold">{{ requerimiento.eval_areas || '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nivel de requisitos normativos aplicables</td>
                                            <td class="text-center font-weight-bold">{{ requerimiento.eval_requisitos || '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nivel de documentación requerida</td>
                                            <td class="text-center font-weight-bold">{{ requerimiento.eval_documentacion || '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td>Impacto en otros procesos institucionales</td>
                                            <td class="text-center font-weight-bold">{{ requerimiento.eval_impacto || '-' }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <th class="text-right">PUNTAJE TOTAL / NIVEL DE COMPLEJIDAD:</th>
                                            <th class="text-center">
                                                <span class="badge" :class="getComplejidadBadgeClass">
                                                    {{ complejidad.valor }} pts - {{ complejidad.nivel.toUpperCase() }}
                                                </span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <!-- Firma -->
                            <div class="mt-5 pt-4">
                                <div class="row">
                                    <div class="col-6 text-center">
                                        <div style="border-top: 1px solid #333; width: 200px; margin: 0 auto; padding-top: 10px;">
                                            <p class="mb-0"><strong>Firma del Solicitante</strong></p>
                                            <p class="text-muted small mb-0">Facilitador / Propietario de Proceso</p>
                                        </div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <div style="border-top: 1px solid #333; width: 200px; margin: 0 auto; padding-top: 10px;">
                                            <p class="mb-0"><strong>V°B° Jefatura</strong></p>
                                            <p class="text-muted small mb-0">Responsable de Área</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="text-center mt-4 text-muted small">
                                <hr>
                                <p class="mb-0">Documento generado el {{ currentDate }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Modal } from 'bootstrap';
import html2pdf from 'html2pdf.js';

const props = defineProps({
    requerimiento: {
        type: Object,
        default: () => ({})
    },
    complejidad: {
        type: Object,
        default: () => ({ valor: 0, nivel: '' })
    }
});

const emit = defineEmits(['close']);

const printModal = ref(null);
const pdfContent = ref(null);
const modalInstance = ref(null);
const generating = ref(false);

const currentDate = computed(() => {
    return new Date().toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
});

const logoUrl = computed(() => '/images/logo.png');

const getComplejidadClass = computed(() => {
    switch (props.complejidad.nivel?.toLowerCase()) {
        case 'baja': return 'text-success';
        case 'media': return 'text-info';
        case 'alta': return 'text-warning';
        case 'muy alta': return 'text-danger';
        default: return '';
    }
});

const getComplejidadBadgeClass = computed(() => {
    switch (props.complejidad.nivel?.toLowerCase()) {
        case 'baja': return 'badge-success';
        case 'media': return 'badge-info';
        case 'alta': return 'badge-warning';
        case 'muy alta': return 'badge-danger';
        default: return 'badge-secondary';
    }
});

const formatDate = (dateString) => {
    if (!dateString) return new Date().toLocaleDateString('es-ES');
    return new Date(dateString).toLocaleDateString('es-ES');
};

const open = () => {
    if (modalInstance.value) {
        modalInstance.value.show();
    }
};

const close = () => {
    if (modalInstance.value) {
        modalInstance.value.hide();
    }
    emit('close');
};

const downloadPdf = async () => {
    if (!pdfContent.value) return;
    
    generating.value = true;
    
    const opt = {
        margin: 10,
        filename: `requerimiento_${props.requerimiento.id || 'borrador'}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    try {
        await html2pdf().set(opt).from(pdfContent.value).save();
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Error al generar el PDF');
    } finally {
        generating.value = false;
    }
};

const printDocument = () => {
    if (!pdfContent.value) return;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Requerimiento ${props.requerimiento.id || ''}</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
            <style>
                body { padding: 20px; }
                @media print {
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                }
            </style>
        </head>
        <body>
            ${pdfContent.value.innerHTML}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.onload = () => {
        printWindow.print();
        printWindow.close();
    };
};

onMounted(() => {
    if (printModal.value) {
        modalInstance.value = new Modal(printModal.value, { backdrop: 'static' });
    }
});

onBeforeUnmount(() => {
    if (modalInstance.value) {
        modalInstance.value.dispose();
    }
});

defineExpose({ open, close });
</script>

<style scoped>
.pdf-container {
    background-color: #e9ecef;
}
.pdf-document {
    font-family: 'Arial', sans-serif;
    font-size: 12px;
    line-height: 1.5;
}
.pdf-document h4 {
    font-size: 18px;
}
.pdf-document h6 {
    font-size: 14px;
    font-weight: 600;
}
.pdf-document .table {
    font-size: 11px;
}
</style>

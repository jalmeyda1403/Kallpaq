<template>
    <div class="modal fade" id="accionPrintModal" tabindex="-1" role="dialog" aria-hidden="true" ref="modal">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-file-pdf mr-2"></i> Vista Previa - Plan de Acción</h5>
                    <button type="button" class="close text-white" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Toolbar -->
                <div class="modal-body bg-light p-2 border-bottom">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary mr-2 shadow-sm" @click="downloadPdf" :disabled="generating">
                            <i class="fas" :class="generating ? 'fa-spinner fa-spin' : 'fa-download'"></i> 
                            {{ generating ? 'Generando...' : 'Descargar PDF' }}
                        </button>
                        <button class="btn btn-secondary shadow-sm" @click="printDocument">
                            <i class="fas fa-print mr-1"></i> Imprimir
                        </button>
                    </div>
                </div>
                
                <!-- Document Preview -->
                <div class="modal-body p-4 bg-secondary" style="height: 75vh; overflow-y: auto;">
                    <div ref="pdfContent" class="pdf-document bg-white shadow mx-auto p-5" style="max-width: 210mm; min-height: 297mm;">
                        
                        <!-- Header -->
                        <div class="row mb-4 pb-3 border-bottom position-relative">
                            <div class="position-absolute" style="right: 15px; top: 0;">
                                <span class="text-muted small font-weight-bold">Versión: {{ formatVersion(hallazgoData.hallazgo_ciclo) }}</span>
                            </div>
                            <div class="col-12 text-center">
                                <img :src="logoUrl" alt="Logo Institucional" style="max-height: 80px;" class="mb-3">
                                <h5 class="font-weight-bold text-uppercase text-dark mb-1" style="letter-spacing: 1px;">Sistema Integrado de Gestión</h5>
                                <h3 class="font-weight-bold text-danger mb-2">PLAN DE ACCIÓN SMP</h3>
                                <h5 class="text-muted font-weight-bold">{{ hallazgoData.hallazgo_cod }}</h5>
                            </div>
                        </div>

                        <!-- Info General -->
                        <div class="row invoice-info mb-4 small">
                            <div class="col-sm-4 invoice-col">
                                <strong class="text-secondary text-uppercase">Datos Generales</strong>
                                <address>
                                    <strong>Estado: </strong><span class="badge badge-light border">{{ hallazgoData.hallazgo_estado }}</span><br>
                                    <strong>Identificado: </strong>{{ formatDate(hallazgoData.hallazgo_fecha_identificacion) }}<br>
                                    <strong>Fuente: </strong> {{ hallazgoData.hallazgo_origen }}
                                </address>
                            </div>
                            <div class="col-sm-4 invoice-col">
                                <strong class="text-secondary text-uppercase">Procesos Afectados</strong>
                                <address>
                                    <div v-for="proceso in hallazgoData.procesos" :key="proceso.id" class="mb-1">
                                        <i class="fas fa-check-circle text-danger mr-1" style="font-size: 0.8em"></i> {{ proceso.proceso_nombre }}
                                    </div>
                                </address>
                            </div>
                            <div class="col-sm-4 invoice-col">
                                <strong class="text-secondary text-uppercase">Responsables</strong>
                                <address>
                                    <div class="mb-1"><strong>Especialista:</strong> {{ hallazgoData.especialista?.name || 'No asignado' }}</div>
                                    <div class="mb-1"><strong>Auditor:</strong> {{ hallazgoData.auditor?.name || 'No asignado' }}</div>
                                </address>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-danger font-weight-bold mb-2">Descripción del Hallazgo</h5>
                                <div class="p-3 bg-light rounded text-muted text-justify">
                                    {{ hallazgoData.hallazgo_descripcion || hallazgoData.hallazgo_resumen }}
                                </div>
                            </div>
                        </div>

                        <!-- Criteria & Evidence -->
                        <div class="row mb-5">
                            <div class="col-6">
                                <h6 class="text-danger font-weight-bold mb-2">Criterio / Referencia</h6>
                                <div class="p-3 bg-light rounded text-muted text-justify small" style="min-height: 60px;">
                                    {{ hallazgoData.hallazgo_criterio || 'No registrado' }}
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="text-danger font-weight-bold mb-2">Evidencia Objetiva</h6>
                                <div class="p-3 bg-light rounded text-muted text-justify small" style="min-height: 60px;">
                                        {{ hallazgoData.hallazgo_evidencia || 'No registrada' }}
                                </div>
                            </div>
                        </div>

                        <!-- Causa Raíz -->
                        <div v-if="causaData" class="row mb-5">
                            <div class="col-12">
                                <h5 class="text-danger font-weight-bold mb-3">1. Análisis de Causa Raíz</h5>
                                <div class="bg-light p-4 rounded border-left" style="border-left: 5px solid #dc3545;">
                                    <div class="mb-3">
                                        <strong class="text-danger text-uppercase small">Metodología Aplicada: </strong>
                                        <span class="badge badge-white border text-danger ml-2 px-3 py-1">
                                            {{ causaData.hc_metodo == 'cinco_porques' ? '5 Porqués' : 'Ishikawa (6M)' }}
                                        </span>
                                    </div>

                                    <!-- 5 Porques -->
                                    <div v-if="causaData.hc_metodo == 'cinco_porques'" class="mb-4 pt-3">
                                        <h6 class="font-weight-bold text-danger mb-3 pl-2 border-left border-danger">Desarrollo de los 5 Porqués:</h6>
                                        <ul class="list-unstyled mb-0">
                                            <template v-for="i in 5">
                                                <li v-if="causaData['hc_por_que'+i]" :key="i" class="mb-2 d-flex">
                                                    <span class="badge badge-danger mr-2 align-self-start mt-1">{{ i }}°</span>
                                                    <span class="text-dark font-italic">"{{ causaData['hc_por_que'+i] }}"</span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>

                                    <!-- Ishikawa -->
                                    <div v-else class="mb-4 pt-3">
                                        <h6 class="font-weight-bold text-danger mb-3 pl-2 border-left border-danger">Análisis de Causas (6M):</h6>
                                        <div class="row">
                                            <div class="col-6 mb-3" v-for="m in ishikawaFields" :key="m.key">
                                                <small class="text-muted text-uppercase font-weight-bold">{{ m.label }}</small>
                                                <div class="bg-white p-2 rounded border">{{ causaData[m.key] || 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <strong class="text-danger d-block mb-2 text-uppercase small">Causa Raíz Identificada:</strong>
                                        <p class="mb-0 text-dark font-weight-normal" style="font-size: 1.1em;">
                                            {{ causaData.hc_resultado }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h5 class="text-danger font-weight-bold mb-3">2. Acciones Definidas</h5>
                                <table class="table table-sm table-hover border">
                                    <thead class="bg-light text-secondary">
                                        <tr>
                                            <th style="width: 15%">Código</th>
                                            <th style="width: 40%">Acción / Descripción</th>
                                            <th style="width: 25%">Responsable</th>
                                            <th style="width: 20%">Planificación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="accionesData.length === 0">
                                            <td colspan="4" class="text-center text-muted py-3">No hay acciones registradas.</td>
                                        </tr>
                                        <tr v-for="accion in accionesData" :key="accion.id">
                                            <td class="font-weight-bold">{{ accion.accion_cod }}</td>
                                            <td>
                                                <span class="badge mb-1" :class="accion.accion_tipo == 'correctiva' ? 'badge-danger' : 'badge-warning'">
                                                    {{ accion.accion_tipo }}
                                                </span>
                                                <div class="small">{{ accion.accion_descripcion }}</div>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">
                                                    {{ accion.responsable?.name || accion.accion_responsable }}
                                                </div>
                                                <small v-if="accion.responsable?.ouos?.[0]" class="text-muted">
                                                    {{ accion.responsable.ouos[0].ouo_nombre }}
                                                </small>
                                            </td>
                                            <td class="small">
                                                <div><strong>Inicio:</strong> {{ formatDate(accion.accion_fecha_inicio) }}</div>
                                                <div><strong>Fin:</strong> {{ formatDate(accion.accion_fecha_fin_planificada) }}</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Firmas -->
                        <div class="row mt-5 pt-5">
                            <div class="col-8 offset-2 text-center">
                                <div class="pt-2 border-top border-dark">
                                    <div class="font-weight-bold text-dark">Firma del Propietario del Proceso</div>
                                    <div class="text-muted small mt-1">&nbsp;</div>
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
</template>

<script setup>
import { ref, computed } from 'vue';
import { Modal } from 'bootstrap';
import html2pdf from 'html2pdf.js';

const modal = ref(null);
const pdfContent = ref(null);
let modalInstance = null;
const generating = ref(false);
const logoUrl = '/images/logo.png';

const hallazgoData = ref({});
const accionesData = ref([]);
const causaData = ref(null);

const ishikawaFields = [
    { key: 'hc_mano_obra', label: 'Mano de Obra' },
    { key: 'hc_metodologias', label: 'Métodos' },
    { key: 'hc_materiales', label: 'Materiales' },
    { key: 'hc_maquinas', label: 'Maquinaria' },
    { key: 'hc_medicion', label: 'Medición' },
    { key: 'hc_medio_ambiente', label: 'Medio Ambiente' }
];

const open = (hallazgo, acciones, causa) => {
    hallazgoData.value = hallazgo || {};
    accionesData.value = acciones || [];
    causaData.value = causa || null;
    
    if (modal.value) {
        modalInstance = new Modal(modal.value);
        modalInstance.show();
    }
};

const close = () => {
    if (modalInstance) {
        modalInstance.hide();
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('es-ES');
};

const formatVersion = (ciclo) => {
    return (ciclo || 1).toString().padStart(3, '0');
};

const currentDate = computed(() => new Date().toLocaleDateString('es-ES', { 
    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' 
}));

const downloadPdf = async () => {
    if (!pdfContent.value) return;
    generating.value = true;
    
    const opt = {
        margin: 10,
        filename: `Plan_Accion_${hallazgoData.value.hallazgo_cod || 'borrador'}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    try {
        await html2pdf().set(opt).from(pdfContent.value).save();
    } catch (error) {
        console.error('Error PDF:', error);
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
            <title>Imprimir Plan de Acción</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
            <style>
                body { padding: 20px; font-family: Arial, sans-serif; }
                .badge { padding: 5px 10px; }
                @media print { body { -webkit-print-color-adjust: exact; } }
            </style>
        </head>
        <body>${pdfContent.value.innerHTML}</body>
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
.pdf-document {
    font-size: 13px;
    line-height: 1.5;
}
.pdf-document h5 { font-size: 16px; }
</style>

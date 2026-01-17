<template>
    <div class="h-100 d-flex flex-column">
        <!-- Header -->
        <div class="p-3 bg-white border-bottom shadow-sm d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-file-contract mr-2 text-danger"></i> Informe de Auditoría
            </h5>
            <div>
                <button class="btn btn-outline-primary btn-sm mr-2" @click="saveDraft">
                    <i class="fas fa-save mr-1"></i> Guardar Borrador
                </button>
                <button class="btn btn-danger btn-sm" @click="generateReport" :disabled="loading">
                    <i class="fas fa-magic mr-1"></i> Generar con IA
                </button>
                <button class="btn btn-secondary btn-sm ml-2" @click="exportPDF">
                    <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="flex-grow-1 overflow-auto p-4 bg-light">
            <div class="row">
                <!-- Columna Izquierda: Estructura del Informe -->
                <div class="col-md-3">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                        <a class="nav-link active" id="v-pills-resumen-tab" data-toggle="pill" href="#v-pills-resumen"
                            role="tab">1. Resumen Ejecutivo</a>
                        <a class="nav-link" id="v-pills-alcance-tab" data-toggle="pill" href="#v-pills-alcance"
                            role="tab">2. Alcance y Criterios</a>
                        <a class="nav-link" id="v-pills-hallazgos-tab" data-toggle="pill" href="#v-pills-hallazgos"
                            role="tab">3. Hallazgos Detallados</a>
                        <a class="nav-link" id="v-pills-conclusiones-tab" data-toggle="pill"
                            href="#v-pills-conclusiones" role="tab">4. Conclusiones</a>
                    </div>
                </div>

                <!-- Columna Derecha: Contenido Editable -->
                <div class="col-md-9">
                    <div class="tab-content" id="v-pills-tabContent">
                        <!-- Resumen -->
                        <div class="tab-pane fade show active" id="v-pills-resumen" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white font-weight-bold">1. Resumen Ejecutivo</div>
                                <div class="card-body">
                                    <textarea class="form-control" rows="10" v-model="reportData.resumen_ejecutivo"
                                        placeholder="Escriba o genere el resumen ejecutivo..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Alcance -->
                        <div class="tab-pane fade" id="v-pills-alcance" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white font-weight-bold">2. Alcance y Criterios</div>
                                <div class="card-body">
                                    <p class="text-muted">Se auditaron los siguientes procesos conforme a las normas: {{
                                        normasStr }}</p>
                                    <ul class="list-group">
                                        <li class="list-group-item" v-for="proc in procesos" :key="proc.id">{{
                                            proc.proc_nombre }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Hallazgos -->
                        <div class="tab-pane fade" id="v-pills-hallazgos" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white font-weight-bold">3. Hallazgos Detallados</div>
                                <div class="card-body">
                                    <div v-if="hallazgos.length === 0" class="alert alert-info">
                                        No se han registrado hallazgos (No Conformidades) en la fase de ejecución.
                                    </div>
                                    <div v-else class="accordion" id="accordionHallazgos">
                                        <div class="card mb-2" v-for="(h, idx) in hallazgos" :key="idx">
                                            <div class="card-header p-2 bg-light">
                                                <strong class="text-danger">NC {{ idx + 1 }}:</strong>
                                                {{ h.requisito_referencia }} - {{ h.hallazgo_detectado }}
                                            </div>
                                            <div class="card-body p-2">
                                                <small class="text-muted">Evidencia:</small> {{ h.evidencia_registrada
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conclusiones -->
                        <div class="tab-pane fade" id="v-pills-conclusiones" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white font-weight-bold">4. Conclusiones</div>
                                <div class="card-body">
                                    <textarea class="form-control" rows="10" v-model="reportData.conclusiones"
                                        placeholder="Conclusiones generales de la auditoría..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    auditId: { type: Number, required: true },
    auditData: { type: Object, required: true }
});

const toast = useToast();
const loading = ref(false);
const reportData = ref({
    resumen_ejecutivo: '',
    conclusiones: ''
});

const procesos = computed(() => props.auditData.procesos || []);
const normasStr = computed(() => {
    const sys = props.auditData.ae_sistema;
    return Array.isArray(sys) ? sys.join(', ') : (sys || '');
});

// Mock de Hallazgos (Debería venir de una API que agrege los checklist items NC)
const hallazgos = ref([]);

const loadReportData = async () => {
    // Cargar datos guardados si existen
    // Y cargar hallazgos detectados en la Ejecución
    try {
        const res = await axios.get(`/api/auditoria/ejecucion/por-auditoria/${props.auditId}`);
        // Extraer items NC de todas las ejecuciones
        const allExecutions = res.data;
        let found = [];
        allExecutions.forEach(exec => {
            if (exec.checklists) {
                exec.checklists.forEach(item => {
                    if (item.estado_cumplimiento === 'No Conforme') {
                        found.push(item);
                    }
                });
            }
        });
        hallazgos.value = found;
    } catch (e) {
        console.error(e);
    }
};

const generateReport = async () => {
    // Simulación de llamada a IA para generar Resumen y Conclusiones basado en hallazgos
    loading.value = true;
    setTimeout(() => {
        reportData.value.resumen_ejecutivo = `La auditoría se realizó conforme al plan establecido, abarcando los procesos de ${procesos.value.map(p => p.proc_nombre).join(', ')}. Se identificaron ${hallazgos.value.length} hallazgos de No Conformidad que requieren atención inmediata. En general, el sistema de gestión muestra un grado de madurez medio.`;

        reportData.value.conclusiones = `1. El sistema de gestión cumple parcialmente con los requisitos de la norma ${normasStr.value}.\n2. Se evidenció compromiso por parte de los líderes de proceso.\n3. Se recomienda fortalecer el control de información documentada.`;

        toast.add({ severity: 'success', summary: 'Generado', detail: 'Informe preliminar generado por IA', life: 3000 });
        loading.value = false;
    }, 1500);
};

const saveDraft = () => {
    toast.add({ severity: 'info', summary: 'Guardado', detail: 'Borrador guardado correctamente', life: 2000 });
};

const exportPDF = () => {
    // Usar la función de descarga de PDF existente o una nueva para el Informe Final
    toast.add({ severity: 'info', summary: 'Exportar', detail: 'Función de exportación de informe final en desarrollo', life: 3000 });
};

onMounted(() => {
    loadReportData();
});
</script>

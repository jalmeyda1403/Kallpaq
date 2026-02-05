<template>
    <div class="modal fade" id="asistenteIAModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;"
        data-backdrop="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-robot mr-2"></i> Asistente Jaris: Extracción de Obligaciones
                    </h5>
                    <button type="button" class="close text-white" @click="closeModal" @mousedown.prevent
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <!-- FASE 1: Búsqueda o Carga -->
                    <div v-if="phase === 'selection'" class="text-center py-3">
                        <div class="mb-4">
                            <i class="fas fa-file-contract fa-4x text-danger mb-3"></i>
                            <h4 class="font-weight-bold">¿Cómo quieres identificar la obligación?</h4>
                            <p class="text-muted">Jaris puede buscar en el repositorio de documentos o analizar uno
                                nuevo.</p>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 hover-card shadow-sm border p-4" @click="startSearch"
                                    style="cursor: pointer;">
                                    <i class="fas fa-search fa-3x text-primary mb-3"></i>
                                    <h5>Buscar Documento</h5>
                                    <p class="small text-muted mb-0">Selecciona un documento técnico o norma legal del
                                        sistema.</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 hover-card shadow-sm border p-4" @click="startUpload"
                                    style="cursor: pointer;">
                                    <i class="fas fa-upload fa-3x text-success mb-3"></i>
                                    <h5>Subir Documento</h5>
                                    <p class="small text-muted mb-0">Carga un archivo (PDF, Word, TXT) para análisis
                                        inmediato.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FASE 2: Buscador -->
                    <div v-if="phase === 'search'">
                        <div class="d-flex align-items-center mb-3">
                            <button class="btn btn-link text-muted p-0 mr-3" @click="phase = 'selection'">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                            <h5 class="mb-0 font-weight-bold">Buscar en Repositorio</h5>
                        </div>
                        <div class="input-group mb-4 shadow-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0 text-muted">
                                    <i class="fas fa-search" v-if="!searchingDocs"></i>
                                    <i class="fas fa-spinner fa-spin" v-else></i>
                                </span>
                            </div>
                            <input type="text" class="form-control border-left-0" v-model="searchQuery"
                                @input="debouncedSearch"
                                placeholder="Escribe el nombre del documento (ej. Reglamento, Ley, Manual)...">
                        </div>

                        <div class="list-group overflow-auto" style="max-height: 300px;">
                            <a v-for="doc in searchResults" :key="doc.id" href="#"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                @click.prevent="extractFromDoc(doc)">
                                <div>
                                    <i class="fas fa-file-pdf text-danger mr-2" v-if="doc.cod_documento"></i>
                                    {{ doc.nombre_documento || doc.descripcion }}
                                </div>
                                <i class="fas fa-chevron-right text-muted small"></i>
                            </a>
                            <div v-if="searchResults.length === 0 && searchQuery.length > 2 && !searchingDocs"
                                class="text-center py-4 text-muted">
                                <i class="fas fa-search-minus fa-2x mb-2"></i>
                                <p>No se encontraron resultados para "{{ searchQuery }}"</p>
                            </div>
                        </div>
                    </div>

                    <!-- FASE 3: Upload -->
                    <div v-if="phase === 'upload'">
                        <div class="d-flex align-items-center mb-3">
                            <button class="btn btn-link text-muted p-0 mr-3" @click="phase = 'selection'">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
                            <h5 class="mb-0 font-weight-bold">Cargar Nuevo Documento</h5>
                        </div>
                        <div class="upload-zone border-dashed rounded p-5 text-center bg-light"
                            @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleFileDrop"
                            style="border: 2px dashed #cbd5e0; cursor: pointer;">
                            <input type="file" ref="fileInput" class="d-none" @change="handleFileUpload"
                                accept=".pdf,.doc,.docx,.txt">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h5>{{ selectedFile ? selectedFile.name : 'Arrastra un archivo aquí o haz clic para subir'
                                }}</h5>
                            <p class="text-muted small">Se aceptan archivos PDF, Word o Texto (Máx 10MB)</p>
                            <button v-if="selectedFile" class="btn btn-primary mt-3" @click.stop="analyzeFile">
                                Analizar con Jaris
                            </button>
                        </div>
                    </div>

                    <!-- FASE 4: Loader de IA -->
                    <div v-if="phase === 'processing'" class="text-center py-5">
                        <div class="ai-brain-wrapper mb-4">
                            <i class="fas fa-brain fa-4x text-danger heartbeat"></i>
                            <div class="ai-pulse"></div>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-2">Jaris está leyendo el documento...</h5>
                        <p class="text-muted small mb-4">Extrayendo la obligación principal y el propósito normativo.
                        </p>

                        <div class="progress mx-auto"
                            style="height: 12px; width: 80%; max-width: 450px; border-radius: 20px; overflow: hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
                            <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated"
                                role="progressbar" :style="{ width: aiProgress + '%' }">
                                <span class="font-weight-bold" style="font-size: 0.75rem;">{{ aiProgress }}%</span>
                            </div>
                        </div>
                        <div class="mt-3 small text-danger font-italic animate-flicker">
                            {{ aiStatusText }}
                        </div>
                    </div>

                    <!-- FASE 5: Resultado -->
                    <div v-if="phase === 'result'">
                        <h5 class="font-weight-bold text-dark mb-3">
                            <i class="fas fa-check-circle text-success mr-2"></i> Obligación Identificada
                        </h5>
                        <div class="alert alert-light border p-4 mb-4"
                            style="background-color: #f8fafc; border-left: 4px solid #e53e3e !important;">
                            <p class="mb-0 text-dark lead" style="font-size: 1.1rem; line-height: 1.6;">
                                {{ extractionResult }}
                            </p>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-outline-secondary mr-2"
                                @click="phase = 'selection'">Reintentar</button>
                            <button class="btn btn-danger px-4" @click="useResult">
                                <i class="fas fa-arrow-down mr-1"></i> Usar esta Obligación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';
import Swal from 'sweetalert2';
import * as bootstrap from 'bootstrap';

const emit = defineEmits(['selected']);

const phase = ref('selection');
const searchQuery = ref('');
const searchResults = ref([]);
const searchingDocs = ref(false);
const selectedFile = ref(null);
const aiProgress = ref(0);
const aiStatusText = ref('Iniciando análisis...');
const extractionResult = ref('');
const progressInterval = ref(null);

const startSearch = () => {
    phase.value = 'search';
    searchQuery.value = '';
    searchResults.value = [];
};

const startUpload = () => {
    phase.value = 'upload';
    selectedFile.value = null;
};

const debouncedSearch = debounce(async () => {
    if (searchQuery.value.length < 3) {
        searchResults.value = [];
        return;
    }
    searchingDocs.value = true;
    try {
        const response = await axios.get('/documentos/buscar', {
            params: { query: searchQuery.value }
        });
        searchResults.value = response.data;
    } catch (error) {
        searchResults.value = [];
        // Solo mostrar error si no es un 404 (no hay resultados)
        if (error.response?.status !== 404) {
            console.error('Error searching docs:', error);
        }
    } finally {
        searchingDocs.value = false;
    }
}, 300);

const handleFileUpload = (e) => {
    selectedFile.value = e.target.files[0];
};

const handleFileDrop = (e) => {
    selectedFile.value = e.dataTransfer.files[0];
};

const extractFromDoc = async (doc) => {
    phase.value = 'processing';
    startProgressAnimation();

    try {
        const response = await axios.post('/api/obligaciones/ai/extract', {
            documento_id: doc.id
        });
        finishAnalysis(response.data.obligacion);
    } catch (error) {
        handleError(error);
    }
};

const analyzeFile = async () => {
    if (!selectedFile.value) return;
    phase.value = 'processing';
    startProgressAnimation();

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        const response = await axios.post('/api/obligaciones/ai/analyze-file', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        finishAnalysis(response.data.obligacion);
    } catch (error) {
        handleError(error);
    }
};

const startProgressAnimation = () => {
    aiProgress.value = 0;
    aiStatusText.value = 'Conectando con Jaris...';

    const statuses = [
        'Abriendo documento...',
        'Escaneando texto legal...',
        'Identificando cláusulas...',
        'Jaris está razonando...',
        'Redactando obligación principal...',
        'Finalizando...'
    ];
    let statusIdx = 0;

    progressInterval.value = setInterval(() => {
        if (aiProgress.value < 90) {
            aiProgress.value += Math.floor(Math.random() * 5) + 1;
            if (aiProgress.value % 20 === 0) {
                statusIdx = Math.min(statusIdx + 1, statuses.length - 1);
                aiStatusText.value = statuses[statusIdx];
            }
        }
    }, 400);
};

const finishAnalysis = (result) => {
    clearInterval(progressInterval.value);
    aiProgress.value = 100;
    aiStatusText.value = '¡Análisis completado!';
    setTimeout(() => {
        extractionResult.value = result;
        phase.value = 'result';
    }, 600);
};

const handleError = (error) => {
    clearInterval(progressInterval.value);
    const msg = error.response?.data?.error || 'No se pudo procesar el documento.';
    Swal.fire('Error', msg, 'error');
    phase.value = 'selection';
};

const useResult = () => {
    emit('selected', extractionResult.value);
    closeModal();
};

const closeModal = () => {
    const modalElement = document.getElementById('asistenteIAModal');
    if (modalElement) {
        const bsModal = bootstrap.Modal.getInstance(modalElement);
        if (bsModal) {
            bsModal.hide();
        }
    }
    setTimeout(() => {
        phase.value = 'selection';
    }, 300);
};

</script>

<style scoped>
.hover-card:hover {
    border-color: #e53e3e !important;
    transform: translateY(-5px);
    transition: all 0.3s ease;
}

.ai-brain-wrapper {
    position: relative;
    display: inline-block;
}

.heartbeat {
    animation: heartbeat 1.5s ease-in-out infinite both;
}

.ai-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    background: rgba(229, 62, 62, 0.2);
    border-radius: 50%;
    z-index: -1;
    animation: ai-pulse 2s infinite;
}

@keyframes heartbeat {
    from {
        transform: scale(1);
        transform-origin: center center;
    }

    10% {
        transform: scale(0.91);
    }

    17% {
        transform: scale(0.98);
    }

    33% {
        transform: scale(0.87);
    }

    45% {
        transform: scale(1);
    }
}

@keyframes ai-pulse {
    0% {
        transform: translate(-50%, -50%) scale(0.95);
        box-shadow: 0 0 0 0 rgba(229, 62, 62, 0.4);
    }

    70% {
        transform: translate(-50%, -50%) scale(1.6);
        box-shadow: 0 0 0 10px rgba(229, 62, 62, 0);
    }

    100% {
        transform: translate(-50%, -50%) scale(0.95);
        box-shadow: 0 0 0 0 rgba(229, 62, 62, 0);
    }
}

.animate-flicker {
    animation: flicker 1.5s infinite;
}

@keyframes flicker {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}

/* Asegurar que el modal de IA esté por encima del modal de obligaciones */
#asistenteIAModal {
    z-index: 1060 !important;
}

/* Evitar que el backdrop bloquee el scroll del modal padre */
#asistenteIAModal~.modal-backdrop {
    z-index: 1055 !important;
}
</style>

<template>
    <div class="h-100 d-flex flex-column">
        <!-- Header Estilo Breadcrumb -->
        <div class="header-container mb-3 d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0 bg-transparent" style="font-size: 1rem;">
                    <li class="breadcrumb-item">
                        <a href="#" @click.prevent="$emit('back')" class="text-danger font-weight-bold">
                            Auditoría: {{ auditCode }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-dark font-weight-bold" aria-current="page">
                        {{ processName }}
                    </li>
                </ol>
            </nav>
            <div class="d-flex align-items-center">
                <div class="text-muted small">
                    <i class="fas fa-user-circle mr-1"></i> {{ auditorName }}
                </div>
            </div>
        </div>


        <!-- Progress y Acciones Secundarias -->
        <div class="px-3 mb-3">
            <div class="d-flex justify-content-between align-items-end mb-1">
                <div class="d-flex align-items-center">
                    <small class="text-muted mr-3">{{ completedItems }} / {{ checklists.length }} verificados</small>
                    <button class="btn btn-soft-danger btn-xs" @click="generateChecklist" :disabled="store.loading">
                        <i class="fas fa-magic mr-1"></i> Generar Checklist con IA
                    </button>
                </div>
                <small class="font-weight-bold text-primary">{{ progress }}% Completado</small>
            </div>
            <div class="progress" style="height: 6px; border-radius: 10px;">
                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar"
                    :style="{ width: progress + '%' }"></div>
            </div>
        </div>

        <!-- Lista de Verificación (Scrollable) -->
        <div class="flex-grow-1 overflow-auto p-3 bg-light position-relative">
            <!-- AI GENERATING LOADER -->
            <div v-if="isGeneratingAI" class="ai-loader-container text-center py-5">
                <div class="ai-brain-wrapper mb-4">
                    <i class="fas fa-brain fa-4x text-danger heartbeat"></i>
                    <div class="ai-pulse"></div>
                </div>
                <h5 class="font-weight-bold text-dark mb-2">Jaris está analizando tus requisitos...</h5>
                <p class="text-muted small mb-4">Generando preguntas de auditoría técnica y evidencia objetiva basada en
                    normas.</p>

                <div class="progress mx-auto"
                    style="height: 12px; width: 80%; max-width: 450px; border-radius: 20px; overflow: hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
                    <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" role="progressbar"
                        :style="{ width: aiProgress + '%' }">
                        <span class="font-weight-bold" style="font-size: 0.75rem;">{{ aiProgress }}%</span>
                    </div>
                </div>
                <div class="mt-3 small text-danger font-italic animate-flicker">
                    {{ aiStatusText }}
                </div>
            </div>

            <div v-else-if="loading" class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-danger"></i>
                <p class="mt-2 text-muted">Cargando elementos de verificación...</p>
            </div>


            <div v-else-if="checklists.length === 0" class="text-center py-5 text-muted">
                <i class="fas fa-clipboard-list fa-3x mb-3 text-secondary"></i>
                <h5>Lista de Verificación Vacía</h5>
                <p>Presione "Generar Checklist con IA" para obtener las preguntas guía basadas en las normas aplicables.
                </p>
            </div>

            <div v-else class="accordion" id="checklistAccordion">
                <div v-for="(item, index) in checklists" :key="item.id" class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white d-flex align-items-center justify-content-between py-2"
                        :id="'heading' + item.id" data-toggle="collapse" :data-target="'#collapse' + item.id"
                        style="cursor: pointer;">

                        <div class="d-flex align-items-center" style="width: 70%;">
                            <div class="mr-3 text-center" style="min-width: 40px;">
                                <span class="badge badge-pill badge-light border">{{ index + 1 }}</span>
                            </div>
                            <div>
                                <h6 class="mb-0 font-weight-bold text-dark">{{ item.pregunta }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-book mr-1"></i> {{ item.norma_referencia }}
                                    <span class="mx-1">•</span>
                                    Req. {{ item.requisito_referencia }}
                                </small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <span v-if="item.estado_cumplimiento && item.estado_cumplimiento !== 'Sin Evaluar'"
                                class="badge px-3 py-2 mr-3" :class="getComplianceClass(item.estado_cumplimiento)">
                                {{ item.estado_cumplimiento }}
                            </span>
                            <i class="fas fa-chevron-down text-muted"></i>
                        </div>
                    </div>

                    <div :id="'collapse' + item.id" class="collapse" :aria-labelledby="'heading' + item.id"
                        data-parent="#checklistAccordion">
                        <div class="card-body border-top bg-light">
                            <div class="row">
                                <!-- Columna Izquierda: Guía -->
                                <div class="col-md-4 border-right">
                                    <div class="alert alert-info py-2 px-3 small mb-3">
                                        <strong><i class="fas fa-info-circle mr-1"></i> Evidencia Esperada:</strong>
                                        <p class="mb-0 mt-1">{{ item.evidencia_esperada || 'No especificada' }}</p>
                                    </div>
                                    <div class="small text-justify text-muted">
                                        <strong>Requisito Contenido:</strong> {{ item.requisito_contenido }}
                                    </div>
                                </div>

                                <!-- Columna Derecha: Evaluación -->
                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold small">Estado de Cumplimiento</label>
                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn-sm"
                                                :class="item.estado_cumplimiento === 'Conforme' ? 'btn-success' : 'btn-outline-success'"
                                                @click="updateItem(item, 'estado_cumplimiento', 'Conforme')">
                                                <i class="fas fa-check"></i> Conforme
                                            </button>
                                            <button type="button" class="btn btn-sm"
                                                :class="item.estado_cumplimiento === 'No Conforme' ? 'btn-danger' : 'btn-outline-danger'"
                                                @click="updateItem(item, 'estado_cumplimiento', 'No Conforme')">
                                                <i class="fas fa-times"></i> No Conforme
                                            </button>
                                            <button type="button" class="btn btn-sm"
                                                :class="item.estado_cumplimiento === 'Oportunidad de Mejora' ? 'btn-warning' : 'btn-outline-warning'"
                                                @click="updateItem(item, 'estado_cumplimiento', 'Oportunidad de Mejora')">
                                                <i class="fas fa-lightbulb"></i> Mejora
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold small">Evidencia Objetiva / Observaciones</label>
                                        <textarea class="form-control form-control-sm" rows="4"
                                            v-model="item.evidencia_registrada" @blur="saveEvidence(item)"
                                            placeholder="Describa la evidencia encontrada..."></textarea>
                                    </div>

                                    <div v-if="item.estado_cumplimiento === 'No Conforme'"
                                        class="mt-2 p-2 bg-white border border-danger rounded shadow-sm">
                                        <label class="text-danger font-weight-bold small mb-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Hallazgo Detectado
                                        </label>
                                        <div class="input-group input-group-sm align-items-start">
                                            <textarea class="form-control" v-model="item.hallazgo_detectado" rows="4"
                                                placeholder="Descripción detallada del hallazgo de auditoría..."
                                                @blur="saveEvidence(item)"></textarea>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-danger" type="button"
                                                    style="height: 100%; min-height: 40px;"
                                                    title="Mejorar redacción con IA" @click="improveHallazgo(item)"
                                                    :disabled="improvingHallazgo[item.id]">
                                                    <i class="fas"
                                                        :class="improvingHallazgo[item.id] ? 'fa-spinner fa-spin' : 'fa-magic'"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="item.estado_cumplimiento === 'Oportunidad de Mejora'"
                                        class="mt-2 p-2 bg-white border border-warning rounded shadow-sm">
                                        <label class="text-warning font-weight-bold small mb-1">
                                            <i class="fas fa-lightbulb mr-1"></i> Oportunidad de Mejora
                                        </label>
                                        <div class="input-group input-group-sm align-items-start">
                                            <textarea class="form-control" v-model="item.hallazgo_detectado" rows="4"
                                                placeholder="Propuesta detallada para la mejora técnica del proceso..."
                                                @blur="saveEvidence(item)"></textarea>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-warning" type="button"
                                                    style="height: 100%; min-height: 40px;"
                                                    title="Mejorar redacción con IA" @click="improveMejora(item)"
                                                    :disabled="improvingMejora[item.id]">
                                                    <i class="fas"
                                                        :class="improvingMejora[item.id] ? 'fa-spinner fa-spin' : 'fa-magic'"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>


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
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import { useAuditoriaEjecucionStore } from '../../../stores/auditoriaEjecucionStore';

const props = defineProps({
    agendaId: { type: Number, required: true },
    auditCode: { type: String, default: '---' },
    processName: { type: String, default: 'Cargando...' }
});

const toast = useToast();
const store = useAuditoriaEjecucionStore();
const emit = defineEmits(['back']);

// Use Computed from Store for Reactivity
const loading = computed(() => store.loading);
const executionDetail = computed(() => store.executionDetails[props.agendaId] || null);
const checklists = computed(() => executionDetail.value ? (executionDetail.value.checklists || []) : []);

// Names from Store or Props (props take precedence for immediate display)
const auditorName = computed(() => store.getAuditorName(props.agendaId));


const completedItems = computed(() => {
    return checklists.value?.filter(i => i.estado_cumplimiento && i.estado_cumplimiento !== 'Sin Evaluar').length || 0;
});

const progress = computed(() => {
    if (!checklists.value || checklists.value.length === 0) return 0;
    return Math.round((completedItems.value / checklists.value.length) * 100);
});

const statusBadgeClass = computed(() => {
    const status = executionDetail.value?.estado;
    switch (status) {
        case 'En Curso': return 'badge-primary';
        case 'Concluida': return 'badge-success';
        case 'Cerrado': return 'badge-dark';
        default: return 'badge-secondary';
    }
});

const getComplianceClass = (status) => {
    switch (status) {
        case 'Conforme': return 'badge-success';
        case 'No Conforme': return 'badge-danger';
        case 'Oportunidad de Mejora': return 'badge-warning';
        default: return 'badge-secondary';
    }
};

const loadDetails = async () => {
    if (props.agendaId) {
        await store.fetchExecutionDetail(props.agendaId);
    }
};

const isGeneratingAI = ref(false);
const aiProgress = ref(0);
const aiStatusText = ref('Iniciando motor de IA...');

const generateChecklist = async () => {
    isGeneratingAI.value = true;
    aiProgress.value = 0;
    aiStatusText.value = 'Interpretando requisitos y normas...';

    // Simulación de progreso para mejor UX
    const statusTexts = [
        'Interpretando requisitos y normas...',
        'Consultando base de conocimiento ISO...',
        'Cruzando información con el proceso...',
        'Formulando preguntas de auditoría...',
        'Determinando evidencia objetiva esperada...',
        'Finalizando estructura del checklist...'
    ];

    let textIdx = 0;
    const interval = setInterval(() => {
        if (aiProgress.value < 92) {
            aiProgress.value += Math.floor(Math.random() * 8) + 2;
            if (aiProgress.value % 20 === 0) {
                textIdx = (textIdx + 1) % statusTexts.length;
                aiStatusText.value = statusTexts[textIdx];
            }
        }
    }, 600);

    try {
        const url = window.route ? window.route('api.auditoria.ejecucion.generate', { id: props.agendaId }) : `/api/auditoria/ejecucion/generate-checklist/${props.agendaId}`;
        await axios.post(url);


        aiProgress.value = 100;
        aiStatusText.value = '¡Checklist generado con éxito!';
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Checklist generado por IA', life: 3000 });
        await store.fetchExecutionDetail(props.agendaId, true);
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error generando checklist', life: 3000 });
    } finally {
        clearInterval(interval);
        setTimeout(() => {
            isGeneratingAI.value = false;
        }, 800);
    }
};


const updateItem = async (item, field, value) => {
    item[field] = value;
    try {
        const url = window.route ? window.route('api.auditoria.ejecucion.item.update', { id: item.id }) : `/api/auditoria/ejecucion/item/${item.id}`;

        await axios.put(url, {
            [field]: value
        });
        toast.add({ severity: 'success', summary: 'Guardado', detail: 'Item actualizado', life: 1000 });
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error guardando', life: 3000 });
    }
};

const saveEvidence = async (item) => {
    try {
        const url = window.route ? window.route('api.auditoria.ejecucion.item.update', { id: item.id }) : `/api/auditoria/ejecucion/item/${item.id}`;
        await axios.put(url, {

            evidencia_registrada: item.evidencia_registrada,
            hallazgo_detectado: item.hallazgo_detectado
        });

        toast.add({ severity: 'success', summary: 'Guardado', detail: 'Evidencia guardada', life: 1000 });
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error guardando evidencia', life: 3000 });
    }
};

const improvingHallazgo = ref({});
const improvingMejora = ref({});

const improveHallazgo = async (item) => {
    if (!item.hallazgo_detectado || item.hallazgo_detectado.length < 5) return;

    improvingHallazgo.value[item.id] = true;
    try {
        const url = window.route ? window.route('api.auditoria.ejecucion.improve-hallazgo') : '/api/auditoria/ejecucion/improve-hallazgo';
        const response = await axios.post(url, { text: item.hallazgo_detectado });
        item.hallazgo_detectado = response.data.improved;
        toast.add({ severity: 'success', summary: 'Mejorado', detail: 'Hallazgo actualizado con IA', life: 2000 });
        saveEvidence(item);
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo mejorar la redacción', life: 3000 });
    } finally {
        improvingHallazgo.value[item.id] = false;
    }
};

const improveMejora = async (item) => {
    if (!item.hallazgo_detectado || item.hallazgo_detectado.length < 5) return;

    improvingMejora.value[item.id] = true;
    try {
        const url = window.route ? window.route('api.auditoria.ejecucion.improve-mejora') : '/api/auditoria/ejecucion/improve-mejora';
        const response = await axios.post(url, { text: item.hallazgo_detectado });
        item.hallazgo_detectado = response.data.improved;
        toast.add({ severity: 'success', summary: 'Mejorado', detail: 'Mejora actualizada con IA', life: 2000 });
        saveEvidence(item);
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo mejorar la redacción', life: 3000 });
    } finally {
        improvingMejora.value[item.id] = false;
    }
};


onMounted(() => {
    loadDetails();
});

watch(() => props.agendaId, (newId) => {
    if (newId) loadDetails();
});

</script>

<style scoped>
.header-container {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
}

.accordion .card-header:hover {
    background-color: #f8f9fa !important;
}

.btn-xs {
    padding: 0.125rem 0.4rem;
    font-size: 0.7rem;
}

.btn-soft-danger {
    background-color: #fff5f5;
    color: #e53e3e;
    border: 1px solid #feb2b2;
}

.btn-soft-danger:hover {
    background-color: #feb2b2;
    color: #fff;
}

/* AI LOADER STYLES */
.ai-loader-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.ai-brain-wrapper {
    position: relative;
    display: inline-block;
}

.heartbeat {
    animation: heartbeat 1.5s ease-in-out infinite both;
    position: relative;
    z-index: 2;
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
    z-index: 1;
    animation: ai-pulse 2s infinite;
}

@keyframes heartbeat {
    from {
        transform: scale(1);
        transform-origin: center center;
        animation-timing-function: ease-out;
    }

    10% {
        transform: scale(0.91);
        animation-timing-function: ease-in;
    }

    17% {
        transform: scale(0.98);
        animation-timing-function: ease-out;
    }

    33% {
        transform: scale(0.87);
        animation-timing-function: ease-in;
    }

    45% {
        transform: scale(1);
        animation-timing-function: ease-out;
    }
}

@keyframes ai-pulse {
    0% {
        transform: translate(-50%, -50%) scale(0.95);
        box-shadow: 0 0 0 0 rgba(229, 62, 62, 0.5);
    }

    70% {
        transform: translate(-50%, -50%) scale(1.6);
        box-shadow: 0 0 0 15px rgba(229, 62, 62, 0);
    }

    100% {
        transform: translate(-50%, -50%) scale(0.95);
        box-shadow: 0 0 0 0 rgba(229, 62, 62, 0);
    }
}

.animate-flicker {
    animation: flicker 2s infinite;
}

@keyframes flicker {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.4;
    }
}
</style>

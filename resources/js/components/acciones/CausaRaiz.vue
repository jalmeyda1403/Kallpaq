<template>
    <div :class="['transition-all', { 'card mt-3 shadow-sm border-0': !embedded }]">
        <!-- Header solo si NO está embebido -->
        <div v-if="!embedded" class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark font-weight-bold">
                    <i class="fas fa-search-location text-danger mr-2"></i>Análisis de Causa Raíz
                </h5>
                <div v-if="!isEditing && hasCausa">
                    <button :disabled="!hallazgoStore.accionesPermitidas"
                        class="btn btn-outline-danger btn-sm rounded-pill px-3" @click="enableEdit"
                        :title="!hallazgoStore.accionesPermitidas ? 'No se puede editar en este estado de hallazgo' : ''">
                        <i class="fas fa-edit mr-1"></i> Editar
                    </button>
                </div>
            </div>
        </div>

        <div :class="{ 'card-body': !embedded, 'mt-1': embedded && !hideTitle, 'mt-0': embedded && hideTitle }">
            <!-- Header de controles si ESTÁ embebido -->
            <div v-if="embedded && !hideTitle" class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 text-secondary font-weight-bold">
                    <i class="fas fa-search-location text-danger mr-2"></i>Análisis de Causa Raíz
                </h5>
                <div v-if="!isEditing && hasCausa && !hideEditButton">
                    <button :disabled="!hallazgoStore.accionesPermitidas"
                        class="btn btn-outline-danger btn-sm rounded-pill px-3" @click="enableEdit"
                        :title="!hallazgoStore.accionesPermitidas ? 'No se puede editar en este estado de hallazgo' : ''">
                        <i class="fas fa-edit mr-1"></i> Editar
                    </button>
                </div>
            </div>

            <!-- Modo Vista: Solo mostrar resultado -->
            <div v-if="!isEditing && hasCausa" class="animate__animated animate__fadeIn">
                <div class="d-flex align-items-center mb-3">
                    <span class="text-muted mr-2">Método aplicado:</span>
                    <span class="badge badge-secondary px-3 py-2 rounded-pill"
                        style="font-size: 0.9rem;">
                        {{ getMetodoLabel(causa.hc_metodo) }}
                    </span>
                </div>

                <div class="bg-light p-3 rounded-lg position-relative" style="border-left: 4px solid #dc3545;">
                    <div class="small text-danger font-weight-bold mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Causa Raíz Identificada</div>
                    <p class="mb-0 text-dark small" style="line-height: 1.5;">
                        {{ causa.hc_resultado || 'No especificado' }}
                    </p>
                </div>
            </div>

            <!-- Modo Edición: Formulario completo -->
            <div v-else class="animate__animated animate__fadeIn">
                <div class="form-group">
                    <label class="font-weight-bold text-secondary">Seleccione el Método de Análisis</label>
                    <select v-model="causa.hc_metodo" class="form-control custom-select shadow-sm border-secondary"
                        @change="resetFields">
                        <option value="">-- Seleccione --</option>
                        <option value="cinco_porques">5 Porqués</option>
                        <option value="ishikawa">Ishikawa (6M)</option>
                    </select>
                </div>

                <!-- 5 Porqués -->
                <div v-if="causa.hc_metodo === 'cinco_porques'" class="mt-4">
                    <h6 class="text-danger mb-3 border-bottom pb-2">Desarrollo de los 5 Porqués</h6>
                    <div class="form-group" v-for="i in 5" :key="i">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white text-danger font-weight-bold border-right-0"
                                    style="width: 40px;">{{ i }}</span>
                            </div>
                            <input type="text" v-model="causa['hc_por_que' + i]" class="form-control border-left-0"
                                :placeholder="'¿Por qué ocurre el problema? (Nivel ' + i + ')'">
                        </div>
                    </div>
                </div>

                <!-- Ishikawa (6M) -->
                <div v-if="causa.hc_metodo === 'ishikawa'" class="mt-4">
                    <h6 class="text-danger mb-3 border-bottom pb-2">Diagrama de Ishikawa (6M)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted small text-uppercase font-weight-bold">Mano de Obra</label>
                                <textarea v-model="causa.hc_mano_obra" class="form-control shadow-sm"
                                    rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="text-muted small text-uppercase font-weight-bold">Metodologías</label>
                                <textarea v-model="causa.hc_metodologias" class="form-control shadow-sm"
                                    rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="text-muted small text-uppercase font-weight-bold">Materiales</label>
                                <textarea v-model="causa.hc_materiales" class="form-control shadow-sm"
                                    rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted small text-uppercase font-weight-bold">Máquinas</label>
                                <textarea v-model="causa.hc_maquinas" class="form-control shadow-sm"
                                    rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="text-muted small text-uppercase font-weight-bold">Medición</label>
                                <textarea v-model="causa.hc_medicion" class="form-control shadow-sm"
                                    rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="text-muted small text-uppercase font-weight-bold">Medio Ambiente</label>
                                <textarea v-model="causa.hc_medio_ambiente" class="form-control shadow-sm"
                                    rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Análisis Final (Causa Raíz) - Obligatorio para ambos métodos -->
                <div v-if="causa.hc_metodo" class="form-group mt-4">
                    <label class="font-weight-bold text-dark">Conclusión del Análisis (Causa Raíz) <span
                            class="text-danger">*</span></label>
                    <textarea v-model="causa.hc_resultado" class="form-control shadow-sm border-danger" rows="4"
                        placeholder="Redacte aquí la causa raíz definitiva identificada tras el análisis..."
                        required></textarea>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i> Este resultado fundamentará los planes de acción
                        posteriores.
                    </small>
                </div>

                <div class="mt-4 text-right border-top pt-3">
                    <button v-if="hasCausa" class="btn btn-light text-secondary mr-2" @click="cancelEdit">
                        Cancelar
                    </button>
                    <button class="btn btn-danger px-4 shadow-sm" @click="saveCausa"
                        :disabled="isSaving || !canSave || !hallazgoStore.accionesPermitidas">
                        <i class="fas fa-save mr-1"></i> {{ isSaving ? 'Guardando...' : 'Guardar Análisis' }}
                    </button>

                    <div v-if="!hallazgoStore.accionesPermitidas" class="alert alert-warning mt-3" role="alert">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        El análisis de causa raíz está deshabilitado para este estado de hallazgo
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import axios from 'axios';
import { route } from 'ziggy-js';
import Swal from 'sweetalert2';

const props = defineProps({
    hallazgoId: {
        type: [Number, String],
        required: true
    },
    embedded: {
        type: Boolean,
        default: false
    },
    hideTitle: {
        type: Boolean,
        default: false
    },
    hideEditButton: {
        type: Boolean,
        default: false
    }
});

const hallazgoStore = useHallazgoStore();
const { causaRaiz: causa } = storeToRefs(hallazgoStore);

const isSaving = ref(false);

// Computed para verificar si ya existe una causa guardada (si tiene ID o resultado)
const hasCausa = computed(() => {
    return causa.value.id || (causa.value.hc_resultado && causa.value.hc_resultado.length > 0);
});

// Inicializar isEditing basado en el estado actual de la causa
const isEditing = ref(!(causa.value.id || (causa.value.hc_resultado && causa.value.hc_resultado.length > 0)));

// Watch para actualizar hasCausa sin afectar isEditing
// Solo actualizamos hasCausa si no estamos en modo edición
watch(causa, (newVal) => {
    // Este watcher se mantiene para actualizar hasCausa,
    // pero no debe cambiar isEditing mientras el usuario está editando
}, { deep: true, immediate: true });

const canSave = computed(() => {
    return causa.value.hc_metodo && causa.value.hc_resultado && causa.value.hc_resultado.trim().length > 0;
});

const getMetodoLabel = (metodo) => {
    const labels = {
        'cinco_porques': '5 Porqués',
        'ishikawa': 'Ishikawa (6M)'
    };
    return labels[metodo] || metodo;
};

const saveCausa = async () => {
    if (!canSave.value) {
        Swal.fire('Atención', 'Debe completar el método y la conclusión del análisis.', 'warning');
        return;
    }

    isSaving.value = true;
    try {
        await hallazgoStore.saveCausaRaiz();
        // Swal.fire('Éxito', 'Análisis guardado correctamente', 'success'); // Ya lo hace el store
        isEditing.value = false;
    } catch (error) {
        // Error manejado en store
    } finally {
        isSaving.value = false;
    }
};

const enableEdit = () => {
    isEditing.value = true;
};

const cancelEdit = async () => {
    await hallazgoStore.fetchCausaRaiz(props.hallazgoId); // Recargar original
    isEditing.value = false;
};

const resetFields = () => {
    // Limpiar campos específicos pero mantener resultado
    // Esto se maneja reactivamente en el store, pero podemos forzar limpieza si es necesario
    // Por ahora confiamos en que el usuario rellenará lo nuevo
};

// No necesitamos onMounted fetch aquí porque lo hará el padre (AccionesIndex)

defineExpose({
    enableEdit
});
</script>

<style scoped>
/* Transiciones suaves para cambios de modo */
.transition-all {
    transition: all 0.3s ease-in-out;
}

/* Animaciones de entrada */
.animate__animated.animate__fadeIn {
    animation-duration: 0.4s;
}

/* Mejora visual del badge de método */
.badge {
    transition: all 0.2s ease-in-out;
}

.badge:hover {
    transform: scale(1.05);
}

/* Estilo para el resultado de causa raíz */
.rounded-lg {
    border-radius: 0.5rem;
}

/* Transiciones para botones */
.btn {
    transition: all 0.2s ease-in-out;
}

.btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
}

/* Mejora de inputs y textareas */
.form-control {
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>
```

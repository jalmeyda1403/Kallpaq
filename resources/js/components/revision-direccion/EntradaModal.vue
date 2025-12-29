<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); overflow-y: auto;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-xl overflow-hidden">

                <!-- Overlay de carga interna similar al CompromisoModal -->
                <div v-if="!isDataLoaded" class="loading-overlay d-flex align-items-center justify-content-center">
                    <i class="fas fa-circle-notch fa-spin fa-2x text-primary"></i>
                </div>

                <!-- Header con diseño premium -->
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded p-2 mr-3 shadow-sm">
                            <i class="fas fa-sign-in-alt text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold mb-0">
                                {{ isEdit ? 'Editar Entrada' : 'Nueva Entrada' }}
                            </h5>
                            <small class="text-primary font-weight-bold">ISO 9001 §9.3.2</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white" @click="$emit('close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="guardar">
                    <div class="modal-body p-4 bg-light"
                        :class="{ 'opacity-0': !isDataLoaded, 'transition-opacity': true }">

                        <div class="row">
                            <!-- Campo: Tipo de Entrada -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark mb-1 required">Tipo de Entrada</label>
                                    <div class="input-group shadow-sm rounded-lg overflow-hidden">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-0 text-muted"><i
                                                    class="fas fa-list-ul"></i></span>
                                        </div>
                                        <select v-model="form.tipo_entrada"
                                            class="form-control border-0 no-focus-outline" required>
                                            <option value="">Seleccionar...</option>
                                            <option v-for="(label, value) in tiposEntrada" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo: Estado -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark mb-1">Estado</label>
                                    <div class="input-group shadow-sm rounded-lg overflow-hidden">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-0 text-muted"><i
                                                    class="fas fa-info-circle"></i></span>
                                        </div>
                                        <select v-model="form.estado" class="form-control border-0 no-focus-outline">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="revisado">Revisado</option>
                                            <option value="aprobado">Aprobado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo: Título -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark mb-1 required">Título de la Entrada</label>
                            <input type="text" v-model="form.titulo"
                                class="form-control shadow-sm border-0 rounded-lg no-focus-outline" required
                                placeholder="Ej: Resultados de la auditoría interna Q3 2023">
                        </div>

                        <!-- Campo: Descripción -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark mb-1 required">Descripción Detallada</label>
                            <textarea v-model="form.descripcion"
                                class="form-control shadow-sm border-0 rounded-lg no-focus-outline" rows="4" required
                                placeholder="Describa la información relevante para la revisión..."></textarea>
                        </div>

                        <!-- Campo: Fuente -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark mb-1">Fuente de Información</label>
                            <div class="input-group shadow-sm rounded-lg overflow-hidden">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-0 text-muted"><i
                                            class="fas fa-file-contract"></i></span>
                                </div>
                                <input type="text" v-model="form.fuente" class="form-control border-0 no-focus-outline"
                                    placeholder="Ej: Reporte de Calidad, Matriz de Riesgos...">
                            </div>
                        </div>

                        <!-- Campo: Conclusión -->
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark mb-1">Conclusión / Análisis</label>
                            <textarea v-model="form.conclusion"
                                class="form-control shadow-sm border-0 rounded-lg no-focus-outline" rows="3"
                                placeholder="Conclusiones preliminares o análisis de los datos presentados..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top py-3">
                        <button type="button" class="btn btn-light rounded-pill px-4 font-weight-bold shadow-sm"
                            @click="$emit('close')">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="btn btn-primary rounded-pill px-5 font-weight-bold shadow transition-all hover-up"
                            :disabled="isLoading || !isDataLoaded">
                            <i class="fas fa-save mr-1" v-if="!isLoading"></i>
                            <i class="fas fa-spinner fa-spin mr-1" v-else></i>
                            {{ isLoading ? 'Guardando...' : (isEdit ? 'Actualizar Entrada' : 'Guardar Entrada') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';

const props = defineProps({
    revisionId: { type: [Number, String], required: true },
    entrada: { type: Object, default: null }
});

const emit = defineEmits(['close', 'saved']);
const store = useRevisionDireccionStore();

const isLoading = ref(false);
const isDataLoaded = ref(false);
const tiposEntrada = computed(() => store.tiposEntrada);

const isEdit = computed(() => !!props.entrada);

const form = reactive({
    tipo_entrada: '',
    titulo: '',
    descripcion: '',
    fuente: '',
    conclusion: '',
    estado: 'pendiente'
});

onMounted(async () => {
    // Asegurar que tipos están cargados
    if (Object.keys(tiposEntrada.value).length === 0) {
        await store.fetchTiposEntrada();
    }

    if (isEdit.value) {
        Object.assign(form, {
            tipo_entrada: props.entrada.tipo_entrada || '',
            titulo: props.entrada.titulo || '',
            descripcion: props.entrada.descripcion || '',
            fuente: props.entrada.fuente || '',
            conclusion: props.entrada.conclusion || '',
            estado: props.entrada.estado || 'pendiente'
        });
    }

    isDataLoaded.value = true;
});

const guardar = async () => {
    isLoading.value = true;
    try {
        if (isEdit.value) {
            await store.updateEntrada(props.entrada.id, form);
            emit('saved', 'Entrada actualizada exitosamente');
        } else {
            await store.addEntrada(props.revisionId, form);
            emit('saved', 'Entrada agregada exitosamente');
        }
        emit('close');
    } catch (err) {
        alert('Error al guardar: ' + (err.response?.data?.message || err.message));
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.rounded-xl {
    border-radius: 1rem !important;
}

.no-focus-outline:focus {
    outline: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075), 0 0 0 0.1rem rgba(0, 123, 255, 0.25) !important;
}

label.required::after {
    content: ' *';
    color: #dc3545;
}

.transition-all {
    transition: all 0.3s ease;
}

.hover-up:hover {
    transform: translateY(-2px);
}

.loading-overlay {
    position: absolute;
    top: 100px;
    /* Debajo del header */
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(248, 249, 250, 0.8);
    z-index: 10;
}

.opacity-0 {
    opacity: 0;
}

.transition-opacity {
    transition: opacity 0.4s ease-in-out;
}
</style>

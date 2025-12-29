<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); overflow-y: auto;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-xl overflow-hidden">

                <!-- Overlay de carga interna -->
                <div v-if="!isDataLoaded" class="loading-overlay d-flex align-items-center justify-content-center">
                    <i class="fas fa-circle-notch fa-spin fa-2x text-success"></i>
                </div>

                <!-- Header con diseño premium -->
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success rounded p-2 mr-3 shadow-sm">
                            <i class="fas fa-sign-out-alt text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold mb-0">
                                {{ isEdit ? 'Editar Salida/Decisión' : 'Nueva Salida/Decisión' }}
                            </h5>
                            <small class="text-success font-weight-bold">ISO 9001 §9.3.3</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white" @click="$emit('close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="guardar">
                    <div class="modal-body p-4 bg-light"
                        :class="{ 'opacity-0': !isDataLoaded, 'transition-opacity': true }">

                        <div class="alert alert-soft-success border-0 mb-4 shadow-sm">
                            <div class="d-flex">
                                <i class="fas fa-info-circle mr-3 mt-1 text-success"></i>
                                <span class="small text-dark">
                                    Las salidas incluyen decisiones y acciones relacionadas con oportunidades de mejora,
                                    cambios en el SGC y necesidad de recursos.
                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Campo: Tipo de Salida -->
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark mb-1 required">Tipo de Salida</label>
                                    <div class="input-group shadow-sm rounded-lg overflow-hidden">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-0 text-muted"><i
                                                    class="fas fa-tasks"></i></span>
                                        </div>
                                        <select v-model="form.tipo_salida"
                                            class="form-control border-0 no-focus-outline" required>
                                            <option value="">Seleccionar...</option>
                                            <option v-for="(label, value) in tiposSalida" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo: Descripción -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark mb-1 required">Descripción de la
                                Decisión/Acción</label>
                            <textarea v-model="form.descripcion"
                                class="form-control shadow-sm border-0 rounded-lg no-focus-outline" rows="4" required
                                placeholder="Describa la decisión tomada o acción a realizar..."></textarea>
                        </div>

                        <!-- Campo: Justificación -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark mb-1">Justificación / Análisis</label>
                            <textarea v-model="form.justificacion"
                                class="form-control shadow-sm border-0 rounded-lg no-focus-outline" rows="2"
                                placeholder="Base o justificación para esta decisión (opcional)..."></textarea>
                        </div>



                        <!-- Sección de Compromiso Relacionado -->
                        <div v-if="isEdit && props.salida.compromisos?.length > 0" class="mt-2 mb-4">
                            <label class="font-weight-bold text-dark mb-1">Compromiso Asociado</label>
                            <div v-for="comp in props.salida.compromisos" :key="comp.id"
                                class="p-3 bg-white border border-warning rounded-lg shadow-xs d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 font-weight-bold text-dark small">{{ comp.codigo }}: {{
                                        comp.descripcion }}</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-pill font-weight-bold mr-2"
                                            :class="getCompromisoEstadoClass(comp.estado)">
                                            {{ comp.estado }}
                                        </span>
                                        <small class="text-muted"><i class="fas fa-calendar-alt mr-1"></i> {{
                                            comp.fecha_limite }}</small>
                                    </div>
                                </div>
                                <div class="text-center ml-3">
                                    <div class="h5 mb-0 font-weight-bold text-warning">{{ comp.avance || 0 }}%</div>
                                    <small class="extra-small text-muted font-weight-bold uppercase">Avance</small>
                                </div>
                            </div>
                        </div>

                        <!-- Opción de Crear Compromiso (Solo si es nueva salida) -->
                        <div v-if="!isEdit" class="form-group mb-0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="switchCompromiso"
                                    v-model="crearCompromiso">
                                <label class="custom-control-label font-weight-bold text-dark" for="switchCompromiso">
                                    Generar un Compromiso de Seguimiento
                                </label>
                            </div>

                            <div v-if="crearCompromiso"
                                class="mt-3 p-3 bg-white border border-success rounded-lg shadow-xs transition-all animate-fade-in">
                                <h6 class="small font-weight-bold text-success text-uppercase mb-3">
                                    <i class="fas fa-tasks mr-2"></i>Detalles del Nuevo Compromiso
                                </h6>
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold">Descripción Corta del Compromiso</label>
                                    <input type="text" v-model="compromisoData.descripcion"
                                        class="form-control form-control-sm shadow-none border-0 bg-light"
                                        placeholder="Acción concreta a realizar">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold">Responsable</label>
                                        <select v-model="compromisoData.responsable_id"
                                            class="form-control form-control-sm shadow-none border-0 bg-light" required>
                                            <option :value="null">Seleccionar...</option>
                                            <option v-for="user in usuarios" :key="user.id" :value="user.id">
                                                {{ user.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold">Fecha Límite</label>
                                        <input type="date" v-model="compromisoData.fecha_limite"
                                            class="form-control form-control-sm shadow-none border-0 bg-light" required>
                                    </div>
                                </div>

                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold">Recursos Necesarios (Opcional)</label>
                                    <textarea v-model="compromisoData.recursos_necesarios"
                                        class="form-control form-control-sm shadow-none border-0 bg-light" rows="2"
                                        placeholder="Ej: Presupuesto extra, licencias, personal..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top py-3">
                        <button type="button" class="btn btn-light rounded-pill px-4 font-weight-bold shadow-sm"
                            @click="$emit('close')">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="btn btn-success rounded-pill px-5 font-weight-bold shadow transition-all hover-up"
                            :disabled="isLoading || !isDataLoaded">
                            <i class="fas fa-save mr-1" v-if="!isLoading"></i>
                            <i class="fas fa-spinner fa-spin mr-1" v-else></i>
                            {{ isLoading ? 'Guardando...' : (isEdit ? 'Actualizar Salida' : 'Guardar Salida') }}
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
import axios from 'axios';

const props = defineProps({
    revisionId: { type: [Number, String], required: true },
    salida: { type: Object, default: null }
});

const emit = defineEmits(['close', 'saved']);
const store = useRevisionDireccionStore();

const isLoading = ref(false);
const isDataLoaded = ref(false);
const usuarios = ref([]);
const crearCompromiso = ref(false);
const tiposSalida = computed(() => store.tiposSalida);

const isEdit = computed(() => !!props.salida);

const form = reactive({
    tipo_salida: '',
    descripcion: '',
    justificacion: ''
});

const compromisoData = reactive({
    descripcion: '',
    fecha_limite: '',
    responsable_id: null,
    recursos_necesarios: ''
});

onMounted(async () => {
    // Cargar dependencias
    const promises = [
        axios.get('/users/list', { params: { role: 'propietario' } })
    ];

    if (Object.keys(tiposSalida.value).length === 0) {
        promises.push(store.fetchTiposSalida());
    }

    try {
        const [usersRes] = await Promise.all(promises);
        usuarios.value = usersRes.data;
    } catch (err) {
        console.error('Error al cargar datos del modal', err);
    }

    if (isEdit.value) {
        Object.assign(form, {
            tipo_salida: props.salida.tipo_salida || '',
            descripcion: props.salida.descripcion || '',
            justificacion: props.salida.justificacion || ''
        });
    }

    isDataLoaded.value = true;
});

const guardar = async () => {
    isLoading.value = true;
    try {
        if (isEdit.value) {
            await store.updateSalida(props.salida.id, form);
            emit('saved', 'Salida actualizada exitosamente');
        } else {
            const response = await store.addSalida(props.revisionId, form);
            const nuevaSalida = response.data;

            // Si se marcó crear compromiso
            if (crearCompromiso.value && compromisoData.descripcion && compromisoData.fecha_limite) {
                await store.addCompromiso(props.revisionId, {
                    salida_id: nuevaSalida.id,
                    descripcion: compromisoData.descripcion,
                    fecha_limite: compromisoData.fecha_limite,
                    responsable_id: compromisoData.responsable_id,
                    recursos_necesarios: compromisoData.recursos_necesarios
                });
            }
            emit('saved', 'Salida agregada exitosamente');
        }
        emit('close');
    } catch (err) {
        alert('Error al guardar: ' + (err.response?.data?.message || err.message));
    } finally {
        isLoading.value = false;
    }
};

const getCompromisoEstadoClass = (estado) => {
    switch (estado) {
        case 'pendiente': return 'badge-primary';
        case 'en_proceso': return 'badge-info';
        case 'completado': return 'badge-success';
        case 'vencido': return 'badge-danger';
        case 'cancelado': return 'badge-secondary';
        default: return 'badge-light';
    }
};
</script>

<style scoped>
.rounded-xl {
    border-radius: 1rem !important;
}

.no-focus-outline:focus {
    outline: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075), 0 0 0 0.1rem rgba(40, 167, 69, 0.25) !important;
}

label.required::after {
    content: ' *';
    color: #dc3545;
}

.alert-soft-success {
    background-color: rgba(40, 167, 69, 0.1);
    color: #155724;
}

.transition-all {
    transition: all 0.3s ease;
}

.hover-up:hover {
    transform: translateY(-2px);
}

.loading-overlay {
    position: absolute;
    top: 80px;
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

.shadow-xs {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}
</style>

<template>
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-lg text-left" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">
                        {{ isEdit ? 'Editar Revisión' : 'Nueva Revisión por la Dirección' }}
                    </h5>
                    <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="guardar">
                    <div class="modal-body p-4 bg-white">
                        <!-- Título -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold custom-label">Título de la Revisión <span
                                    class="text-danger">*</span></label>
                            <input type="text" v-model="form.titulo" class="form-control form-control-lg"
                                :class="{ 'is-invalid': errors.titulo }"
                                placeholder="Ej: Revisión del Sistema de Gestión - Q1 2025" required>
                            <div class="invalid-feedback" v-if="errors.titulo">{{ errors.titulo }}</div>
                        </div>

                        <!-- Fila 1: Contexto -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Periodo <span
                                            class="text-danger">*</span></label>
                                    <select v-model="form.periodo" class="form-control" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Q1">Q1 (Trimestral)</option>
                                        <option value="Q2">Q2 (Trimestral)</option>
                                        <option value="Q3">Q3 (Trimestral)</option>
                                        <option value="Q4">Q4 (Trimestral)</option>
                                        <option value="S1">S1 (Semestral)</option>
                                        <option value="S2">S2 (Semestral)</option>
                                        <option value="A">Anual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Año <span
                                            class="text-danger">*</span></label>
                                    <select v-model="form.anio" class="form-control" required>
                                        <option v-for="a in anios" :key="a" :value="a">{{ a }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Responsable <span
                                            class="text-danger">*</span></label>
                                    <select v-model="form.responsable_id" class="form-control" required>
                                        <option value="">Seleccionar...</option>
                                        <option v-for="user in usuarios" :key="user.id" :value="user.id">
                                            {{ user.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Fila 2: Planificación y Estado -->
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Fecha Programada <span
                                            class="text-danger">*</span></label>
                                    <input type="date" v-model="form.fecha_programada" class="form-control"
                                        :class="{ 'is-invalid': errors.fecha_programada }" required>
                                </div>
                            </div>
                            <div class="col-md-4" v-if="isEdit">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label text-danger">Fecha Real</label>
                                    <input type="date" v-model="form.fecha_reunion" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4" v-if="isEdit">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Estado</label>
                                    <select v-model="form.estado"
                                        class="form-control font-weight-bold text-dark text-capitalize">
                                        <option value="programada">Programada</option>
                                        <option value="aprobada">Aprobada</option>
                                        <option value="realizada">Realizada</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Alcance / Sistemas -->
                        <div class="form-group mt-2">
                            <label class="font-weight-bold custom-label mb-2">Sistemas de Gestión / Alcance</label>
                            <div class="bg-light p-3 rounded border d-flex flex-wrap gap-2">
                                <div v-for="sys in availableSystems" :key="sys" class="chip-check">
                                    <input type="checkbox" :id="'sys-' + sys" :value="sys"
                                        v-model="form.sistemas_gestion" class="d-none">
                                    <label :for="'sys-' + sys" class="chip-label mb-0"
                                        :class="{ 'active': form.sistemas_gestion.includes(sys) }">
                                        {{ sys }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles -->
                        <div class="form-group mt-3">
                            <label class="font-weight-bold custom-label">Participantes</label>
                            <textarea v-model="form.participantes" class="form-control" rows="2"
                                placeholder="Nombres o cargos de los asistentes..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold custom-label">Agenda / Temas</label>
                            <textarea v-model="form.agenda" class="form-control" rows="3"
                                placeholder="Puntos principales a tratar..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" @click="close">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger px-4" :disabled="isLoading">
                            <span v-if="isLoading" class="spinner-border spinner-border-sm mr-1"></span>
                            <span v-else><i class="fas fa-save mr-1"></i></span>
                            {{ isEdit ? 'Guardar Cambios' : 'Guardar Revisión' }}
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
import { Modal } from 'bootstrap';
import axios from 'axios';

const emit = defineEmits(['close', 'saved']);
const store = useRevisionDireccionStore();

const modalRef = ref(null);
let modalInstance = null;

const usuarios = ref([]);
const errors = reactive({});

// Cacheamos usuarios en el cliente para evitar recargas innecesarias
const cachedUsers = window.usersCache || null;

const isEdit = computed(() => store.modalMode === 'edit');
const isLoading = computed(() => store.isLoading);

const anios = computed(() => {
    const currentYear = new Date().getFullYear();
    const list = [];
    for (let i = currentYear - 2; i <= currentYear + 1; i++) {
        list.push(i);
    }
    return list.sort((a, b) => b - a);
});

const form = reactive({
    titulo: '',
    periodo: '',
    anio: new Date().getFullYear(),
    fecha_programada: '',
    fecha_reunion: '',
    responsable_id: '',
    participantes: '',
    agenda: '',
    estado: 'programada',
    sistemas_gestion: []
});

const availableSystems = ['SGC', 'SGCM', 'SGCO', 'SGAS', 'SGSI', 'Riesgos'];

// Función para poblar el formulario INMEDIATAMENTE
const populateForm = () => {
    if (isEdit.value && store.revisionActual) {
        Object.assign(form, {
            titulo: store.revisionActual.titulo,
            periodo: store.revisionActual.periodo,
            anio: store.revisionActual.anio,
            fecha_programada: store.revisionActual.fecha_programada?.split('T')[0] || '',
            fecha_reunion: store.revisionActual.fecha_reunion?.split('T')[0] || '',
            responsable_id: store.revisionActual.responsable_id,
            participantes: store.revisionActual.participantes || '',
            agenda: store.revisionActual.agenda || '',
            estado: store.revisionActual.estado,
            // Asegurar que sea array, incluso si viene null
            sistemas_gestion: store.revisionActual.sistemas_gestion || []
        });
    }
};

const close = () => {
    if (modalInstance) {
        modalInstance.hide();
    }
};

onMounted(() => {
    // 1. Inicializar Modal (UI)
    if (modalRef.value) {
        modalInstance = new Modal(modalRef.value, {
            backdrop: 'static',
            keyboard: false
        });
        modalInstance.show();
        modalRef.value.addEventListener('hidden.bs.modal', () => emit('close'));
    }

    // 2. Cargar Datos del Formulario (Síncrono/Inmediato)
    // Esto previene que el usuario vea el formulario vacío y luego salte
    populateForm();

    // 3. Cargar Usuarios (Asíncrono/Background)
    // No bloqueamos la UI esperando esto. Si ya están cacheados, los usamos.
    if (window.usersCache) {
        usuarios.value = window.usersCache;
    } else {
        axios.get('/users/list')
            .then(response => {
                usuarios.value = response.data;
                // Simple window cache para esta sessión
                window.usersCache = response.data;
            })
            .catch(err => console.error('Error al cargar usuarios', err));
    }
});

const guardar = async () => {
    Object.keys(errors).forEach(k => delete errors[k]);
    try {
        if (isEdit.value) {
            await store.updateRevision(store.revisionActual.id, form);
            emit('saved', 'Revisión actualizada exitosamente');
        } else {
            await store.createRevision(form);
            emit('saved', 'Revisión creada exitosamente');
        }
        close();
    } catch (err) {
        if (err.response?.status === 422) {
            Object.assign(errors, err.response.data.errors);
        } else {
            alert('Error al guardar la revisión');
        }
    }
};
</script>

<style scoped>
/* Estilos extraídos de SalidaNCModal.vue para consistencia exacta */
.custom-label {
    font-size: 0.9em !important;
    font-weight: 600 !important;
    color: #495057 !important;
    margin-bottom: 0.3rem;
}

.modal-content {
    border-radius: 0.5rem;
    /* Standard Bootstrap radius */
}

/* Chips de Selección (Clean & Professional UX) */
.gap-2 {
    gap: 0.6rem;
}

.chip-label {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
    border: 1px solid #dee2e6;
    background-color: #fff;
    color: #6c757d;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    user-select: none;
}

.chip-label:hover {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.chip-label.active {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #fff;
    box-shadow: 0 2px 5px rgba(220, 53, 69, 0.3);
}

textarea {
    resize: none;
}
</style>

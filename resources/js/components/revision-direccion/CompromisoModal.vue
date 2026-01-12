<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); overflow-y: auto;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header estilo SalidasNC -->
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">
                        {{ isEdit ? 'Editar Compromiso' : 'Nuevo Compromiso' }}
                    </h5>
                    <button type="button" class="close text-white" @click="$emit('close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="guardar">
                    <div class="modal-body p-4 bg-white">
                        <!-- Descripción -->
                        <div class="form-group">
                            <label class="font-weight-bold custom-label">Descripción del Compromiso <span
                                    class="text-danger">*</span></label>
                            <textarea v-model="form.descripcion" class="form-control" rows="3" required maxlength="500"
                                placeholder="Describa claramente el compromiso..."></textarea>
                            <small class="text-muted float-right mt-1">{{ form.descripcion.length }}/500</small>
                        </div>

                        <div class="row mt-4">
                            <!-- Responsable -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Responsable <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" :value="responsableNombre" readonly
                                            placeholder="Seleccionar..." required @click="openResponsableModal"
                                            style="cursor: pointer; background-color: #fff;">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                @click="openResponsableModal">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fecha Límite -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Fecha Límite <span
                                            class="text-danger">*</span></label>
                                    <input type="date" v-model="form.fecha_limite" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Prioridad -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Prioridad</label>
                                    <select v-model="form.prioridad" class="form-control text-capitalize">
                                        <option value="baja">Baja</option>
                                        <option value="media">Media</option>
                                        <option value="alta">Alta</option>
                                        <option value="critica">Crítica</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Estado (Solo Edit) -->
                            <div class="col-md-6" v-if="isEdit">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Estado</label>
                                    <select v-model="form.estado" class="form-control text-capitalize">
                                        <option value="programada">Programada</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="en_proceso">En Proceso</option>
                                        <option value="completado">Completado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sistemas de Gestión (Chips Style) -->
                        <div class="form-group mt-3">
                            <label class="font-weight-bold custom-label mb-2">Sistemas de Gestión Relacionados</label>
                            <div class="bg-light p-3 rounded border d-flex flex-wrap gap-2">
                                <div v-for="sys in availableSystems" :key="sys" class="chip-check">
                                    <input type="checkbox" :id="'comp-sys-' + sys" :value="sys"
                                        v-model="form.sistemas_gestion" class="d-none">
                                    <label :for="'comp-sys-' + sys" class="chip-label mb-0"
                                        :class="{ 'active': form.sistemas_gestion.includes(sys) }">
                                        {{ sys }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Recursos -->
                        <div class="form-group mt-3">
                            <label class="font-weight-bold custom-label">Recursos Necesarios</label>
                            <textarea v-model="form.recursos_necesarios" class="form-control" rows="2" maxlength="1000"
                                placeholder="Indique personal, presupuesto o herramientas necesarias..."></textarea>
                        </div>

                        <!-- Observaciones -->
                        <div class="form-group">
                            <label class="font-weight-bold custom-label">Observaciones</label>
                            <textarea v-model="form.observaciones" class="form-control" rows="2"
                                maxlength="500"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary px-4" @click="$emit('close')">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger px-4" :disabled="isLoading">
                            <span v-if="isLoading"><i class="fas fa-spinner fa-spin mr-1"></i> Guardando...</span>
                            <span v-else><i class="fas fa-save mr-1"></i> Guardar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hijo para seleccionar Responsable -->
    <ModalHijo v-if="showResponsableModal" ref="responsableModal" fetchUrl="/users/list?role=propietario"
        targetId="responsable_id" targetDesc="Responsable" @update-target="handleResponsableSelected"
        @close="showResponsableModal = false" />
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';
import ModalHijo from '@/components/generales/ModalHijo.vue';

const props = defineProps({
    revisionId: { type: [Number, String], required: true },
    compromiso: { type: Object, default: null }
});

const emit = defineEmits(['close', 'saved']);
const store = useRevisionDireccionStore();

const isLoading = ref(false);
const isEdit = computed(() => !!props.compromiso);

const form = reactive({
    descripcion: '',
    responsable_id: '',
    fecha_limite: '',
    prioridad: 'media',
    estado: 'pendiente',
    sistemas_gestion: [],
    recursos_necesarios: '',
    observaciones: ''
});

const responsableNombre = ref('');
const showResponsableModal = ref(true);
const responsableModal = ref(null);

const availableSystems = ['SGC', 'SGCM', 'SGCO', 'SGAS', 'SGSI', 'Riesgos'];

onMounted(() => {
    if (isEdit.value) {
        Object.assign(form, {
            descripcion: props.compromiso.descripcion || '',
            responsable_id: props.compromiso.responsable_id || '',
            fecha_limite: props.compromiso.fecha_limite ? new Date(props.compromiso.fecha_limite).toISOString().split('T')[0] : '',
            prioridad: props.compromiso.prioridad || 'media',
            estado: props.compromiso.estado || 'pendiente',
            sistemas_gestion: props.compromiso.sistemas_gestion || [],
            recursos_necesarios: props.compromiso.recursos_necesarios || '',
            observaciones: props.compromiso.observaciones || ''
        });
        if (props.compromiso.responsable) {
            responsableNombre.value = props.compromiso.responsable.name;
        }
    }
});

const openResponsableModal = () => {
    if (responsableModal.value) responsableModal.value.open();
};

const handleResponsableSelected = (payload) => {
    form.responsable_id = payload.idValue;
    responsableNombre.value = payload.descValue;
};

const guardar = async () => {
    isLoading.value = true;
    try {
        if (isEdit.value) {
            await store.updateCompromiso(props.compromiso.id, form);
            emit('saved', 'Compromiso actualizado exitosamente');
        } else {
            await store.addCompromiso(props.revisionId, form);
            emit('saved', 'Compromiso creado exitosamente');
        }
        emit('close');
    } catch (err) {
        alert('Error: ' + (err.response?.data?.message || err.message));
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-weight: 600 !important;
    color: #495057 !important;
    margin-bottom: 0.3rem;
}

.modal-content {
    border-radius: 0.5rem;
}

/* Chip Styles */
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
    transition: all 0.2s ease;
    user-select: none;
}

.chip-label:hover {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

.chip-label.active {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
    box-shadow: 0 2px 5px rgba(220, 53, 69, 0.3);
}

textarea {
    resize: none;
}
</style>

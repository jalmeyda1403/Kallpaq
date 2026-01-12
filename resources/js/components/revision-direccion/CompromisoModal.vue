<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); overflow-y: auto;">

        <!-- El modal siempre muestra su estructura básica para evitar el "salto" visual -->
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-xl overflow-hidden">
                <!-- Header con diseño premium (Danger/Gris) -->
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <div class="d-flex align-items-center">

                        <div>
                            <h5 class="modal-title font-weight-bold mb-0">
                                {{ isEdit ? 'Editar Compromiso' : 'Nuevo Compromiso' }}
                            </h5>
                            <small class="text-white-50 font-weight-bold">Revisión por la Dirección</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white opacity-1" @click="$emit('close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="guardar">
                    <div class="modal-body p-4 bg-light position-relative" style="min-height: 400px;">

                        <!-- Overlay de carga sutil (solo sobre el cuerpo si los datos no están listos) -->

                        <div v-if="!isDataLoaded"
                            class="loading-overlay rounded-lg d-flex align-items-center justify-content-center">
                            <i class="fas fa-circle-notch fa-spin fa-2x text-danger"></i>
                        </div>

                        <!-- Contenido que se muestra "suave" cuando está listo -->
                        <div :class="{ 'opacity-0': !isDataLoaded, 'transition-opacity': true }">
                            <!-- Campo: Descripción -->
                            <div class="form-group mb-4">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <label class="font-weight-bold text-dark mb-0 required">Descripción del
                                        Compromiso</label>
                                    <span class="char-counter-top"
                                        :class="{ 'text-danger': form.descripcion.length >= 500 }">
                                        {{ form.descripcion.length }} / 500
                                    </span>
                                </div>
                                <textarea v-model="form.descripcion"
                                    class="form-control shadow-sm border-0 rounded-lg no-focus-outline" rows="4"
                                    required maxlength="500"
                                    placeholder="Describa claramente el compromiso adquirido..."></textarea>
                            </div>

                            <div class="row">
                                <!-- Campo: Responsable -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-1 required">Responsable</label>
                                        <div class="input-group shadow-sm rounded-lg overflow-hidden">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0 text-muted"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text"
                                               class="form-control border-left-0 no-focus-outline bg-white"
                                               :value="responsableNombre"
                                               readonly
                                               placeholder="Seleccione..."
                                               required
                                               @click="openResponsableModal"
                                               style="cursor: pointer; height: 38px;">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary border-left-0" type="button" @click="openResponsableModal">
                                            <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Campo: Fecha Límite -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-1 required">Fecha Límite</label>
                                        <div class="input-group shadow-sm rounded-lg overflow-hidden">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-0 text-muted"><i
                                                        class="fas fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="date" v-model="form.fecha_limite"
                                                class="form-control border-0 no-focus-outline" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Campo: Prioridad -->
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-1">Prioridad</label>
                                        <div class="input-group shadow-sm rounded-lg overflow-hidden">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-0 text-muted"><i
                                                        class="fas fa-layer-group"></i></span>
                                            </div>
                                            <select v-model="form.prioridad"
                                                class="form-control border-0 no-focus-outline">
                                                <option value="baja">Baja</option>
                                                <option value="media">Media</option>
                                                <option value="alta">Alta</option>
                                                <option value="critica">Crítica</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Campo: Estado (Solo en Edición) -->
                                <div class="col-md-6" v-if="isEdit">
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-1">Estado</label>
                                        <div class="input-group shadow-sm rounded-lg overflow-hidden">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-0 text-muted"><i
                                                        class="fas fa-info-circle"></i></span>
                                            </div>
                                            <select v-model="form.estado"
                                                class="form-control border-0 no-focus-outline">
                                                <option value="programada">Programada</option>
                                                <option value="pendiente">Pendiente</option>
                                                <option value="en_proceso">En Proceso</option>
                                                <option value="completado">Completado</option>
                                                <option value="cancelado">Cancelado</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo: Sistemas de Gestión -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-dark mb-1">Sistemas de Gestión Relacionados</label>
                                <MultiSelect v-model="form.sistemas_gestion" :options="availableSystems" 
                                    placeholder="Seleccionar sistemas involucrados" 
                                    class="w-100 custom-multiselect"
                                    display="chip" />
                            </div>

                            <!-- Campo: Recursos Necesarios -->
                            <div class="form-group mb-4">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <label class="font-weight-bold text-dark mb-0">Recursos Necesarios</label>
                                    <span class="char-counter-top"
                                        :class="{ 'text-danger': (form.recursos_necesarios?.length || 0) >= 1000 }">
                                        {{ form.recursos_necesarios?.length || 0 }} / 1000
                                    </span>
                                </div>
                                <textarea v-model="form.recursos_necesarios"
                                    class="form-control shadow-sm border-0 rounded-lg no-focus-outline text-area-custom"
                                    rows="2" maxlength="1000"
                                    placeholder="Personal, herramientas, infraestructura..."></textarea>
                            </div>

                            <!-- Campo: Observaciones -->
                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <label class="font-weight-bold text-dark mb-0">Observaciones / Notas</label>
                                    <span class="char-counter-top"
                                        :class="{ 'text-danger': (form.observaciones?.length || 0) >= 500 }">
                                        {{ form.observaciones?.length || 0 }} / 500
                                    </span>
                                </div>
                                <textarea v-model="form.observaciones"
                                    class="form-control shadow-sm border-0 rounded-lg no-focus-outline text-area-custom"
                                    rows="2" maxlength="500" placeholder="Comentarios adicionales..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top py-3">
                        <button type="button" class="btn btn-secondary" @click="$emit('close')">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                        <button type="submit"
                            class="btn btn-danger"
                            :disabled="isLoading || !isDataLoaded">
                            <i class="fas fa-save mr-1" v-if="!isLoading"></i>
                            <i class="fas fa-spinner fa-spin mr-1" v-else></i>
                            {{ isLoading ? 'Guardando...' : 'Guardar Compromiso' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    
    <!-- Modal Hijo para seleccionar Responsable -->
    <ModalHijo 
        v-if="showResponsableModal"
        ref="responsableModal"
        fetchUrl="/users/list?role=propietario"
        targetId="responsable_id"
        targetDesc="Responsable"
        @update-target="handleResponsableSelected"
        @close="showResponsableModal = false" 
    />
    <!-- Nota: ModalHijo emite evento 'close' si se cierra manualmente o hay que manejarlo con v-if?
         El componente ModalHijo tiene un botón close que llama a this.close() y emite nada por defecto,
         pero aquí estamos usando v-if para montarlo/desmontarlo.
         Revisando ModalHijo.vue:
         - Tiene <button @click="close">
         - close() llama a modalInstance.hide()
         - No emite 'close'. 
         - Pero bootstrap oculta el modal.
         - Si usamos v-if, necesitamos saber cuando se cerró para ponerlo en false.
         - ModalHijo usa bootstrap modal nativo.
         - Para simplificar, usaremos ref y el método open. Y v-show o simplemente lo dejaremos montado pero hidden?
         - ModalHijo.vue: mounted() instancia el modal. open() lo muestra.
         - Si usamos v-if, se monta y se crea la instancia.
         - Vamos a usar v-if para asegurar frescura, y necesitamos que nos avise al cerrar si queremos destruir el v-if.
         - El usuario pidió usar ModalHijo. Asumiremos que funciona como otros modales hijos en el sistema.
         - Si ModalHijo no emite close, el v-if se quedará true -> problema si se quiere reabrir.
         - Solución: Modificaré el script para usar ref y open(), sin v-if o con v-if manejado con cuidado.
         - Verificando ModalHijo.vue:
           - close() { this.modalInstance.hide(); this.search=''; this.selectedItemId=null; }
           - No emite close.
         - Sin embargo, para abrirlo llamamos a open().
         - Así que lo instanciamos siempre (sin v-if o v-if=true).
         - Pero espera, el template original de ModalHijo tiene `ref="modalEl"`.
    -->
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';
import axios from 'axios';
import MultiSelect from 'primevue/multiselect';
import ModalHijo from '@/components/generales/ModalHijo.vue';

const props = defineProps({
    revisionId: { type: [Number, String], required: true },
    compromiso: { type: Object, default: null }
});

const emit = defineEmits(['close', 'saved']);
const store = useRevisionDireccionStore();

const isLoading = ref(false);
const isDataLoaded = ref(false);
const usuarios = ref([]);

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
const showResponsableModal = ref(true); // Siempre montado para poder acceder a la ref
const responsableModal = ref(null);

const availableSystems = ['SGC', 'SGCM', 'SGCO', 'SGAS', 'SGSI', 'Riesgos'];

onMounted(() => {
    // Si es edición, poblar el formulario
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
        // Setear nombre del responsable si existe
        if (props.compromiso.responsable) {
            responsableNombre.value = props.compromiso.responsable.name;
        }
    }

    isDataLoaded.value = true;
});

const openResponsableModal = () => {
    // Llamar al método open del componente hijo
    if (responsableModal.value) {
        responsableModal.value.open();
    }
};

const handleResponsableSelected = (payload) => {
    // payload: { targetId, targetDesc, idValue, descValue }
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

/* CSS Updates for UX */
.no-focus-outline:focus {
    outline: none;
    box-shadow: 0 0 0 0.1rem rgba(220, 53, 69, 0.25) !important;
    border-color: #dc3545 !important;
}

label.required::after {
    content: ' *';
    color: #dc3545;
}

.text-area-custom {
    resize: none;
}

.char-counter-top {
    font-size: 0.75rem;
    color: #ADB5BD;
    font-weight: 600;
}

.transition-all {
    transition: all 0.3s ease;
}

.hover-up:hover {
    transform: translateY(-2px);
}

/* Overlay de carga sutil dentro del cuerpo del modal */
.loading-overlay {
    position: absolute;
    top: 0;
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

/* Scrollbar personalizado */
textarea::-webkit-scrollbar {
    width: 4px;
}

textarea::-webkit-scrollbar-track {
    background: transparent;
}

textarea::-webkit-scrollbar-thumb {
    background: #ADB5BD;
    border-radius: 10px;
    background: #ADB5BD;
    border-radius: 10px;
}

/* MultiSelect styling */
::v-deep(.custom-multiselect) {
    background: #fff;
    border: 1px solid #ced4da;
    border-radius: 0.5rem;
}
::v-deep(.custom-multiselect .p-multiselect-label) {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
::v-deep(.custom-multiselect .p-multiselect-token) {
    background-color: #343a40 !important; /* Negro/Gris oscuro */
    color: #fff !important;
    border-radius: 4px;
    padding: 2px 8px;
    font-size: 0.85rem;
    margin-right: 2px;
}
::v-deep(.custom-multiselect .p-multiselect-token-icon) {
    color: #fff !important;
    margin-left: 0.5rem;
}
::v-deep(.custom-multiselect.p-focus) {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
::v-deep(.p-multiselect-item.p-highlight) {
    background-color: #ffece8 !important; /* Light red */
    color: #dc3545 !important;
}
</style>

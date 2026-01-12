<template>
    <div class="modal fade" id="parteInteresadaModal" tabindex="-1" aria-hidden="true" ref="modalElement" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-users mr-2"></i> {{ formTitle }}
                    </h5>
                    <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body bg-light" style="max-height: 75vh; overflow-y: auto;">
                    <div class="p-3 bg-white border rounded shadow-sm">
                        <div class="row">
                            <div class="col-md-7 border-right">
                                <div class="row">
                                     <div class="col-md-7">
                                        <div class="form-group small">
                                            <label class="font-weight-bold text-danger">Nombre <span class="text-danger">*</span></label>
                                            <input type="text" v-model="form.pi_nombre" class="form-control" placeholder="Ej. Gerencia General" required />
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group small">
                                            <label class="font-weight-bold text-danger">Tipo</label>
                                            <select v-model="form.pi_tipo" class="form-control">
                                                <option v-for="opt in tiposOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group small mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="font-weight-bold text-danger mb-0">Descripción / Contexto</label>
                                        <small class="text-muted">{{ form.pi_descripcion?.length || 0 }}/500</small>
                                    </div>
                                    <textarea v-model="form.pi_descripcion" class="form-control" rows="4" maxlength="500" placeholder="Rol y relevancia..."></textarea>
                                </div>
                            </div>

                            <div class="col-md-5 pl-4">
                                <h6 class="text-dark font-weight-bold mb-3 border-bottom pb-2">Matriz Poder / Interés</h6>
                                
                                <div class="form-row">
                                    <div class="col-md-6 form-group small">
                                        <label class="text-muted font-weight-bold">Influencia</label>
                                        <select v-model="form.pi_nivel_influencia" class="form-control form-control-sm">
                                            <option v-for="opt in nivelOptions" :key="opt" :value="opt">{{ opt }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group small">
                                        <label class="text-muted font-weight-bold">Interés</label>
                                        <select v-model="form.pi_nivel_interes" class="form-control form-control-sm">
                                            <option v-for="opt in nivelOptions" :key="opt" :value="opt">{{ opt }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div v-if="computedCuadrante" class="text-center mt-2 p-3 border rounded bg-light transition-all">
                                    <div class="badge badge-pill mb-2 px-3 py-2 shadow-sm" :class="cuadranteBadgeClass" style="font-size: 1.2rem;">{{ computedCuadrante.cuadrante }}</div>
                                    <div class="font-weight-bold text-uppercase" :class="cuadranteTextColor">{{ computedCuadrante.valoracion }}</div>
                                    <small class="text-muted d-block mt-1">{{ computedCuadrante.mensaje }}</small>
                                </div>
                                <div v-else class="text-center mt-4 text-muted small font-italic">
                                    Seleccione niveles...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light justify-content-between">
                    <div class="text-muted small">
                        <span class="text-danger font-weight-bold">*</span> Campos obligatorios
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary shadow-sm mr-2" @click="closeModal">Cancelar</button>
                        <button type="button" class="btn btn-danger shadow-sm px-4" @click="save()">
                            <i class="fas fa-save mr-1"></i> {{ form.id ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useParteStore } from '@/stores/parteInteresadaStore';
import { Modal } from 'bootstrap'; 
import axios from 'axios';
import Swal from 'sweetalert2';

const store = useParteStore();

// UI Refs
const modalElement = ref(null);
let modalInstance = null;

// Form Data
const form = ref({});

// Options
const tiposOptions = [
    { label: 'Interna', value: 'interna' },
    { label: 'Externa', value: 'externa' },
    { label: 'Cliente', value: 'cliente' },
    { label: 'Proveedor', value: 'proveedor' },
    { label: 'Regulador', value: 'regulador' }
];
const nivelOptions = ['bajo', 'medio', 'alto'];

// Computed
const formTitle = computed(() => form.value.id ? 'Editar Parte Interesada' : 'Nueva Parte Interesada');

// Logic
const closeModal = () => {
    store.showFormModal = false;
};

// Quadrants Logic (Preserved)
const computedCuadrante = computed(() => {
    if (!form.value.pi_nivel_influencia || !form.value.pi_nivel_interes) return null;
    const inf = form.value.pi_nivel_influencia.toLowerCase();
    const int = form.value.pi_nivel_interes.toLowerCase();
    let c = '';
    
    // Matrix Logic
    // Infl (Y) | Int (X)
    // High=3, Med=2, Low=1
    
    if (inf === 'alto') {
        if (int === 'alto' || int === 'medio') c = 'I'; // C1
        else c = 'II'; // C2
    } else if (inf === 'medio') {
        if (int === 'alto') c = 'I'; // C1
        else if (int === 'medio') c = 'III'; // C3
        else c = 'II'; // C2
    } else { // Low
        if (int === 'alto') c = 'III'; // C3
        else c = 'IV'; // C4
    }
    
    let label = '';
    let msg = '';
    
    if (c === 'I') { label = 'Jugador Clave'; msg = 'Gestión proactiva: Participación total / Consultoría / Involucramiento'; }
    else if (c === 'II') { label = 'Satisfacer'; msg = 'Mantener satisfecho: Evitar uso de poder en contra / Consultas puntuales'; }
    else if (c === 'III') { label = 'Informar'; msg = 'Mantener informado: Notificaciones / Comunicación regular'; }
    else if (c === 'IV') { label = 'Monitorear'; msg = 'Mínimo esfuerzo: Vigilancia pasiva / Solo lectura'; }
    
    return { 
        cuadrante: c, 
        valoracion: label,
        mensaje: msg
    };
});

const cuadranteTextColor = computed(() => {
    const c = computedCuadrante.value?.cuadrante;
    if (c === 'I') return 'text-danger';
    if (c === 'II') return 'text-warning';
    return 'text-secondary';
});

const cuadranteBadgeClass = computed(() => {
    const c = computedCuadrante.value?.cuadrante;
    if (c === 'I') return 'badge-danger text-white';
    if (c === 'II') return 'badge-warning text-dark';
    if (c === 'IV') return 'badge-info text-white';
    return 'badge-secondary text-white';
});

const save = async () => {
    try {
        const saved = await store.saveParte(form.value);
        if(!form.value.id) {
             form.value.id = saved.parte.id;
        }
    } catch(e) {}
};

// Watchers
watch(() => store.currentParte, (val) => {
    if (val) {
        form.value = JSON.parse(JSON.stringify(val));
    } else {
        form.value = { pi_tipo: 'interna' }; // Default
    }
}, { immediate: true });

onMounted(async () => {
    if (modalElement.value) {
        modalInstance = new Modal(modalElement.value, { backdrop: 'static', keyboard: false });
        modalElement.value.addEventListener('hidden.bs.modal', (event) => {
             // Only close if it's the main modal (not child modals)
             if (event.target === modalElement.value) store.showFormModal = false;
        });
    }

    store.$subscribe((mutation, state) => {
        if (state.showFormModal) modalInstance?.show();
        else modalInstance?.hide();
    });
});
</script>

<style scoped>
.modal-header {
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
}
.nav-tabs .nav-link {
    color: #495057;
}
.nav-tabs .nav-link.active {
    color: #dc3545;
    font-weight: bold;
}
.cursor-pointer { cursor: pointer; }

/* Transitions */
.transition-all {
    transition: all 0.3s ease;
}

.p-dialog-kallpaq .p-dialog-header {
    padding: 0.5rem 1rem;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
</style>

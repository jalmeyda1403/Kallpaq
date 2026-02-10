<template>
    <div ref="modalRef" id="normasISOFormModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title font-weight-bold d-flex align-items-center">
                        <i class="fas fa-balance-scale mr-2"></i>
                        {{ isEdit ? 'Editar Norma' : 'Nueva Norma Auditable' }}
                    </h5>
                    <button type="button" class="close text-white opacity-1" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light p-4">
                    <!-- Sección Info General -->
                    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 border-0">
                            <h6 class="m-0 font-weight-bold text-dark">Información General</h6>
                        </div>
                        <div class="card-body bg-white pt-0">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-uppercase text-muted">Nombre de la
                                            Norma
                                            <span class="text-danger">*</span></label>
                                        <input v-model="form.na_nombre" type="text"
                                            class="form-control border-light-gray shadow-none"
                                            placeholder="Ej. ISO 9001:2015">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-uppercase text-muted">Sistema
                                        </label>
                                        <input v-model="form.na_sistema" type="text"
                                            class="form-control border-light-gray shadow-none"
                                            placeholder="Ej. ISO 9001:2015">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label
                                            class="small font-weight-bold text-uppercase text-muted text-center w-100">
                                            Requisitos</label>
                                        <div
                                            class="form-control border-light-gray bg-light text-center font-weight-bold text-primary">
                                            {{ form.requisitos.length }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-uppercase text-muted">Descripción</label>
                                <textarea v-model="form.na_descripcion"
                                    class="form-control border-light-gray shadow-none" rows="2"
                                    placeholder="Breve descripción del alcance..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Requisitos -->
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div
                            class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
                            <h6 class="font-weight-bold m-0 text-dark">Requisitos de la Norma</h6>
                            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                <button class="btn btn-sm btn-white border-right" @click="addRequisito"
                                    title="Agregar Fila">
                                    <i class="fas fa-plus text-info"></i> <span class="d-none d-md-inline ml-1">Agregar
                                        Fila</span>
                                </button>
                                <button class="btn btn-sm btn-white border-right" @click="openImportModal"
                                    title="Importar desde Excel">
                                    <i class="fas fa-file-import text-success"></i> <span
                                        class="d-none d-md-inline ml-1">Importar</span>
                                </button>
                                <button class="btn btn-sm btn-white" @click="generateAI" :disabled="generating"
                                    title="Generar con IA">
                                    <i class="fas fa-magic"
                                        :class="{ 'fa-spin text-primary': generating, 'text-primary': !generating }"></i>
                                    <span class="d-none d-md-inline ml-1">{{ generating ? 'Generando...' : 'I.A.'
                                        }}</span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0 position-relative">
                            <div v-if="isLoading"
                                class="loading-overlay d-flex justify-content-center align-items-center">
                                <div class="spinner-border text-danger" role="status"
                                    style="width: 2rem; height: 2rem;">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm m-0 border-0">
                                    <thead class="bg-light sticky-top shadow-sm" style="z-index: 2;">
                                        <tr class="text-uppercase small font-weight-bold text-muted">
                                            <th class="border-0 py-2 pl-3" style="width: 100px;">Numeral</th>
                                            <th class="border-0 py-2" style="width: 35%;">Denominación</th>
                                            <th class="border-0 py-2">Detalle</th>
                                            <th class="border-0 py-2 pr-3 text-center" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr v-for="(req, index) in form.requisitos" :key="index"
                                            class="align-middle transition-all border-bottom border-light">
                                            <td class="py-1 pl-3">
                                                <input type="text" v-model="req.nr_numeral"
                                                    class="form-control form-control-sm border-0 font-weight-bold text-info bg-transparent focus-none px-1"
                                                    placeholder="4.1" style="height: 24px;">
                                            </td>
                                            <td class="py-1">
                                                <input type="text" v-model="req.nr_denominacion"
                                                    class="form-control form-control-sm border-0 bg-transparent focus-none font-weight-500 px-1"
                                                    placeholder="Título..." style="height: 24px;">
                                            </td>
                                            <td class="py-1">
                                                <textarea v-model="req.nr_detalle"
                                                    class="form-control form-control-sm border-0 bg-transparent focus-none px-1 py-0"
                                                    rows="1" placeholder="Descripción..." @input="autoResize"
                                                    style="min-height: 24px; resize: none; overflow: hidden;"></textarea>
                                            </td>
                                            <td class="py-1 pr-3 text-center">
                                                <button class="btn btn-icon-danger-soft rounded-circle btn-sm-custom"
                                                    @click="removeRequisito(index)" title="Quitar">
                                                    <i class="fas fa-minus-circle"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="form.requisitos.length === 0">
                                            <td colspan="4" class="text-center py-5">
                                                <div class="empty-state py-4 text-muted">
                                                    <i class="fas fa-layer-group fa-3x text-light mb-3"></i>
                                                    <p class="mb-0">No hay requisitos configurados.</p>
                                                    <small>Usa los botones superiores para agregar datos.</small>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 p-4">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill"
                        @click="closeModal">Cancelar</button>
                    <button type="button" class="btn btn-dark px-5 rounded-pill shadow-sm" @click="save"
                        :disabled="saving">
                        <i class="fas fa-save mr-2"></i> {{ saving ? 'Guardando...' : 'Guardar Norma' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Componente de Importación -->
    <NormasImportModal ref="importModal" :norma-id="form.id" @imported="handleImportedData" />
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import NormasImportModal from './partials/NormasImportModal.vue';

const emit = defineEmits(['saved']);

const modalRef = ref(null);
const importModal = ref(null);
let modalInstance = null;

const saving = ref(false);
const generating = ref(false);
const isLoading = ref(false);
const isEdit = ref(false);

const form = ref({
    id: null,
    na_nombre: '',
    na_descripcion: '',
    na_sistema: '',
    requisitos: []
});

const open = (norma = null) => {
    isEdit.value = !!norma;
    resetForm(); // Limpiar siempre primero para evitar ver datos anteriores

    if (norma) {
        // Precargar datos básicos inmediatamente para evitar parpadeo en info general
        form.value.id = norma.id;
        form.value.na_nombre = norma.na_nombre;
        form.value.na_descripcion = norma.na_descripcion;
        form.value.na_sistema = norma.na_sistema;

        // Cargar requisitos desde el servidor
        loadNorma(norma.id);
    }

    if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalRef.value, { backdrop: 'static', keyboard: false });
    }
    modalInstance.show();
};

const resetForm = () => {
    form.value = {
        id: null,
        na_nombre: '',
        na_descripcion: '',
        na_sistema: '',
        requisitos: []
    };
};

const loadNorma = async (id) => {
    isLoading.value = true;
    try {
        const response = await axios.get(`/api/auditoria/normas/${id}`);
        form.value = response.data;
        // Resize textareas after data is rendered
        triggerResize();
    } catch (e) {
        console.error("Error loading update", e);
        Swal.fire('Error', 'No se pudo cargar la norma.', 'error');
    } finally {
        isLoading.value = false;
    }
};

const closeModal = () => {
    modalInstance?.hide();
};

const addRequisito = () => {
    form.value.requisitos.push({ nr_numeral: '', nr_denominacion: '', nr_detalle: '' });

    // Scroll y foco a la nueva fila
    nextTick(() => {
        const rows = document.querySelectorAll('#normasISOFormModal tbody tr');
        const lastRow = rows[rows.length - 1];
        if (lastRow) {
            lastRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const firstInput = lastRow.querySelector('input');
            if (firstInput) {
                firstInput.focus();
            }
        }
    });
};

const removeRequisito = (index) => {
    form.value.requisitos.splice(index, 1);
};

// Lógica de Importación
const openImportModal = () => {
    importModal.value.open();
};

const handleImportedData = (data) => {
    const processData = (newData) => {
        if (form.value.requisitos.length > 0) {
            Swal.fire({
                title: 'Datos Importados',
                text: "¿Deseas reemplazar los requisitos actuales o agergarlos al final?",
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Reemplazar',
                denyButtonText: 'Agregar al final',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Limpiar primero para forzar reactividad completa y evitar problemas de renderizado
                    form.value.requisitos = [];
                    nextTick(() => {
                        form.value.requisitos = newData;
                        triggerResize();
                    });
                } else if (result.isDenied) {
                    form.value.requisitos = [...form.value.requisitos, ...newData];
                    triggerResize();
                }
            });
        } else {
            form.value.requisitos = newData;
            triggerResize();
        }
    };
    processData(data);
};

const triggerResize = () => {
    nextTick(() => {
        if (!modalRef.value) return;

        // Usar requestAnimationFrame para evitar bloquear el hilo principal y suavizar el renderizado
        requestAnimationFrame(() => {
            const textareas = modalRef.value.querySelectorAll('textarea');
            for (let i = 0; i < textareas.length; i++) {
                const el = textareas[i];
                el.style.height = 'auto';
                el.style.height = (el.scrollHeight) + 'px';
            }
        });
    });
};

const generateAI = async () => {
    if (!form.value.na_nombre) {
        Swal.fire('Atención', 'Ingresa el nombre de la norma primero.', 'warning');
        return;
    }

    generating.value = true;
    try {
        const response = await axios.post('/api/auditoria/normas/generate', { na_nombre: form.value.na_nombre });
        if (Array.isArray(response.data)) {
            // Map AI response to new field names
            const mappedData = response.data.map(r => ({
                nr_numeral: r.numeral || r.nr_numeral,
                nr_denominacion: r.denominacion || r.nr_denominacion,
                nr_detalle: r.detalle || r.nr_detalle
            }));

            handleImportedData(mappedData);
        } else {
            Swal.fire('Error', 'La respuesta de la IA no tuvo el formato esperado.', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'No se pudieron generar los requisitos.', 'error');
    } finally {
        generating.value = false;
    }
};

const save = async () => {
    if (!form.value.na_nombre) return;
    saving.value = true;
    try {
        if (isEdit.value) {
            await axios.put(`/api/auditoria/normas/${form.value.id}`, form.value);
        } else {
            await axios.post('/api/auditoria/normas', form.value);
        }
        Swal.fire('Guardado', 'La norma se ha guardado correctamente.', 'success');
        emit('saved');
        closeModal();
    } catch (e) {
        Swal.fire('Error', 'No se pudo guardar la norma.', 'error');
    } finally {
        saving.value = false;
    }
};

const autoResize = (event) => {
    const el = event.target;
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
};

defineExpose({ open });

onMounted(() => {
    // === SOLUCIÓN DEFINITIVA: MutationObserver ===
    // En lugar de depender de eventos del hijo, vigilamos el body directamente.
    // Si Bootstrap quita la clase 'modal-open' por error, la volvemos a poner de inmediato.

    const observer = new MutationObserver((mutations) => {
        if (!modalRef.value) return;

        // Verificar si nuestro modal principal está visible (tiene clase 'show')
        // Nota: Bootstrap agrega 'show' y 'display: block'
        const isMyModalOpen = modalRef.value.classList.contains('show') ||
            modalRef.value.style.display === 'block';

        if (isMyModalOpen) {
            // Si nuestro modal está abierto, EL BODY DEBE TENER 'modal-open'
            if (!document.body.classList.contains('modal-open')) {
                // 1. Restaurar clase en body
                document.body.classList.add('modal-open');

                // 2. Restaurar padding para evitar saltos (compensar scrollbar)
                // Solo si no tiene ya un padding seteado
                if (!document.body.style.paddingRight) {
                    const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
                    if (scrollBarWidth > 0) document.body.style.paddingRight = `${scrollBarWidth}px`;
                }

                // 3. Forzar scroll en nuestro modal
                modalRef.value.style.overflowX = 'hidden';
                modalRef.value.style.overflowY = 'auto';
            }
        }
    });

    // Iniciar observación del body (solo cambios en atributos, específicamente 'class')
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });

    // Limpieza al desmontar
    onUnmounted(() => {
        observer.disconnect();

        // Limpieza final de Bootstrap si cerramos todo
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop && document.querySelectorAll('.modal.show').length === 0) {
            backdrop.remove();
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
        }
    });
});
</script>

<style scoped>
.border-light-gray {
    border: 1px solid #e9ecef;
}

.btn-white {
    background: #fff;
    color: #6c757d;
    font-weight: 500;
    font-size: 0.8rem;
}

.btn-white:hover {
    background: #f8f9fa;
    color: #333;
}

.focus-none:focus {
    box-shadow: none !important;
    outline: none !important;
}

.transition-all {
    transition: all 0.2s ease;
}

.font-weight-500 {
    font-weight: 500;
}

/* .scroll-none removed to allow expansion */

.btn-icon-danger-soft {
    width: 24px;
    height: 24px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff5f5;
    color: #ff5e5e;
    border: none;
    transition: all 0.2s;
}

.btn-sm-custom {
    font-size: 0.75rem;
}

.btn-icon-danger-soft:hover {
    background: #ff5e5e;
    color: #fff;
    transform: scale(1.1);
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    z-index: 10;
    border-radius: 0.25rem;
}

.table-hover tbody tr:hover {
    background-color: #fcfcfc;
}

.opacity-1 {
    opacity: 1 !important;
}
</style>

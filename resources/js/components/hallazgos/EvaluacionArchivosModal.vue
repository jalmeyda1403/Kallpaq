<template>
    <div class="modal fade" id="evaluacionArchivosModal" tabindex="-1" role="dialog"
        aria-labelledby="evaluacionArchivosModalLabel" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="evaluacionArchivosModalLabel">Documentos de Evaluación</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                        @click="cerrarModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Hallazgo:</strong> {{ hallazgo?.hallazgo_cod }} - {{ hallazgo?.hallazgo_resumen }}
                    </div>

                    <!-- Lista de Archivos Existentes -->
                    <div v-if="evidencias.length > 0" class="mb-4">
                        <h6>Documentos Subidos:</h6>
                        <ul class="list-group">
                            <li v-for="(file, index) in evidencias" :key="index"
                                class="list-group-item d-flex justify-content-between align-items-center">
                                <a :href="file.path" target="_blank">
                                    <i class="fas fa-file-alt mr-2"></i> {{ file.name }}
                                </a>
                                <!-- Aquí se podría agregar botón para eliminar si fuera necesario -->
                            </li>
                        </ul>
                    </div>
                    <div v-else class="mb-4 text-muted">
                        No hay documentos subidos aún.
                    </div>

                    <!-- Formulario de Subida -->
                    <form @submit.prevent="subirArchivos">
                        <div class="form-group">
                            <label for="files">Subir Nuevos Documentos (PDF, Imágenes, Office):</label>
                            <input type="file" id="files" class="form-control-file" multiple @change="handleFileUpload"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                @click="cerrarModal">Cerrar</button>
                            <button type="submit" class="btn btn-primary"
                                :disabled="loading || selectedFiles.length === 0">
                                <span v-if="loading" class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                Subir Archivos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { route } from 'ziggy-js';
import { Modal } from 'bootstrap';

const props = defineProps({
    visible: Boolean,
    hallazgo: Object
});

const emit = defineEmits(['cerrar', 'archivos-subidos']);

const modalRef = ref(null);
let modalInstance = null;
const loading = ref(false);
const evidencias = ref([]);
const selectedFiles = ref([]);

const fetchEvaluacion = async () => {
    if (!props.hallazgo) return;
    try {
        const response = await axios.get(route('hallazgo.evaluacion.get', props.hallazgo.id));
        if (response.data && response.data.evidencias) {
            evidencias.value = response.data.evidencias;
        } else {
            evidencias.value = [];
        }
    } catch (error) {
        console.error('Error al cargar evaluación:', error);
        evidencias.value = [];
    }
};

const handleFileUpload = (event) => {
    selectedFiles.value = Array.from(event.target.files);
};

const subirArchivos = async () => {
    if (selectedFiles.value.length === 0) return;

    loading.value = true;
    const formData = new FormData();
    selectedFiles.value.forEach(file => {
        formData.append('files[]', file);
    });

    try {
        const response = await axios.post(route('hallazgo.evaluacion.upload', props.hallazgo.id), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        evidencias.value = response.data.evidencias; // Actualizar lista con la respuesta
        selectedFiles.value = []; // Limpiar selección
        // Limpiar input file
        document.getElementById('files').value = '';

        Swal.fire({
            icon: 'success',
            title: 'Subido',
            text: 'Documentos subidos correctamente.',
            timer: 2000,
            showConfirmButton: false
        });

        emit('archivos-subidos');
    } catch (error) {
        console.error('Error al subir archivos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron subir los archivos.'
        });
    } finally {
        loading.value = false;
    }
};

const cerrarModal = () => {
    emit('cerrar');
    if (modalInstance) modalInstance.hide();
};

const abrirModal = () => {
    if (modalInstance) modalInstance.show();
};

onMounted(() => {
    modalInstance = new Modal(modalRef.value, {
        backdrop: 'static',
        keyboard: false
    });
});

watch(() => props.visible, (newVal) => {
    if (newVal) {
        fetchEvaluacion();
        selectedFiles.value = [];
        abrirModal();
    } else {
        cerrarModal();
    }
});
</script>

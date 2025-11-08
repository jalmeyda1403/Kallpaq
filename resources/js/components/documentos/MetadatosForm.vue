<template>
    <div>
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ documentoStore.documentoForm.cod_documento ? 'Información del Documento' :
                    'Nuevo Documento' }}</span>
                <span class="mx-2 text-secondary">
                    <i class="fas fa-chevron-right fa-xs"></i>
                </span>
                <span class="text-dark">{{ documentoStore.documentoForm.cod_documento || '' }}</span>
            </h6>
        </div>
        <div class="text-left mb-4">
            <h6 class="mb-1" style="font-weight: bold;">
                METADATOS
            </h6>
            <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                Este módulo permite asociar los metadatos al documento, lo que permitirá mejorar la organización y 
                búsqueda de documentos.
            </p>
        </div>
        <div class="form-overlay-container">
            <div v-if="documentoStore.loadingMetadatos || localLoading" class="loading-overlay">
                <div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Cargando Metadatos...</span>
                </div>
            </div>
            <form @submit.prevent="submitForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group small">
                            <label for="area_compliance_id">Área Temática</label>
                            <select id="area_compliance_id" v-model="documentoStore.metadatosForm.area_compliance_id"
                                class="form-control" required>
                                <option value="">Seleccione Área...</option>
                                <option v-for="area in documentoStore.areaCompliance" :key="area.id" :value="area.id">
                                    {{ area.area_compliance_nombre }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group small">
                            <label for="palabras_clave_documento">Palabras clave (separadas por coma)</label>
                            <input type="text" id="palabras_clave_documento"
                                v-model="documentoStore.metadatosForm.palabras_clave_documento" class="form-control"
                                placeholder="ej: planeamiento, pei, estrategia">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group small">
                            <label for="subarea_compliance_id">Subcategoría Temática</label>
                            <select id="subarea_compliance_id"
                                v-model="documentoStore.metadatosForm.subarea_compliance_id" class="form-control"
                                :disabled="!documentoStore.metadatosForm.area_compliance_id" required>
                                <option value="">Seleccione Subcategoría...</option>
                                <option v-for="subarea in filteredSubareaCompliance" :key="subarea.id"
                                    :value="subarea.id">
                                    {{ subarea.subarea_compliance_nombre }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group small">
                            <label for="tags">Etiquetas (Tags)</label>
                            <select ref="tagSelectElementRef" id="tags" class="form-control select2" multiple="multiple"
                                style="width: 100%;">
                                <option v-for="tag in documentoStore.tagsDisponibles" :key="tag.id" :value="tag.id">
                                    {{ tag.nombre }}
                                </option>
                            </select>

                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-center w-100">
                    <button type="submit" class="btn btn-danger btn-sm" :disabled="documentoStore.loading">
                        <span v-if="documentoStore.loading" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                        Guardar Metadatos
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount} from 'vue';
import { useDocumentoStore } from '@/stores/documentoStore';
import { route } from 'ziggy-js';


const documentoStore = useDocumentoStore();
const tagSelectElementRef = ref(null);
const localLoading = ref(false);


const filteredSubareaCompliance = computed(() => {
    if (!documentoStore.metadatosForm.area_compliance_id) {
        return [];
    }
    return documentoStore.subareaCompliance.filter(subarea =>
        subarea.area_compliance_id === documentoStore.metadatosForm.area_compliance_id);
});


// Llama a la acción correcta del store, pasando 'metadatos' como tipo de formulario
const submitForm = async () => {
    await documentoStore.saveDocumento('metadatos');
};

const initializeSelect2 = () => {

    const el = $(tagSelectElementRef.value);

    if (el.data('select2')) {
        el.select2('destroy');
    }
    documentoStore.tagsDisponibles.forEach(tag => {
        if (!el.find(`option[value='${tag.id}']`).length) {
            const option = new Option(tag.nombre, tag.id, true, true);
            el.append(option);
        }
    });
    el.select2({
        width: '100%',
        placeholder: 'Seleccione o escriba un tag...',
        allowClear: true,
        dropdownParent: el.closest('.modal-content'),
        ajax: {
            url: route('tag.buscar'),
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(tag => ({ id: tag.id, text: tag.nombre }))
            }),
            cache: true,
        },
        tags: true,
    }).on('change', function () {
        documentoStore.metadatosForm.tags = $(this).val();
    });
    el.val(documentoStore.metadatosForm.tags).trigger('change.select2');
   
};

onMounted(() => {
    documentoStore.loadAreasCompliance();
    initializeSelect2();
});


onBeforeUnmount(() => {
    const el = $(tagSelectElementRef.value);
    if (el.data('select2')) {
        el.select2('destroy');
    }
});
</script>

<style scoped>
/* Estilos para el overlay del spinner */
.form-overlay-container {
    position: relative;
    min-height: 200px;
    /* Asegura que el contenedor tenga una altura mínima */
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.35);
    /* Fondo semi-transparente */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
    /* Asegura que esté por encima del formulario */
}

/* Estilos de los campos de formulario */
.form-group small {
    font-size: 0.75rem;
}

.form-label.text-danger {
    font-weight: bold;
}

/* Estilo general de opciones */
.select2-container--default .select2-results__option {
    font-size: 12px;
}

/* Hover en opciones */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #6c757d !important;
    /* grey */
    color: white !important;
}

/* Estilo de etiquetas seleccionadas */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #dc3545 !important;
    /* danger */
    border-color: #dc3545 !important;
    color: white !important;
    font-size: 12px;
}

/* Estilo del botón de cerrar en etiquetas */
.select2-container--default .select2-selection__choice__remove {
    color: white !important;
    font-size: 12px;
}

.select2-container--default .select2-selection__choice__remove:hover {
    color: #ffcccc !important;
}

/* Estilo del campo de búsqueda interno (placeholder y texto) */
.select2-container--default .select2-selection--multiple .select2-search__field {
    font-size: 13px !important;
}

select:invalid {
    color: #94939a;
}

.select2-hidden-init {
    visibility: hidden;
}

.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    /* Color gris claro de Bootstrap */
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    /* Esta línea crea la franja naranja */
    display: flex;
    /* Para centrar verticalmente el contenido si es necesario */
    align-items: center;
}
</style>

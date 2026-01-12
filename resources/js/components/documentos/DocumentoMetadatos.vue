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
                            <AutoComplete v-model="selectedTags" :suggestions="filteredTags"
                                @complete="searchTags" @keydown.enter.prevent="onTagInputEnter" 
                                optionLabel="nombre" multiple class="w-100"
                                placeholder="Seleccione o escriba un tag..." />
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
import { ref, computed, onMounted, watch } from 'vue';
import { useDocumentoStore } from '@/stores/documentoStore';
import { route } from 'ziggy-js';
import axios from 'axios';
import AutoComplete from 'primevue/autocomplete';

const documentoStore = useDocumentoStore();
const localLoading = ref(false);
const selectedTags = ref([]);
const filteredTags = ref([]);

const filteredSubareaCompliance = computed(() => {
    if (!documentoStore.metadatosForm.area_compliance_id) {
        return [];
    }
    return documentoStore.subareaCompliance.filter(subarea =>
        subarea.area_compliance_id === documentoStore.metadatosForm.area_compliance_id);
});


// Llama a la acción correcta del store, pasando 'metadatos' como tipo de formulario
const submitForm = async () => {
    // Map selected objects back to IDs (or names for new tags) for the store
    documentoStore.metadatosForm.tags = selectedTags.value.map(tag => {
        return typeof tag === 'object' ? tag.id : tag;
    });
    await documentoStore.saveDocumento('metadatos');
};

const onTagInputEnter = (event) => {
    // Prevent form submission or other default actions
    event.preventDefault();
    
    // The input value is usually in event.target.value for native input, 
    // but for AutoComplete it might be managed differently.
    // PrimeVue AutoComplete doesn't expose the input value in the event directly in all versions.
    // However, event.target.value works on the underlying input.
    const inputValue = event.target.value.trim();
    
    if (inputValue) {
        // Check if already selected
        const alreadySelected = selectedTags.value.some(t => 
            (t.nombre && t.nombre.toLowerCase() === inputValue.toLowerCase()) || 
            (typeof t === 'string' && t.toLowerCase() === inputValue.toLowerCase())
        );

        if (!alreadySelected) {
            // Check if it exists in filteredTags (suggestions)
            const existingTag = filteredTags.value.find(t => t.nombre.toLowerCase() === inputValue.toLowerCase());
            
            if (existingTag) {
                selectedTags.value.push(existingTag);
            } else {
                // Add as new tag (string or object with same structure)
                // Using object structure to match optionLabel="nombre"
                selectedTags.value.push({ nombre: inputValue, id: inputValue });
            }
        }
        
        // Clear input (This might effectively require clearing the internal Model if bound)
        // PrimeVue AutoComplete manages its own input value, resetting it can be tricky without internal ref access.
        // A common trick is to clear the search query or rely on v-model update.
        // But since we are intercepting the event, we might need to manually clear the input element.
        event.target.value = '';
        filteredTags.value = []; 
    }
};

const searchTags = async (event) => {
    try {
        const response = await axios.get(route('tag.buscar'), {
            params: { q: event.query }
        });
        filteredTags.value = response.data;
    } catch (error) {
        console.error('Error searching tags:', error);
        filteredTags.value = [];
    }
};

// Sync store tags (IDs) to local selectedTags (Objects)
const syncTagsFromStore = () => {
    if (documentoStore.metadatosForm.tags && documentoStore.metadatosForm.tags.length > 0) {
         // Reconstruct objects from available tags
         selectedTags.value = documentoStore.metadatosForm.tags.map(tagId => {
            // Check if it's an ID or a String (new tag not saved yet)
            const found = documentoStore.tagsDisponibles.find(t => t.id == tagId);
            if (found) return found;
            // If not found in disponibles, and is string, treat as new tag text
            if (typeof tagId === 'string' && isNaN(tagId)) return { nombre: tagId, id: tagId }; // temporary object for display
            return null;
         }).filter(t => t !== null);
    } else {
        selectedTags.value = [];
    }
};

onMounted(() => {
    documentoStore.loadAreasCompliance();
    syncTagsFromStore();
});

// Watch for changes in selectedTags to populate tagsDisponibles (Preserve state across tab switches)
watch(selectedTags, (newTags) => {
    newTags.forEach(tag => {
        if (typeof tag === 'object' && tag.id) {
             const exists = documentoStore.tagsDisponibles.find(t => t.id === tag.id);
             if (!exists) {
                 documentoStore.tagsDisponibles.push(tag);
             }
        }
    });
}, { deep: true });

// Watch for store changes (External load or Reset)
watch(() => documentoStore.metadatosForm.tags, (newVal) => {
    // Only sync if the length implies a major change (like reset to 0) OR if we are just loading
    // We avoid syncing if we just performed the save logic which converts objects to IDs, 
    // because that might cause a brief flash if IDs are numbers and we need objects.
    // However, fetchDocumento populates tagsDisponibles, so syncTagsFromStore SHOULD work fine.
    
    // Check if we need to sync:
    const currentIds = selectedTags.value.map(t => t.id || t);
    const arraysEqual = currentIds.length === newVal.length && currentIds.every((value, index) => value == newVal[index]);
    
    if (!arraysEqual) {
        syncTagsFromStore();
    }
}, { deep: true });

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

/* PrimeVue AutoComplete Customization */
:deep(.p-autocomplete) {
    display: block;
}

:deep(.p-autocomplete-multiple-container) {
    width: 100%;
    border: 1px solid #ced4da; /* Bootstrap border */
    border-radius: 0.25rem;
    padding: 0.2rem 0.5rem;
    font-size: 0.875rem; 
}

:deep(.p-autocomplete-multiple-container:focus-within) {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

:deep(.p-autocomplete-token) {
    background-color: #dc3545; /* Danger color */
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

:deep(.p-autocomplete-token-icon) {
    color: white;
    font-size: 0.75rem;
}

:deep(.p-autocomplete-input-token input) {
    font-size: 0.875rem;
    font-family: inherit;
}

select:invalid {
    color: #94939a;
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

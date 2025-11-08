<template>
    <div>
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ documentoStore.documentoForm.cod_documento ? 'Jeraraquia del Documento' :
                    'Nuevo Documento' }}</span>
                <span class="mx-2 text-secondary">
                    <i class="fas fa-chevron-right fa-xs"></i>
                </span>
                <span class="text-dark">{{ documentoStore.documentoForm.cod_documento || '' }}</span>
            </h6>
        </div>
        <div class="text-left mb-4">
            <h6 class="mb-1" style="font-weight: bold;">
                JERARQUIA
            </h6>
            <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                Este módulo permite asociar los parientes del documento, lo que permitirá mejorar la organización y
                búsqueda de documentos.
            </p>
        </div>
        <div class="form-overlay-container mt-4">
            <div v-if="documentoStore.loadingJerarquia" class="loading-overlay">
                <div class="spinner-border text-danger" role="status"><span class="sr-only"></span></div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title font-weight-bold mb-3">Dependencia Jerárquica (Padre)</h5>


                    <div class="input-group">
                        <input type="text" id="documentoPadre" class="form-control"
                            :value="documentoStore.documentoPadre ? `${documentoStore.documentoPadre.cod_documento} - ${documentoStore.documentoPadre.nombre_documento}` : ''"
                            placeholder="Este es un documento de nivel superior." readonly>

                        <div class="input-group-append">
                            <button class="btn btn-dark" @click="abrirModalPadre"
                                title="Buscar y asignar un documento padre">
                                <i class="fas fa-search"></i> {{ documentoStore.documentoPadre ? 'Cambiar' : 'Asignar'
                                }}
                            </button>
                            <button v-if="documentoStore.documentoPadre" class="btn btn-outline-danger"
                                @click="quitarPadre" title="Quitar dependencia">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">Define si este documento es un sub-elemento (hijo) de otro
                        documento principal.</small>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title font-weight-bold mb-0">Documentos Dependientes (Hijos)</h5>
                    <p class="card-text text-muted small mb-0">Formatos, instructivos u otros documentos que dependen de este.</p>
                </div>

                <div class="card-body">
                    <div class="p-3 bg-light rounded mb-4">
                        <label class="form-label small font-weight-bold">Añadir dependencia (Hijo):</label>
                        <div class="input-group">
                            <input type="text" class="form-control" 
                                   placeholder="Haga clic en buscar para seleccionar un documento..." 
                                   :value="hijoSeleccionado.nombre_documento"
                                   readonly>
                            <div class="input-group-append">
                                <button class="btn btn-dark" @click="abrirModalHijos" title="Buscar documento">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-danger" @click="asociarHijo" :disabled="!hijoSeleccionado.id">
                                    <i class="fas fa-link"></i> Asociar
                                </button>
                            </div>
                        </div>
                    </div>
                    <h6 class="mb-3 font-weight-bold">Dependencias Actuales</h6>
                    <div v-if="!documentoStore.documentosHijos.length" class="text-center py-4">
                        <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                        <h6 class="font-weight-bold">Sin dependencias</h6>
                        <p class="text-muted small">Este documento aún no tiene elementos hijos.</p>
                    </div>

                    <ul v-else class="list-group list-group-flush">
                        <li v-for="hijo in documentoStore.documentosHijos" :key="hijo.id"
                            class="list-group-item d-flex justify-content-between align-items-center px-1">
                            <div>
                                <i class="fas fa-file-alt text-muted mr-2"></i>
                                <span class="small text-muted ">
                                    <strong >{{ hijo.cod_documento }}</strong> - {{ hijo.nombre_documento }}
                                </span>
                            </div>
                            <button class="btn btn-light btn-sm border-0" @click="quitarHijo(hijo.id)" title="Desvincular este documento">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>  
        </div>


        <modal-hijo ref="modalPadre" :fetch-url="documento_route" @update-target="asignarPadre" />
        <modal-hijo ref="modalHijos" :fetch-url="documento_route" @update-target="handleHijoSeleccionado" />
    </div>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import { useDocumentoStore } from '@/stores/documentoStore';
import { route } from 'ziggy-js';
import ModalHijo from '../generales/ModalHijo.vue';

const documentoStore = useDocumentoStore();

// Referencias a los modales
const modalPadre = ref(null);
const modalHijos = ref(null);
const documento_route = route('documentos.buscar');
const hijoSeleccionado = ref({ id: null, nombre_documento: '' });

// --- LÓGICA PARA EL PADRE ---
const abrirModalPadre = () => modalPadre.value.open();
const asignarPadre = ({ idValue }) => {
    if (idValue === documentoStore.documentoForm.id) {
        alert("Un documento no puede ser su propio padre.");
        return;
    }
    documentoStore.asignarPadre(idValue);
};

const quitarPadre = () => {
    if (confirm("¿Está seguro de que desea quitar la dependencia del documento padre?")) {
        documentoStore.quitarPadre();
    }
};

// --- LÓGICA PARA LOS HIJOS ---
const abrirModalHijos = () => modalHijos.value.open();
const handleHijoSeleccionado = ({ idValue, descValue }) => {
    hijoSeleccionado.value = { id: idValue, nombre_documento: descValue };
};
const asociarHijo = async () => {
    const hijoId = hijoSeleccionado.value.id;
    if (!hijoId) return;

    if (hijoId === documentoStore.documentoForm.id) {
        alert("Un documento no puede ser su propio hijo.");
        return;
    }
    // Llamamos a una nueva acción en el store para un solo hijo
    await documentoStore.asignarHijo(hijoId);

    // Limpiamos el input después de asociar
    hijoSeleccionado.value = { id: null, nombre_documento: '' };
};
const quitarHijo = (hijoId) => {
    if (confirm("¿Está seguro de que desea desvincular este documento hijo?")) {
        documentoStore.quitarHijo(hijoId);
    }
};
// Cargar los datos al montar
onMounted(() => {
    documentoStore.fetchJerarquia();
});
</script>

<style scoped>
/* Reutilizamos los estilos anteriores para mantener la consistencia */
.form-overlay-container {
    position: relative;
    min-height: 250px;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}
</style>
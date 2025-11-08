<template>
    <div>
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ documentoStore.documentoForm.cod_documento ? 'Documentos Relacionados'
                    : '' }}></span>
                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                <span class="text-dark">{{ documentoStore.documentoForm.cod_documento || '' }}</span>
            </h6>
        </div>

        <h6 class="mb-1" style="font-weight: bold;">
            DOCUMENTOS RELACIONADOS
        </h6>
        <p class="mb-3 text-muted" style="font-size: 0.875rem;">
            Este módulo permite registrar y visualizar las relaciones entre documentos para asegurar una mejor
            trazabilidad y gestión.
        </p>
        <h6 class="mb-3 font-weight-bold my-4">Busque y vincular un documento</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" placeholder="Buscar documento..."
                        v-model="selectedDocumento.descripcion" readonly />
                    <div class="input-group-append">
                        <button type="button" class="btn btn-dark btn-sm" @click="openDocumentoModal">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="input-group mr-3">
                        <select id="tipoRelacion" class="form-control form-control-sm"
                            v-model="tipoRelacionSeleccionado">
                            <option value="" disabled>Elija una opción...</option>
                            <option value="impacta">Impacta a</option>
                            <option value="modifica">Modifica a</option>
                            <option value="deroga">Deroga a</option>
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger btn-sm w-100" @click="handleAssociate"
                                :disabled="!selectedDocumento.id || !tipoRelacionSeleccionado">
                                <i class="fas fa-link"></i> Asociar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-overlay-container">
            <div v-if="documentoStore.loadingRelacionados" class="loading-overlay">
                <div class="spinner-border text-danger" role="status"><span class="sr-only"></span></div>
            </div>
            <h6 class="mb-3 font-weight-bold my-4">Documentos de referencia</h6>

            <div v-if="!documentoStore.relacionesSalientes.length" class="text-muted small mb-4">
                No se han definido relaciones salientes.
            </div>
            <div v-else class="table-responsive mb-4">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Código</th>
                            <th>Nombre del Documento</th>
                            <th class="text-center">Tipo de Relación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="doc in documentoStore.relacionesSalientes" :key="doc.id">
                            <td>{{ doc.cod_documento }}</td>
                            <td>{{ doc.nombre_documento }}</td>
                            <td class="text-center">
                                <span class="badge" :class="getBadgeClass(doc.pivot.tipo_relacion)">{{
                                    doc.pivot.tipo_relacion }}</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm"
                                    @click="documentoStore.disociarDocumentoRelacionado(doc.id)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h6 class="mb-3 font-weight-bold my-4">Documentos de dependencia</h6>
            <div v-if="!documentoStore.relacionesEntrantes.length" class="text-muted small">
                Ningún documento tiene una relación definida hacia este.
            </div>
            <div v-else class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Código</th>
                            <th>Nombre del Documento</th>
                            <th class="text-center">Tipo de Relación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="doc in documentoStore.relacionesEntrantes" :key="doc.id">
                            <td>{{ doc.cod_documento }}</td>
                            <td>{{ doc.nombre_documento }}</td>
                            <td class="text-center">
                                <span class="badge" :class="getBadgeClass(doc.pivot.tipo_relacion)">Es {{
                                    doc.pivot.tipo_relacion }} por</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <modal-hijo ref="documentoModal" :fetch-url="documento_route" target-id="id" target-desc="nombre_documento"
            @update-target="handleDocumentoSelection" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useDocumentoStore } from '@/stores/documentoStore';
import { route } from 'ziggy-js';
import ModalHijo from '../generales/ModalHijo.vue';

const documentoStore = useDocumentoStore();

// Estado local para el formulario de nueva relación
const documentoModal = ref(null);
const selectedDocumento = ref({ id: null, descripcion: '' });
const tipoRelacionSeleccionado = ref('');

const documento_route = route('documentos.buscar');

// Métodos para el modal de búsqueda
const openDocumentoModal = () => documentoModal.value.open();
const handleDocumentoSelection = ({ idValue, descValue }) => {
    selectedDocumento.value = { id: idValue, descripcion: descValue };
};

// Método para asociar
const handleAssociate = async () => {
    const relacionadoId = selectedDocumento.value.id;
    const tipo = tipoRelacionSeleccionado.value;
    if (!relacionadoId || !tipo) return;

    if (relacionadoId === documentoStore.documentoForm.id) {
        alert("No se puede relacionar un documento consigo mismo.");
        return;
    }
    await documentoStore.asociarDocumentoRelacionado(relacionadoId, tipo);

    // Limpiar formulario después de asociar
    selectedDocumento.value = { id: null, descripcion: '' };
    tipoRelacionSeleccionado.value = '';
};

// Cargar datos al montar el componente
onMounted(() => {
    documentoStore.fetchDocumentosRelacionados();
});

// Función de utilidad para los badges
const getBadgeClass = (tipo) => {
    const map = {
        impacta: 'bg-primary text-white',
        modifica: 'bg-warning text-dark',
        deroga: 'bg-danger text-white',
    };
    return map[tipo] || 'bg-secondary text-white';
};
</script>

<style scoped>
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

.table th,
.table td {
    font-size: 0.8rem;
    vertical-align: middle;
}

.table td input[type="checkbox"] {
    transform: scale(0.9);
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

.badge {
    font-size: 0.7rem;
    text-transform: capitalize;
}
</style>
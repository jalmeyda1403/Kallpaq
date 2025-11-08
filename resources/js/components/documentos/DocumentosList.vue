<template>
    <div>
        <h6 class="mb-3 font-weight-bold d-flex align-items-center">
            <span class="text-secondary">{{ procesoNombre }}</span>
            <span class="mx-2 text-secondary">
                <i class="fas fa-chevron-right fa-xs"></i>
            </span>
            <span class="text-dark">Documentos Asociados</span>
        </h6>

        <div class="d-flex align-items-center mb-4">
            <div class="input-group mr-3">
                <input type="hidden" v-model="selectedDocumento.id" />
                <input type="text" class="form-control" placeholder="Seleccione el Documento a Asociar"
                    v-model="selectedDocumento.descripcion" readonly />
                <div class="input-group-append">
                    <button type="button" class="btn btn-dark" @click="openDocumentoModal">
                        <i class="fas fa-search"></i>
                    </button>
                    <button type="button" class="btn btn-danger" :disabled="!selectedDocumento.id"
                        @click="associateDocumento">
                        <i class="fas fa-link"></i> Asociar
                    </button>
                </div>
            </div>

        </div>

        <div class="form-overlay-container">
            <div v-if="loading" class="loading-overlay">
                <div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
            <h6 class="mb-3 font-weight-bold">Documentos Asociados</h6>
            <div class="table-responsive">
                <table id="associatedDocumentosTable" class="table table-sm table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Código Documento</th>
                            <th>Nombre Documento</th>
                            <th class="text-center">Versión</th>
                            <th class="text-center text-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="doc in associatedDocumentos" :key="doc.id">
                            <td>{{ doc.cod_documento }}</td>
                            <td>{{ doc.nombre_documento }}</td>
                            <td class="text-center">                               
                                {{
                                    String(doc.usa_versiones_documento ? (doc.ultima_version?.version || 0) : 0).padStart(2,
                                '0')
                                }}
                            </td>
                            <td class="text-center text-nowrap">
                                <button class="btn btn-danger btn-sm" @click="deleteDocumento(doc.id)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <modal-hijo ref="documentoModal" :fetch-url="documento_route" target-id="documento_id"
            target-desc="documento_nombre" @update-target="handleDocumentoSelection" />
    </div>
</template>

<script>
import { route } from 'ziggy-js';
import ModalHijo from '../generales/ModalHijo.vue';

export default {
    components: { ModalHijo },
    props: {
        procesoId: { type: [Number, String], required: true },
    },
    data() {
        return {
            documento_route: route('documentos.buscar'), // Ruta para el ModalHijo
            associatedDocumentos: [],
            selectedDocumento: { id: null, descripcion: '' },
            loading: false,
            procesoNombre: '',
        };
    },
    watch: {
        procesoId: {
            immediate: true,
            handler(newVal) {
                if (newVal) {
                    this.fetchProcesoName(newVal);
                    this.loadAssociatedDocumentos();
                }
            },
        },
    },
    methods: {
        async fetchProcesoName(id) {
            try {
                const response = await fetch(route('procesos.show', { id }));
                if (!response.ok) throw new Error("Error al obtener el nombre del proceso.");
                const data = await response.json();
                this.procesoNombre = data.proceso_nombre;
            } catch (error) {
                console.error(error);
                this.procesoNombre = 'Error al cargar el nombre';
            }
        },
        async loadAssociatedDocumentos() {
            this.loading = true;
            try {
                const response = await fetch(route('procesos.listarDocumentos', { proceso_id: this.procesoId }));
                if (!response.ok) throw new Error("Error al obtener los documentos asociados.");
                this.associatedDocumentos = await response.json();
            } catch (error) {
                alert("Hubo un problema al cargar los documentos.");
            } finally {
                this.loading = false;
            }
        },
        openDocumentoModal() {
            this.$refs.documentoModal.open();
        },
        handleDocumentoSelection({ idValue, descValue }) {
            this.selectedDocumento.id = idValue;
            this.selectedDocumento.descripcion = descValue;
        },
        async associateDocumento() {
            if (!this.selectedDocumento.id) {
                alert('Por favor, selecciona un documento para asociar.');
                return;
            }
            try {
                const url = route('procesos.asociarDocumentos', { proceso: this.procesoId });
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ documento_id: this.selectedDocumento.id }),
                });
                if (!response.ok) throw new Error('No se pudo asociar el documento.');
                alert('Documento asociado correctamente.');
                this.selectedDocumento = { id: null, descripcion: '' };
                await this.loadAssociatedDocumentos();
            } catch (error) {
                console.error(error);
                alert('Error: ' + error.message);
            }
        },
        async deleteDocumento(documentoId) {
            if (!confirm('¿Estás seguro de que quieres eliminar esta asociación?')) return;
            try {
                const url = route('procesos.disociarDocumentos', { proceso: this.procesoId, documento: documentoId });
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                });
                if (!response.ok) throw new Error('No se pudo eliminar la asociación.');
                alert('Asociación eliminada correctamente.');
                await this.loadAssociatedDocumentos();
            } catch (error) {
                console.error(error);
                alert('Error: ' + error.message);
            }
        },
    },
};
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

.table th,
.table td {
    font-size: 0.8rem;
    vertical-align: middle;
}

.table td input[type="checkbox"] {
    transform: scale(0.9);
}
</style>
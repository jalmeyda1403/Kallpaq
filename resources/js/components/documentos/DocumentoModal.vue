<template>
    <div class="modal fade" tabindex="-1" aria-labelledby="documentoModalLabel" aria-hidden="true" ref="modal"
        id="documentoModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ documentoStore.modalTitle }}</h5>
                    <button type="button" class="close text-white" aria-label="Close"
                        @click="documentoStore.closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-body-scrollable d-flex">

                    <div class="col-md-3 border-right p-0">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <h6 class="text-secondary mx-3 mt-2">General</h6>
                            <a class="nav-link"
                                :class="{ 'text-danger active': documentoStore.currentTab === 'DocumentoForm' }"
                                @click="documentoStore.setCurrentTab('DocumentoForm')" id="v-pills-principal-tab"
                                role="tab">
                                <i class="fas fa-info"></i> Información del Documento
                            </a>

                            <hr class="my-2 border-secondary">
                            <h6 class="text-secondary mx-3 mt-3">Asociaciones</h6>
                            <div :class="{ 'disabled-links': !documentoStore.isEditing }">
                                <a class="nav-link"
                                    :class="{ 'text-danger active': documentoStore.currentTab === 'MetadatosForm' }"
                                    @click="documentoStore.setCurrentTab('MetadatosForm')"><i class="fas fa-tags"></i>
                                    Metadatos</a>
                                <a class="nav-link"
                                    :class="{ 'text-danger active': documentoStore.currentTab === 'ProcesosList' }"
                                    @click="documentoStore.setCurrentTab('ProcesosList')"><i class="fas fa-cogs"></i>
                                    Procesos</a>
                                <a class="nav-link"
                                    :class="{ 'text-danger active': documentoStore.currentTab === 'DocumentoJerarquia' }"
                                    @click="documentoStore.setCurrentTab('DocumentoJerarquia')"><i
                                        class="fas fa-sitemap"></i> Jeararquia (Padre/Hijos)</a>

                                <a class="nav-link"
                                    :class="{ 'text-danger active': documentoStore.currentTab === 'DocumentosRelacionados' }"
                                    @click="documentoStore.setCurrentTab('DocumentosRelacionados')"><i
                                        class="fas fa-project-diagram"></i> Documentos Relacionados</a>
                            </div>

                            <hr class="my-2 border-secondary">

                            <h6 class="text-secondary mx-3 mt-3">Gestión y Ciclo de Vida</h6>
                             <div v-if="documentoStore.isEditing && documentoStore.documentoForm.usa_versiones_documento == '1'">
                                <a class="nav-link"
                                    :class="{ 'text-danger active': documentoStore.currentTab === 'VersionesList' }"
                                    @click="documentoStore.setCurrentTab('VersionesList')" id="v-pills-versionestab"
                                    role="tab">
                                    <i class="fas fa-code-branch"></i> Versiones
                                </a>
                            </div>
                            <div :class="{ 'disabled-links': !documentoStore.isEditing }">
                                <a class="nav-link"
                                    :class="{ 'text-danger active': documentoStore.currentTab === 'DocumentoHistorial' }"
                                    @click="documentoStore.setCurrentTab('DocumentoHistorial')"><i
                                        class="fas fa-history"></i> Historial de Cambios</a>
                            </div>



                        </div>
                    </div>
                    <div class="col-md-9 px-4">

                        <component :is="tabs[documentoStore.currentTab]" ref="dynamicComponent"></component>

                    </div>
                </div>

            </div>
        </div>
    </div>

</template>

<script setup>
import { onMounted, ref, shallowRef, watch } from 'vue';
import { useDocumentoStore } from '@/stores/documentoStore';

// Importa los demás componentes aquí
import DocumentoForm from './DocumentoForm.vue';
import MetadatosForm from './MetadatosForm.vue';
import VersionesList from './VersionesList.vue';
import ProcesosList from './ProcesosList.vue';
import DocumentoHistorial from './DocumentoHistorial.vue';
import DocumentosRelacionados from './DocumentosRelacionados.vue';
import DocumentoJerarquia from './DocumentoJerarquia.vue';




const documentoStore = useDocumentoStore();
const modal = ref(null);


const tabs = shallowRef({
    DocumentoForm,
    MetadatosForm,
    VersionesList,
    ProcesosList,
    DocumentoHistorial,
    DocumentosRelacionados,
    DocumentoJerarquia

});



onMounted(() => {
    $(modal.value).modal({
        backdrop: 'static',
        keyboard: false
    });

    // Observa el estado isModalOpen del store para mostrar/ocultar el modal
    documentoStore.$subscribe((mutation, state) => {
        if (state.isModalOpen) {
            $(modal.value).modal('show');
            // Asegúrate de que el tab por defecto sea HallazgoForm al abrir el modal
            if (!documentoStore.currentTab) {
                documentoStore.setCurrentTab('DocumentoForm');
            }
        } else {
            $(modal.value).modal('hide');
        }
    });
    // --- CAMBIO CLAVE: Usa el evento shown.bs.modal ---
    $(modal.value).on('shown.bs.modal', () => {

        if (documentoStore.currentTab === 'MetadatosForm' && documentoFormRef.value) {
            documentoFormRef.value.reInitializeSelect2(); // Llama a un método expuesto por HallazgoForm
        }
    });

    // Manejar el evento 'hidden.bs.modal' para limpiar el store
    $(modal.value).on('hidden.bs.modal', () => {
        if (event.target === modal.value) {
            documentoStore.resetForm();
        }
    });
});


</script>

<style scoped>
/* Estilos del menú lateral */
.nav-pills .nav-link {
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
    border-radius: 0.25rem;
    text-align: left !important;
    transition: background-color 0.2s ease-in-out;
}

.nav-pills .nav-link:not(.active):hover {
    background-color: #f8f9fa;
    color: #000;
}

.nav-pills .nav-link.active {
    background-color: #fff;
    font-weight: bold;
    color: #dc3545 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.nav-link i {
    width: 1.5rem;
    text-align: left !important;
}

.nav-pills h6 {
    text-transform: uppercase;
    font-size: 0.7rem;
    margin-bottom: 0.5rem;
    letter-spacing: 0.05rem;
    text-align: left !important;
}

/* Estilos de los campos de formulario */
.form-group small {
    font-size: 0.75rem;
}

.form-label.text-danger {
    font-weight: bold;
}

.disabled-links {
    pointer-events: none;
    /* Deshabilita el click */
    opacity: 0.5;
    /* Atenúa visualmente los enlaces */
}

.modal-body-scrollable {
    height: 90vh;
    /* Ajusta este valor según lo necesites */
    overflow-y: auto;
}
</style>
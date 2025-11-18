<template>
    <div class="modal fade" tabindex="-1" aria-labelledby="inventarioModalLabel" aria-hidden="true" ref="modal"
        id="inventarioModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ inventarioStore.modalTitle }}</h5>
                    <button type="button" class="close text-white" aria-label="Close" @click="inventarioStore.closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-body-scrollable d-flex">

                    <div class="col-md-3 border-right p-0">
                        <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                            <h6 class="text-secondary mx-3 mt-2">GENERAL</h6>
                            <a class="nav-link"
                                :class="{ 'text-danger active': inventarioStore.currentTab === 'InventarioForm' }"
                                @click="inventarioStore.setCurrentTab('InventarioForm')" role="tab">
                                <i class="fas fa-clipboard-list"></i> Información del Inventario
                            </a>

                            <hr class="my-2">

                            <h6 class="text-secondary mx-3 mt-3" v-if="inventarioStore.isEditing">PROCESOS</h6>
                            <div :class="{ 'disabled-links': !inventarioStore.isEditing }">
                                <a class="nav-link"
                                    :class="{ 'text-danger active': inventarioStore.currentTab === 'AsociarProcesos' }"
                                    @click="inventarioStore.setCurrentTab('AsociarProcesos')" role="tab" v-if="inventarioStore.isEditing">
                                    <i class="fas fa-tasks"></i> Asociar Procesos
                                </a>
                                <a class="nav-link"
                                    :class="{ 'text-danger active': inventarioStore.currentTab === 'GestionarProcesos' }"
                                    @click="inventarioStore.setCurrentTab('GestionarProcesos')" role="tab" v-if="inventarioStore.isEditing">
                                    <i class="fas fa-tasks"></i> Gestionar Procesos
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9 px-4">
                        <KeepAlive>
                            <component :is="tabs[inventarioStore.currentTab]" :key="inventarioStore.currentTab"
                                :inventario-data="inventarioStore.currentInventario"
                                :is-editing="inventarioStore.isEditing"
                                @saved="onInventarioSaved"
                                @cancelled="inventarioStore.closeModal">
                            </component>
                        </KeepAlive>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useInventarioStore } from '@/stores/inventarioStore';

// Importa todos tus componentes de las pestañas
import InventarioForm from './InventarioForm.vue';
import AsignarProceso from './AsignarProceso.vue'; // Re-import AsignarProceso
import GestionarProcesos from './GestionarProcesos.vue';

import axios from 'axios';

const inventarioStore = useInventarioStore();
const modal = ref(null); // Ref para el elemento modal de Bootstrap

// Componentes disponibles para las pestañas
const tabs = {
    InventarioForm,
    AsociarProcesos: AsignarProceso, // Add AsociarProcesos
    GestionarProcesos
};

const onInventarioSaved = async (inventarioData) => {
    console.log('Datos recibidos para guardar:', inventarioData);

    try {
        const formData = new FormData();
        formData.append('nombre', inventarioData.nombre);
        formData.append('descripcion', inventarioData.descripcion);
        formData.append('enlace', inventarioData.enlace || '');
        formData.append('vigencia', inventarioData.vigencia);

        // Manejar el documento de aprobación
        if (inventarioData.documento_aprueba instanceof File) {
            // Si es un archivo nuevo, lo subimos
            console.log('Subiendo nuevo archivo:', inventarioData.documento_aprueba.name);
            formData.append('documento_aprueba', inventarioData.documento_aprueba);
        }
        // En caso de edición sin nuevo archivo, no enviamos nada para documento_aprueba,
        // asumiendo que el backend mantiene el archivo actual si no se envía uno nuevo

        // Solo enviar estado_flujo y estado si están presentes en el formulario
        if (inventarioData.estado_flujo !== undefined) {
            formData.append('estado_flujo', inventarioData.estado_flujo);
        }
        if (inventarioData.estado !== undefined) {
            formData.append('estado', inventarioData.estado);
        }

        console.log('FormData construido:', Array.from(formData.entries()));

        // Agregar el método PUT como campo oculto para que Laravel lo procese correctamente
        if (inventarioData.id) {
            formData.append('_method', 'PUT');
        }

        let response;
        if (inventarioData.id) {
            // Update - usar POST con _method para evitar problemas con PUT + multipart/form-data
            console.log('Actualizando inventario con ID:', inventarioData.id);
            response = await axios.post(`/api/inventarios/${inventarioData.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        }
        else {
            // Create
            console.log('Creando nuevo inventario');
            response = await axios.post('/api/inventarios', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        }

        inventarioStore.closeModal();
        // Emitir evento para que el index actualice la lista
        window.dispatchEvent(new CustomEvent('inventarios-actualizados'));

        // Opcional: Mostrar mensaje de éxito
        alert('Inventario guardado correctamente.');
    }
    catch (error) {
        console.error('Error completo al guardar inventario:', error);
        console.error('Error response:', error.response);
        console.error('Error request:', error.request);
        console.error('Error message:', error.message);

        // Mostrar mensaje de error al usuario
        console.log('Datos completos de error.response:', error.response);

        let errorMessage = 'Error al guardar el inventario.';
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                // Mostrar errores de validación
                Object.values(error.response.data.errors).forEach(messages => {
                    messages.forEach(msg => errorMessage += `\n- ${msg}`);
                });
            }
            else if (error.response.data.message) {
                errorMessage = error.response.data.message;
            }
            else {
                // Mostrar cualquier otro contenido del error
                errorMessage = JSON.stringify(error.response.data);
            }
        }
        alert(errorMessage);
    }
};

onMounted(() => {
    // Inicializa el modal de Bootstrap manualmente para controlar su ciclo de vida
    $(modal.value).modal({
        backdrop: 'static',
        keyboard: false
    });

    // Observa el estado isModalOpen del store para mostrar/ocultar el modal
    inventarioStore.$subscribe((mutation, state) => {
        if (state.isModalOpen) {
            $(modal.value).modal('show');
            // Asegúrate de que el tab por defecto sea InventarioForm al abrir el modal
            if (!inventarioStore.currentTab) {
                inventarioStore.setCurrentTab('InventarioForm');
            }
        }
        else {
            $(modal.value).modal('hide');
        }
    });

    // Manejar el evento 'hidden.bs.modal' para limpiar el store
    $(modal.value).on('hidden.bs.modal', (event) => {
        if (event.target === modal.value) {
            inventarioStore.resetForm();
        }
    });
});
</script>

<style scoped>
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
import { defineStore } from 'pinia';
import { route } from 'ziggy-js';
import axios from 'axios';
import Swal from 'sweetalert2';


export const useDocumentoStore = defineStore('documento', {
    state: () => ({
        // Estado principal del formulario, incluyendo todas las propiedades del documento
        documentoForm: {
            id: null,
            cod_documento: '',
            tipo_documento_id: '',
            nombre_documento: '',
            resumen_documento: '',
            fuente_documento: '',
            estado_documento: '',
            observaciones_documento: '',
            archivo_path_documento: '',
            usa_versiones_documento: '0',
            fecha_aprobacion_documento: '',
            frecuencia_revision_documento: '',
            instrumento_aprueba_documento: '',
            origen_documento: 'file',
            enlace_valido: null,


        },

        metadatosForm: {
            area_compliance_id: null, // Agregado al estado principal
            subarea_compliance_id: null, // Agregado al estado principal
            palabras_clave_documento: '', // Agregado al estado principal
            tags: [],
        },
        // Estado para la interfaz y el control del modal
        modalTitle: '',
        isModalOpen: false,
        loading: false,
        loadingMetadatos: false,
        loadingVersiones: false,
        errors: {},
        currentTab: 'DocumentoForm',

        // Datos globales para los selects y tags
        tipoDocumento: [],
        areaCompliance: [],
        subareaCompliance: [],
        tagsDisponibles: [],
        versiones: [],

        // Estado para la gestión de versiones (asumo que está en el store)
        showVersionForm: false,
        isEditingVersion: false,
        versionForm: {
            id: null,
            documento_id: null,
            dv_version: '',
            dv_archivo: null,
            dv_control_cambios: '',
            dv_enlace_valido: false,
            dv_instrumento_aprueba: '',
            dv_fecha_aprobacion: '',
            dv_fecha_vigencia: '',
        },
        //Estado para Procesos
        associatedProcesos: [],
        loadingProcesos: false,
        procesosCargadasParaDocumentoId: null,
        //Estado jerarquia
        documentoPadre: null,
        documentosHijos: [],
        loadingJerarquia: false,

        //Estado Documentos Relaciondos
        relacionesSalientes: [],
        relacionesEntrantes: [],
        loadingRelacionados: false,
        relacionesCargadasParaDocumentoId: null,


        //Estado Historial

        historial: [],
        loadingHistorial: false,
    }),

    getters: {
        // Propiedad computada para saber si estamos editando un documento
        isEditing: (state) => !!state.documentoForm.id,
    },

    actions: {
        // Cambia la pestaña del formulario
        setCurrentTab(tabName) {
            this.currentTab = tabName;
        },
        // Carga los datos globales (tipos, áreas, etc.) al inicio
        async loadTipoDocumento() {
            if (this.tipoDocumento.length > 0) return; // Ya cargado
            // No usamos el 'this.loading' general aquí para no bloquear todo el modal
            // Puedes añadir un 'loadingTipoDocumento' si es necesario, pero generalmente es rápido.
            try {
                const response = await axios.get(route('tipoDocumento.buscar'));
                this.tipoDocumento = response.data;
            } catch (error) {
                console.error('Error al cargar tipos de documento:', error);
                this.errors.tipoDocumento = 'No se pudieron cargar los tipos de documento.';
            }
        },

        async loadAreasCompliance() {
            if (this.areaCompliance.length > 0 && this.subareaCompliance.length > 0) {
                return;
            }

            this.loadingMetadatos = true;
            this.errors.metadatos = null; // Limpiamos errores previos

            try {
                // Promise.all ejecuta ambas peticiones de forma simultánea (en paralelo).
                const [areasResponse, subareasResponse] = await Promise.all([
                    axios.get(route('areaCompliance.buscar')),
                    axios.get(route('subareaCompliance.buscar'))
                ]);

                // Solo cuando AMBAS promesas se resuelven con éxito, 
                // asignamos los datos al estado.
                this.areaCompliance = areasResponse.data;
                this.subareaCompliance = subareasResponse.data;

            } catch (error) {
                console.error('Error al cargar áreas de compliance:', error);
                this.errors.areaCompliance = 'No se pudieron cargar las áreas de compliance.';
            } finally {
                this.loadingMetadatos = false; // Desactiva el loading de metadatos
            }
        },



        // Método para abrir el modal y, opcionalmente, cargar un documento
        async openModal(documentoId = null) {
            this.resetForm(); // Resetear el formulario primero es crucial
            this.errors = {}; // Limpia errores


            await this.loadTipoDocumento();

            this.modalTitle = documentoId ? 'Editar Documento' : 'Nuevo Documento';
            this.isModalOpen = true; // Abre el modal

            this.currentTab = 'DocumentoForm'; // Asegura que la primera pestaña sea DocumentoForm

            if (documentoId) {
                await this.fetchDocumento(documentoId); // Carga los datos específicos del documento
            } else {
                this.isEditing = false;
            }
        },

        // Método para cerrar el modal
        closeModal() {
            this.isModalOpen = false;
            this.resetForm();
            this.tipoDocumento = [];
            this.areaCompliance = [];
            this.subareaCompliance = [];
            this.tagsDisponibles = [];
            this.versiones = []
        },

        // Carga los datos de un documento desde el backend
        async fetchDocumento(id) {
            try {
                this.loading = true;
                const response = await axios.get(route('documentos.show', id));
                const data = response.data;

                // Asigna los datos recibidos al estado del formulario
                Object.assign(this.documentoForm, data);
                this.documentoForm.fecha_aprobacion_documento = this.formatDateForInput(data.fecha_aprobacion_documento);
                this.documentoForm.origen_documento = data.archivo_path_documento && data.archivo_path_documento.startsWith('http') ? 'url' : 'file';

                this.metadatosForm.area_compliance_id = data.area_compliance_id;
                this.metadatosForm.subarea_compliance_id = data.subarea_compliance_id;
                this.metadatosForm.palabras_clave_documento = data.palabras_clave_documento;
                if (data.tags && data.tags.length > 0) {
                    // Guardamos los IDs para el v-model de Select2
                    this.metadatosForm.tags = data.tags.map(tag => tag.id);
                    // ¡CRÍTICO! Guardamos los objetos completos para renderizar los <option> iniciales
                    this.tagsDisponibles = data.tags;
                } else {
                    this.metadatosForm.tags = [];
                    this.tagsDisponibles = [];
                }
                this.errors = {};
            } catch (error) {
                console.error('Error al cargar el documento:', error);
                this.errors.fetch = 'No se pudo cargar el documento.';
            } finally {
                this.loading = false;
            }
        },

        async fetchVersiones(id) {
            // Se asume que esto se llama desde el componente de Versiones.
            // Podrías añadir un 'loadingVersiones' específico para esto, si no quieres usar el general.
            if (!this.documentoForm.id) {
                return;
            }
            if (this.versiones.length > 0 && this.documentoForm.id === id) { // Evitar recargar si ya está cargado para el mismo ID
                return;
            }
            try {
                this.loadingVersiones = true;
                const response = await axios.get(route('documento.versiones', id));
                this.versiones = response.data;
            } catch (error) {
                console.error('Error al obtener las versiones:', error);
                this.errors.versiones = 'No se pudieron cargar las versiones del documento.';
            } finally {
                this.loadingVersiones = false;
            }
        },
        // Metodos para asociar Procesos
        async fetchAssociatedProcesos(force = false) {
            // Es una buena práctica asegurarse de que tenemos un ID antes de continuar.
            if (!this.documentoForm.id) return;
            if (!force && this.procesosCargadasParaDocumentoId === this.documentoForm.id) {
                return;
            }
            this.loadingProcesos = true;
            try {
                // El error está en la siguiente línea.
                // PROBABLEMENTE TIENES: route('...', { documento: id })
                // DEBE SER: route('...', { documento: this.documentoForm.id })
                const response = await axios.get(route('documento.procesos.listar', {
                    documento: this.documentoForm.id
                }));

                this.associatedProcesos = response.data;
                this.procesosCargadasParaDocumentoId = this.documentoForm.id;
            } catch (error) {
                console.error("Error al cargar los procesos asociados:", error);
            } finally {
                this.loadingProcesos = false;
            }
        },
        async asociarProceso(procesoId) {
            this.loadingProcesos = true;
            try {
                await axios.post(route('documento.procesos.asociar', { documento: this.documentoForm.id }), {
                    proceso_id: procesoId
                });
                // CRÍTICO: Refresca el estado para mantener la UI sincronizada
                await this.fetchAssociatedProcesos(true);
            } catch (error) {
                console.error("Error al asociar el proceso:", error);
                alert('Error: ' + (error.response?.data?.message || error.message));
            } finally {
                this.loadingProcesos = false;
            }
        },

        async disociarProceso(procesoId) {
            if (!confirm('¿Está seguro de que desea eliminar esta asociación?')) return;
            this.loadingProcesos = true;
            try {
                await axios.delete(route('documento.procesos.disociar', { documento: this.documentoForm.id, proceso: procesoId }));
                // Esta llamada ahora funcionará porque la función que llama está corregida.
                await this.fetchAssociatedProcesos(true);
            } catch (error) {
                console.error("Error al desasociar el proceso:", error);
                alert('Error: ' + error.message);
            } finally {
                this.loadingProcesos = false;
            }
        },
        // Metodos para asociar Documentos Relacionados
        async fetchDocumentosRelacionados(force = false) {
            if (!this.documentoForm.id) return;
            if (!force && this.relacionesCargadasParaDocumentoId === this.documentoForm.id) {
                return;
            }
            this.loadingRelacionados = true;
            try {                // Hacemos una única llamada al backend que nos devolverá ambas listas
                const response = await axios.get(route('documento.relacionados.listar', { documento: this.documentoForm.id }));
                this.relacionesSalientes = response.data.salientes;
                this.relacionesEntrantes = response.data.entrantes;
                this.relacionesCargadasParaDocumentoId = this.documentoForm.id;
            } catch (error) {
                console.error("Error al cargar relaciones:", error);
            } finally {
                this.loadingRelacionados = false;
            }
        },

        async asociarDocumentoRelacionado(relacionadoId, tipoRelacion) { // <-- AÑADIR tipoRelacion AQUÍ
            this.loadingRelacionados = true;
            try {
                await axios.post(route('documento.relacionados.asociar', { documento: this.documentoForm.id }), {
                    relacionado_id: relacionadoId,
                    tipo_relacion: tipoRelacion // Ahora 'tipoRelacion' tiene el valor correcto
                });
                await this.fetchDocumentosRelacionados(true);
            } catch (error) {
                console.error("Error al asociar documento:", error);
                alert('Error: ' + (error.response?.data?.message || error.message));
            } finally {
                this.loadingRelacionados = false;
            }
        },

        async disociarDocumentoRelacionado(relacionadoId) {
            if (!confirm('¿Está seguro de que desea eliminar esta relación?')) return;
            this.loadingRelacionados = true;
            try {
                await axios.delete(route('documento.relacionados.disociar', { documento: this.documentoForm.id, relacionado: relacionadoId }));
                await this.fetchDocumentosRelacionados(true); // Recargar ambas listas
            } catch (error) {
                console.error("Error al disociar documento:", error);
            } finally {
                this.loadingRelacionados = false;
            }
        },

        //Metodos para configurar Jerarquias.
        async fetchJerarquia() {
            if (!this.documentoForm.id) return;
            this.loadingJerarquia = true;
            try {
                const response = await axios.get(route('documento.jerarquia.get', { documento: this.documentoForm.id }));
                this.documentoPadre = response.data.padre;
                this.documentosHijos = response.data.hijos;
            } catch (error) { console.error("Error al cargar la jerarquía:", error); }
            finally { this.loadingJerarquia = false; }
        },

        async asignarPadre(padreId) {
            this.loadingJerarquia = true;
            try {
                await axios.post(route('documento.jerarquia.setPadre', { documento: this.documentoForm.id }), { documento_padre_id: padreId });
                await this.fetchJerarquia(); // Recargar
            } catch (error) { console.error("Error al asignar padre:", error); }
            finally { this.loadingJerarquia = false; }
        },

        async quitarPadre() {
            this.loadingJerarquia = true;
            try {
                await axios.post(route('documento.jerarquia.removePadre', { documento: this.documentoForm.id }));
                await this.fetchJerarquia(); // Recargar
            } catch (error) { console.error("Error al quitar padre:", error); }
            finally { this.loadingJerarquia = false; }
        },

        async asignarHijo(hijoId) {
            this.loadingJerarquia = true;
            try {
                await axios.post(route('documento.jerarquia.setHijo', { documento: this.documentoForm.id }), {
                    hijo_id: hijoId
                });
                await this.fetchJerarquia();
            } catch (error) {
                alert("Ocurrió un error al asignar la dependencia:".error);
            } finally {
                this.loadingJerarquia = false;
            }
        },

        async quitarHijo(hijoId) {
            this.loadingJerarquia = true;
            try {
                await axios.post(route('documento.jerarquia.removeHijo', { documento: this.documentoForm.id, hijo: hijoId }));
                await this.fetchJerarquia(); // Recargar
            } catch (error) { console.error("Error al quitar hijo:", error); }
            finally { this.loadingJerarquia = false; }
        },


        //Metodos para Historial de Documentos
        async fetchHistorial() {
            if (!this.documentoForm.id) return;
            this.loadingHistorial = true;
            try {
                const response = await axios.get(route('documento.historial.listar', { documento: this.documentoForm.id }));
                this.historial = response.data;
            } catch (error) {
                console.error("Error al cargar el historial:", error);
            } finally {
                this.loadingHistorial = false;
            }
        },
        // Guarda o actualiza el documento según el tipo de formulario
        async saveDocumento(formType) {

            this.errors = {};
            let url = '';
            let data = {}; // Variable para el payload de datos

            // Determina la URL y los datos a enviar según el tipo de formulario
            if (formType === 'general') {
                url = this.isEditing ? route('documento.updateInfoGeneral', { id: this.documentoForm.id }) : route('documentos.store');
                data = this.documentoForm; // Envía el formulario general
            } else if (formType === 'metadatos') {
                // Para metadatos, la ruta es siempre de actualización
                url = route('documento.updateMetadatos', { id: this.documentoForm.id });
                data = this.metadatosForm; // Envía solo el formulario de metadatos
            } else {
                throw new Error('Tipo de formulario desconocido');
            }

            try {
                // Usa el método PUT para las actualizaciones y POST para la creación
                const method = this.isEditing && formType !== 'general' ? 'put' : 'post';

                // Es mejor usar un enfoque condicional para evitar el 'put' al crear
                if (formType === 'general' && !this.isEditing) {
                    await axios.post(url, data);
                } else {
                    // axios.put maneja las actualizaciones para ambos formularios
                    await axios.put(url, data);
                }

            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    alert('Ocurrió un error inesperado al guardar el documento.');
                }
            } finally {
                alert('¡Documento guardado correctamente!');
            }
        },
        // Resetea el formulario a sus valores iniciales
        resetForm() {
            this.documentoForm = {
                id: null,
                cod_documento: '',
                tipo_documento_id: '',
                nombre_documento: '',
                resumen_documento: '',
                fuente_documento: '',
                estado_documento: '',
                observaciones_documento: '',
                archivo_path_documento: '',
                usa_versiones_documento: '1',
                fecha_aprobacion_documento: '',
                frecuencia_revision_documento: '',
                instrumento_aprueba_documento: '',
                origen_documento: 'file',
                enlace_valido: '0',
                tags: [],
                procesos: [],
                versiones: [],
                documentos_relacionados: [],


            };
            this.metadatosForm = { // Resetear también los metadatos
                area_compliance_id: null,
                subarea_compliance_id: null,
                palabras_clave_documento: '',
                tags: [],
            };
            this.errors = {};
            this.loading = false;
            this.loadingMetadatos = false; // Resetear loading de metadatos
            this.loadingVersiones = false; // Resetear loading de versiones
            this.currentTab = 'DocumentoForm';
            this.modalTitle = '';
            this.versiones = []; // Limpia las versiones también
            this.associatedProcesos = []; //procesos
            this.loadingProcesos = false;
            this.relacionesSalientes = [];
            this.relacionesEntrantes = [];
            this.loadingRelacionados = false;
            this.historial = [];
            this.loadingHistorial = false;
            this.documentoPadre = null;
            this.documentosHijos = [];
            this.loadingJerarquia = false;

        },
        openVersionForm() {
            this.showVersionForm = true;
            this.isEditingVersion = false;
            this.resetVersionForm();

            // Auto-calculate next version
            let maxVersion = -1;
            if (this.versiones && this.versiones.length > 0) {
                // Extract numeric part if version matches expected integer format
                const versions = this.versiones.map(v => parseInt(v.version || 0));
                maxVersion = Math.max(...versions);
            }
            this.versionForm.version = maxVersion + 1;
        },
        resetVersionForm() {
            this.versionForm = {
                id: null,
                documento_id: this.documentoForm.id,
                version: '',
                control_cambios: '',
                fecha_aprobacion: '',
                fecha_publicacion: '',
                instrumento_aprueba: '',
                enlace_valido: false,
                archivo: null,
            };
        },
        editVersion(version) {
            this.showVersionForm = true;
            this.isEditingVersion = true;
            this.versionForm = {
                id: version.id,
                documento_id: version.documento_id,
                version: version.version,
                control_cambios: version.control_cambios,
                fecha_aprobacion: version.fecha_aprobacion,
                fecha_publicacion: version.fecha_publicacion,
                instrumento_aprueba: version.instrumento_aprueba,
                enlace_valido: Boolean(version.enlace_valido),
                archivo: null,
            };
        },
        closeVersionForm() {
            this.showVersionForm = false;
            this.resetVersionForm();
        },

        // Formatea la fecha para el input
        formatDateForInput(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return `${date.getFullYear()} -${String(date.getMonth() + 1).padStart(2, '0')} -${String(date.getDate()).padStart(2, '0')} `;
        },
        async validateUrl() {
            this.errors.archivo_path_documento = [];
            this.documentoForm.enlace_valido = false;

            try {
                const url = route('documento.validarUrl');
                const response = await axios.get(url, { params: { url: this.documentoForm.archivo_path_documento } });

                this.documentoForm.enlace_valido = response.data.isValid;

                if (!response.data.isValid) {
                    this.errors.archivo_path_documento = ["La URL no es válida. Por favor, revísela."];
                }
                return response.data.isValid;

            } catch (error) {

                this.documentoForm.enlace_valido = false;
                this.errors.archivo_path_documento = ["Ocurrió un error al validar la URL."];
            }
        },

        async fetchVersiones(documentoId, trashed = false) {
            if (!documentoId) return;
            this.loadingVersiones = true;
            try {
                const url = route('documento.versiones', { documento_id: documentoId });
                const response = await axios.get(url, { params: { trashed: trashed ? 1 : 0 } });
                this.versiones = response.data;
            } catch (error) {
                console.error("Error al cargar las versiones:", error);
                this.versiones = [];
            } finally {
                this.loadingVersiones = false;
            }
        },

        async saveVersion() {
            this.loading = true;
            try {
                const formData = new FormData();
                formData.append('documento_id', this.versionForm.documento_id);
                formData.append('version', this.versionForm.version);

                if (this.versionForm.archivo) {
                    formData.append('archivo', this.versionForm.archivo);
                }

                formData.append('control_cambios', this.versionForm.control_cambios || '');
                formData.append('instrumento_aprueba', this.versionForm.instrumento_aprueba || '');
                formData.append('fecha_aprobacion', this.versionForm.fecha_aprobacion || '');
                formData.append('fecha_publicacion', this.versionForm.fecha_publicacion || '');
                formData.append('enlace_valido', this.versionForm.enlace_valido ? 1 : 0);

                let url;
                let message = '';
                if (this.isEditingVersion) {
                    url = route('documento.versiones.update', { id: this.versionForm.id });
                    formData.append('_method', 'PUT');
                    await axios.post(url, formData);
                    message = 'Versión actualizada correctamente.';
                } else {
                    // Using the route for creation
                    url = route('documento.versiones.store');
                    await axios.post(url, formData);
                    message = 'Nueva versión creada correctamente.';
                }

                // Refresh the list
                await this.fetchVersiones(this.versionForm.documento_id);

                this.closeVersionForm();

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: message,
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#d33' // Matches red theme
                });

            } catch (error) {
                console.error("Error saving version:", error);
                if (error.response?.status === 422) {
                    this.errors.version = error.response.data.errors;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Validación',
                        text: 'Por favor, revise los campos obligatorios.',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#d33'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al guardar la versión.',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#d33'
                    });
                }
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteVersion(versionId) {
            try {
                const url = route('documento.versiones.destroy', { id: versionId });
                await axios.delete(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: 'Versión eliminada correctamente.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#d33'
                });
            } catch (error) {
                console.error("Error deleting version:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al eliminar la versión.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#d33'
                });
                throw error;
            }
        },

        async restoreVersion(versionId) {
            try {
                const url = route('documento.versiones.restore', { id: versionId });
                await axios.post(url);
                Swal.fire({
                    icon: 'success',
                    title: 'Restaurado',
                    text: 'Versión restaurada correctamente.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#d33'
                });
            } catch (error) {
                console.error("Error restoring version:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al restaurar la versión.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#d33'
                });
                throw error;
            }
        },
    },
});
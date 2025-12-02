<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div>
                    <!-- Encabezado -->
                    <div class="header-container">
                        <h6 class="mb-0 d-flex align-items-center">
                            <span class="text-dark">{{ formatBreadcrumbId(store.riesgoForm.id) }}</span>
                            <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                            <span class="text-dark">Identificar Riesgo</span>
                        </h6>
                    </div>
                    <div class="text-left mb-4">
                        <h6 class="mb-1" style="font-weight: bold;">IDENTIFICACIÓN DEL RIESGO</h6>
                        <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                            Complete la información general del riesgo, incluyendo su descripción, causa y valoración.
                        </p>
                    </div>

                    <form @submit.prevent="saveRiesgo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold mb-0">Proceso <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                v-model="store.riesgoForm.proceso_nombre" readonly
                                                placeholder="Seleccione un proceso"
                                                :class="{ 'is-invalid': store.errors.proceso_id }">
                                            <div class="input-group-append">
                                                <button class="btn btn-dark" type="button" @click="openProcesoModal">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                <button class="btn btn-danger" type="button"
                                                    v-if="store.riesgoForm.proceso_id" @click="clearProceso">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback" v-if="store.errors.proceso_id">
                                                {{ store.errors.proceso_id[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="riesgo_cod">Código</label>
                                        <input type="text" class="form-control" id="riesgo_cod"
                                            v-model="store.riesgoForm.riesgo_cod"
                                            :class="{ 'is-invalid': store.errors.riesgo_cod }">
                                        <div class="invalid-feedback" v-if="store.errors.riesgo_cod">
                                            {{ store.errors.riesgo_cod[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="riesgo_tipo">Tipo</label>
                                        <select class="form-control" id="riesgo_tipo"
                                            v-model="store.riesgoForm.riesgo_tipo"
                                            :class="{ 'is-invalid': store.errors.riesgo_tipo }">
                                            <option value="" disabled>Seleccione tipo</option>
                                            <option v-for="tipo in tiposRiesgo" :key="tipo" :value="tipo">{{ tipo }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-if="store.errors.riesgo_tipo">
                                            {{ store.errors.riesgo_tipo[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div>
                                                <label class="font-weight-bold mb-0">Descripción del Riesgo <span
                                                        class="text-danger">*</span></label>
                                                <button type="button" class="btn btn-sm btn-outline-info ml-2"
                                                    @click="improveDescription" :disabled="improvingDescription"
                                                    title="Mejorar redacción con IA">
                                                    <i class="fas fa-magic"
                                                        :class="{ 'fa-spin': improvingDescription }"></i>
                                                    <span v-if="!improvingDescription"></span>
                                                    <span v-else> Mejorando...</span>
                                                </button>
                                            </div>
                                            <small class="text-muted">
                                                {{ store.riesgoForm.riesgo_nombre ?
                                                    store.riesgoForm.riesgo_nombre.length :
                                                    0 }}/500
                                            </small>
                                        </div>
                                        <textarea class="form-control" id="riesgo_nombre" rows="5"
                                            v-model="store.riesgoForm.riesgo_nombre"
                                            placeholder="Describa el riesgo identificado..." maxlength="500"
                                            :class="{ 'is-invalid': store.errors.riesgo_nombre }"></textarea>
                                        <div class="invalid-feedback" v-if="store.errors.riesgo_nombre">
                                            {{ store.errors.riesgo_nombre[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div>
                                                <label class="font-weight-bold mb-0">Consecuencia <span
                                                        class="text-danger">*</span></label>
                                                <button type="button" class="btn btn-sm btn-outline-info ml-2"
                                                    @click="improveConsecuencia" :disabled="improvingConsecuencia"
                                                    title="Sugerir o mejorar consecuencia con IA">
                                                    <i class="fas fa-magic"
                                                        :class="{ 'fa-spin': improvingConsecuencia }"></i>
                                                    <span v-if="!improvingConsecuencia"> </span>
                                                    <span v-else> Generando...</span>
                                                </button>
                                            </div>
                                            <small class="text-muted">
                                                {{ store.riesgoForm.riesgo_consecuencia ?
                                                    store.riesgoForm.riesgo_consecuencia.length :
                                                    0 }}/500
                                            </small>
                                        </div>
                                        <textarea class="form-control" id="riesgo_consecuencia" rows="5"
                                            v-model="store.riesgoForm.riesgo_consecuencia"
                                            placeholder="Describa la consecuencia de la materialización del riesgo..."
                                            maxlength="500"
                                            :class="{ 'is-invalid': store.errors.riesgo_consecuencia }"></textarea>
                                        <div class="invalid-feedback" v-if="store.errors.riesgo_consecuencia">
                                            {{ store.errors.riesgo_consecuencia[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="factor">Factor de Riesgo</label>
                                        <select class="form-control" id="factor" v-model="store.riesgoForm.factor_id"
                                            :class="{ 'is-invalid': store.errors.factor_id }">
                                            <option :value="null" disabled>Seleccione factor</option>
                                            <option v-for="factor in factores" :key="factor.id" :value="factor.id">
                                                {{ factor.nombre }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-if="store.errors.factor_id">
                                            {{ store.errors.factor_id[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="riesgo_matriz">Matriz</label>
                                        <select class="form-control" id="riesgo_matriz"
                                            v-model="store.riesgoForm.riesgo_matriz"
                                            :class="{ 'is-invalid': store.errors.riesgo_matriz }">
                                            <option value="" disabled>Seleccione matriz</option>
                                            <option v-for="matriz in matrices" :key="matriz" :value="matriz">{{ matriz
                                                }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-if="store.errors.riesgo_matriz">
                                            {{ store.errors.riesgo_matriz[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer justify-content-center w-100">
                                <button type="submit" class="btn btn-danger" :disabled="store.loading">
                                    <span v-if="store.loading" class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    {{ store.isEditing ? 'Actualizar' : 'Grabar' }}
                                </button>
                                <button type="button" class="btn btn-secondary ml-2" @click="store.closeModal">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <ModalHijo ref="modalProceso" :fetch-url="processRoute" targetId="proceso_id" targetDesc="proceso_nombre"
                @update-target="onProcesoSelected" />

        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import ModalHijo from '@/components/generales/ModalHijo.vue';

const store = useRiesgoStore();

const formatBreadcrumbId = (id) => {
    if (!id) return 'Nuevo Riesgo';
    return `R${id.toString().padStart(6, '0')}`;
};

const modalProceso = ref(null);
const factores = ref([]);
const improvingDescription = ref(false);

const processRoute = route('procesos.buscar');

const tiposRiesgo = ['Riesgo', 'Oportunidad'];
const matrices = ['estrategica', 'tactica'];

const improveDescription = async () => {
    if (!store.riesgoForm.riesgo_nombre || store.riesgoForm.riesgo_nombre.length < 10) {
        Swal.fire('Atención', 'Por favor ingrese una descripción preliminar más detallada para mejorar.', 'warning');
        return;
    }

    improvingDescription.value = true;
    try {
        const improvedText = await store.improveRiskDescription(store.riesgoForm.riesgo_nombre);
        store.riesgoForm.riesgo_nombre = improvedText;
        Swal.fire('Redacción Mejorada', 'La descripción del riesgo ha sido actualizada con la sugerencia de la IA.', 'success');
    } catch (error) {
        Swal.fire('Error', 'No se pudo mejorar la redacción. Intente nuevamente.', 'error');
    } finally {
        improvingDescription.value = false;
    }
};

const improvingConsecuencia = ref(false);

const improveConsecuencia = async () => {
    if (!store.riesgoForm.riesgo_nombre || store.riesgoForm.riesgo_nombre.length < 10) {
        Swal.fire('Atención', 'Primero debe ingresar una descripción del riesgo válida para obtener sugerencias.', 'warning');
        return;
    }

    improvingConsecuencia.value = true;
    try {
        const result = await store.improveRiskConsecuencia(
            store.riesgoForm.riesgo_nombre,
            store.riesgoForm.riesgo_consecuencia
        );

        // Check if result contains options (suggestions) or just text (improvement)
        if (result.includes('Opción 1') || result.includes('- ')) {
            store.riesgoForm.riesgo_consecuencia = result; // Let user choose/edit from suggestions
            Swal.fire('Sugerencias IA', 'Se han generado sugerencias. Por favor seleccione o edite la que mejor se ajuste.', 'success');
        } else {
            store.riesgoForm.riesgo_consecuencia = result;
            Swal.fire('Redacción Mejorada', 'La consecuencia ha sido mejorada por la IA.', 'success');
        }

    } catch (error) {
        Swal.fire('Error', 'No se pudo procesar la solicitud. Intente nuevamente.', 'error');
    } finally {
        improvingConsecuencia.value = false;
    }
};

const saveRiesgo = async () => {
    try {
        await store.saveRiesgo();
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Riesgo guardado correctamente',
            timer: 1500,
            showConfirmButton: false
        });
        // Emit saved event if needed, or just rely on store state
    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al guardar el riesgo. Por favor verifique los campos.',
        });
    }
};

const fetchFactores = async () => {
    try {
        // Mock temporal si no existe endpoint, o usar el endpoint real si existe
        // Idealmente esto debería venir de un store o API
        factores.value = [
            { id: 1, nombre: 'Estratégico' },
            { id: 2, nombre: 'Operacional' },
            { id: 3, nombre: 'Corrupción' },
            { id: 4, nombre: 'Cumplimiento' },
            { id: 5, nombre: 'Reputacional' },
            { id: 6, nombre: 'Ambiental' },
            { id: 7, nombre: 'Seguridad' }
        ];
    } catch (e) {
        console.error(e);
    }
};

const openProcesoModal = () => {
    modalProceso.value.open();
};

const onProcesoSelected = (payload) => {
    store.riesgoForm.proceso_id = payload.idValue;
    store.riesgoForm.proceso_nombre = payload.descValue;
};

const clearProceso = () => {
    store.riesgoForm.proceso_id = null;
    store.riesgoForm.proceso_nombre = '';
};

onMounted(() => {
    fetchFactores();
});
</script>

<style scoped>
.form-group label {
    font-weight: bold;
    font-size: 0.9rem;
}

.cursor-pointer {
    cursor: pointer;
}

.text-xs {
    font-size: 0.75rem;
}

.badge-orange {
    background-color: #fd7e14;
    color: white;
}

/* Improved badge styling */
.badge {
    font-size: 0.75em;
    padding: 0.25em 0.5em;
    font-weight: 600;
    border-radius: 0.25rem;
    display: inline-block;
    text-align: center;
    vertical-align: middle;
    min-width: 60px;
}

.badge-danger-custom {
    background-color: #dc3545 !important;
    color: white !important;
    border: 1px solid #dc3545;
}

.badge-high {
    background-color: #fd7e14 !important;
    color: white !important;
    border: 1px solid #fd7e14;
}

.badge-medium {
    background-color: #ffc107 !important;
    color: #212529 !important;
    border: 1px solid #ffc107;
}

.badge-low {
    background-color: #28a745 !important;
    color: white !important;
    border: 1px solid #28a745;
}

.badge-secondary {
    background-color: #6c757d !important;
    color: white !important;
    border: 1px solid #6c757d;
}

.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #dc3545;
    display: flex;
    align-items: center;
}
</style>
<style scoped>
.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #dc3545;
    display: flex;
    align-items: center;
}
</style>

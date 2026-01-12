<template>
    <div class="p-3">
         <!-- HEADER / BREADCRUMB -->
         <div class="header-container mb-3">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Requisito</span>
                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                <span class="text-muted small text-truncate" style="max-width: 300px;">{{ form.id ? 'Detalle y Edición' : 'Nuevo Registro' }}</span>
            </h6>
        </div>

        <div class="mb-3">
             <h6 class="mb-1 font-weight-bold text-uppercase text-dark">Información General</h6>
             <p class="mb-0 text-muted small">
                Defina los detalles principales de la necesidad o expectativa de la parte interesada.
            </p>
        </div>

        <div class="card border-0 shadow-sm mb-3 bg-white">
            <div class="card-body p-3">
                <div class="form-group small">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="font-weight-bold text-dark mb-0">Descripción <span class="text-danger">*</span></label>
                        <small class="text-muted">{{ form.exp_descripcion ? form.exp_descripcion.length : 0 }}/500</small>
                    </div>
                    <textarea v-model="form.exp_descripcion" class="form-control" rows="3" maxlength="500" placeholder="Describa la necesidad o expectativa..."></textarea>
                </div>

                <div class="form-row">
                    <div class="col-md-6 form-group small">
                        <label class="font-weight-bold text-dark">Tipo</label>
                        <select v-model="form.exp_tipo" class="form-control font-weight-bold" :class="form.exp_tipo === 'necesidad' ? 'text-danger' : 'text-info'">
                            <option value="necesidad">Necesidad (Obligatorio)</option>
                            <option value="expectativa">Expectativa (Deseable)</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group small">
                        <label class="font-weight-bold text-dark">Norma Asociada</label>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm btn-block dropdown-toggle text-left bg-white" type="button" id="normasDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Normas ({{ form.exp_normas?.length || 0 }})
                            </button>
                            <div class="dropdown-menu p-2" aria-labelledby="normasDropdown" style="width: 100%;">
                                <div v-for="norma in normasList" :key="norma" class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" class="custom-control-input" :id="'chk-'+norma" :value="norma" v-model="form.exp_normas">
                                    <label class="custom-control-label small" :for="'chk-'+norma">{{ norma }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-3 form-group small">
                        <label class="font-weight-bold text-muted">Criticidad (1-5)</label>
                        <select v-model="form.exp_criticidad" class="form-control">
                            <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group small">
                        <label class="font-weight-bold text-muted">Viabilidad (1-5)</label>
                        <select v-model="form.exp_viabilidad" class="form-control">
                            <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group small">
                        <label class="font-weight-bold text-dark mb-1">Estado Implementación</label>
                        <select v-model="form.exp_estado" class="form-control">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_proceso">En Proceso</option>
                            <option value="implementado">Implementado</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group small">
                    <label class="font-weight-bold text-dark mb-1">Observaciones / Evidencia</label>
                    <textarea v-model="form.exp_observaciones" class="form-control" rows="2" placeholder="Comentarios sobre la implementación..."></textarea>
                </div>
                
                <div class="text-right mt-3">
                     <button class="btn btn-danger btn-sm px-4" @click="save">
                        <i class="fas fa-save mr-1"></i> Guardar Cambios
                     </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { inject } from 'vue';
import Swal from 'sweetalert2';

// Injected from MasterModal
const masterForm = inject('masterForm'); 
const saveAction = inject('saveAction');

// Use masterForm directly (aliased as form for template compatibility)
const form = masterForm;
const normasList = ['ISO 9001', 'ISO 37001', 'ISO 37301', 'ISO 27001', 'ISO 21001', 'Innovación'];

const save = () => {
    saveAction();
};
</script>

<style scoped>
.header-container {
    padding: 0.75rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    border-left: 5px solid #dc3545;
    display: flex;
    align-items: center;
}
</style>

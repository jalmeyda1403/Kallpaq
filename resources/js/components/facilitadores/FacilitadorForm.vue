<template>
    <div class="modal fade" id="facilitadorModal" ref="modalEl" tabindex="-1" aria-labelledby="facilitadorModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ facilitadorStore.isEditing ? 'Editar Facilitador' : 'Nuevo Facilitador' }}</h5>
                    <button type="button" class="close text-white" @click="facilitadorStore.closeFormModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="handleSubmit">
                        <div class="form-group">
                            <label for="user_id">Usuario</label>
                            <select id="user_id" class="form-control" v-model="facilitadorStore.form.user_id" required>
                                <option value="">Seleccione un usuario</option>
                                <option v-for="user in facilitadorStore.users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                            <div v-if="facilitadorStore.errors.user_id" class="text-danger">{{ facilitadorStore.errors.user_id[0] }}</div>
                        </div>

                        <div class="form-group">
                            <label for="cargo">Cargo</label>
                            <select id="cargo" class="form-control" v-model="facilitadorStore.form.cargo" required>
                                <option value="">Seleccione un cargo</option>
                                <option value="facilitador">Facilitador</option>
                                <option value="propietario">Propietario</option>
                            </select>
                            <div v-if="facilitadorStore.errors.cargo" class="text-danger">{{ facilitadorStore.errors.cargo[0] }}</div>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" class="form-control" v-model="facilitadorStore.form.estado" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                            <div v-if="facilitadorStore.errors.estado" class="text-danger">{{ facilitadorStore.errors.estado[0] }}</div>
                        </div>

                        <div class="form-group mt-3">
                            <label>Procesos Asignados</label>
                            <div class="input-group mb-3">
                                <input type="hidden" v-model="selectedProceso.id" />
                                <input type="text" class="form-control" placeholder="Seleccione el Proceso a Asignar"
                                    v-model="selectedProceso.descripcion" readonly />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-dark" @click="openProcesoModal">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" :disabled="!selectedProceso.id"
                                        @click="attachProceso">
                                        <i class="fas fa-link"></i> Asignar
                                    </button>
                                </div>
                            </div>
                            <ul class="list-group">
                                <li v-for="proceso in facilitadorStore.form.procesos" :key="proceso.id" class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ proceso.proceso_nombre }}
                                    <button type="button" class="btn btn-danger btn-sm" @click="detachProceso(proceso.id)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </li>
                                <li v-if="!facilitadorStore.form.procesos || facilitadorStore.form.procesos.length === 0" class="list-group-item text-muted">
                                    No hay procesos asignados.
                                </li>
                            </ul>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="facilitadorStore.closeFormModal">Cancelar</button>
                            <button type="submit" class="btn btn-danger" :disabled="facilitadorStore.loading">
                                <i class="fas fa-save"></i> {{ facilitadorStore.isEditing ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <modal-hijo ref="procesoModal" fetch-url="/buscarProcesos" target-id="id" target-desc="proceso_nombre"
        @update-target="handleProcesoSelection" />
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useFacilitadorStore } from '@/stores/facilitadorStore';
import ModalHijo from '../generales/ModalHijo.vue';
import * as bootstrap from 'bootstrap';

const facilitadorStore = useFacilitadorStore();
const selectedProceso = ref({ id: null, descripcion: '' });
const procesoModal = ref(null);
const modalEl = ref(null);
let modalInstance = null;

onMounted(() => {
    facilitadorStore.fetchUsers();
    if (modalEl.value) {
        modalInstance = new bootstrap.Modal(modalEl.value, {
            backdrop: 'static',
            keyboard: false
        });

        facilitadorStore.$subscribe((mutation, state) => {
            if (state.isFormModalOpen) {
                modalInstance.show();
            } else {
                modalInstance.hide();
            }
        });

        modalEl.value.addEventListener('hidden.bs.modal', () => {
            facilitadorStore.closeFormModal();
        });

        // WORKAROUND: When the child modal closes, focus returns to this parent modal.
        // We re-add the 'modal-open' class to the body to fix the scrollbar issue.
        modalEl.value.addEventListener('focusin', () => {
            document.body.classList.add('modal-open');
        });
    }
});

onUnmounted(() => {
    if (modalInstance) {
        modalInstance.dispose();
    }
});

const handleSubmit = () => {
    facilitadorStore.saveFacilitador();
};

const openProcesoModal = () => {
    procesoModal.value.open();
};

const handleProcesoSelection = ({ idValue, descValue }) => {
    selectedProceso.value.id = idValue;
    selectedProceso.value.descripcion = descValue;
};

const attachProceso = () => {
    if (!selectedProceso.value.id || !facilitadorStore.form.id) return;
    facilitadorStore.attachProceso(facilitadorStore.form.id, selectedProceso.value.id);
    selectedProceso.value = { id: null, descripcion: '' };
};

const detachProceso = (procesoId) => {
    if (!facilitadorStore.form.id) return;
    if (confirm('¿Está seguro de que desea desasociar este proceso?')) {
        facilitadorStore.detachProceso(facilitadorStore.form.id, procesoId);
    }
};
</script>
<style scoped>

.modal-body-scrollable {
    height: 90vh !important;
    /* Ajusta este valor según lo necesites */
    overflow-y: auto;
}
</style>
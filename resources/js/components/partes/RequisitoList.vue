<template>
    <div class="p-0">
        <!-- Toolbar -->
        <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-2 rounded border-bottom">
            <h6 class="text-dark mb-0 small font-weight-bold ml-2">
                <i class="fas fa-list-ul mr-2 text-danger"></i> Requisitos Registrados
            </h6>
            <button class="btn btn-sm btn-primary shadow-sm" @click="$emit('create')">
                <i class="fas fa-plus mr-1"></i> Agregar Nuevo
            </button>
        </div>

        <DataTable :value="requirements" responsiveLayout="scroll" class="p-datatable-sm small-text border rounded" stripedRows
            paginator :rows="7" paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink">
            
            <Column field="exp_descripcion" header="Requisito">
                    <template #body="slotProps">
                    <span class="font-weight-500 text-dark">{{ slotProps.data.exp_descripcion }}</span>
                    <div class="mt-1" v-if="slotProps.data.procesos?.length">
                        <span class="badge badge-light border text-muted">
                            <i class="fas fa-cogs mr-1"></i> {{ slotProps.data.procesos.length }} Procesos
                        </span>
                    </div>
                </template>
            </Column>
            <Column field="exp_tipo" header="Tipo" style="width: 100px;">
                    <template #body="slotProps">
                    <span :class="['badge badge-pill', slotProps.data.exp_tipo === 'necesidad' ? 'badge-danger' : 'badge-info']">
                        {{ slotProps.data.exp_tipo === 'necesidad' ? 'Necesidad' : 'Expectativa' }}
                    </span>
                </template>
            </Column>
            <Column field="exp_estado" header="Estado" style="width: 120px;">
                    <template #body="slotProps">
                    <span class="badge" :class="{
                        'badge-secondary': slotProps.data.exp_estado === 'pendiente',
                        'badge-warning': slotProps.data.exp_estado === 'en_proceso',
                        'badge-success': slotProps.data.exp_estado === 'implementado'
                    }">
                        {{ slotProps.data.exp_estado === 'implementado' ? 'Listo' : (slotProps.data.exp_estado === 'en_proceso' ? 'Proceso' : 'Pendiente') }}
                    </span>
                </template>
            </Column>
            <Column header="Acciones" style="width: 150px; text-align: center">
                <template #body="slotProps">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-light text-dark" @click="emitEdit(slotProps.data, 'compromisos')" title="Gestionar Compromisos">
                            <i class="fas fa-tasks"></i>
                            <span class="badge badge-light ml-1 border" v-if="slotProps.data.compromisos?.length">{{ slotProps.data.compromisos.length }}</span>
                        </button>
                        <button class="btn btn-light text-primary" @click="emitEdit(slotProps.data, 'general')" title="Editar"><i class="fas fa-pencil-alt"></i></button>
                        <button class="btn btn-light text-danger" @click="deleteItem(slotProps.data.id)" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </template>
            </Column>
        </DataTable>
        
        <div v-if="!requirements || requirements.length === 0" class="text-center py-4 bg-white">
            <small class="text-muted font-italic">No hay requisitos registrados para esta parte interesada.</small>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import DataTable from 'primevue/datatable'; 
import Column from 'primevue/column';
import Swal from 'sweetalert2';
import axios from 'axios'; 
import { useParteStore } from '@/stores/parteInteresadaStore'; 

const props = defineProps({
    requirements: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['create', 'edit', 'delete']);
const store = useParteStore();

const emitEdit = (req, tab = 'general') => {
    emit('edit', req, tab);
};

const deleteItem = async (id) => {
     Swal.fire({
        title: '¿Eliminar requisito?',
        text: "Se eliminarán también los compromisos asociados.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar'
    }).then(async (result) => {
        if (result.isConfirmed) {
             try {
                await store.deleteExpectativa(id);
                // Parent should refresh
             } catch(e) { /* handled in store */ }
        }
    });
};
</script>

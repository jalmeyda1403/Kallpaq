<template>
    <Dialog :visible="show" modal :header="modalTitle" @hide="close">
        <form @submit.prevent="submitForm">
            <div class="p-fluid">
                <div class="field">
                    <label for="descripcion">Descripción de Acción</label>
                    <Textarea id="descripcion" v-model="form.accion_descripcion" rows="3" />
                </div>
                <div class="field">
                    <label for="responsable">Responsable</label>
                    <Dropdown id="responsable" :options="users" optionLabel="name" optionValue="id"
                        v-model="form.accion_responsable_id" placeholder="Selecciona" />
                </div>
                <div class="field">
                    <label for="fechaPlanificada">Fecha Planificada</label>
                    <Calendar id="fechaPlanificada" v-model="form.accion_fecha_planificada" dateFormat="yy-mm-dd" showIcon />
                </div>
                <div class="field">
                    <label for="fechaReal">Fecha Real</label>
                    <Calendar id="fechaReal" v-model="form.accion_fecha_real" dateFormat="yy-mm-dd" showIcon />
                </div>
                <div class="field">
                    <label for="estado">Estado</label>
                    <Dropdown id="estado" :options="['planificada','en progreso','finalizada','cancelada']"
                        v-model="form.accion_estado" placeholder="Selecciona" />
                </div>
            </div>
            <div class="flex justify-content-end mt-3">
                <Button label="Cancelar" icon="pi pi-times" class="p-button-text mr-2" @click="close" />
                <Button label="Guardar" icon="pi pi-check" type="submit" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';
import { route } from 'ziggy-js';

const props = defineProps({
    show: Boolean,
    sncId: Number,
    accion: Object, // null for create, object for edit
});

const emit = defineEmits(['update:show', 'saved']);

const toast = useToast();

const form = ref({
    accion_descripcion: '',
    accion_responsable_id: null,
    accion_fecha_planificada: null,
    accion_fecha_real: null,
    accion_estado: 'planificada',
});

const users = ref([]);

const modalTitle = ref('Nueva Acción');

const loadUsers = async () => {
    try {
        const res = await axios.get(route('api.usuarios.index'));
        users.value = res.data;
    } catch (e) {
        console.error(e);
    }
};

onMounted(() => {
    loadUsers();
});

watch(() => props.accion, (newVal) => {
    if (newVal) {
        modalTitle.value = 'Editar Acción';
        form.value = { ...newVal };
    } else {
        modalTitle.value = 'Nueva Acción';
        form.value = {
            accion_descripcion: '',
            accion_responsable_id: null,
            accion_fecha_planificada: null,
            accion_fecha_real: null,
            accion_estado: 'planificada',
        };
    }
});

const close = () => {
    emit('update:show', false);
};

const submitForm = async () => {
    try {
        if (props.accion) {
            await axios.put(route('api.snc.acciones.update', props.accion.id), form.value);
        } else {
            await axios.post(route('api.salidas-nc.' + props.sncId + '.acciones.store'), form.value);
        }
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Acción guardada', life: 3000 });
        emit('saved');
        close();
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo guardar la acción', life: 5000 });
        console.error(e);
    }
};
</script>

<style scoped>
/* Custom styles if needed */
</style>

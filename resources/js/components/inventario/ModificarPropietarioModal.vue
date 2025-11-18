<template>
    <div>
        <div class="form-group" v-if="inventarioStore.selectedProceso">
            <label for="propietario">Nuevo Propietario</label>
            <select id="propietario" v-model="inventarioStore.selectedProceso.id_ouo_propietario" class="form-control">
                <option value="" disabled>Seleccione un propietario</option>
                <option v-for="user in inventarioStore.users" :key="user.id" :value="user.id">{{ user.name }}</option>
            </select>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-secondary mr-2" @click="inventarioStore.closeModifyOwnerModal">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="inventarioStore.savePropietario">Guardar</button>
        </div>
    </div>
</template>

<script setup>
import { useInventarioStore } from '@/stores/inventarioStore';

const inventarioStore = useInventarioStore();
</script>

<template>
    <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Asignar Permisos a: {{ user?.name }}</h5>
                    <button type="button" class="close" aria-label="Close" @click="close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Contenido del PermisosModal para {{ user?.name }}</p>
                    <!-- Actual permission assignment UI will go here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" @click="close">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" @click="savePermissions">Guardar Permisos</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Modal } from 'bootstrap';

const props = defineProps({
    user: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['permissions-updated', 'close']);

const modalEl = ref(null);
let modalInstance = null;

onMounted(() => {
    if (modalEl.value) {
        modalInstance = new Modal(modalEl.value, {
            backdrop: 'static',
            keyboard: false
        });
    }
});

watch(() => props.user, (newUser) => {
    if (newUser) {
        // Load permissions for newUser
        console.log('Loading permissions for user:', newUser.name);
    }
});

const open = () => {
    modalInstance.show();
};

const close = () => {
    modalInstance.hide();
    emit('close');
};

const savePermissions = () => {
    // Implement actual save logic here
    console.log('Saving permissions for user:', props.user.name);
    emit('permissions-updated');
    close();
};

defineExpose({
    open,
    close
});
</script>

<style scoped>
/* Add any specific styles for this component here */
</style>

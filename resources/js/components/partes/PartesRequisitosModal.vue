<template>
    <!-- Wrapper only, implementation moved to RequisitoMasterModal -->
    <RequisitoMasterModal 
        ref="masterModalRef" 
        :parte-id="parte?.id" 
        :filter-context="filterContext"
        @close="emitClose"
    />
</template>

<script setup>
import { ref, onMounted, defineProps, watch, defineEmits } from 'vue';
import RequisitoMasterModal from './RequisitoMasterModal.vue';

const props = defineProps({
    visible: Boolean,
    parte: Object,
    filterContext: {
        type: String,
        default: 'all'
    }
});

const emit = defineEmits(['close']);
const masterModalRef = ref(null);

watch(() => props.visible, (val) => {
    if (val) {
        masterModalRef.value?.open();
    } else {
        masterModalRef.value?.closeModal();
    }
});

const emitClose = () => {
    emit('close');
};

</script>

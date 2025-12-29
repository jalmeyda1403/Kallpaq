<template>
    <div class="error-state" :class="[sizeClass, variantClass]">
        <div class="error-icon">
            <i :class="iconClass"></i>
        </div>
        <h4 class="error-title">{{ title }}</h4>
        <p class="error-message" v-if="message">{{ message }}</p>
        <div class="error-details" v-if="details && showDetails">
            <code>{{ details }}</code>
        </div>
        <div class="error-actions">
            <button v-if="showRetry" class="btn btn-outline-secondary me-2" @click="$emit('retry')">
                <i class="fas fa-redo me-1"></i> Reintentar
            </button>
            <button v-if="details" class="btn btn-link btn-sm" @click="toggleDetails">
                {{ showDetails ? 'Ocultar detalles' : 'Ver detalles' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Ha ocurrido un error'
    },
    message: {
        type: String,
        default: 'No se pudieron cargar los datos. Por favor, intente nuevamente.'
    },
    details: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: 'fas fa-exclamation-triangle'
    },
    variant: {
        type: String,
        default: 'danger', // 'danger', 'warning', 'info'
        validator: (value) => ['danger', 'warning', 'info'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    showRetry: {
        type: Boolean,
        default: true
    }
});

defineEmits(['retry']);

const showDetails = ref(false);

const sizeClass = computed(() => `error-${props.size}`);
const variantClass = computed(() => `error-${props.variant}`);
const iconClass = computed(() => `${props.icon} error-icon-glyph`);

const toggleDetails = () => {
    showDetails.value = !showDetails.value;
};
</script>

<style scoped>
.error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 50px 20px;
    text-align: center;
    border-radius: 12px;
}

.error-danger {
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
    border: 1px solid #fc8181;
}

.error-warning {
    background: linear-gradient(135deg, #fffaf0 0%, #feebc8 100%);
    border: 1px solid #f6ad55;
}

.error-info {
    background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);
    border: 1px solid #63b3ed;
}

.error-sm {
    padding: 25px 15px;
}

.error-lg {
    padding: 70px 30px;
}

.error-icon {
    margin-bottom: 16px;
}

.error-icon-glyph {
    font-size: 3.5rem;
}

.error-danger .error-icon-glyph {
    color: #e53e3e;
}

.error-warning .error-icon-glyph {
    color: #dd6b20;
}

.error-info .error-icon-glyph {
    color: #3182ce;
}

.error-sm .error-icon-glyph {
    font-size: 2.5rem;
}

.error-title {
    font-weight: 600;
    margin-bottom: 8px;
}

.error-danger .error-title {
    color: #c53030;
}

.error-warning .error-title {
    color: #c05621;
}

.error-info .error-title {
    color: #2b6cb0;
}

.error-message {
    color: #4a5568;
    max-width: 450px;
    margin-bottom: 16px;
    line-height: 1.5;
}

.error-details {
    background: rgba(0,0,0,0.05);
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 16px;
    max-width: 100%;
    overflow-x: auto;
}

.error-details code {
    font-size: 0.85rem;
    color: #742a2a;
    white-space: pre-wrap;
    word-break: break-word;
}

.error-actions {
    margin-top: 8px;
}
</style>

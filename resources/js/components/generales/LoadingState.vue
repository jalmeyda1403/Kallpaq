<template>
    <div class="loading-state" :class="sizeClass">
        <div class="loading-spinner">
            <div class="spinner-border" :class="spinnerClass" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
        <p class="loading-text" v-if="text">{{ text }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    text: {
        type: String,
        default: 'Cargando datos...'
    },
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg'
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    variant: {
        type: String,
        default: 'primary'
    }
});

const sizeClass = computed(() => `loading-${props.size}`);
const spinnerClass = computed(() => `text-${props.variant}`);
</script>

<style scoped>
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
}

.loading-sm {
    padding: 20px 10px;
}

.loading-sm .spinner-border {
    width: 1.5rem;
    height: 1.5rem;
}

.loading-md .spinner-border {
    width: 2.5rem;
    height: 2.5rem;
}

.loading-lg {
    padding: 60px 20px;
}

.loading-lg .spinner-border {
    width: 3.5rem;
    height: 3.5rem;
}

.loading-text {
    margin-top: 16px;
    color: #6c757d;
    font-size: 0.95rem;
}

.loading-sm .loading-text {
    font-size: 0.85rem;
    margin-top: 10px;
}
</style>

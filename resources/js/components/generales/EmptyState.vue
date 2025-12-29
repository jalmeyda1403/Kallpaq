<template>
    <div class="empty-state" :class="sizeClass">
        <div class="empty-icon">
            <i :class="iconClass"></i>
        </div>
        <h4 class="empty-title">{{ title }}</h4>
        <p class="empty-description" v-if="description">{{ description }}</p>
        <div class="empty-action" v-if="actionText">
            <button class="btn" :class="actionClass" @click="$emit('action')">
                <i :class="actionIcon" v-if="actionIcon"></i>
                {{ actionText }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'No hay datos disponibles'
    },
    description: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: 'fas fa-inbox'
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    actionText: {
        type: String,
        default: ''
    },
    actionIcon: {
        type: String,
        default: 'fas fa-plus'
    },
    actionVariant: {
        type: String,
        default: 'primary'
    }
});

defineEmits(['action']);

const sizeClass = computed(() => `empty-${props.size}`);
const iconClass = computed(() => `${props.icon} empty-icon-glyph`);
const actionClass = computed(() => `btn-${props.actionVariant}`);
</script>

<style scoped>
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    border: 2px dashed #dee2e6;
}

.empty-sm {
    padding: 30px 15px;
}

.empty-lg {
    padding: 80px 30px;
}

.empty-icon {
    margin-bottom: 20px;
}

.empty-icon-glyph {
    font-size: 4rem;
    color: #adb5bd;
}

.empty-sm .empty-icon-glyph {
    font-size: 2.5rem;
}

.empty-lg .empty-icon-glyph {
    font-size: 5rem;
}

.empty-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 8px;
}

.empty-sm .empty-title {
    font-size: 1rem;
}

.empty-description {
    color: #6c757d;
    max-width: 400px;
    margin-bottom: 20px;
    line-height: 1.5;
}

.empty-action {
    margin-top: 10px;
}

.empty-action .btn {
    padding: 10px 24px;
    font-weight: 500;
}

.empty-action .btn i {
    margin-right: 8px;
}
</style>

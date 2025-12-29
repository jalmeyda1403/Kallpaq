<template>
    <div class="skeleton-wrapper" :class="wrapperClass">
        <!-- Table Skeleton -->
        <template v-if="type === 'table'">
            <div class="skeleton-table">
                <div class="skeleton-header">
                    <div class="skeleton-line skeleton-header-cell" 
                         v-for="n in columns" 
                         :key="'h-'+n"
                         :style="{ width: getColumnWidth(n) }">
                    </div>
                </div>
                <div class="skeleton-row" v-for="row in rows" :key="'r-'+row">
                    <div class="skeleton-line skeleton-cell" 
                         v-for="n in columns" 
                         :key="'c-'+row+'-'+n"
                         :style="{ width: getColumnWidth(n) }">
                    </div>
                </div>
            </div>
        </template>

        <!-- Card Skeleton -->
        <template v-else-if="type === 'card'">
            <div class="skeleton-card">
                <div class="skeleton-card-header">
                    <div class="skeleton-avatar" v-if="showAvatar"></div>
                    <div class="skeleton-card-title">
                        <div class="skeleton-line" style="width: 60%;"></div>
                        <div class="skeleton-line" style="width: 40%; height: 10px;"></div>
                    </div>
                </div>
                <div class="skeleton-card-body">
                    <div class="skeleton-line" v-for="n in lines" :key="n" 
                         :style="{ width: getLineWidth(n) }"></div>
                </div>
            </div>
        </template>

        <!-- Form Skeleton -->
        <template v-else-if="type === 'form'">
            <div class="skeleton-form">
                <div class="skeleton-form-group" v-for="n in fields" :key="n">
                    <div class="skeleton-label"></div>
                    <div class="skeleton-input"></div>
                </div>
            </div>
        </template>

        <!-- Chart/Dashboard Skeleton -->
        <template v-else-if="type === 'chart'">
            <div class="skeleton-chart">
                <div class="skeleton-chart-title"></div>
                <div class="skeleton-chart-area">
                    <div class="skeleton-bar" v-for="n in 6" :key="n" 
                         :style="{ height: getBarHeight(n) }"></div>
                </div>
            </div>
        </template>

        <!-- Default Lines Skeleton -->
        <template v-else>
            <div class="skeleton-line" 
                 v-for="n in lines" 
                 :key="n" 
                 :style="{ width: getLineWidth(n), height: height + 'px' }">
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'lines', // 'lines', 'table', 'card', 'form', 'chart'
        validator: (value) => ['lines', 'table', 'card', 'form', 'chart'].includes(value)
    },
    lines: {
        type: Number,
        default: 3
    },
    rows: {
        type: Number,
        default: 5
    },
    columns: {
        type: Number,
        default: 4
    },
    fields: {
        type: Number,
        default: 4
    },
    height: {
        type: Number,
        default: 16
    },
    showAvatar: {
        type: Boolean,
        default: false
    },
    animated: {
        type: Boolean,
        default: true
    }
});

const wrapperClass = computed(() => ({
    'skeleton-animated': props.animated
}));

const getLineWidth = (n) => {
    const widths = ['100%', '85%', '70%', '90%', '60%'];
    return widths[(n - 1) % widths.length];
};

const getColumnWidth = (n) => {
    if (n === 1) return '15%';
    if (n === props.columns) return '10%';
    return `${75 / (props.columns - 2)}%`;
};

const getBarHeight = (n) => {
    const heights = ['60%', '80%', '45%', '90%', '70%', '55%'];
    return heights[(n - 1) % heights.length];
};
</script>

<style scoped>
.skeleton-wrapper {
    width: 100%;
}

.skeleton-line {
    background: linear-gradient(90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
    background-size: 200% 100%;
    border-radius: 4px;
    margin-bottom: 12px;
    height: 16px;
}

.skeleton-animated .skeleton-line,
.skeleton-animated .skeleton-avatar,
.skeleton-animated .skeleton-label,
.skeleton-animated .skeleton-input,
.skeleton-animated .skeleton-bar,
.skeleton-animated .skeleton-chart-title,
.skeleton-animated .skeleton-chart-area {
    animation: skeleton-loading 1.5s infinite ease-in-out;
}

@keyframes skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Table Skeleton */
.skeleton-table {
    width: 100%;
}

.skeleton-header {
    display: flex;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 4px 4px 0 0;
    margin-bottom: 0;
}

.skeleton-header-cell {
    height: 20px;
    margin-bottom: 0;
}

.skeleton-row {
    display: flex;
    gap: 12px;
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
}

.skeleton-cell {
    height: 16px;
    margin-bottom: 0;
}

/* Card Skeleton */
.skeleton-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.skeleton-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    border-bottom: 1px solid #e9ecef;
}

.skeleton-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
    background-size: 200% 100%;
    flex-shrink: 0;
}

.skeleton-card-title {
    flex: 1;
}

.skeleton-card-title .skeleton-line {
    margin-bottom: 8px;
}

.skeleton-card-title .skeleton-line:last-child {
    margin-bottom: 0;
}

.skeleton-card-body {
    padding: 16px;
}

/* Form Skeleton */
.skeleton-form {
    width: 100%;
}

.skeleton-form-group {
    margin-bottom: 20px;
}

.skeleton-label {
    width: 30%;
    height: 14px;
    background: linear-gradient(90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
    background-size: 200% 100%;
    border-radius: 4px;
    margin-bottom: 8px;
}

.skeleton-input {
    width: 100%;
    height: 38px;
    background: linear-gradient(90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
    background-size: 200% 100%;
    border-radius: 4px;
}

/* Chart Skeleton */
.skeleton-chart {
    width: 100%;
    padding: 16px;
}

.skeleton-chart-title {
    width: 40%;
    height: 24px;
    background: linear-gradient(90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
    background-size: 200% 100%;
    border-radius: 4px;
    margin-bottom: 20px;
}

.skeleton-chart-area {
    display: flex;
    align-items: flex-end;
    gap: 16px;
    height: 150px;
    padding: 16px 0;
    border-bottom: 2px solid #e9ecef;
}

.skeleton-bar {
    flex: 1;
    background: linear-gradient(90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
    background-size: 200% 100%;
    border-radius: 4px 4px 0 0;
}
</style>

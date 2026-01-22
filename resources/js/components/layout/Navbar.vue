<template>
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 shadow-sm"
        style="border-bottom: 1px solid #eee !important;">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#" role="button" @click.prevent="uiStore.toggleSidebar">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { useUIStore } from '../../stores/uiStore';
import { useAuthStore } from '../../stores/authStore';

const uiStore = useUIStore();
const authStore = useAuthStore();
</script>

<template>
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="#" role="button" @click.prevent="uiStore.toggleSidebar">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block" v-if="authStore.isAuthenticated">
                <router-link to="/home" class="nav-link">Home</router-link>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">Contact</a>
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

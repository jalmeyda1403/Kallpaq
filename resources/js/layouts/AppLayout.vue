<template>
    <div class="wrapper">
        <Navbar />
        <Sidebar />

        <div class="content-wrapper">
            <router-view></router-view>
        </div>

        <footer class="main-footer bg-secondary">
            <strong>¡Mejorando Juntos!</strong>
            <div class="float-right d-none d-sm-inline-block">
                Contraloría General de la República
            </div>
        </footer>

        <ChatbotWidget />
    </div>
</template>

<script setup>
import Navbar from '../components/layout/Navbar.vue';
import Sidebar from '../components/layout/Sidebar.vue';
import ChatbotWidget from '../components/chatbot/ChatbotWidget.vue';
import { onMounted } from 'vue';

// Initialize AdminLTE plugins if needed, or rely on them being loaded globally
onMounted(() => {
    // If using AdminLTE's jQuery plugins, they might need re-initialization on route change
    // But since we are moving to Vue, we should try to minimize reliance on them.
    // However, the body class 'sidebar-mini' etc are set in master.blade.php
});
</script>

import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useUIStore = defineStore('ui', () => {
    const isSidebarCollapsed = ref(false);

    const toggleSidebar = () => {
        isSidebarCollapsed.value = !isSidebarCollapsed.value;
        if (isSidebarCollapsed.value) {
            document.body.classList.add('sidebar-collapse');
        } else {
            document.body.classList.remove('sidebar-collapse');
        }
    };

    return {
        isSidebarCollapsed,
        toggleSidebar
    };
});

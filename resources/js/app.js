import './bootstrap';
import 'select2'; // Import Select2 after jQuery is made global


import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './components/App.vue'; // This is the root component for the SPA

// PrimeVue Imports
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice'; // Import ToastService
import Toast from 'primevue/toast'; // Import Toast component
import 'primevue/resources/themes/saga-blue/theme.css'; // Choose a theme
import 'primevue/resources/primevue.min.css'; // Core CSS
import 'primeicons/primeicons.css'; // Icons

// Componentes
import ProcesoModal from './components/procesos/ProcesoModal.vue';
import DocumentoModal from './components/documentos/DocumentoModal.vue';
import HallazgoModal from './components/hallazgos/HallazgoModal.vue';
import PdfModal from './components/generales/PdfModal.vue';
import RequerimientoAsignacionModal from './components/requerimientos/RequerimientoAsignacionModal.vue';
import RequerimientoEvaluacionModal from './components/requerimientos/RequerimientoEvaluacionModal.vue';
import RequerimientoSeguimientoModal from './components/requerimientos/RequerimientoSeguimientoModal.vue';
import RequerimientoAvanceModal from './components/requerimientos/RequerimientoAvanceModal.vue';
// import RequerimientoFormWizard from './components/requerimientos/RequerimientoFormWizard.vue'; // This is used as a route component, not global
import EvidenciasModal from './components/requerimientos/EvidenciasModal.vue';
import ResumenGeneral from './components/dashboard/ResumenGeneral.vue';
import ResumenGrafico from './components/dashboard/ResumenGrafico.vue';
import ResumenAlertas from './components/dashboard/ResumenAlertas.vue';
import ResumenEspecialistas from './components/dashboard/ResumenEspecialistas.vue';
import DetalleEspecialistaModal from './components/dashboard/DetalleEspecialistaModal.vue';

import router from './router/index.js';

const pinia = createPinia();
const rootAppElement = document.getElementById('app');

if (rootAppElement) {
    let vueApp;

    if (rootAppElement.hasAttribute('data-spa')) {
        // Full SPA mode: mount the root App component and use the router
        vueApp = createApp(App);
        vueApp.use(router);
    } else {
        // Component-only mode: mount an empty root component, no router
        vueApp = createApp({});
    }

    vueApp.use(pinia);
    vueApp.use(PrimeVue); // Add this line
    vueApp.use(ToastService); // Register ToastService
    vueApp.component('Toast', Toast); // Register Toast component globally

    // Register global components
    vueApp.component('proceso-modal', ProcesoModal);
    vueApp.component('documento-modal', DocumentoModal);
    vueApp.component('hallazgo-modal', HallazgoModal);
    vueApp.component('pdf-modal', PdfModal);
    vueApp.component('requerimiento-asignacion-modal', RequerimientoAsignacionModal);
    vueApp.component('requerimiento-evaluacion-modal', RequerimientoEvaluacionModal);
    vueApp.component('requerimiento-seguimiento-modal', RequerimientoSeguimientoModal);
    vueApp.component('requerimiento-avance-modal', RequerimientoAvanceModal);
    vueApp.component('evidencias-modal', EvidenciasModal);
    vueApp.component('resumen-general', ResumenGeneral);
    vueApp.component('resumen-grafico', ResumenGrafico);
    vueApp.component('resumen-alertas', ResumenAlertas);
    vueApp.component('resumen-especialistas', ResumenEspecialistas);
    vueApp.component('detalle-especialista-modal', DetalleEspecialistaModal);

    vueApp.mount('#app');
}

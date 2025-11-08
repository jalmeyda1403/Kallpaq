import './bootstrap';
import 'select2'; // Import Select2 after jQuery is made global


import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './components/App.vue';

// Componentes
import ProcesoModal from './components/procesos/ProcesoModal.vue';
import DocumentoModal from './components/documentos/DocumentoModal.vue';
import HallazgoModal from './components/hallazgos/HallazgoModal.vue';
import PdfModal from './components/generales/PdfModal.vue';
import RequerimientoAsignacionModal from './components/requerimientos/RequerimientoAsignacionModal.vue';
import RequerimientoEvaluacionModal from './components/requerimientos/RequerimientoEvaluacionModal.vue';
import RequerimientoSeguimientoModal from './components/requerimientos/RequerimientoSeguimientoModal.vue';
import RequerimientoAvanceModal from './components/requerimientos/RequerimientoAvanceModal.vue';
import RequerimientoFormWizard from './components/requerimientos/RequerimientoFormWizard.vue';
import EvidenciasModal from './components/requerimientos/EvidenciasModal.vue';
import ResumenGeneral from './components/dashboard/ResumenGeneral.vue';
import ResumenGrafico from './components/dashboard/ResumenGrafico.vue';
import ResumenAlertas from './components/dashboard/ResumenAlertas.vue';
import ResumenEspecialistas from './components/dashboard/ResumenEspecialistas.vue';
import DetalleEspecialistaModal from './components/dashboard/DetalleEspecialistaModal.vue';

import router from './router/index.js';

const app = createApp(App);
const pinia = createPinia();

// Registrar componentes
app.component('proceso-modal', ProcesoModal);
app.component('documento-modal', DocumentoModal);
app.component('hallazgo-modal', HallazgoModal);
app.component('pdf-modal', PdfModal);
app.component('requerimiento-asignacion-modal', RequerimientoAsignacionModal);
app.component('requerimiento-evaluacion-modal', RequerimientoEvaluacionModal);
app.component('requerimiento-seguimiento-modal', RequerimientoSeguimientoModal);
app.component('requerimiento-avance-modal', RequerimientoAvanceModal);

app.component('evidencias-modal', EvidenciasModal);
app.component('resumen-general', ResumenGeneral);
app.component('resumen-grafico', ResumenGrafico);
app.component('resumen-alertas', ResumenAlertas);
app.component('resumen-especialistas', ResumenEspecialistas);
app.component('detalle-especialista-modal', DetalleEspecialistaModal);

app.use(router);
app.use(pinia);
app.mount('#app');

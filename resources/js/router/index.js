import { createRouter, createWebHistory } from 'vue-router';

// Importa los componentes de tus vistas
import DocumentoIndex from '@/components/documentos/DocumentoIndex.vue';
import HallazgosIndex from '@/components/hallazgos/HallazgoIndex.vue'; 
import RequerimientosIndex from '@/components/requerimientos/RequerimientosIndex.vue';
import RequerimientosIndexMe from '@/components/requerimientos/RequerimientosIndexMe.vue';
import RequerimientoFormWizard from '@/components/requerimientos/RequerimientoFormWizard.vue';


const routes = [
    {
        path: '/documentos', 
        name: 'documentos.index',
        component: DocumentoIndex,
    },
     {
        path: '/mejora', 
        name: 'hallazgos.index',
        component: HallazgosIndex,
    },
    {
        path: '/requerimientos/index',
        name: 'requerimientos.index',
        component: RequerimientosIndex,
    },
    {
        path: '/requerimientos/crear',
        name: 'requerimientos.create',
        component: RequerimientoFormWizard,
    },
    {
        path: '/requerimientos/:id/edit',
        name: 'requerimientos.edit',
        component: RequerimientoFormWizard,
        props: route => ({ requerimientoId: route.params.id })
    },
    {
        path: '/mis-requerimientos',
        name: 'requerimientos.mine',
        component: RequerimientosIndexMe,
    },
   
]

const router = createRouter({
    history: createWebHistory('/vue/'),
    routes,
});

export default router;
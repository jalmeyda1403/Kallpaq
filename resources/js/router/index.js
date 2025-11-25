import { createRouter, createWebHistory } from 'vue-router';

// Importa los componentes de tus vistas
import DocumentoIndex from '@/components/documentos/DocumentoIndex.vue';
import HallazgosIndex from '@/components/hallazgos/HallazgoIndex.vue';
import RequerimientosIndex from '@/components/requerimientos/RequerimientosIndex.vue';
import RequerimientosIndexMe from '@/components/requerimientos/RequerimientosIndexMe.vue';
import RequerimientoFormWizard from '@/components/requerimientos/RequerimientoFormWizard.vue';
import UsuariosIndex from '@/components/administracion/UsuariosIndex.vue'; // Import the new component
import InventarioPublico from '@/components/inventario/InventarioPublico.vue'; // Importa el componente público del inventario
import InventarioIndex from '@/components/inventario/InventarioIndex.vue'; // Importa el componente de Gestión del Inventario
import MisHallazgos from '@/components/hallazgos/MisHallazgos.vue'; // Import MisHallazgos

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
        path: '/mis-hallazgos', // New route for Mis Hallazgos
        name: 'hallazgos.mine.vue',
        component: MisHallazgos,
    },
    {
        path: '/bandeja-eficacia',
        name: 'hallazgos.eficacia',
        component: () => import('@/components/hallazgos/BandejaEficacia.vue'),
    },
    {
        path: '/acciones/imprimir/:hallazgoId',
        name: 'acciones.imprimir',
        component: () => import('@/components/acciones/PlanAccionImprimirPage.vue'),
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
    {
        path: '/requerimientos/seguimiento',
        name: 'requerimientos.seguimiento',
        component: () => import('@/components/requerimientos/RequerimientosSeguimiento.vue'),
    },
    {
        path: '/obligaciones',
        name: 'obligaciones.index',
        component: () => import('@/components/obligaciones/ObligacionesIndex.vue'),
    },
    {
        path: '/administracion/asignacion-ouos', // Changed path to reflect new location
        name: 'administracion.asignacion-ouos.index', // Changed name
        component: () => import('@/components/administracion/AsignacionUsuariosIndex.vue'), // Updated path
    },
    {
        path: '/administracion/usuarios', // New path for user management
        name: 'administracion.usuarios.index',
        component: UsuariosIndex,
    },
    {
        path: '/hallazgos/:hallazgoId/acciones',
        name: 'acciones.index',
        component: () => import('@/components/acciones/AccionesIndex.vue'),
        props: true
    },
    {
        path: '/smp-ouo',
        name: 'smp.ouo.index',
        component: () => import('@/components/hallazgos/HallazgoIndex.vue'), // Reutilizando el componente existente (en singular)
        props: { fromOuo: true } // Prop para indicar que se está accediendo desde OUO
    },

    {
        path: '/inventario-publico/:id',
        name: 'inventario.publico',
        component: InventarioPublico,
        props: true // Permitir que el ID del inventario llegue como prop
    },
    {
        path: '/inventario-gestion',
        name: 'inventario.gestion',
        component: InventarioIndex,
    },
    {
        path: '/salidas-nc',
        name: 'salidas-nc.index',
        component: () => import('@/components/salidas-nc/SalidasNCIndex.vue'),
    },
    {
        path: '/dashboard/mejora',
        name: 'dashboard.mejora',
        component: () => import('@/components/dashboard/DashboardMejora.vue'),
    },
    {
        path: '/riesgos/index',
        name: 'riesgos.index',
        component: () => import('@/components/riesgos/RiesgosIndex.vue'),
    },
    {
        path: '/riesgos/mis-riesgos',
        name: 'riesgos.mine',
        component: () => import('@/components/riesgos/MisRiesgos.vue'),
    },
    {
        path: '/indicadores',
        name: 'indicadores.index',
        component: () => import('@/components/indicadores/IndicadoresMain.vue'),
    },
]

const router = createRouter({
    history: createWebHistory('/vue/'),
    routes,
});

export default router;
import { createRouter, createWebHistory } from 'vue-router';

// Importa los componentes de tus vistas
import DocumentoIndex from '@/components/documentos/DocumentoIndex.vue';
import HallazgosIndex from '@/components/hallazgos/HallazgoIndex.vue';
import RequerimientosIndex from '@/components/requerimientos/RequerimientosIndex.vue';
import RequerimientosIndexMe from '@/components/requerimientos/RequerimientosIndexMe.vue';
import RequerimientoFormWizard from '@/components/requerimientos/RequerimientoFormWizard.vue';
import UsuariosIndex from '@/components/administracion/UsuariosIndex.vue'; // Import the new component
import InventarioProcesos from '@/components/inventario/InventarioProcesos.vue'; // Importa el nuevo componente


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
        path: '/inventario/:id',
        name: 'inventario.procesos',
        component: InventarioProcesos,
        props: true // Permitir que el ID del inventario llegue como prop
    },
]

const router = createRouter({
    history: createWebHistory('/vue/'),
    routes,
});

export default router;
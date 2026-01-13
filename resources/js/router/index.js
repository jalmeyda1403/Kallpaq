import { createRouter, createWebHistory } from 'vue-router';

// Layouts
import AppLayout from '@/layouts/AppLayout.vue';

// Components
import DocumentoIndex from '@/components/documentos/DocumentoIndex.vue';
import HallazgosIndex from '@/components/hallazgos/HallazgoIndex.vue';
import RequerimientosIndex from '@/components/requerimientos/RequerimientosIndex.vue';
import MisRequerimientos from '@/components/requerimientos/MisRequerimientos.vue';
import RequerimientoFormWizard from '@/components/requerimientos/RequerimientoFormWizard.vue';
import UsuariosIndex from '@/components/administracion/UsuariosIndex.vue';
import ProcesosIndex from '../components/procesos/ProcesosIndex.vue';
import Login from '../components/auth/Login.vue';
import InventarioPublico from '@/components/inventario/InventarioPublico.vue';
import InventarioIndex from '@/components/inventario/InventarioIndex.vue';
import MisHallazgos from '@/components/hallazgos/MisHallazgos.vue';
import BladeViewPlaceholder from '@/components/BladeViewPlaceholder.vue';
import Home from '@/components/Home.vue';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true }
    },
    {
        path: '/',
        component: AppLayout,
        children: [
            // Home / Dashboard
            {
                path: '',
                redirect: '/inventario-publico/0' // Default redirect
            },

            {
                path: 'home',
                name: 'home',
                component: Home
            },

            // Documentación por Procesos
            {
                path: 'inventario-publico/:id',
                name: 'inventario.publico',
                component: InventarioPublico,
                props: true
            },
            {
                path: 'inventario-publico/explorador/:id',
                name: 'inventario.explorador',
                component: () => import('@/components/inventario/InventarioExplorador.vue'),
                props: true
            },
            {
                path: 'procesos',
                name: 'procesos.index',
                component: ProcesosIndex
            },
            {
                path: 'procesos/:id/subprocesos',
                name: 'procesos.subprocesos',
                component: ProcesosIndex
            },
            {
                path: 'procesos/mapa',
                name: 'procesos.mapa',
                component: () => import('@/components/procesos/ProcesosMapa.vue')
            },
            {
                path: 'procesos/index',
                redirect: { name: 'procesos.index' }
            },
            {
                path: 'auditor/listado',
                name: 'auditores.index',
                component: () => import('@/components/auditoria/AuditoresIndex.vue')
            },
            {
                path: 'auditoria/normas',
                name: 'normas.index',
                component: () => import('@/components/normas/NormasISOIndex.vue')
            },
            // Auditoría
            {
                path: 'programa',
                name: 'programa.index',
                component: () => import('@/components/auditoria/ProgramaAuditoriaIndex.vue')
            },
            {
                path: 'auditoria/programa/:id/gantt',
                name: 'auditoria.gantt',
                component: () => import('@/components/auditoria/ProgramaAuditoriaGantt.vue'),
                props: true
            },
            // Placeholder for Specific Plan Manager (Agenda)
            /* {
                path: 'auditoria/especifica/:id/plan',
                name: 'auditoria.especifica.plan',
                component: () => import('@/components/auditoria/PlanAuditoriaManager.vue'),
                props: true
            }, */
            {
                path: 'documentos/listado',
                name: 'documentos.listado',
                component: () => import('@/components/documentos/DocumentoPublicoIndex.vue')
            },

            // Gestión de Requerimientos
            {
                path: 'requerimientos/index',
                name: 'requerimientos.index',
                component: RequerimientosIndex,
            },
            {
                path: 'requerimientos/crear',
                name: 'requerimientos.create',
                component: RequerimientoFormWizard,
            },
            {
                path: 'requerimientos/:id/edit',
                name: 'requerimientos.edit',
                component: RequerimientoFormWizard,
                props: route => ({ requerimientoId: route.params.id })
            },
            {
                path: 'mis-requerimientos',
                name: 'requerimientos.mine',
                component: MisRequerimientos,
            },
            {
                path: 'requerimientos/asignados/:rol',
                name: 'requerimientos.asignados',
                component: BladeViewPlaceholder, // Placeholder
                props: true
            },
            {
                path: 'requerimientos/atendidos/:rol',
                name: 'requerimientos.atendidos',
                component: BladeViewPlaceholder, // Placeholder
                props: true
            },
            {
                path: 'requerimientos/seguimiento/:rol?',
                name: 'requerimientos.seguimiento',
                component: () => import('@/components/dashboard/DashboardRequerimientos.vue'),
                props: true
            },
            {
                path: 'requerimientos/especialista',
                name: 'requerimientos.especialista',
                component: () => import('@/components/requerimientos/RequerimientosEspecialista.vue'),
            },

            // Gestión por Procesos
            {
                path: 'inventario-gestion',
                name: 'inventario.gestion',
                component: InventarioIndex,
            },

            {
                path: 'documentos',
                name: 'documentos.index',
                component: DocumentoIndex,
            },
            {
                path: 'lmde',
                name: 'documentos.lmde',
                component: () => import('@/components/documentos/DocumentoLMDEIndex.vue'),
            },
            {
                path: 'indicadores-gestion',
                name: 'indicadores.gestion',
                component: () => import('@/components/indicadores/IndicadoresIndex.vue'),
            },
            {
                path: 'partes',
                name: 'partes.index',
                component: () => import('@/components/partes/PartesInteresadasIndex.vue')
            },
            {
                path: 'dashboard/procesos',
                name: 'dashboard.procesos',
                component: () => import('@/components/dashboard/DashboardProcesos.vue')
            },

            // Gestión de la Mejora
            {
                path: 'mejora',
                name: 'hallazgos.index',
                component: HallazgosIndex,
            },
            {
                path: 'mis-hallazgos',
                name: 'hallazgos.mine.vue',
                component: MisHallazgos,
            },
            {
                path: 'bandeja-eficacia',
                name: 'hallazgos.eficacia',
                component: () => import('@/components/hallazgos/BandejaEficacia.vue'),
            },
            {
                path: 'hallazgos/:hallazgoId/acciones',
                name: 'acciones.index',
                component: () => import('@/components/acciones/AccionesIndex.vue'),
                props: true
            },
            {
                path: 'acciones/imprimir/:hallazgoId',
                name: 'acciones.imprimir',
                component: () => import('@/components/acciones/PlanAccionImprimirPage.vue'),
            },
            {
                path: 'dashboard/mejora',
                name: 'dashboard.mejora',
                component: () => import('@/components/dashboard/DashboardMejora.vue'),
            },
            {
                path: 'smp-ouo',
                name: 'smp.ouo.index',
                component: () => import('@/components/hallazgos/HallazgoIndex.vue'),
                props: { fromOuo: true }
            },

            // Gestión de Obligaciones
            {
                path: 'obligaciones',
                name: 'obligaciones.index',
                component: () => import('@/components/obligaciones/ObligacionesIndex.vue'),
            },
            {
                path: 'mis-obligaciones',
                name: 'obligaciones.mine',
                component: () => import('@/components/obligaciones/MisObligaciones.vue'),
            },
            {
                path: 'obligaciones/seguimiento',
                name: 'obligaciones.seguimiento',
                component: BladeViewPlaceholder // Placeholder
            },
            {
                path: 'dashboard/obligaciones',
                name: 'dashboard.obligaciones',
                component: BladeViewPlaceholder // Placeholder
            },
            {
                path: 'radar-obligaciones',
                name: 'radar.index',
                component: () => import('@/components/obligaciones/RadarObligaciones.vue'),
            },

            // Gestión de Riesgos
            {
                path: 'riesgos/index',
                name: 'riesgos.index',
                component: () => import('@/components/riesgos/RiesgosIndex.vue'),
            },
            {
                path: 'riesgos/mis-riesgos',
                name: 'riesgos.mine',
                component: () => import('@/components/riesgos/MisRiesgos.vue'),
            },
            {
                path: 'riesgos/verificacion',
                name: 'riesgos.verificacion',
                component: () => import('@/components/riesgos/RiesgoVerificacionIndex.vue'),
            },
            {
                path: 'dashboard/riesgos',
                name: 'dashboard.riesgos',
                component: BladeViewPlaceholder // Placeholder
            },

            // Gestión de Continuidad
            {
                path: 'continuidad/planes',
                name: 'continuidad.planes',
                component: () => import('@/components/continuidad/PlanesIndex.vue')
            },
            {
                path: 'continuidad/planes/:id',
                name: 'continuidad.plan.detalle',
                component: () => import('@/components/continuidad/PlanDetalle.vue'),
                props: true
            },
            {
                path: 'continuidad/escenarios',
                name: 'continuidad.escenarios',
                component: () => import('@/components/continuidad/EscenariosIndex.vue')
            },
            {
                path: 'continuidad/activos',
                name: 'continuidad.activos',
                component: () => import('@/components/continuidad/ActivosIndex.vue')
            },
            {
                path: 'continuidad/pruebas',
                name: 'continuidad.pruebas',
                component: () => import('@/components/continuidad/PruebasIndex.vue')
            },
            {
                path: 'dashboard/continuidad',
                name: 'dashboard.continuidad',
                component: () => import('@/components/continuidad/DashboardContinuidad.vue')
            },

            // Satisfacción del Cliente
            {
                path: 'salidas-nc',
                name: 'salidas-nc.index',
                component: () => import('@/components/salidas-nc/SalidasNCIndex.vue'),
            },
            {
                path: 'sugerencias',
                name: 'sugerencias.index',
                component: () => import('@/components/sugerencias/SugerenciasIndex.vue'),
            },
            {
                path: 'encuestas-satisfaccion',
                name: 'encuestas.index',
                component: () => import('@/components/encuestas/EncuestasIndex.vue'),
            },
            {
                path: 'encuestas-satisfaccion/dashboard',
                name: 'encuestas.dashboard',
                component: () => import('@/components/encuestas/EncuestasDashboard.vue'),
            },
            {
                path: 'reportes-satisfaccion',
                name: 'reportes-satisfaccion.index',
                component: () => import('@/components/satisfaccion/ReporteSatisfaccionIndex.vue')
            },
            {
                path: 'reportes-satisfaccion/nuevo/:id?',
                name: 'reportes-satisfaccion.wizard',
                component: () => import('@/components/satisfaccion/ReporteSatisfaccionWizard.vue'),
                props: true // Pass route params as props
            },

            // Alta Dirección - Revisión por la Dirección
            {
                path: 'revision-direccion',
                name: 'revision-direccion.index',
                component: () => import('@/components/revision-direccion/RevisionDireccionIndex.vue'),
            },
            {
                path: 'revision-direccion/:id',
                name: 'revision-direccion.detalle',
                component: () => import('@/components/revision-direccion/RevisionDireccionDetalle.vue'),
                props: true
            },

            // Administración
            {
                path: 'administracion/usuarios',
                name: 'administracion.usuarios.index',
                component: UsuariosIndex,
            },
            {
                path: 'administracion/asignacion-ouos',
                name: 'administracion.asignacion-ouos.index',
                component: () => import('@/components/administracion/AsignacionUsuariosIndex.vue'),
            },
            {
                path: 'administracion/configuracion',
                name: 'administracion.configuracion',
                component: BladeViewPlaceholder // Placeholder
            },
            {
                path: 'dashboard/administracion',
                name: 'dashboard.administracion',
                component: BladeViewPlaceholder // Placeholder
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory('/vue/'),
    routes,
});

export default router;
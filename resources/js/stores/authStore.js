import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

/**
 * Configuración de menús por rol
 * Define qué módulos son visibles para cada tipo de usuario
 */
const menuConfigByRole = {
    admin: {
        modules: ['*'], // Todos los módulos
        priority: 1
    },
    especialista: {
        modules: [
            'documentacion',
            'requerimientos',
            'procesos',
            'mejora',
            'obligaciones',
            'riesgos',
            'auditoria',
            'continuidad',
            'satisfaccion',
            'direccion'
        ],
        priority: 2
    },
    propietario: {
        modules: [
            'documentacion',
            'requerimientos',
            'procesos',
            'mejora',
            'riesgos',
            'indicadores',
            'direccion'
        ],
        priority: 3
    },
    facilitador: {
        modules: [
            'documentacion',
            'requerimientos',
            'procesos',
            'mejora'
        ],
        priority: 4
    },
    // Roles adicionales existentes (mapear a los nuevos)
    subgerente: {
        modules: [
            'documentacion',
            'requerimientos',
            'procesos',
            'mejora',
            'riesgos',
            'indicadores',
            'direccion'
        ],
        priority: 3 // Mismo nivel que propietario
    },
    supervisor: {
        modules: [
            'documentacion',
            'requerimientos',
            'procesos',
            'mejora',
            'obligaciones',
            'riesgos',
            'auditoria'
        ],
        priority: 2
    },
    gestor: {
        modules: [
            'documentacion',
            'requerimientos',
            'procesos',
            'mejora',
            'riesgos'
        ],
        priority: 4
    }
};

/**
 * Permisos por módulo y rol
 */
const modulePermissions = {
    documentacion: {
        facilitador: ['view'],
        propietario: ['view', 'approve'],
        especialista: ['view', 'manage'],
        admin: ['view', 'manage', 'delete']
    },
    requerimientos: {
        facilitador: ['view', 'create', 'edit_own'],
        propietario: ['view', 'approve'],
        especialista: ['view', 'assign', 'monitor'],
        admin: ['view', 'create', 'edit', 'delete', 'assign', 'approve']
    },
    procesos: {
        facilitador: ['view', 'register_data'],
        propietario: ['view', 'approve'],
        especialista: ['view', 'configure'],
        admin: ['view', 'create', 'edit', 'delete', 'configure']
    },
    mejora: {
        facilitador: ['view', 'register', 'attend'],
        propietario: ['view', 'approve_close'],
        especialista: ['view', 'assign', 'verify'],
        admin: ['view', 'create', 'edit', 'delete', 'assign', 'verify']
    },
    riesgos: {
        facilitador: ['view', 'identify'],
        propietario: ['view', 'approve_treatment'],
        especialista: ['view', 'evaluate', 'monitor'],
        admin: ['view', 'create', 'edit', 'delete', 'evaluate', 'monitor']
    },
    obligaciones: {
        facilitador: ['view', 'register_evidence'],
        propietario: ['view', 'approve_compliance'],
        especialista: ['view', 'monitor'],
        admin: ['view', 'create', 'edit', 'delete', 'monitor']
    },
    auditoria: {
        facilitador: ['view', 'attend_findings'],
        propietario: ['view', 'approve_plans'],
        especialista: ['view', 'plan', 'execute'],
        admin: ['view', 'plan', 'execute', 'manage']
    },
    continuidad: {
        facilitador: ['view', 'register_assets'],
        propietario: ['view', 'approve_plans'],
        especialista: ['view', 'plan'],
        admin: ['view', 'create', 'edit', 'delete', 'plan']
    },
    direccion: {
        facilitador: [],
        propietario: ['view', 'participate'],
        especialista: ['view', 'prepare'],
        admin: ['view', 'manage']
    },
    satisfaccion: {
        facilitador: ['view'],
        propietario: ['view'],
        especialista: ['view', 'manage'],
        admin: ['view', 'manage', 'delete']
    },
    administracion: {
        facilitador: [],
        propietario: [],
        especialista: ['view_limited'],
        admin: ['view', 'manage']
    }
};

export const useAuthStore = defineStore('auth', () => {
    const user = ref(window.App?.user || null);

    const isAuthenticated = computed(() => !!user.value);

    const roles = computed(() => user.value?.roles || []);

    /**
     * Obtiene el rol principal del usuario (el de mayor prioridad)
     */
    const primaryRole = computed(() => {
        if (roles.value.length === 0) return null;

        let highestPriority = Infinity;
        let primaryRoleName = roles.value[0];

        for (const role of roles.value) {
            const config = menuConfigByRole[role];
            if (config && config.priority < highestPriority) {
                highestPriority = config.priority;
                primaryRoleName = role;
            }
        }

        return primaryRoleName;
    });

    const hasRole = (roleName) => {
        return roles.value.includes(roleName);
    };

    const hasAnyRole = (roleNames) => {
        return roleNames.some(role => hasRole(role));
    };

    /**
     * Verifica si el usuario puede acceder a un módulo específico
     */
    const canAccessModule = (moduleName) => {
        if (!isAuthenticated.value) return false;

        for (const role of roles.value) {
            const config = menuConfigByRole[role];
            if (config) {
                if (config.modules.includes('*') || config.modules.includes(moduleName)) {
                    return true;
                }
            }
        }
        return false;
    };

    /**
     * Obtiene los módulos disponibles para el usuario según sus roles
     */
    const getMenusForRole = () => {
        const availableModules = new Set();

        for (const role of roles.value) {
            const config = menuConfigByRole[role];
            if (config) {
                if (config.modules.includes('*')) {
                    // Admin: retornar todos los módulos
                    return Object.keys(modulePermissions);
                }
                config.modules.forEach(module => availableModules.add(module));
            }
        }

        return Array.from(availableModules);
    };

    /**
     * Obtiene los permisos específicos del usuario para un módulo
     */
    const getPermissionsForModule = (moduleName) => {
        const permissions = new Set();

        for (const role of roles.value) {
            const modulePerms = modulePermissions[moduleName]?.[role];
            if (modulePerms) {
                modulePerms.forEach(perm => permissions.add(perm));
            }
            // Admin tiene todos los permisos
            if (role === 'admin') {
                return ['view', 'create', 'edit', 'delete', 'manage', 'approve', 'assign'];
            }
        }

        return Array.from(permissions);
    };

    /**
     * Verifica si el usuario tiene un permiso específico en un módulo
     */
    const hasPermission = (moduleName, permission) => {
        const permissions = getPermissionsForModule(moduleName);
        return permissions.includes(permission);
    };

    const login = async (credentials) => {
        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.post('/login', credentials);
        user.value = response.data.user;
        window.location.href = '/home';
    };

    const logout = async () => {
        await axios.post('/logout');
        user.value = null;
        window.location.href = '/login';
    };

    return {
        user,
        isAuthenticated,
        roles,
        primaryRole,
        hasRole,
        hasAnyRole,
        canAccessModule,
        getMenusForRole,
        getPermissionsForModule,
        hasPermission,
        login,
        logout
    };
});

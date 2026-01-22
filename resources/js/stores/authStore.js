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
            'mejora',
            'satisfaccion'
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
    const activeRole = ref(localStorage.getItem('activeRole') || null);

    const isAuthenticated = computed(() => !!user.value);

    const roles = computed(() => user.value?.roles || []);
    const permissions = computed(() => user.value?.permissions || []);

    /**
     * Obtiene la configuración de un rol (manejando mayúsculas/minúsculas)
     */
    const getRoleConfig = (roleName) => {
        if (!roleName) return null;
        const roleKey = roleName.toLowerCase();
        // Mapeos adicionales si los nombres de la DB difieren de las llaves del config
        if (roleKey === 'administrador') return menuConfigByRole['admin'];
        return menuConfigByRole[roleKey] || null;
    };

    /**
     * Obtiene el rol por defecto del usuario (el de menor privilegios / mayor número de prioridad)
     */
    const primaryRole = computed(() => {
        if (roles.value.length === 0) return null;

        let lowestPriorityLevel = -Infinity;
        let primaryRoleName = roles.value[0];

        for (const role of roles.value) {
            const config = getRoleConfig(role);
            if (config && config.priority > lowestPriorityLevel) {
                lowestPriorityLevel = config.priority;
                primaryRoleName = role;
            }
        }

        return primaryRoleName;
    });

    /**
     * Obtiene el rol actualmente seleccionado o el principal por defecto
     */
    const currentRole = computed(() => {
        if (!activeRole.value || !roles.value.includes(activeRole.value)) {
            return primaryRole.value;
        }
        return activeRole.value;
    });

    const setActiveRole = (role) => {
        if (roles.value.includes(role)) {
            activeRole.value = role;
            localStorage.setItem('activeRole', role);
        }
    };

    const hasRole = (roleName) => {
        return currentRole.value?.toLowerCase() === roleName.toLowerCase();
    };

    const hasAnyRole = (roleNames) => {
        return roleNames.some(role => hasRole(role));
    };

    /**
     * Verifica si el usuario puede acceder a un módulo específico basado en el rol activo
     */
    const canAccessModule = (moduleName) => {
        if (!isAuthenticated.value) return false;

        const role = currentRole.value;
        const config = getRoleConfig(role);
        if (config) {
            if (config.modules.includes('*') || config.modules.includes(moduleName)) {
                return true;
            }
        }
        return false;
    };

    /**
     * Obtiene los módulos disponibles para el usuario según el rol activo
     */
    const getMenusForRole = () => {
        const role = currentRole.value;
        const config = getRoleConfig(role);
        if (config) {
            if (config.modules.includes('*')) {
                // Admin: retornar todos los módulos
                return Object.keys(modulePermissions);
            }
            return config.modules;
        }

        return [];
    };

    /**
     * Obtiene los permisos específicos del usuario para un módulo según el rol activo
     */
    const getPermissionsForModule = (moduleName) => {
        const role = currentRole.value;
        if (!role) return [];

        const config = getRoleConfig(role);
        // Admin tiene todos los permisos
        if (config && config.modules.includes('*')) {
            return ['view', 'create', 'edit', 'delete', 'manage', 'approve', 'assign'];
        }

        // Mapear rol a llave de modulePermissions (normalizar)
        const roleKey = role.toLowerCase();
        const permissionsForModule = modulePermissions[moduleName]?.[roleKey] || [];
        return permissionsForModule;
    };

    /**
     * Verifica si el usuario tiene un permiso específico en un módulo según el rol activo
     */
    const hasPermission = (moduleName, permission) => {
        const permissions = getPermissionsForModule(moduleName);
        return permissions.includes(permission);
    };

    /**
     * Verifica si el usuario tiene un permiso específico (basado en Spatie permissions)
     * Respetando el rol activo y sus módulos permitidos.
     */
    const can = (permissionName) => {
        if (!isAuthenticated.value) return false;

        // 1. Verificar si el usuario TIENE el permiso (Spatie)
        if (!permissions.value.includes(permissionName)) {
            return false;
        }

        // 2. Si es Admin, tiene acceso a todo lo que posea
        const role = currentRole.value;
        const config = getRoleConfig(role);
        if (config && config.modules.includes('*')) {
            return true;
        }

        // 3. Filtrar permisos por módulo (ej: menu.documentacion.inventario)
        const parts = permissionName.split('.');
        if (parts.length >= 2 && parts[0] === 'menu') {
            const moduleName = parts[1];
            // Si el rol activo no puede ver este módulo, denegar permiso
            if (!canAccessModule(moduleName)) {
                return false;
            }
        }

        return true;
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
        activeRole,
        currentRole,
        primaryRole,
        setActiveRole,
        hasRole,
        hasAnyRole,
        canAccessModule,
        getMenusForRole,
        getPermissionsForModule,
        hasPermission,
        can,
        login,
        logout
    };
});
